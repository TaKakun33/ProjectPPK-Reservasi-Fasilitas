<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use Illuminate\Http\Request;

// Controller untuk manajemen fasilitas oleh Admin (CRUD & aktivasi)
class FacilityController extends Controller
{
    // Menampilkan daftar fasilitas dengan filter status dan fitur pencarian
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

        if ($request->filled('status')) {
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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'facility_name' => 'required|string|max:100',
            'type'          => 'required|string|max:50',
            'location'      => 'required|string|max:150',
            'capacity'      => 'required|integer|min:1',
            'description'   => 'nullable|string',
        ]);

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

    // Menonaktifkan fasilitas (mengubah status menjadi 'nonaktif')
    public function destroy(Facility $fasilitas)
    {
        $fasilitas->update(['facility_status' => 'nonaktif']);

        return redirect()->route('admin.fasilitas.index')
            ->with('success', 'Fasilitas berhasil dinonaktifkan.');
    }

    // Mengaktifkan kembali fasilitas yang dinonaktifkan
    public function activate(Facility $fasilitas)
    {
        // Sebelum diaktifkan, cek dulu: apakah fasilitas ini masih punya
        // laporan kerusakan yang sedang ditangani petugas ('diproses')?
        // Kalau iya, jangan langsung dibuat 'aktif' — kembalikan ke
        // 'dalam perbaikan' supaya statusnya tetap konsisten dengan
        // laporan yang belum selesai (bukan hasil keputusan admin lagi).
        $masihDiperbaiki = $fasilitas->reports()
            ->where('report_status', 'diproses')
            ->exists();

        $fasilitas->update([
            'facility_status' => $masihDiperbaiki ? 'dalam perbaikan' : 'aktif',
        ]);

        $message = $masihDiperbaiki
            ? 'Fasilitas diaktifkan, namun statusnya dikembalikan ke "dalam perbaikan" karena masih ada laporan kerusakan yang sedang diproses petugas.'
            : 'Fasilitas berhasil diaktifkan kembali.';

        return redirect()->route('admin.fasilitas.index')
            ->with('success', $message);
    }
}