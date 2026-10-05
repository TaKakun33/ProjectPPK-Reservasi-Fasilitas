<?php

namespace App\Services;

use App\Models\LogStatusReservasi;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

// Menolak otomatis reservasi 'pending' yang waktu mulainya sudah lewat tanpa diproses petugas.
// Tanpa ini, reservasi pending basi tetap menghitung kuota pemesan dan memblokir slot selamanya.
class ReservationExpiry
{
    public const ALASAN = 'Kedaluwarsa: tidak diproses petugas sebelum jadwal mulai.';

    // Jalankan pembersihan. Mengembalikan jumlah reservasi yang dikedaluwarsakan.
    public static function kedaluwarsakan(): int
    {
        $zona        = config('app.timezone', 'Asia/Jakarta');
        $sekarang    = Carbon::now($zona);
        $hariIni     = $sekarang->toDateString();
        $jamSekarang = $sekarang->format('H:i:s');

        $daftarId = Reservation::where('reservation_status', 'pending')
            ->where(function ($q) use ($hariIni, $jamSekarang) {
                $q->where('date', '<', $hariIni)
                  ->orWhere(function ($q2) use ($hariIni, $jamSekarang) {
                      $q2->where('date', $hariIni)
                         ->where('start_time', '<=', $jamSekarang);
                  });
            })
            ->pluck('id_reservasi');

        $jumlah = 0;

        foreach ($daftarId as $id) {
            DB::transaction(function () use ($id, &$jumlah) {
                // Baca ulang dengan lock: petugas mungkin baru saja memproses reservasi ini
                $reservasi = Reservation::whereKey($id)->lockForUpdate()->first();

                if (! $reservasi || $reservasi->reservation_status !== 'pending') {
                    return;
                }

                $reservasi->update([
                    'reservation_status' => 'rejected',
                    'alasan_ditolak'     => self::ALASAN,
                ]);

                LogStatusReservasi::create([
                    'id_reservasi'  => $reservasi->id_reservasi,
                    'status_before' => 'pending',
                    'status_after'  => 'rejected',
                    'changed_by'    => null,
                    'notes'         => self::ALASAN,
                    'created_at'    => now(),
                ]);

                $jumlah++;
            });
        }

        return $jumlah;
    }

    // Versi aman untuk dipanggil dari halaman web: dibatasi maksimal 1x per menit (kecuali $paksa)
    // dan tidak pernah membuat halaman error bila pembersihan gagal.
    public static function kedaluwarsakanDiamDiam(bool $paksa = false): void
    {
        try {
            if (! $paksa && ! Cache::add('reservasi:kedaluwarsa-terakhir', 1, 60)) {
                return;
            }

            self::kedaluwarsakan();
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
