<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Petugas\UbahStatusLaporanRequest;
use App\Models\Facility;
use App\Models\LogStatusLaporan;
use App\Models\LogStatusReservasi;
use App\Models\Report;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

// Controller penanganan laporan kerusakan fasilitas oleh Petugas
class ReportController extends Controller
{
    // Menampilkan daftar antrian & riwayat laporan kerusakan
    public function index(Request $request)
    {
        $query = Report::with(['user', 'facility', 'category', 'photos']);

        if ($request->filled('status') && in_array($request->status, ['baru', 'diproses', 'selesai', 'ditolak'])) {
            $query->where('report_status', $request->status);
        }

        $reports = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $selectedStatus = $request->input('status', 'all');

        return view('petugas.reports.index', compact('reports', 'selectedStatus'));
    }

    // Menampilkan halaman detail satu laporan kerusakan dan form aksi penanganannya
    public function show(Request $request, string $laporan)
    {
        $report = Report::with(['user', 'facility', 'category', 'photos'])->findOrFail($laporan);

        // Reservasi disetujui yang masih akan berlangsung di fasilitas ini: terdampak bila fasilitas masuk perbaikan
        $reservasiTerdampak = collect();
        if (in_array($report->report_status, ['baru', 'diproses'], true)) {
            $reservasiTerdampak = Reservation::with('user')
                ->where('id_fasilitas', $report->id_fasilitas)
                ->where('reservation_status', 'approved')
                ->mendatang()
                ->orderBy('date')
                ->orderBy('start_time')
                ->limit(10)
                ->get();
        }

        // Laporan lain yang masih terbuka untuk fasilitas + kategori yang sama (indikasi laporan ganda)
        $laporanSerupa = Report::where('id_fasilitas', $report->id_fasilitas)
            ->where('id_kategori', $report->id_kategori)
            ->where('id_laporan', '!=', $report->id_laporan)
            ->whereIn('report_status', ['baru', 'diproses'])
            ->count();

        return view('petugas.reports.show', [
            'laporan'            => $report,
            'reservasiTerdampak' => $reservasiTerdampak,
            'laporanSerupa'      => $laporanSerupa,
        ]);
    }

    // Transisi status yang diizinkan (E7): laporan tidak boleh "mundur" atau lompat sembarangan
    private const TRANSISI = [
        'baru'     => ['diproses', 'ditolak'],
        'diproses' => ['selesai', 'ditolak'],
        'selesai'  => [],
        'ditolak'  => [],
    ];

    // Memperbarui status penanganan laporan (diproses/selesai/ditolak) dan sinkronisasi status fasilitas
    public function updateStatus(UbahStatusLaporanRequest $request, string $laporan)
    {
        $validated = $request->validated();

        // Hanya relevan saat laporan mulai diproses; petugas yang memutuskan, bukan otomatis
        $tutupFasilitas = $validated['report_status'] === 'diproses' && $request->boolean('menutup_fasilitas');

        // Jumlah reservasi yang dibatalkan/ditolak otomatis saat fasilitas ditutup
        $jumlahDibatalkan = 0;
        $jumlahDitolak    = 0;

        DB::transaction(function () use ($laporan, $request, $validated, $tutupFasilitas, &$jumlahDibatalkan, &$jumlahDitolak) {
            // Urutan lock SELALU fasilitas dulu, baru laporan: dua petugas yang menangani laporan
            // berbeda pada fasilitas yang sama jadi berurutan, sehingga status fasilitas dihitung
            // dari kondisi terbaru (tidak salah menimpa 'aktif' padahal masih ada laporan 'diproses').
            $idFasilitas = Report::where('id_laporan', $laporan)->value('id_fasilitas');
            abort_if($idFasilitas === null, 404);

            Facility::whereKey($idFasilitas)->lockForUpdate()->first();

            // Lock baris laporan agar dua petugas tidak mengubah status bersamaan
            $report = Report::where('id_laporan', $laporan)->lockForUpdate()->firstOrFail();

            $diizinkan = self::TRANSISI[$report->report_status] ?? [];
            if (! in_array($validated['report_status'], $diizinkan, true)) {
                throw ValidationException::withMessages([
                    'report_status' => "Status laporan '{$report->report_status}' tidak dapat diubah menjadi '{$validated['report_status']}'.",
                ]);
            }

            $statusBefore = $report->report_status;

            $report->update([
                'report_status'    => $validated['report_status'],
                'menutup_fasilitas' => $validated['report_status'] === 'diproses' ? $tutupFasilitas : $report->menutup_fasilitas,
                // Jangan menimpa catatan lama dengan null bila petugas tidak mengisi catatan baru
                'resolution_notes' => $validated['resolution_notes'] ?? $report->resolution_notes,
                'handled_by'       => $request->user()->id_user,
            ]);

            // Status fasilitas DITURUNKAN dari kondisi laporan yang masih berjalan (bukan toggle buta)
            $this->sinkronkanStatusFasilitas($report->id_fasilitas);

            // Fasilitas ditutup oleh keputusan petugas: reservasi mendatang tidak boleh menggantung.
            // Approved -> dibatalkan, pending -> ditolak, keduanya dengan alasan dan tercatat di log.
            if ($tutupFasilitas) {
                [$jumlahDibatalkan, $jumlahDitolak] = $this->tutupReservasiMendatang($report->id_fasilitas, $request->user()->id_user);
            }

            LogStatusLaporan::create([
                'id_laporan'    => $report->id_laporan,
                'status_before' => $statusBefore,
                'status_after'  => $validated['report_status'],
                'changed_by'    => $request->user()->id_user,
                'notes'         => $validated['resolution_notes'] ?? null,
                'created_at'    => now(),
            ]);
        });

        $redirect = redirect()
            ->route('petugas.reports.show', $laporan)
            ->with('success', 'Status laporan berhasil diperbarui.');

        if ($jumlahDibatalkan > 0 || $jumlahDitolak > 0) {
            $redirect->with('warning', "Fasilitas ditutup untuk perbaikan: {$jumlahDibatalkan} reservasi disetujui dibatalkan dan "
                . "{$jumlahDitolak} reservasi menunggu ditolak otomatis (alasan tercatat dan terlihat oleh pemesan).");
        }

        return $redirect;
    }

