<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    public function index(Request $request)
    {
        $query = Facility::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('facility_name', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === 'true');
        }

        $facilities = $query->latest()->paginate(10)->withQueryString();

        return view('admin.facilities.index', compact('facilities'));
    }

    public function create()
    {
        return view('admin.facilities.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'facility_name' => 'required|string|max:100',
            'type'          => 'required|string|max:50',
            'location'      => 'required|string|max:150',
            'capacity'      => 'required|integer|min:1',
            'description'   => 'nullable|string',
        ]);

        $validated['is_active'] = true;
        $validated['facility_status'] = 'aktif';

        Facility::create($validated);

        return redirect()->route('admin.fasilitas.index')
            ->with('success', 'Fasilitas berhasil ditambahkan.');
    }

    public function edit(Facility $fasilitas)
    {
        return view('admin.facilities.edit', compact('fasilitas'));
    }

    public function update(Request $request, Facility $fasilitas)
    {
        $validated = $request->validate([
            'facility_name' => 'required|string|max:100',
            'type'          => 'required|string|max:50',
            'location'      => 'required|string|max:150',
            'capacity'      => 'required|integer|min:1',
            'description'   => 'nullable|string',
        ]);

        $fasilitas->update($validated);

        return redirect()->route('admin.fasilitas.index')
            ->with('success', 'Fasilitas berhasil diperbarui.');
    }

    public function destroy(Facility $fasilitas)
    {
        $fasilitas->update(['is_active' => false]);

        return redirect()->route('admin.fasilitas.index')
            ->with('success', 'Fasilitas berhasil dinonaktifkan.');
    }

    public function activate(Facility $fasilitas)
    {
        $fasilitas->update(['is_active' => true]);

        return redirect()->route('admin.fasilitas.index')
            ->with('success', 'Fasilitas berhasil diaktifkan kembali.');
    }
}