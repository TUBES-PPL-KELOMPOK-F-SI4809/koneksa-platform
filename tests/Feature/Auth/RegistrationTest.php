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

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'role' => 'umkm',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_register_as_mitra_or_pemda(): void
    {
        $responseMitra = $this->post('/register', [
            'name' => 'Mitra User',
            'role' => 'mitra',
            'email' => 'mitra@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $responseMitra->assertRedirect(route('dashboard', absolute: false));

        auth()->logout();

        $responsePemda = $this->post('/register', [
            'name' => 'Pemda User',
            'role' => 'pemda',
            'email' => 'pemda@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $responsePemda->assertRedirect(route('dashboard', absolute: false));
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
