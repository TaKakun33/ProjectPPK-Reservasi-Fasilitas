<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SimpanFasilitasRequest;
use App\Models\Facility;
use App\Models\LogStatusReservasi;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Controller untuk manajemen fasilitas oleh Admin (CRUD & aktivasi)
class FacilityController extends Controller
{
    // Escape karakter wildcard LIKE supaya input pencarian diperlakukan sebagai teks biasa
    private function escapeLike(string $nilai): string
    {
        return addcslashes($nilai, '%_\\');
    }

    // Menampilkan daftar fasilitas dengan filter status dan fitur pencarian
    public function index(Request $request)
    {
        $query = Facility::query();

        if ($request->filled('search')) {
            $search = $this->escapeLike(mb_substr((string) $request->search, 0, 100));
            $query->where(function ($q) use ($search) {
                $q->where('facility_name', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && in_array($request->status, ['aktif', 'dalam perbaikan', 'nonaktif'], true)) {
            $query->where('facility_status', $request->status);
        }

        $facilities = $query->latest()->paginate(10)->withQueryString();

        return view('admin.facilities.index', compact('facilities'));
    }

    // Menampilkan form tambah fasilitas baru
    public function create()
    {
        return view('admin.facilities.create');
    }

    // Menyimpan data fasilitas baru ke database (default status 'aktif')
    public function store(SimpanFasilitasRequest $request)
    {
        $validated = $request->validated();

        $validated['facility_status'] = 'aktif';

        Facility::create($validated);

        return redirect()->route('admin.fasilitas.index')
            ->with('success', 'Fasilitas berhasil ditambahkan.');
    }

    // Menampilkan form edit fasilitas
    public function edit(Facility $fasilitas)
    {
        return view('admin.facilities.edit', compact('fasilitas'));
    }

    // Memperbarui data fasilitas
    public function update(SimpanFasilitasRequest $request, Facility $fasilitas)
    {
        $validated = $request->validated();

        $fasilitas->update($validated);

        return redirect()->route('admin.fasilitas.index')
            ->with('success', 'Fasilitas berhasil diperbarui.');
    }

    // Menonaktifkan fasilitas (mengubah status menjadi 'nonaktif').
    // Ditolak bila masih ada reservasi DISETUJUI yang akan berlangsung (pemohon bisa datang ke fasilitas yang tutup);
    // reservasi PENDING yang akan berlangsung ditolak otomatis dengan alasan tercatat.
    public function destroy(Request $request, Facility $fasilitas)
    {
        $adminId = $request->user()->id_user;

        $hasil = DB::transaction(function () use ($fasilitas, $adminId) {
            // Kunci baris fasilitas: pengajuan reservasi baru juga mengunci baris ini, jadi tidak ada yang menyelip
            $f = Facility::whereKey($fasilitas->id_fasilitas)->lockForUpdate()->first();

            if (! $f) {
                return ['status' => 'hilang', 'jumlah' => 0];
            }

            $jumlahApproved = Reservation::where('id_fasilitas', $f->id_fasilitas)
                ->where('reservation_status', 'approved')
                ->mendatang()
                ->count();

            if ($jumlahApproved > 0) {
                return ['status' => 'ditolak', 'jumlah' => $jumlahApproved];
            }

            $alasan = 'Fasilitas dinonaktifkan oleh admin.';

            $pending = Reservation::where('id_fasilitas', $f->id_fasilitas)
                ->where('reservation_status', 'pending')
                ->mendatang()
                ->lockForUpdate()
                ->get();

            foreach ($pending as $reservasi) {
                $reservasi->update([
                    'reservation_status' => 'rejected',
                    'alasan_ditolak'     => $alasan,
                    'processed_by'       => $adminId,
                ]);

                LogStatusReservasi::create([
                    'id_reservasi'  => $reservasi->id_reservasi,
                    'status_before' => 'pending',
                    'status_after'  => 'rejected',
                    'changed_by'    => $adminId,
                    'notes'         => $alasan,
                    'created_at'    => now(),
                ]);
            }

            $f->update(['facility_status' => 'nonaktif']);

            return ['status' => 'ok', 'jumlah' => $pending->count()];
        });

        if ($hasil['status'] === 'hilang') {
            abort(404);
        }

        if ($hasil['status'] === 'ditolak') {
            return redirect()->route('admin.fasilitas.index')->with('error',
                "Fasilitas tidak dapat dinonaktifkan: masih ada {$hasil['jumlah']} reservasi disetujui yang akan berlangsung. "
                . 'Minta petugas membatalkannya (dengan alasan) di menu Reservasi, lalu nonaktifkan lagi.');
        }

        $pesan = 'Fasilitas berhasil dinonaktifkan.';

        if ($hasil['jumlah'] > 0) {
            $pesan .= " {$hasil['jumlah']} reservasi pending yang akan berlangsung ditolak otomatis.";
        }

        return redirect()->route('admin.fasilitas.index')->with('success', $pesan);
    }

    // Mengaktifkan kembali fasilitas yang dinonaktifkan
    public function activate(Facility $fasilitas)
    {
        // Sebelum diaktifkan, cek dulu: apakah fasilitas ini masih punya
        // laporan kerusakan 'diproses' yang oleh petugas ditandai menutup fasilitas?
        // Kalau iya, jangan langsung dibuat 'aktif' — kembalikan ke
        // 'dalam perbaikan' supaya statusnya tetap konsisten dengan
        // laporan yang belum selesai (bukan hasil keputusan admin lagi).
        // Cek + update dilakukan dalam satu transaksi dengan lock fasilitas
        // (urutan lock sama dengan modul petugas: fasilitas dulu).
        $masihDiperbaiki = DB::transaction(function () use ($fasilitas) {
            $f = Facility::whereKey($fasilitas->id_fasilitas)->lockForUpdate()->firstOrFail();

            $diperbaiki = $f->reports()
                ->where('report_status', 'diproses')
                ->where('menutup_fasilitas', true)
                ->exists();

            $f->update([
                'facility_status' => $diperbaiki ? 'dalam perbaikan' : 'aktif',
            ]);

            return $diperbaiki;
        });

        $message = $masihDiperbaiki
            ? 'Fasilitas diaktifkan, namun statusnya dikembalikan ke "dalam perbaikan" karena masih ada laporan kerusakan yang sedang diproses petugas.'
            : 'Fasilitas berhasil diaktifkan kembali.';

        return redirect()->route('admin.fasilitas.index')
            ->with('success', $message);
    }
}
