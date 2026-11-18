<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'role' => User::ROLE_UMKM,
            'account_status' => User::STATUS_ENABLED,
            'phone' => '0812'.fake()->numerify('########'),
            'avatar_path' => null,
            'email_verified_at' => now(),
            'must_change_password' => false,
            'current_session_id' => null,
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user is an UMKM.
     */
    public function umkm(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_UMKM,
        ]);
    }

    /**
     * Indicate that the user is a Mitra Industri.
     */
    public function mitra(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_MITRA,
        ]);
    }

    /**
     * Indicate that the user is a Pemerintah Daerah (Pemda).
     */
    public function pemda(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_PEMDA,
        ]);
    }

    /**
     * Indicate that the user is a Superadmin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_ADMIN,
        ]);
    }

    /**
     * Indicate that the user account is disabled.
     */
    public function disabled(): static
    {
        return $this->state(fn (array $attributes) => [
            'account_status' => User::STATUS_DISABLED,
        ]);
    }

    /**
     * Indicate that the user must change password upon next login.
     */
    public function mustChangePassword(): static
    {
        return $this->state(fn (array $attributes) => [
            'must_change_password' => true,
        ]);
    }
}
