<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BusinessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $businesses = [
            [
                'id' => 1,
                'user_id' => 3,
                'business_category_id' => 1,
                'name' => 'Kopi Aroma Braga',
                'description' => 'Produsen biji kopi sangrai legendaris khas Priangan dan kedai kopi artisanal di jantung Kota Bandung.',
                'address' => 'Jl. Braga No. 12, Braga, Kec. Sumur Bandung',
                'district' => 'Sumur Bandung',
                'subdistrict' => 'Braga',
                'contact_phone' => '081223344551',
                'logo_path' => null,
                'business_needs' => 'Membutuhkan integrasi sistem pembayaran kasir QRIS digital dan perluasan distribusi kemasan ritel.',
                'published_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'user_id' => 4,
                'business_category_id' => 2,
                'name' => 'Batik Shibori Priangan',
                'description' => 'Pengrajin busana etnik modern dengan teknik pewarnaan alami shibori dan motif khas Sunda.',
                'address' => 'Jl. Ir. H. Juanda No. 88, Dago, Kec. Coblong',
                'district' => 'Coblong',
                'subdistrict' => 'Dago',
                'contact_phone' => '081223344552',
                'logo_path' => null,
                'business_needs' => 'Mencari mitra pendanaan untuk pembelian mesin jahit industri dan pembinaan sertifikasi ekspor.',
                'published_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'user_id' => 5,
                'business_category_id' => 3,
                'name' => 'Kerajinan Bambu Manglayang',
                'description' => 'Pusat kerajinan perabot rumah tangga, lampu hias, dan wadah ramah lingkungan berbahan bambu lokal.',
                'address' => 'Jl. Cibiru Hilir No. 45, Pasirbiru, Kec. Cibiru',
                'district' => 'Cibiru',
                'subdistrict' => 'Pasirbiru',
                'contact_phone' => '081223344553',
                'logo_path' => null,
                'business_needs' => 'Pendampingan pemasaran digital di marketplace dan sertifikasi verifikasi UMKM binaan Pemda.',
                'published_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($businesses as $business) {
            DB::table('businesses')->updateOrInsert(['id' => $business['id']], $business);
        }
    }
}
