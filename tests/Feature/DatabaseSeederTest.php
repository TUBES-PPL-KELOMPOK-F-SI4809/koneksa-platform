<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_executes_successfully(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('users', 7);
        $this->assertDatabaseCount('business_categories', 7);
        $this->assertDatabaseCount('cooperation_types', 6);
        $this->assertDatabaseCount('businesses', 3);
        $this->assertDatabaseCount('partner_profiles', 2);
        $this->assertDatabaseCount('partner_cooperation_types', 4);
        $this->assertDatabaseCount('products', 4);
        $this->assertDatabaseCount('product_variants', 7);
        $this->assertDatabaseCount('sales', 2);
        $this->assertDatabaseCount('sale_items', 3);
        $this->assertDatabaseCount('stock_movements', 10);
        $this->assertDatabaseCount('digital_offerings', 3);
        $this->assertDatabaseCount('collaboration_proposals', 2);
        $this->assertDatabaseCount('programs', 2);
        $this->assertDatabaseCount('program_applications', 2);
        $this->assertDatabaseCount('verification_requests', 2);
    }
}
