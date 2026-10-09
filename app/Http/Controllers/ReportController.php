<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\SimpanLaporanRequest;
use App\Models\Facility;
use App\Models\Report;
use App\Models\ReportCategory;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ReportController extends Controller
{
    // Halaman /laporan ini cuma buat role "pengguna". Kalau yang login petugas, 
    // lempar ke halaman laporan miliknya sendiri di /petugas/laporan. Kalau admin, langsung ditolak (403) — admin
    protected function ensurePengguna(): ?RedirectResponse
    {
        $role = auth()->user()->role;

        if ($role === UserRole::Petugas) {
            return redirect()->route('petugas.reports.index');
        }

        abort_if($role === UserRole::Admin, 403, 'Halaman laporan ini khusus untuk pengguna.');

        return null;
    }

    public function index(Request $request)
    {
        if ($redirect = $this->ensurePengguna()) {
            return $redirect;
        }

        $reports = Report::query()
            ->where('id_user', auth()->id())
            ->with(['facility', 'category'])
            ->latest()
            ->paginate(10);

        return view('reports.index', compact('reports'));
    }

    public function create(Request $request)
    {
        if ($redirect = $this->ensurePengguna()) {
            return $redirect;
        }

        $facilities = Facility::visible()
            ->orderBy('facility_name')
            ->get();

        $categories = ReportCategory::where('is_active', true)
            ->orderBy('category_name')
            ->get();

        return view('reports.create', compact('facilities', 'categories'));
    }

    public function store(SimpanLaporanRequest $request)
    {
        // Otorisasi role (hanya pengguna) + seluruh validasi ada di SimpanLaporanRequest
        $validated = $request->validated();

        $laporan = null;
        $folderFoto = null;

        try {
            // PERBAIKAN E8: laporan + foto dalam satu transaksi. Jika penyimpanan foto gagal di
            // tengah jalan, baris laporan dibatalkan dan file yang sudah tersimpan dihapus.
            DB::transaction(function () use ($validated, $request, &$laporan, &$folderFoto) {
                // Kunci baris pelapor lalu cek laporan ganda DI DALAM transaksi: pengecekan di luar
                // transaksi bisa dilewati dua request paralel (double-submit) yang sama-sama lolos cek.
                User::whereKey(auth()->id())->lockForUpdate()->first();

                $sudahAda = Report::where('id_user', auth()->id())
                    ->where('id_fasilitas', $validated['id_fasilitas'])
                    ->where('id_kategori', $validated['id_kategori'])
                    ->whereIn('report_status', ['baru', 'diproses'])
                    ->exists();

                if ($sudahAda) {
                    throw ValidationException::withMessages([
                        'id_fasilitas' => 'Anda sudah memiliki laporan yang masih berjalan untuk fasilitas dan kategori ini. Pantau statusnya di Riwayat Laporan.',
                    ]);
                }

                $laporan = Report::create([
                    'id_user'       => auth()->id(),
                    'id_fasilitas'  => $validated['id_fasilitas'],
                    'id_kategori'   => $validated['id_kategori'],
                    'description'   => $validated['description'],
                    'report_status' => 'baru',
                ]);

                if ($request->hasFile('photos')) {
                    $folderFoto = 'reports/' . $laporan->id_laporan;

                    foreach ($request->file('photos') as $index => $photoFile) {
                        // Disk 'local' (storage/app/private) — TIDAK di-symlink ke public/storage,
                        // sehingga foto hanya bisa diakses lewat ReportController@photo
                        // yang mengecek otorisasi.
                        $photoPath = $photoFile->store($folderFoto, 'local');

                        if ($photoPath === false) {
                            throw new \RuntimeException('Gagal menyimpan foto laporan.');
                        }

                        $laporan->photos()->create([
                            'photo_path' => $photoPath,
                            'urutan'     => $index,
                        ]);
                    }
                }
            });
        } catch (ValidationException $e) {
            // Biarkan Laravel mengubahnya menjadi redirect back + error (bukan "gagal disimpan")
            throw $e;
        } catch (\Throwable $e) {
            if ($folderFoto) {
                Storage::disk('local')->deleteDirectory($folderFoto);
            }

            report($e);

            return back()->withInput()->withErrors([
                'photos' => 'Laporan gagal disimpan. Silakan coba lagi.',
            ]);
        }

        return redirect()
            ->route('reports.show', $laporan)
            ->with('success', 'Laporan kerusakan berhasil dikirim dengan status baru.');
    }

    public function show(Request $request, Report $laporan)
    {
        if ($redirect = $this->ensurePengguna()) {
            return $redirect;
        }

        abort_unless($laporan->id_user === auth()->id(), 403);

        $laporan->load('photos');

        return view('reports.show', compact('laporan'));
    }

    // Serve satu foto laporan dari disk privat. Hanya pemilik laporan,
    // petugas, atau admin yang boleh melihatnya — beda dengan disk 'public' lama yang bisa diakses siapa saja yang tahu/menebak URL-nya.
    public function photo(Request $request, \App\Models\ReportPhoto $foto)
    {
        $laporan = $foto->report;
        $user = auth()->user();

        abort_unless(
            $laporan->id_user === $user->id_user
                || $user->role === UserRole::Petugas
                || $user->role === UserRole::Admin,
            403
        );

        abort_unless(Storage::disk('local')->exists($foto->photo_path), 404);

        return Storage::disk('local')->response($foto->photo_path, null, [
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control'          => 'private, max-age=0, no-store',
        ]);
    }
}