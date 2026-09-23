<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\LogStatusLaporan;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Controller penanganan laporan kerusakan fasilitas oleh Petugas
class ReportController extends Controller
{
    // Menampilkan daftar antrian laporan kerusakan yang perlu ditangani ('baru' dan 'diproses')
    public function index(Request $request)
    {
        // Tampilkan laporan yang masih dalam proses penanganan: 'baru' dan 'diproses'.
        // Laporan 'diproses' tetap muncul supaya petugas bisa menandainya 'selesai'.
        $reports = Report::with(['user', 'facility', 'category', 'photos'])
            ->whereIn('report_status', ['baru', 'diproses'])
            ->orderBy('created_at', 'asc')
            ->paginate(15);

        return view('petugas.reports.index', compact('reports'));
    }

    // Menampilkan halaman detail satu laporan kerusakan dan form aksi penanganannya
    public function show(Request $request, string $laporan)
    {
        $report = Report::with(['user', 'facility', 'category', 'photos'])->findOrFail($laporan);

        return view('petugas.reports.show', ['laporan' => $report]);
    }

    // Memperbarui status penanganan laporan (diproses/selesai/ditolak) dan sinkronisasi status fasilitas
    public function updateStatus(Request $request, string $laporan)
    {
        $report = Report::findOrFail($laporan);

        // Validasi input: status baru + catatan. Catatan hanya WAJIB saat
        // menolak laporan ('ditolak'); untuk 'diproses' & 'selesai' opsional.
        $validated = $request->validate([
            'report_status'    => ['required', 'in:diproses,selesai,ditolak'],
            'resolution_notes' => ['nullable', 'string', 'max:1000', 'required_if:report_status,ditolak'],
        ], [
            'resolution_notes.required_if' => 'Catatan untuk pelapor wajib diisi saat menolak laporan.',
        ]);

        DB::transaction(function () use ($report, $request, $validated) {
            $statusBefore = $report->report_status;

            // Update laporan: status, catatan resolusi, dan siapa yang menangani.
            $report->update([
                'report_status'    => $validated['report_status'],
                'resolution_notes' => $validated['resolution_notes'],
                'handled_by'       => $request->user()->id_user,
            ]);

            // Toggle status fasilitas sesuai status laporan:
            // - laporan 'diproses' -> fasilitas jadi 'dalam perbaikan'
            // - laporan 'selesai'  -> fasilitas kembali 'aktif'
            // - laporan 'ditolak'  -> fasilitas tidak diubah
            // Pengecualian: kalau admin sudah menonaktifkan fasilitas
            // ('nonaktif', lewat /admin/fasilitas), toggle otomatis ini
            // TIDAK boleh menimpanya balik ke 'aktif' — keputusan admin
            // menang. Fasilitas nonaktif tetap tidak reservable meskipun
            // laporan kerusakannya sudah selesai ditangani.
            if ($validated['report_status'] === 'diproses') {
                Facility::where('id_fasilitas', $report->id_fasilitas)
                    ->where('facility_status', '!=', 'nonaktif')
                    ->update(['facility_status' => 'dalam perbaikan']);
            } elseif ($validated['report_status'] === 'selesai') {
                Facility::where('id_fasilitas', $report->id_fasilitas)
                    ->where('facility_status', '!=', 'nonaktif')
                    ->update(['facility_status' => 'aktif']);
            }

            // Catat log perubahan status ke LogStatusLaporan.
            LogStatusLaporan::create([
                'id_laporan'    => $report->id_laporan,
                'status_before' => $statusBefore,
                'status_after'  => $validated['report_status'],
                'changed_by'    => $request->user()->id_user,
                'notes'         => $validated['resolution_notes'],
            ]);
        });

        return redirect()
            ->route('petugas.reports.show', $report->id_laporan)
            ->with('success', 'Status laporan berhasil diperbarui.');
    }
}
