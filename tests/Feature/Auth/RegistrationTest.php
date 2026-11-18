<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register_as_umkm(): void
    {
        $response = $this->post('/register', [
            'name' => 'Pelaku UMKM',
            'role' => 'umkm',
            'email' => 'umkm@koneksa.id',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $user = User::where('email', 'umkm@koneksa.id')->first();
        $this->assertNotNull($user);
        $this->assertEquals(User::ROLE_UMKM, $user->role);
        $this->assertEquals(User::STATUS_ENABLED, $user->account_status);
        $this->assertTrue(Hash::check('password', $user->password));
    }

    public function test_users_can_register_as_mitra(): void
    {
        $response = $this->post('/register', [
            'name' => 'Mitra Industri User',
            'role' => 'mitra',
            'email' => 'mitra@koneksa.id',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $user = User::where('email', 'mitra@koneksa.id')->first();
        $this->assertNotNull($user);
        $this->assertEquals(User::ROLE_MITRA, $user->role);
    }

    public function test_users_cannot_register_as_pemda_via_public_form(): void
    {
        // Kebijakan BR-01 / FR-01.1: Akun Pemda hanya dibuat oleh Superadmin
        $response = $this->post('/register', [
            'name' => 'Petugas Pemda',
            'role' => 'pemda',
            'email' => 'pemda@bandung.go.id',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('role');
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'pemda@bandung.go.id']);
    }

    public function test_users_cannot_register_as_admin_via_public_form(): void
    {
        // Pencegahan privilege escalation: role admin dilarang pada formulir publik
        $response = $this->post('/register', [
            'name' => 'Fake Admin',
            'role' => 'admin',
            'email' => 'fakeadmin@koneksa.id',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('role');
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'fakeadmin@koneksa.id']);
    }

    public function test_users_cannot_register_without_role(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'norole@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('role');
        $this->assertGuest();
    }

    public function test_users_cannot_register_with_invalid_role(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'role' => 'invalid_role',
            'email' => 'invalid@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('role');
        $this->assertGuest();
    }
}
