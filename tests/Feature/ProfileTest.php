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

    public function test_unauthenticated_user_cannot_access_or_update_profile(): void
    {
        $this->get('/profile')->assertRedirect(route('login'));

        $this->patch('/profile', [
            'name' => 'Hacker',
            'email' => 'hacker@example.com',
        ])->assertRedirect(route('login'));
    }

    public function test_profile_information_can_be_updated_including_phone(): void
    {
        $user = User::factory()->create([
            'phone' => '081200000001',
        ]);

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Nama Pengguna Baru',
                'email' => 'baru@koneksa.id',
                'phone' => '081298765432',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Nama Pengguna Baru', $user->name);
        $this->assertSame('baru@koneksa.id', $user->email);
        $this->assertSame('081298765432', $user->phone);
        $this->assertNull($user->email_verified_at);
    }

    public function test_phone_format_validation(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Valid Name',
                'email' => $user->email,
                'phone' => 'bukan-angka-telepon',
            ]);

        $response->assertSessionHasErrors('phone');
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

    public function test_privileged_fields_cannot_be_modified_via_profile_update(): void
    {
        // Pengguna memiliki role 'umkm' dan status 'enabled'
        $user = User::factory()->umkm()->create([
            'account_status' => User::STATUS_ENABLED,
            'must_change_password' => false,
        ]);

        // Percobaan manipulasi role & status melalui request payload profil
        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'User Biasa',
                'email' => $user->email,
                'role' => User::ROLE_ADMIN, // Percobaan eskalasi hak istimewa
                'account_status' => User::STATUS_DISABLED,
                'must_change_password' => true,
                'current_session_id' => 'fake_session_token',
            ]);

        $response->assertSessionHasNoErrors();
        $user->refresh();

        // Field privileged harus tetap utuh dan tidak berubah
        $this->assertSame(User::ROLE_UMKM, $user->role);
        $this->assertSame(User::STATUS_ENABLED, $user->account_status);
        $this->assertFalse($user->must_change_password);
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
        $this->assertNull(User::find($user->id));
        $this->assertSoftDeleted($user);
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
            ->assertSessionHasErrors('password')
            ->assertRedirect('/profile');

        $this->assertNotNull(User::find($user->id));
    }
}
