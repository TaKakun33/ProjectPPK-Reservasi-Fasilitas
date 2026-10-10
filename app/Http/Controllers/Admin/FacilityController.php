<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SimpanFasilitasRequest;
use App\Models\Facility;
use App\Models\LogStatusReservasi;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
        $query = Facility::with('photos');

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
        return view('admin.facilities.create', $this->opsiIsian());
    }

    // Menyimpan data fasilitas baru ke database (default status 'aktif') beserta foto-fotonya (opsional, maks. 5)
    public function store(SimpanFasilitasRequest $request)
    {
        $validated = $request->validated();

        $validated['facility_status'] = 'aktif';
        $validated['amenities'] = $this->bersihkanSarana($validated['amenities'] ?? []);
        unset($validated['photos'], $validated['remove_photos'], $validated['cover_photo']);

        try {
            $berkasBaru = $this->simpanBerkas($request);
        } catch (\RuntimeException $e) {
            return back()->withInput()->withErrors(['photos' => $e->getMessage()]);
        }

        try {
            DB::transaction(function () use ($validated, $berkasBaru) {
                $fasilitas = Facility::create($validated);

                foreach ($berkasBaru as $i => $path) {
                    $fasilitas->photos()->create(['photo_path' => $path, 'urutan' => $i]);
                }
            });
        } catch (\Throwable $e) {
            // Jangan tinggalkan berkas yatim bila penyimpanan data gagal
            $this->hapusBerkas($berkasBaru);
            throw $e;
        }

        return redirect()->route('admin.fasilitas.index')
            ->with('success', 'Data fasilitas kampus berhasil ditambahkan ke dalam sistem.');
    }

    // Menampilkan form edit fasilitas
    public function edit(Request $request, Facility $fasilitas)
    {
        $fasilitas->load('photos');

        // Permintaan AJAX (pop-up sunting): kembalikan formulirnya saja, tanpa layout halaman
        if ($request->ajax()) {
            return view('admin.facilities.partials.form-sunting', ['fasilitas' => $fasilitas, 'modal' => true] + $this->opsiIsian());
        }

        return view('admin.facilities.edit', ['fasilitas' => $fasilitas] + $this->opsiIsian());
    }

    // Memperbarui data fasilitas, menghapus foto terpilih, menambah foto baru, dan mengatur foto utama
    public function update(SimpanFasilitasRequest $request, Facility $fasilitas)
    {
        $validated = $request->validated();
        // Tanpa item sama sekali, field tidak terkirim: dianggap daftar kosong
        $validated['amenities'] = $this->bersihkanSarana($validated['amenities'] ?? []);
        unset($validated['photos'], $validated['remove_photos'], $validated['cover_photo']);

        $idHapus = array_values(array_filter((array) $request->input('remove_photos', []), 'is_string'));
        $idUtama = $request->input('cover_photo');

        try {
            $berkasBaru = $this->simpanBerkas($request);
        } catch (\RuntimeException $e) {
            return back()->withInput()->withErrors(['photos' => $e->getMessage()]);
        }

        $berkasTerhapus = [];

        try {
            DB::transaction(function () use ($fasilitas, $validated, $idHapus, $idUtama, $berkasBaru, &$berkasTerhapus) {
                $fasilitas->update($validated);

                // Hapus hanya foto milik fasilitas ini
                foreach ($fasilitas->photos()->whereIn('id_foto', $idHapus)->get() as $foto) {
                    $berkasTerhapus[] = $foto->photo_path;
                    $foto->delete();
                }

                // Foto baru ditaruh di belakang dulu; urutan dirapikan di bawah
                foreach ($berkasBaru as $i => $path) {
                    $fasilitas->photos()->create(['photo_path' => $path, 'urutan' => 1000 + $i]);
                }

                // Susun ulang urutan 0..n-1; foto yang dipilih sebagai utama ditaruh paling depan
                $semua = $fasilitas->photos()->orderBy('urutan')->get();

                if ($idUtama) {
                    $semua = $semua->sortBy(fn ($foto) => $foto->id_foto === $idUtama ? 0 : 1)->values();
                }

                foreach ($semua as $i => $foto) {
                    if ((int) $foto->urutan !== $i) {
                        $foto->update(['urutan' => $i]);
                    }
                }
            });
        } catch (\Throwable $e) {
            $this->hapusBerkas($berkasBaru);
            throw $e;
        }

        // Berkas foto yang dihapus baru dibuang setelah data berhasil tersimpan
        $this->hapusBerkas($berkasTerhapus);

        return redirect()->route('admin.fasilitas.index')
            ->with('success', 'Pembaruan data fasilitas kampus berhasil disimpan.');
    }

    // Daftar kategori & lokasi yang sudah dipakai fasilitas lain, untuk pilihan combo box di form
    public function opsiIsian(): array
    {
        return [
            'daftarKategori' => Facility::query()->whereNotNull('type')->distinct()->orderBy('type')->pluck('type')->all(),
            'daftarLokasi'   => Facility::query()->whereNotNull('location')->distinct()->orderBy('location')->pluck('location')->all(),
            // Saran sarana penunjang: yang sudah pernah dipakai fasilitas lain
            'daftarSarana'   => Facility::query()->whereNotNull('amenities')->pluck('amenities')
                ->flatten()->filter(fn ($item) => is_string($item) && $item !== '')
                ->unique()->sort()->values()->take(40)->all(),
        ];
    }

    // Rapikan daftar sarana penunjang: buang spasi berlebih, item kosong, dan duplikat. Kosong => null.
    private function bersihkanSarana(array $items): ?array
    {
        $bersih = [];

        foreach ($items as $item) {
            $item = trim(preg_replace('/\s+/u', ' ', (string) $item));

            if ($item !== '' && ! in_array(mb_strtolower($item), array_map('mb_strtolower', $bersih), true)) {
                $bersih[] = $item;
            }
        }

        return $bersih ?: null;
    }

    // Menyimpan berkas foto yang diunggah ke disk privat; mengembalikan daftar lokasinya.
    // Bila ada yang gagal, berkas yang sudah tersimpan dibuang lagi.
    private function simpanBerkas(Request $request): array
    {
        $paths = [];

        foreach ((array) $request->file('photos', []) as $berkas) {
            $path = $berkas->store('fasilitas', 'local');

            if ($path === false) {
                $this->hapusBerkas($paths);
                throw new \RuntimeException('Foto gagal disimpan. Silakan coba lagi.');
            }

            $paths[] = $path;
        }

        return $paths;
    }

    private function hapusBerkas(array $paths): void
    {
        if ($paths) {
            Storage::disk('local')->delete($paths);
        }
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

            $alasan = 'Fasilitas dinonaktifkan oleh administrator.';

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
                "Fasilitas tidak dapat dinonaktifkan: masih terdapat {$hasil['jumlah']} permohonan reservasi berstatus disetujui yang akan berlangsung. "
                . 'Silakan koordinasikan dengan petugas untuk pembatalan reservasi terlebih dahulu.');
        }

        $pesan = 'Fasilitas kampus berhasil dinonaktifkan dari sistem operasional.';

        if ($hasil['jumlah'] > 0) {
            $pesan .= " Sebanyak {$hasil['jumlah']} permohonan reservasi menunggu persetujuan dibatalkan secara otomatis.";
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
            ? 'Fasilitas kampus telah diaktifkan kembali dengan status "dalam perbaikan" karena masih terdapat laporan kerusakan yang sedang ditangani oleh petugas.'
            : 'Fasilitas kampus berhasil diaktifkan kembali.';

        return redirect()->route('admin.fasilitas.index')
            ->with('success', $message);
    }
}
