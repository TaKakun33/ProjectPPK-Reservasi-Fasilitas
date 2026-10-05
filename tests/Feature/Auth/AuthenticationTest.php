<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        // Pengguna biasa diarahkan ke daftar fasilitas setelah login
        $response->assertRedirect(route('facilities.index', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_akun_belum_terverifikasi_ditolak_tanpa_membuat_sesi(): void
    {
        $user = User::factory()->create(['account_status' => 'pending']);

        $response = $this->post('/login', [
            'email'    => $user->email,
            'password' => 'password',
            'remember' => 'on',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_pembatasan_percobaan_login_per_ip_untuk_banyak_email(): void
    {
        for ($i = 0; $i < 20; $i++) {
            $this->post('/login', ['email' => "orang{$i}@example.com", 'password' => 'salah']);
        }

        $user = User::factory()->create();

        // Email berbeda dari IP yang sama tetap terkunci setelah 20 kegagalan
        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
