<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\MembuatData;
use Tests\TestCase;

class FasilitasAdminTest extends TestCase
{
    use RefreshDatabase, MembuatData;

    public function test_fasilitas_dengan_reservasi_approved_mendatang_tidak_bisa_dinonaktifkan(): void
    {
        $admin     = $this->buatUser(UserRole::Admin);
        $fasilitas = $this->buatFasilitas();
        $this->buatReservasi($this->buatUser(), $fasilitas, 3, '09:00', '10:00', 'approved');

        $this->actingAs($admin)
            ->delete(route('admin.fasilitas.destroy', $fasilitas->id_fasilitas))
            ->assertSessionHas('error');

        $this->assertSame('aktif', $fasilitas->fresh()->facility_status);
    }

    public function test_menonaktifkan_fasilitas_menolak_otomatis_reservasi_pending_mendatang(): void
    {
        $admin     = $this->buatUser(UserRole::Admin);
        $fasilitas = $this->buatFasilitas();
        $pending   = $this->buatReservasi($this->buatUser(), $fasilitas, 3, '09:00', '10:00', 'pending');

        $this->actingAs($admin)
            ->delete(route('admin.fasilitas.destroy', $fasilitas->id_fasilitas))
            ->assertSessionHas('success');

        $this->assertSame('nonaktif', $fasilitas->fresh()->facility_status);
        $this->assertSame('rejected', Reservation::find($pending->id_reservasi)->reservation_status);
    }

    public function test_nama_fasilitas_harus_unik(): void
    {
        $admin     = $this->buatUser(UserRole::Admin);
        $fasilitas = $this->buatFasilitas(['facility_name' => 'Aula Uji']);

        $this->actingAs($admin)
            ->post(route('admin.fasilitas.store'), [
                'facility_name' => 'Aula Uji',
                'type'          => 'Aula',
                'location'      => 'Gedung Uji',
                'capacity'      => 50,
            ])
            ->assertSessionHasErrors('facility_name');
    }
}
