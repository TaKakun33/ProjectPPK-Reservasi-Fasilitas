<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Petugas\BatalkanReservasiRequest;
use App\Http\Requests\Petugas\SetujuiReservasiRequest;
use App\Http\Requests\Petugas\TolakReservasiRequest;
use App\Models\Facility;
use App\Models\LogStatusReservasi;
use App\Models\Reservation;
use App\Services\ReservationAvailability;
use App\Services\ReservationExpiry;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Controller pengelolaan antrian dan verifikasi reservasi oleh Petugas
class ReservationController extends Controller
{
    // Pesan error untuk kode RuntimeException yang dilempar di dalam transaksi
    private const PESAN = [
        'already_processed'      => 'Reservasi ini sudah diproses sebelumnya.',
        'conflict'               => 'Jadwal bentrok dengan reservasi lain.',
        'fasilitas_tidak_aktif'  => 'Fasilitas sedang nonaktif/dalam perbaikan sehingga reservasi tidak dapat disetujui.',
        'sudah_lewat'            => 'Waktu kegiatan reservasi ini sudah lewat.',
        'bukan_approved'         => 'Hanya reservasi yang sudah disetujui yang bisa dibatalkan.',
        'sudah_selesai'          => 'Reservasi ini sudah selesai dilaksanakan sehingga tidak dapat dibatalkan.',
    ];

    // Menampilkan riwayat & antrian reservasi lengkap untuk petugas
    public function index(Request $request)
    {
        // Antrian hanya berisi reservasi pending yang masih relevan: yang sudah lewat jadwalnya ditolak otomatis
        ReservationExpiry::kedaluwarsakanDiamDiam();

        $query = Reservation::with(['user', 'facility']);

        // Filter berdasarkan status jika ditentukan
        if ($request->filled('status') && in_array($request->status, ['pending', 'approved', 'rejected', 'cancelled'], true)) {
            $query->where('reservation_status', $request->status);
        }

        // Riwayat lengkap diurutkan berdasarkan waktu terbaru
        $reservations = $query->orderBy('created_at', 'desc')
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate(15)
            ->withQueryString();

        $selectedStatus = $request->input('status', 'all');

        return view('petugas.reservations.index', compact('reservations', 'selectedStatus'));
    }

    // Menyetujui permohonan reservasi (cek bentrok, status fasilitas, dan waktu — dalam satu transaksi + lock)
    public function approve(SetujuiReservasiRequest $request, string $reservasi)
    {
        $validated = $request->validated();

        try {
            DB::transaction(function () use ($reservasi, $request, $validated) {
                $reservation = Reservation::where('id_reservasi', $reservasi)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($reservation->reservation_status !== 'pending') {
                    throw new \RuntimeException('already_processed');
                }

                // PERBAIKAN E6: fasilitas harus masih aktif saat disetujui
                $fasilitas = Facility::whereKey($reservation->id_fasilitas)->lockForUpdate()->first();
                if (! $fasilitas || ! $fasilitas->isReservable()) {
                    throw new \RuntimeException('fasilitas_tidak_aktif');
                }

                // PERBAIKAN E6: reservasi yang waktunya sudah lewat tidak boleh disetujui
                $zona = config('app.timezone', 'Asia/Jakarta');
                $waktuMulai = Carbon::parse($reservation->date->format('Y-m-d') . ' ' . $reservation->start_time, $zona);
                if ($waktuMulai->isPast()) {
                    throw new \RuntimeException('sudah_lewat');
                }

                if (ReservationAvailability::hasConflict(
                    $reservation->id_fasilitas,
                    $reservation->date->format('Y-m-d'),
                    $reservation->start_time,
                    $reservation->end_time,
                    $reservation->id_reservasi
                )) {
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
                    'notes' => $validated['notes'] ?? null,
                    'created_at' => now(),
                ]);
            });
        } catch (\RuntimeException $e) {
            // ModelNotFoundException juga turunan RuntimeException — jangan ditelan, biarkan jadi 404
            if (! isset(self::PESAN[$e->getMessage()])) {
                throw $e;
            }

            return back()->with('error', self::PESAN[$e->getMessage()]);
        } catch (QueryException $e) {
            // Pengaman terakhir dari trigger DB (SQLSTATE 45000). Error DB lain dilempar ulang.
            if ((string) $e->getCode() !== '45000') {
                throw $e;
            }
            report($e);

            return back()->with('error', self::PESAN['conflict']);
        }

        return back()->with('success', 'Reservasi berhasil disetujui.');
    }

    // Menolak permohonan reservasi dengan alasan penolakan
    public function reject(TolakReservasiRequest $request, string $reservasi)
    {
        $validated = $request->validated();

        try {
            DB::transaction(function () use ($reservasi, $request, $validated) {
                // PERBAIKAN E5: status dibaca ulang di dalam transaksi dengan lock (hindari TOCTOU)
                $reservation = Reservation::where('id_reservasi', $reservasi)->lockForUpdate()->firstOrFail();

                if ($reservation->reservation_status !== 'pending') {
                    throw new \RuntimeException('already_processed');
                }

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
                    'created_at' => now(),
                ]);
            });
        } catch (\RuntimeException $e) {
            // ModelNotFoundException juga turunan RuntimeException — jangan ditelan, biarkan jadi 404
            if (! isset(self::PESAN[$e->getMessage()])) {
                throw $e;
            }

            return back()->with('error', self::PESAN[$e->getMessage()]);
        }

        return back()->with('success', 'Reservasi berhasil ditolak.');
    }

    // Membatalkan reservasi yang sudah disetujui (wajib mencantumkan alasan)
    public function cancel(BatalkanReservasiRequest $request, string $reservasi)
    {
        $validated = $request->validated();

        try {
            DB::transaction(function () use ($reservasi, $request, $validated) {
                $reservation = Reservation::where('id_reservasi', $reservasi)->lockForUpdate()->firstOrFail();

                if ($reservation->reservation_status !== 'approved') {
                    throw new \RuntimeException('bukan_approved');
                }

                // Reservasi yang sudah selesai tidak boleh dibatalkan: rekap okupansi (jam terpakai)
                // dan audit riwayat akan berubah secara diam-diam.
                $zona         = config('app.timezone', 'Asia/Jakarta');
                $waktuSelesai = Carbon::parse($reservation->date->format('Y-m-d') . ' ' . $reservation->end_time, $zona);
                if ($waktuSelesai->lte(Carbon::now($zona))) {
                    throw new \RuntimeException('sudah_selesai');
                }

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
                    'created_at' => now(),
                ]);
            });
        } catch (\RuntimeException $e) {
            // ModelNotFoundException juga turunan RuntimeException — jangan ditelan, biarkan jadi 404
            if (! isset(self::PESAN[$e->getMessage()])) {
                throw $e;
            }

            return back()->with('error', self::PESAN[$e->getMessage()]);
        }

        return back()->with('success', 'Reservasi berhasil dibatalkan.');
    }
}
