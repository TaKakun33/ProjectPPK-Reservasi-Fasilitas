<?php

namespace App\Services;

use App\Models\Reservation;
use Carbon\Carbon;

// Service untuk validasi jam operasional, deteksi bentrok jadwal, dan pembuatan slot waktu 30 menit
class ReservationAvailability
{
    // Jam operasional sistem: 07:00:00 - 20:00:00
    public const OPERATIONAL_START = '07:00:00';
    public const OPERATIONAL_END = '20:00:00';

    // Hari Minggu adalah hari libur: tidak melayani reservasi fasilitas
    public static function isSunday(string $date): bool
    {
        try {
            return Carbon::createFromFormat('!Y-m-d', $date)->isSunday();
        } catch (\Throwable $e) {
            return false;
        }
    }

    // Validasi jam oprasional + kelipatan 30 menit
    public static function isValidSlotTime(string $time): bool
    {
        try {
            $carbonTime = Carbon::createFromTimeString($time);
        } catch (\Throwable $e) {
            return false;
        }

        $timeStr = $carbonTime->format('H:i:s');

        // Cek jam operasional
        if ($timeStr < self::OPERATIONAL_START || $timeStr > self::OPERATIONAL_END) {
            return false;
        }

        // Cek kelipatan 30 menit
        if ($carbonTime->minute % 30 !== 0 || $carbonTime->second !== 0) {
            return false;
        }

        return true;
    }

    // Cek apakah ada jadwal yang bentrok di fasilitas & tanggal yang sama.
    // Dipakai bersama oleh ReservationController@store (satu sumber logika, tidak diduplikasi lagi).
    public static function hasConflict(string $facilityId, string $date, string $startTime, string $endTime, ?string $excludeReservationId = null): bool
    {
        $query = Reservation::where('id_fasilitas', $facilityId)
            ->where('date', $date)
            // Hanya reservasi yang 'approved' atau 'pending' yang dianggap memblokir jadwal
            ->whereIn('reservation_status', ['approved', 'pending'])
            // Formula irisan waktu: (StartA < EndB) AND (EndA > StartB)
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where('start_time', '<', $endTime)
                  ->where('end_time', '>', $startTime);
            });

        // Abaikan ID reservasi tertentu jika ada
        if ($excludeReservationId) {
            $query->where('id_reservasi', '!=', $excludeReservationId);
        }

        return $query->exists();
    }

    // Generate seluruh slot waktu 30 menit dari 07:00 sampai 20:00 beserta statusnya.
    // $facilityReservable=false (fasilitas dalam perbaikan) membuat seluruh slot tidak tersedia.
    public static function getDailySlots(string $facilityId, string $date, bool $facilityReservable = true, ?string $viewerId = null): array
    {
        // Hanya kolom yang dibutuhkan (tanpa relasi user) agar data pemohon tidak ikut dimuat di halaman publik
        $reservations = Reservation::select('id_reservasi', 'id_user', 'start_time', 'end_time', 'reservation_status')
            ->where('id_fasilitas', $facilityId)
            ->where('date', $date)
            ->whereIn('reservation_status', ['approved', 'pending'])
            ->get();

        $slots = [];
        $timezone = config('app.timezone', 'Asia/Jakarta');
        $now = Carbon::now($timezone);
        $minStart = $now->copy()->addHour();
        $isSunday = self::isSunday($date);

        $current = Carbon::parse($date . ' ' . self::OPERATIONAL_START, $timezone);
        $end = Carbon::parse($date . ' ' . self::OPERATIONAL_END, $timezone);

        while ($current->lt($end)) {
            $slotStart = $current->format('H:i:s');
            $next = $current->copy()->addMinutes(30);
            $slotEnd = $next->format('H:i:s');

            // Cek apakah slot ini terkena irisan dengan reservasi yang ada
            $booking = $reservations->first(function ($res) use ($slotStart, $slotEnd) {
                return $res->start_time < $slotEnd && $res->end_time > $slotStart;
            });

            // Slot dianggap tidak dapat dipesan jika waktu mulainya kurang dari 1 jam dari sekarang
            $isPastOrTooSoon = $current->lt($minStart);

            if ($booking) {
                $status = $booking->reservation_status;
            } elseif ($isPastOrTooSoon) {
                $status = 'berlalu';
            } elseif ($isSunday) {
                $status = 'libur';
            } elseif (! $facilityReservable) {
                $status = 'perbaikan';
            } else {
                $status = 'tersedia';
            }

            // PRIVASI: model reservasi (berisi id_user) tidak diteruskan ke view publik;
            // view cukup tahu apakah slot ini milik penonton yang sedang login.
            $slots[] = [
                'start'        => $current->format('H:i'),
                'end'          => $next->format('H:i'),
                'is_available' => $status === 'tersedia',
                'status'       => $status,
                'is_mine'      => $booking !== null && $viewerId !== null && $booking->id_user === $viewerId,
                'is_past'      => $isPastOrTooSoon,
            ];

            $current = $next;
        }

        return $slots;
    }
}
