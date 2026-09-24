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

    // Validasi jam oprasional
    public static function isValidSlotTime(string $time): bool
    {
        $carbonTime = Carbon::createFromTimeString($time);
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
    public static function getDailySlots(string $facilityId, string $date): array
    {
        // Ambil semua reservasi aktif di tanggal tersebut
        $reservations = Reservation::with('user')
            ->where('id_fasilitas', $facilityId)
            ->where('date', $date)
            ->whereIn('reservation_status', ['approved', 'pending'])
            ->get();

        $slots = [];
        $timezone = config('app.timezone', 'Asia/Jakarta');
        $now = Carbon::now($timezone);

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

            // Slot dianggap sudah berlalu jika waktu mulainya ($current) kurang dari atau sama dengan waktu sekarang
            $isPast = $current->lte($now);

            $slots[] = [
                'start'        => $current->format('H:i'),
                'end'          => $next->format('H:i'),
                'is_available' => is_null($booking) && !$isPast,
                'status'       => $booking ? $booking->reservation_status : ($isPast ? 'berlalu' : 'tersedia'),
                'booking'      => $booking,
                'is_past'      => $isPast,
            ];

            $current = $next;
        }

        return $slots;
    }
}
