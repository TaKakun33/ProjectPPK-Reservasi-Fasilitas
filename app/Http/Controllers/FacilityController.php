<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Facility;
use App\Services\ReservationAvailability;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    // Menampilkan daftar fasilitas dan fitur pencarian.
    public function index(Request $request)
    {
        // Admin gak perlu lihat halaman publik ini — langsung lempar
        // ke halaman kelola fasilitas miliknya di /admin/fasilitas.
        if ($request->user()?->role === UserRole::Admin) {
            return redirect()->route('admin.fasilitas.index');
        }

        $query = Facility::visible();

        // Filter: Tipe, Lokasi, Kapasitas
        if ($request->filled('type')) {
            $query->where('type', 'like', '%' . $request->type . '%');
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        if ($request->filled('capacity')) {
            $query->where('capacity', '>=', (int) $request->capacity);
        }

        $facilities = $query->paginate(9)->withQueryString();

        // Ambil daftar unik tipe & lokasi untuk dropdown 
        $types = Facility::visible()->distinct()->pluck('type');
        $locations = Facility::visible()->distinct()->pluck('location');

        return view('facilities.index', compact('facilities', 'types', 'locations'));
    }

    // Menampilkan detail fasilitas dan slot ketersediaan per 30 menit.
    public function show(Request $request, string $fasilitas)
    {
        $facility = Facility::visible()
            ->where('id_fasilitas', $fasilitas)
            ->firstOrFail();

        // Tanggal yang dicek, default adalah hari ini
        $selectedDate = $request->input('date', Carbon::today()->toDateString());

        // Ambil timeline slot waktu dari Service
        $slots = ReservationAvailability::getDailySlots($facility->id_fasilitas, $selectedDate);

        return view('facilities.show', compact('facility', 'selectedDate', 'slots'));
    }
}