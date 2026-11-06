<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CooperationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $types = [
            ['id' => 1, 'name' => 'Permodalan & Pembiayaan Usaha', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Pelatihan, Workshop & Mentoring', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name' => 'Digitalisasi & Adopsi Teknologi', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'name' => 'Akses Pasar, Distribusi & Ekspor', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'name' => 'Pasokan & Pengadaan Bahan Baku', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'name' => 'Fasilitasi Sertifikasi & Legalitas', 'created_at' => $now, 'updated_at' => $now],
        ];

        foreach ($types as $type) {
            DB::table('cooperation_types')->updateOrInsert(['id' => $type['id']], $type);
        }
    }
}
