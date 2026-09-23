<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Facility;
use App\Models\Report;
use App\Models\ReportCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

    public function store(Request $request)
    {
        if ($redirect = $this->ensurePengguna()) {
            return $redirect;
        }

        $validated = $request->validate([
            'id_fasilitas' => ['required', 'exists:facilities,id_fasilitas'],
            'id_kategori'  => ['required', 'exists:report_categories,id_kategori'],
            'description'  => ['required', 'string'],
            'photos'       => ['nullable', 'array', 'max:5'],
            'photos.*'     => ['image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ]);

        $laporan = Report::create([
            'id_user'       => auth()->id(),
            'id_fasilitas'  => $validated['id_fasilitas'],
            'id_kategori'   => $validated['id_kategori'],
            'description'   => $validated['description'],
            'report_status' => 'baru',
        ]);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $index => $photoFile) {
                // Disk 'local' (storage/app/private) — TIDAK di-symlink ke
                // public/storage, jadi foto laporan kerusakan (yang bisa
                // memuat info lokasi/identitas pelapor) tidak bisa diakses
                // langsung lewat URL publik. Satu-satunya jalan masuk yang
                // sah adalah lewat ReportController@photo yang mengecek
                // otorisasi (lihat method photo() di bawah).
                $photoPath = $photoFile->store('reports/'.$laporan->id_laporan, 'local');

                $laporan->photos()->create([
                    'photo_path' => $photoPath,
                    'urutan'     => $index,
                ]);
            }
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

        return Storage::disk('local')->response($foto->photo_path);
    }
}