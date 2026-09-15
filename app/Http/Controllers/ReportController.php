<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\Report;
use App\Models\ReportCategory;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $reports = Report::query()
            ->where('id_user', auth()->id())
            ->with(['facility', 'category'])
            ->latest()
            ->paginate(10);

        return view('reports.index', compact('reports'));
    }

    public function create(Request $request)
    {
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
        $validated = $request->validate([
            'id_fasilitas' => ['required', 'exists:facilities,id_fasilitas'],
            'id_kategori'  => ['required', 'exists:report_categories,id_kategori'],
            'description'  => ['required', 'string'],
            'photo'        => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('reports', 'public');
        }

        $laporan = Report::create([
            'id_user'           => auth()->id(),
            'id_fasilitas'      => $validated['id_fasilitas'],
            'id_kategori'       => $validated['id_kategori'],
            'description'       => $validated['description'],
            'photo'             => $photoPath,
            'report_status'     => 'baru',
        ]);

        return redirect()
            ->route('reports.show', $laporan)
            ->with('success', 'Laporan kerusakan berhasil dikirim dengan status baru.');
    }

    public function show(Request $request, Report $laporan)
    {
        abort_unless($laporan->id_user === auth()->id(), 403);

        return view('reports.show', compact('laporan'));
    }
}