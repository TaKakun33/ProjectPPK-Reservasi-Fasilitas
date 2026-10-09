<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    // Alur registrasi sekarang: akun berstatus pending, TIDAK auto-login, menunggu verifikasi admin
    public function test_new_users_register_as_pending_and_are_not_logged_in(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertGuest();
        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'account_status' => 'pending',
            'role' => 'pengguna',
        ]);
    }

    public function test_pending_users_cannot_login(): void
    {
        $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->post('/login', ['email' => 'test@example.com', 'password' => 'password']);

        $this->assertGuest();
    }
}
