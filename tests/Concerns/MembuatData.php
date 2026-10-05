<?php

namespace Tests\Concerns;

use App\Enums\UserRole;
use App\Models\Facility;
use App\Models\Reservation;
use App\Models\User;

// Helper pembuat data uji (model domain belum punya factory).
trait MembuatData
{
    protected function buatUser(UserRole $role = UserRole::Pengguna, array $atribut = []): User
    {
        return User::factory()->create(array_merge([
            'role'           => $role,
            'account_status' => 'verified',
        ], $atribut));
    }

    protected function buatFasilitas(array $atribut = []): Facility
    {
        return Facility::create(array_merge([
            'facility_name'   => 'Ruang Uji ' . fake()->unique()->numerify('###'),
            'type'            => 'Ruang Rapat',
            'location'        => 'Gedung Uji',
            'capacity'        => 20,
            'description'     => 'Fasilitas untuk pengujian.',
            'facility_status' => 'aktif',
        ], $atribut));
    }

    // Membuat reservasi langsung ke database ($hariKeDepan boleh negatif untuk tanggal yang sudah lewat)
    protected function buatReservasi(User $pemesan, Facility $fasilitas, int $hariKeDepan, string $mulai, string $selesai, string $status = 'pending'): Reservation
    {
        return Reservation::create([
            'id_user'            => $pemesan->id_user,
            'id_fasilitas'       => $fasilitas->id_fasilitas,
            'date'               => now()->addDays($hariKeDepan)->toDateString(),
            'start_time'         => $mulai . ':00',
            'end_time'           => $selesai . ':00',
            'purpose'            => 'Kegiatan uji',
            'reservation_status' => $status,
        ]);
    }

    // Payload form pengajuan reservasi
    protected function dataReservasi(Facility $fasilitas, int $hariKeDepan, string $mulai, string $selesai): array
    {
        return [
            'id_fasilitas' => $fasilitas->id_fasilitas,
            'date'         => now()->addDays($hariKeDepan)->toDateString(),
            'start_time'   => $mulai,
            'end_time'     => $selesai,
            'purpose'      => 'Rapat panitia',
        ];
    }
}
