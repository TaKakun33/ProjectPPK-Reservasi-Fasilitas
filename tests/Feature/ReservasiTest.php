<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\LogStatusReservasi;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\MembuatData;
use Tests\TestCase;

class ReservasiTest extends TestCase
{
    use RefreshDatabase, MembuatData;

    public function test_reservasi_yang_bentrok_ditolak(): void
    {
        $fasilitas = $this->buatFasilitas();
        $data      = $this->dataReservasi($fasilitas, 2, '09:00', '10:00');

        $this->actingAs($this->buatUser())
            ->post(route('reservations.store'), $data)
            ->assertSessionHasNoErrors();

        // Pengguna lain mengajukan jam yang beririsan pada fasilitas dan tanggal yang sama
        $data['start_time'] = '09:30';
        $data['end_time']   = '10:30';

        $this->actingAs($this->buatUser())
            ->post(route('reservations.store'), $data)
            ->assertSessionHasErrors('time');

        $this->assertSame(1, Reservation::count());
    }

    public function test_reservasi_pending_yang_sudah_lewat_tidak_mengunci_kuota(): void
    {
        $pemesan   = $this->buatUser();
        $fasilitas = $this->buatFasilitas();

        // 3 reservasi pending kemarin yang tidak pernah diproses petugas
        $this->buatReservasi($pemesan, $fasilitas, -1, '08:00', '08:30');
        $this->buatReservasi($pemesan, $fasilitas, -1, '09:00', '09:30');
        $this->buatReservasi($pemesan, $fasilitas, -1, '10:00', '10:30');

        $this->actingAs($pemesan)
            ->post(route('reservations.store'), $this->dataReservasi($fasilitas, 2, '09:00', '10:00'))
            ->assertSessionHasNoErrors();

        $this->assertSame(3, Reservation::where('reservation_status', 'rejected')->count());
        $this->assertSame(1, Reservation::where('reservation_status', 'pending')->count());
    }

    public function test_kuota_pending_per_pengguna_maksimal_tiga(): void
    {
        $pemesan   = $this->buatUser();
        $fasilitas = $this->buatFasilitas();

        $this->buatReservasi($pemesan, $fasilitas, 2, '08:00', '08:30');
        $this->buatReservasi($pemesan, $fasilitas, 2, '09:00', '09:30');
        $this->buatReservasi($pemesan, $fasilitas, 2, '10:00', '10:30');

        $this->actingAs($pemesan)
            ->post(route('reservations.store'), $this->dataReservasi($fasilitas, 2, '13:00', '14:00'))
            ->assertSessionHasErrors('time');
    }

    public function test_perintah_kedaluwarsakan_menolak_pending_basi_dan_mencatat_log(): void
    {
        $reservasi = $this->buatReservasi($this->buatUser(), $this->buatFasilitas(), -1, '08:00', '09:00');
        $masihAktif = $this->buatReservasi($this->buatUser(), $this->buatFasilitas(), 3, '08:00', '09:00');

        $this->artisan('reservasi:kedaluwarsakan')->assertExitCode(0);

        $this->assertSame('rejected', $reservasi->fresh()->reservation_status);
        $this->assertSame('pending', $masihAktif->fresh()->reservation_status);

        $log = LogStatusReservasi::where('id_reservasi', $reservasi->id_reservasi)->first();
        $this->assertNotNull($log);
        $this->assertNull($log->changed_by);
        $this->assertSame('rejected', $log->status_after);
    }

    public function test_reservasi_approved_tetap_menghitung_kuota_aktif(): void
    {
        $pemesan   = $this->buatUser();
        $fasilitas = $this->buatFasilitas();

        // 5 reservasi sudah disetujui (tidak ada yang pending) pada hari/jam berbeda
        foreach ([2, 3, 4, 5, 6] as $hari) {
            $this->buatReservasi($pemesan, $fasilitas, $hari, '09:00', '10:00', 'approved');
        }

        $this->actingAs($pemesan)
            ->post(route('reservations.store'), $this->dataReservasi($fasilitas, 10, '09:00', '10:00'))
            ->assertSessionHasErrors('time');

        $this->assertSame(5, Reservation::count());
    }

    public function test_pengguna_tidak_bisa_memesan_dua_fasilitas_di_jam_yang_sama(): void
    {
        $pemesan = $this->buatUser();
        $a       = $this->buatFasilitas();
        $b       = $this->buatFasilitas();

        $this->actingAs($pemesan)
            ->post(route('reservations.store'), $this->dataReservasi($a, 2, '09:00', '10:00'))
            ->assertSessionHasNoErrors();

        $this->actingAs($pemesan)
            ->post(route('reservations.store'), $this->dataReservasi($b, 2, '09:30', '10:30'))
            ->assertSessionHasErrors('time');

        // Jam yang tidak beririsan tetap boleh
        $this->actingAs($pemesan)
            ->post(route('reservations.store'), $this->dataReservasi($b, 2, '10:00', '11:00'))
            ->assertSessionHasNoErrors();

        $this->assertSame(2, Reservation::where('id_user', $pemesan->id_user)->count());
    }

    public function test_admin_dan_petugas_mendapat_403_sebelum_validasi_saat_mengajukan_reservasi(): void
    {
        foreach ([UserRole::Admin, UserRole::Petugas] as $role) {
            // Payload sengaja tidak valid (ID fasilitas asal): tetap harus 403, bukan pesan validasi
            $this->actingAs($this->buatUser($role))
                ->post(route('reservations.store'), ['id_fasilitas' => '00000000-0000-0000-0000-000000000000'])
                ->assertForbidden();
        }

        $this->assertSame(0, Reservation::count());
    }
}
