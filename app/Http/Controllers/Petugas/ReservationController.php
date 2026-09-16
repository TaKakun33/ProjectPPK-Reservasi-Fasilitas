<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\LogStatusReservasi;
use App\Models\Reservation;
use App\Services\ReservationAvailability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ReservationController extends Controller
{
    public function index(Request $request)
    {
        // Antrian reservasi pending, diurutkan dari yang paling lama menunggu.
        $reservations = Reservation::with(['user', 'facility'])
            ->where('reservation_status', 'pending')
            ->orderBy('created_at', 'asc')
            ->paginate(15);

        return view('petugas.reservations.index', compact('reservations'));
    }

    public function approve(Request $request, string $reservasi)
    {
        $reservation = Reservation::findOrFail($reservasi);

        // Hanya reservasi pending yang bisa di-approve.
        if ($reservation->reservation_status !== 'pending') {
            return back()->with('error', 'Reservasi ini sudah diproses sebelumnya.');
        }

        // Reuse logic milik Zhafran: cek bentrok di fasilitas & tanggal yang sama.
        if (ReservationAvailability::hasConflict(
            $reservation->id_fasilitas,
            $reservation->date->format('Y-m-d'),
            $reservation->start_time,
            $reservation->end_time,
            $reservation->id_reservasi,
        )) {
            return back()->with('error', 'Jadwal bentrok dengan reservasi lain.');
        }

        // Update status + catat log dalam satu transaksi biar konsisten.
        DB::transaction(function () use ($reservation, $request) {
            $statusBefore = $reservation->reservation_status;

            $reservation->update([
                'reservation_status' => 'approved',
                'processed_by' => $request->user()->id_user,
            ]);

            LogStatusReservasi::create([
                'id_reservasi' => $reservation->id_reservasi,
                'status_before' => $statusBefore,
                'status_after' => 'approved',
                'changed_by' => $request->user()->id_user,
                'notes' => $request->input('notes'),
            ]);
        });

        return back()->with('success', 'Reservasi berhasil disetujui.');
    }

    public function reject(Request $request, string $reservasi)
    {
        $reservation = Reservation::findOrFail($reservasi);

        if ($reservation->reservation_status !== 'pending') {
            return back()->with('error', 'Reservasi ini sudah diproses sebelumnya.');
        }

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($reservation, $request, $validated) {
            $statusBefore = $reservation->reservation_status;

            $reservation->update([
                'reservation_status' => 'rejected',
                'processed_by' => $request->user()->id_user,
            ]);

            LogStatusReservasi::create([
                'id_reservasi' => $reservation->id_reservasi,
                'status_before' => $statusBefore,
                'status_after' => 'rejected',
                'changed_by' => $request->user()->id_user,
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        return back()->with('success', 'Reservasi berhasil ditolak.');
    }

    public function cancel(Request $request, string $reservasi)
    {
        $reservation = Reservation::findOrFail($reservasi);

        if ($reservation->reservation_status !== 'approved') {
            return back()->with('error', 'Hanya reservasi yang sudah disetujui yang bisa dibatalkan.');
        }

        // Wajib isi alasan pembatalan.
        $validated = $request->validate([
            'cancellation_reason' => ['required', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($reservation, $request, $validated) {
            $statusBefore = $reservation->reservation_status;

            $reservation->update([
                'reservation_status' => 'cancelled',
                'cancellation_reason' => $validated['cancellation_reason'],
                'processed_by' => $request->user()->id_user,
            ]);

            LogStatusReservasi::create([
                'id_reservasi' => $reservation->id_reservasi,
                'status_before' => $statusBefore,
                'status_after' => 'cancelled',
                'changed_by' => $request->user()->id_user,
                'notes' => $validated['cancellation_reason'],
            ]);
        });

        return back()->with('success', 'Reservasi berhasil dibatalkan.');
    }
}