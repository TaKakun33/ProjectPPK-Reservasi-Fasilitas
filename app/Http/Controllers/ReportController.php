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
    /**
     * Halaman /laporan ini cuma buat role "pengguna". Kalau yang login
     * petugas, lempar ke halaman laporan miliknya sendiri di
     * /petugas/laporan. Kalau admin, langsung ditolak (403) — admin
     * memang nggak punya urusan di route ini.
     */
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

        $facilities = Facility::where('is_active', true)
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
                $photoPath = $photoFile->store('reports', 'public');
                $photoData = 'data:'.$photoFile->getMimeType().';base64,'.base64_encode($photoFile->get());

                $laporan->photos()->create([
                    'photo_path' => $photoPath,
                    'photo_data' => $photoData,
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
}