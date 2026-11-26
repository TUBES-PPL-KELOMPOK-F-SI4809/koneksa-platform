<?php

namespace Database\Factories;

use App\Models\PartnerProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PartnerProfile>
 */
class PartnerProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->mitra(),
            'organization_name' => fake()->company(),
            'industry' => fake()->randomElement(PartnerProfile::ENTITY_CATEGORIES),
            'description' => fake()->paragraph(),
            'address' => fake()->address(),
            'contact_phone' => '0811'.fake()->numerify('########'),
            'website_url' => fake()->url(),
            'logo_path' => null,
        ];
    }
}

