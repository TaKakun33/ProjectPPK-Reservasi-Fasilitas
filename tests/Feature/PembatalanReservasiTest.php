<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\MembuatData;
use Tests\TestCase;

class PembatalanReservasiTest extends TestCase
{
    use RefreshDatabase, MembuatData;

    public function test_reservasi_approved_yang_sudah_selesai_tidak_bisa_dibatalkan(): void
    {
        $petugas   = $this->buatUser(UserRole::Petugas);
        $fasilitas = $this->buatFasilitas();
        $reservasi = $this->buatReservasi($this->buatUser(), $fasilitas, -1, '09:00', '10:00', 'approved');

        $this->actingAs($petugas)
            ->patch(route('petugas.reservations.cancel', $reservasi->id_reservasi), ['cancellation_reason' => 'Salah input'])
            ->assertSessionHas('error');

        $this->assertSame('approved', $reservasi->fresh()->reservation_status);
    }

    public function test_reservasi_approved_yang_belum_terjadi_bisa_dibatalkan(): void
    {
        $petugas   = $this->buatUser(UserRole::Petugas);
        $fasilitas = $this->buatFasilitas();
        $reservasi = $this->buatReservasi($this->buatUser(), $fasilitas, 3, '09:00', '10:00', 'approved');

        $this->actingAs($petugas)
            ->patch(route('petugas.reservations.cancel', $reservasi->id_reservasi), ['cancellation_reason' => 'Fasilitas dipakai acara kampus'])
            ->assertSessionHas('success');

        $this->assertSame('cancelled', $reservasi->fresh()->reservation_status);
    }

    public function test_catatan_persetujuan_dibatasi_panjangnya(): void
    {
        $petugas   = $this->buatUser(UserRole::Petugas);
        $fasilitas = $this->buatFasilitas();
        $reservasi = $this->buatReservasi($this->buatUser(), $fasilitas, 3, '09:00', '10:00');

        $this->actingAs($petugas)
            ->patch(route('petugas.reservations.approve', $reservasi->id_reservasi), ['notes' => str_repeat('a', 501)])
            ->assertSessionHasErrors('notes');

        $this->assertSame('pending', $reservasi->fresh()->reservation_status);
    }
}