    // Fasilitas = 'dalam perbaikan' selama masih ada SATU laporan 'diproses' yang oleh petugas
    // ditandai menutup fasilitas; kembali 'aktif' bila tidak ada lagi. Laporan 'diproses' yang
    // tidak menutup fasilitas tidak mengubah status. Fasilitas 'nonaktif' (keputusan admin)
    // tidak pernah ditimpa.
    private function sinkronkanStatusFasilitas(string $idFasilitas): void
    {
        $masihDiperbaiki = Report::where('id_fasilitas', $idFasilitas)
            ->where('report_status', 'diproses')
            ->where('menutup_fasilitas', true)
            ->exists();

        Facility::where('id_fasilitas', $idFasilitas)
            ->where('facility_status', '!=', 'nonaktif')
            ->update(['facility_status' => $masihDiperbaiki ? 'dalam perbaikan' : 'aktif']);
    }

    // Batalkan reservasi approved dan tolak reservasi pending yang belum selesai di fasilitas ini.
    // Dipanggil di dalam transaksi yang sudah mengunci baris fasilitas. Mengembalikan [dibatalkan, ditolak].
    private function tutupReservasiMendatang(string $idFasilitas, string $petugasId): array
    {
        $alasan = 'Fasilitas ditutup untuk perbaikan (laporan kerusakan sedang ditangani petugas).';

        $dibatalkan = 0;
        $ditolak    = 0;

        $daftar = Reservation::where('id_fasilitas', $idFasilitas)
            ->whereIn('reservation_status', ['approved', 'pending'])
            ->mendatang()
            ->lockForUpdate()
            ->get();

        foreach ($daftar as $reservasi) {
            $statusBefore = $reservasi->reservation_status;

            if ($statusBefore === 'approved') {
                $reservasi->update([
                    'reservation_status'  => 'cancelled',
                    'cancellation_reason' => $alasan,
                    'processed_by'        => $petugasId,
                ]);
                $statusAfter = 'cancelled';
                $dibatalkan++;
            } else {
                $reservasi->update([
                    'reservation_status' => 'rejected',
                    'alasan_ditolak'     => $alasan,
                    'processed_by'       => $petugasId,
                ]);
                $statusAfter = 'rejected';
                $ditolak++;
            }

            LogStatusReservasi::create([
                'id_reservasi'  => $reservasi->id_reservasi,
                'status_before' => $statusBefore,
                'status_after'  => $statusAfter,
                'changed_by'    => $petugasId,
                'notes'         => $alasan,
                'created_at'    => now(),
            ]);
        }

        return [$dibatalkan, $ditolak];
    }
}
