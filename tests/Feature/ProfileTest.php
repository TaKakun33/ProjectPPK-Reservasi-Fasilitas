<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        // Mengganti email wajib disertai password saat ini
        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'current_password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_changing_email_requires_current_password(): void
    {
        $user = User::factory()->create();
        $emailLama = $user->email;

        $this->actingAs($user)
            ->patch('/profile', ['name' => 'Test User', 'email' => 'baru@example.com'])
            ->assertSessionHasErrors('current_password');

        $this->actingAs($user)
            ->patch('/profile', ['name' => 'Test User', 'email' => 'baru@example.com', 'current_password' => 'salah'])
            ->assertSessionHasErrors('current_password');

        $this->assertSame($emailLama, $user->fresh()->email);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertSoftDeleted($user);
    }

    public function test_deleting_account_cancels_active_reservations(): void
    {
        $user = User::factory()->create();
        $fasilitas = \App\Models\Facility::create([
            'facility_name' => 'Ruang Uji Hapus Akun',
            'type' => 'Ruang Rapat',
            'location' => 'Gedung Uji',
            'capacity' => 10,
            'facility_status' => 'aktif',
        ]);
        $reservasi = \App\Models\Reservation::create([
            'id_user' => $user->id_user,
            'id_fasilitas' => $fasilitas->id_fasilitas,
            'date' => now()->addDays(3)->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'purpose' => 'Kegiatan uji',
            'reservation_status' => 'approved',
        ]);

        $this->actingAs($user)->delete('/profile', ['password' => 'password']);

        $this->assertSame('cancelled', $reservasi->fresh()->reservation_status);
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }
}
