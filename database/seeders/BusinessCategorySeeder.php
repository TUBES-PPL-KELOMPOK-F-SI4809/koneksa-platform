<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BusinessCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $categories = [
            ['id' => 1, 'name' => 'Kuliner & Makanan Minuman', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Fashion & Pakaian', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name' => 'Kriya & Kerajinan Tangan', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'name' => 'Jasa & Industri Kreatif', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'name' => 'Agribisnis & Pertanian Perkotaan', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'name' => 'Perdagangan & Ritel', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 7, 'name' => 'Kesehatan, Kecantikan & Herbal', 'created_at' => $now, 'updated_at' => $now],
        ];

        foreach ($categories as $category) {
            DB::table('business_categories')->updateOrInsert(['id' => $category['id']], $category);
        }
    }
}
