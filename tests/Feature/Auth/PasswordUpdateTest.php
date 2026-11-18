<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_can_be_updated_with_valid_current_password(): void
    {
        $user = User::factory()->mustChangePassword()->create();

        $this->assertTrue($user->must_change_password);

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->put('/password', [
                'current_password' => 'password',
                'password' => 'New-Secret-Password-123!',
                'password_confirmation' => 'New-Secret-Password-123!',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        // Password tersimpan dalam bentuk hash yang aman (FR-02.2)
        $this->assertTrue(Hash::check('New-Secret-Password-123!', $user->password));

        // Flag must_change_password direset menjadi false
        $this->assertFalse($user->must_change_password);
    }

    public function test_correct_password_must_be_provided_to_update_password(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->put('/password', [
                'current_password' => 'wrong-password',
                'password' => 'new-password-123',
                'password_confirmation' => 'new-password-123',
            ]);

        $response
            ->assertSessionHasErrors('current_password')
            ->assertRedirect('/profile');

        $this->assertTrue(Hash::check('password', $user->refresh()->password));
    }

    public function test_new_password_must_be_confirmed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->put('/password', [
                'current_password' => 'password',
                'password' => 'new-password-123',
                'password_confirmation' => 'mismatched-password',
            ]);

        $response
            ->assertSessionHasErrors('password')
            ->assertRedirect('/profile');
    }

    public function test_new_password_must_meet_minimum_length(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->put('/password', [
                'current_password' => 'password',
                'password' => 'short',
                'password_confirmation' => 'short',
            ]);

        $response
            ->assertSessionHasErrors('password')
            ->assertRedirect('/profile');
    }

    public function test_unauthenticated_user_cannot_update_password(): void
    {
        $response = $this->put('/password', [
            'current_password' => 'password',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ]);

        $response->assertRedirect(route('login'));
    }
}
