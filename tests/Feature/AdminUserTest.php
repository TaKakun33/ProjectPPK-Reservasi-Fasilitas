<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\MembuatData;
use Tests\TestCase;

class AdminUserTest extends TestCase
{
    use RefreshDatabase, MembuatData;

    public function test_akun_yang_ditolak_bisa_diverifikasi_ulang(): void
    {
        $admin = $this->buatUser(UserRole::Admin);
        $user  = $this->buatUser(UserRole::Pengguna, ['account_status' => 'rejected']);

        $this->actingAs($admin)
            ->patch(route('admin.users.verify', $user->id_user))
            ->assertSessionHas('success');

        $this->assertSame('verified', $user->fresh()->account_status);
    }

    public function test_akun_verified_tidak_bisa_diverifikasi_lagi(): void
    {
        $admin = $this->buatUser(UserRole::Admin);
        $user  = $this->buatUser();

        $this->actingAs($admin)
            ->patch(route('admin.users.verify', $user->id_user))
            ->assertSessionHas('error');
    }
}
