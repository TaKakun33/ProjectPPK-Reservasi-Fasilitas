<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\LogStatusReservasi;
use App\Models\Reservation;
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
        // Cek bentrok + approve dilakukan dalam SATU transaksi dengan
        // lockForUpdate(), bukan dipisah seperti sebelumnya. Sebelumnya ada
        // celah race condition: kalau dua petugas approve dua reservasi
        // yang tumpang tindih hampir bersamaan, keduanya bisa lolos cek
        // hasConflict() sebelum salah satu selesai update, menghasilkan
        // dua reservasi approved yang bentrok. Locking baris-baris terkait
        // di dalam transaksi memaksa request kedua menunggu request
        // pertama selesai, baru ikut mengecek ulang kondisi terbaru.
        try {
            DB::transaction(function () use ($reservasi, $request) {
                $reservation = Reservation::where('id_reservasi', $reservasi)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($reservation->reservation_status !== 'pending') {
                    throw new \RuntimeException('already_processed');
                }

                $hasConflict = Reservation::where('id_fasilitas', $reservation->id_fasilitas)
                    ->where('date', $reservation->date->format('Y-m-d'))
                    ->where('id_reservasi', '!=', $reservation->id_reservasi)
                    ->whereIn('reservation_status', ['approved', 'pending'])
                    ->where(function ($q) use ($reservation) {
                        $q->where('start_time', '<', $reservation->end_time)
                          ->where('end_time', '>', $reservation->start_time);
                    })
                    ->lockForUpdate()
                    ->exists();

                if ($hasConflict) {
                    throw new \RuntimeException('conflict');
                }

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
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage() === 'conflict'
                ? 'Jadwal bentrok dengan reservasi lain.'
                : 'Reservasi ini sudah diproses sebelumnya.');
        } catch (\Illuminate\Database\QueryException $e) {
            // Pengaman terakhir dari trigger trg_reservations_no_conflict_upd
            // (migration 2026_09_20_000001) — lihat catatan yang sama di
            // ReservationController@store.
            report($e);

            return back()->with('error', 'Jadwal bentrok dengan reservasi lain.');
        }

        return back()->with('success', 'Reservasi berhasil disetujui.');
    }

    public function reject(Request $request, string $reservasi)
    {
        $reservation = Reservation::findOrFail($reservasi);

        if ($reservation->reservation_status !== 'pending') {
            return back()->with('error', 'Reservasi ini sudah diproses sebelumnya.');
        }

        // Wajib isi alasan penolakan (US #9) biar pengguna tahu kenapa
        // reservasinya ditolak, bukan cuma status berubah jadi "ditolak".
        $validated = $request->validate([
            'alasan_ditolak' => ['required', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($reservation, $request, $validated) {
            $statusBefore = $reservation->reservation_status;

            $reservation->update([
                'reservation_status' => 'rejected',
                'alasan_ditolak' => $validated['alasan_ditolak'],
                'processed_by' => $request->user()->id_user,
            ]);

            LogStatusReservasi::create([
                'id_reservasi' => $reservation->id_reservasi,
                'status_before' => $statusBefore,
                'status_after' => 'rejected',
                'changed_by' => $request->user()->id_user,
                'notes' => $validated['alasan_ditolak'],
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