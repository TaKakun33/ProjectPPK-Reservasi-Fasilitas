<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Facility;
use App\Models\FacilityPhoto;
use App\Services\ReservationAvailability;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FacilityController extends Controller
{
    // Escape wildcard LIKE (% dan _) supaya input filter dibaca sebagai teks biasa
    private function escapeLike(string $nilai): string
    {
        return addcslashes($nilai, '%_\\');
    }

    // Menampilkan daftar fasilitas dan fitur pencarian.
    public function index(Request $request)
    {
        // Admin gak perlu lihat halaman publik ini — langsung lempar
        // ke halaman kelola fasilitas miliknya di /admin/fasilitas.
        if ($request->user()?->role === UserRole::Admin) {
            return redirect()->route('admin.fasilitas.index');
        }

        // Jika yang login adalah Petugas, arahkan langsung ke dashboard miliknya
        if ($request->user()?->role === UserRole::Petugas) {
            return redirect()->route('petugas.dashboard');
        }

        // Fasilitas nonaktif tetap ditampilkan (abu-abu, di urutan paling belakang) agar pengguna tahu fasilitasnya ada
        $query = Facility::query()->with('photos');

        // Pencarian kata kunci: nama, tipe, lokasi, atau deskripsi fasilitas
        $search = Str::limit(trim((string) $request->query('search', '')), 100, '');

        if ($search !== '') {
            $kata = $this->escapeLike($search);
            $query->where(function ($q) use ($kata) {
                $q->where('facility_name', 'like', "%{$kata}%")
                  ->orWhere('type', 'like', "%{$kata}%")
                  ->orWhere('location', 'like', "%{$kata}%")
                  ->orWhere('description', 'like', "%{$kata}%");
            });
        }

        // Filter: Tipe, Lokasi, Kapasitas (input dibersihkan & dibatasi panjangnya)
        $type = Str::limit(trim((string) $request->query('type', '')), 50, '');
        $location = Str::limit(trim((string) $request->query('location', '')), 150, '');

        if ($type !== '') {
            $query->where('type', 'like', '%' . $this->escapeLike($type) . '%');
        }

        if ($location !== '') {
            $query->where('location', 'like', '%' . $this->escapeLike($location) . '%');
        }

        if ($request->filled('capacity') && is_numeric($request->capacity)) {
            $query->where('capacity', '>=', max(0, min((int) $request->capacity, 100000)));
        }

        $facilities = $query->orderByRaw("CASE WHEN facility_status = 'nonaktif' THEN 1 ELSE 0 END")
            ->orderBy('facility_name')
            ->paginate(12)
            ->withQueryString();

        // Ambil daftar unik tipe & lokasi untuk dropdown
        $types = Facility::distinct()->pluck('type');
        $locations = Facility::distinct()->pluck('location');

        return view('facilities.index', compact('facilities', 'types', 'locations'));
    }

    // Menampilkan detail fasilitas dan slot ketersediaan per 30 menit.
    public function show(Request $request, string $fasilitas)
    {
        // Sama seperti index(): halaman detail fasilitas publik ini juga
        // bukan buat petugas.
        abort_if($request->user()?->role === UserRole::Petugas, 403, 'Halaman fasilitas ini khusus untuk pengguna.');

        $facility = Facility::visible()
            ->with('photos')
            ->where('id_fasilitas', $fasilitas)
            ->firstOrFail();

        // PERBAIKAN E3: tanggal wajib berformat Y-m-d yang valid. Sebelumnya ?date=abc
        // membuat Carbon::parse() melempar exception (HTTP 500). Jika tidak valid → hari ini.
        $tanggalInput = (string) $request->query('date', '');
        $selectedDate = Carbon::today()->toDateString();

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggalInput)) {
            try {
                $selectedDate = Carbon::createFromFormat('!Y-m-d', $tanggalInput)->toDateString();
            } catch (\Throwable $e) {
                // tetap pakai hari ini
            }
        }

        // Ambil timeline slot waktu dari Service (slot ditandai tidak tersedia bila fasilitas dalam perbaikan)
        $slots = ReservationAvailability::getDailySlots(
            $facility->id_fasilitas,
            $selectedDate,
            $facility->isReservable(),
            $request->user()?->id_user
        );

        return view('facilities.show', compact('facility', 'selectedDate', 'slots'));
    }

    // Melayani satu foto fasilitas dari disk privat. Foto fasilitas bersifat publik
    // (tampil di daftar fasilitas tanpa login); yang dilayani hanya berkas milik baris foto itu sendiri.
    public function photo(FacilityPhoto $foto)
    {
        abort_unless(Storage::disk('local')->exists($foto->photo_path), 404);

        return Storage::disk('local')->response($foto->photo_path, null, [
            'Cache-Control'          => 'public, max-age=86400',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
