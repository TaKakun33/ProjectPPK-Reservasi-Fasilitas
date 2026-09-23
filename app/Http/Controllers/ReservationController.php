<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Facility;
use App\Models\Reservation;
use App\Services\ReservationAvailability;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 

class ReservationController extends Controller
{
    // Route /reservasi ini cuma buat role "pengguna". Kalau yang login petugas, 
    // langsung lempar ke halaman reservasi miliknya sendiri di /petugas/reservasi (bukan ditolak) 
    // petugas memang gak boleh ajukan reservasi sendiri (lihat catatan self-approval di routes/reservasi.php),
    // tapi dia tetap punya halaman reservasi versi petugas sendiri. Admin tetap ditolak, gak ada urusan di sini.
    protected function ensurePengguna(): ?RedirectResponse
    {
        $role = auth()->user()->role;

        if ($role === UserRole::Petugas) {
            return redirect()->route('petugas.reservations.index');
        }

        abort_if($role === UserRole::Admin, 403, 'Halaman reservasi ini khusus untuk pengguna.');

        return null;
    }

    // Riwayat & status reservasi milik user yang login.
    public function index(Request $request)
    {
        if ($redirect = $this->ensurePengguna()) {
            return $redirect;
        }

        $reservations = Reservation::with('facility')
            ->where('id_user', Auth::id())
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate(10);

        return view('reservations.index', compact('reservations'));
    }

    // Form pengajuan reservasi.
    public function create(Request $request)
    {
        if ($redirect = $this->ensurePengguna()) {
            return $redirect;
        }

        $facilities = Facility::where('facility_status', 'aktif')->get();

        $selectedFacilityId = $request->input('facility_id');
        $selectedDate = $request->input('date', Carbon::today()->toDateString());

        return view('reservations.create', compact('facilities', 'selectedFacilityId', 'selectedDate'));
    }

    // Simpan reservasi baru dengan validasi server.
    public function store(Request $request)
    {
        if ($redirect = $this->ensurePengguna()) {
            return $redirect;
        }

        // 1. Validasi dasar form
        $request->validate([
            'id_fasilitas' => 'required|exists:facilities,id_fasilitas',
            'date'         => 'required|date|after_or_equal:today',
            'start_time'   => 'required|date_format:H:i',
            'end_time'     => 'required|date_format:H:i|after:start_time',
            'purpose'      => 'required|string|max:255',
        ], [
            'date.after_or_equal' => 'Tanggal reservasi tidak boleh di masa lalu.',
            'end_time.after'      => 'Jam selesai harus lebih akhir dari jam mulai.',
            'purpose.required'    => 'Tujuan penggunaan fasilitas wajib diisi.',
        ]);

        $startTime = $request->start_time . ':00';
        $endTime = $request->end_time . ':00';

        // 2. Validasi Jam Operasional & Kelipatan 30 Menit
        if (!ReservationAvailability::isValidSlotTime($startTime) || !ReservationAvailability::isValidSlotTime($endTime)) {
            return back()
                ->withInput()
                ->withErrors(['time' => 'Jam harus di antara 07:00 - 20:00 dan dalam kelipatan 30 menit (contoh: 08:00, 08:30).']);
        }

        // 3. Validasi Kondisi Fasilitas
        $facility = Facility::findOrFail($request->id_fasilitas);
        if (!$facility->isReservable()) {
            return back()->withInput()->withErrors(['id_fasilitas' => 'Fasilitas ini sedang tidak aktif atau dalam perbaikan.']);
        }

        try {
            $reservation = DB::transaction(function () use ($request, $startTime, $endTime) {
                // Lock baris reservasi fasilitas ini+tanggal ini selama transaction
                $hasConflict = Reservation::where('id_fasilitas', $request->id_fasilitas)
                    ->where('date', $request->date)
                    ->whereIn('reservation_status', ['approved', 'pending'])
                    ->where(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<', $endTime)->where('end_time', '>', $startTime);
                    })
                    ->lockForUpdate()
                    ->exists();

                if ($hasConflict) {
                    throw new \RuntimeException('conflict');
                }

                return Reservation::create([
                    'id_user' => Auth::id(),
                    'id_fasilitas' => $request->id_fasilitas,
                    'date' => $request->date,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'purpose' => $request->purpose,
                    'reservation_status' => 'pending',
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->withInput()->withErrors(['time' => 'Jadwal yang Anda pilih sudah terisi atau bertabrakan dengan reservasi lain yang sedang menunggu konfirmasi/disetujui.']);
        } catch (\Illuminate\Database\QueryException $e) {
            // Pengaman terakhir: trigger trg_reservations_no_conflict_ins di level DB menolak insert yang bentrok. 
            // Harusnya jarang kena karena lockForUpdate sudah menangkap duluan, 
            // tapi kalau tetap kena, jangan sampai user lihat error 500 mentah.
            report($e);

            return back()->withInput()->withErrors(['time' => 'Jadwal yang Anda pilih sudah terisi atau bertabrakan dengan reservasi lain yang sedang menunggu konfirmasi/disetujui.']);
        }

        return redirect()->route('reservations.index')->with('success', 'Reservasi berhasil diajukan dan sedang menunggu verifikasi petugas.');
    }
     
    // Detail 1 reservasi milik user yang login — termasuk alasan penolakan/
    // pembatalan dan riwayat perubahan status, biar user paham kenapa
    // status reservasinya seperti itu (bukan cuma badge status doang).
    public function show(Request $request, string $reservasi)
    {
        if ($redirect = $this->ensurePengguna()) {
            return $redirect;
        }

        $reservation = Reservation::with([
                'facility',
                'processedBy',
                'logs' => fn ($query) => $query->orderBy('created_at')->with('changedBy'),
            ])
            ->findOrFail($reservasi);

        if ($reservation->id_user !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk melihat reservasi ini.');
        }

        return view('reservations.show', compact('reservation'));
    }

    // Batalkan reservasi milik sendiri.
    public function destroy(Request $request, string $reservasi)
    {
        if ($redirect = $this->ensurePengguna()) {
            return $redirect;
        }

        $reservation = Reservation::findOrFail($reservasi);
        // 1. Cek kepemilikan (Authorization)
        if ($reservation->id_user !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk membatalkan reservasi ini.');
        }
        // 2. Cek status aktif
        if (in_array($reservation->reservation_status, ['cancelled', 'rejected'])) {
            return back()->with('error', 'Reservasi ini sudah tidak aktif.');
        }
        // 3. Aturan Batas Waktu: Maksimal H-1 sebelum hari kegiatan
        // Jika hari ini sudah sama dengan hari-H atau lewat, tolak pembatalan
        $reservationDate = Carbon::parse($reservation->date->format('Y-m-d'))->startOfDay();
        if (Carbon::today()->gte($reservationDate)) {
            return back()->with('error', 'Pembatalan gagal. Reservasi hanya dapat dibatalkan maksimal H-1 sebelum jadwal kegiatan (maksimal pukul 23:59 WIB).');
        }
        // 4. Update status menjadi cancelled
        $reservation->update([
            'reservation_status'  => 'cancelled',
            'cancellation_reason' => 'Dibatalkan oleh pemesan.',
        ]);
        return redirect()->route('reservations.index')->with('success', 'Reservasi Anda berhasil dibatalkan.');
    }
}