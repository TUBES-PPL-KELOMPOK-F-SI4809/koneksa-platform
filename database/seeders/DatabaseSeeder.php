<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            BusinessCategorySeeder::class,
            CooperationTypeSeeder::class,
            BusinessSeeder::class,
            PartnerProfileSeeder::class,
            ProductSeeder::class,
            SaleSeeder::class,
            DigitalOfferingSeeder::class,
            CollaborationProposalSeeder::class,
            ProgramSeeder::class,
            VerificationRequestSeeder::class,
        ]);
    }
}
