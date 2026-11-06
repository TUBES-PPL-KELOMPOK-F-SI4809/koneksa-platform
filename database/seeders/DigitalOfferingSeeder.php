<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DigitalOfferingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $offerings = [
            [
                'id' => 1,
                'partner_profile_id' => 1,
                'title' => 'Paket Akselerasi UMKM Go-Digital & POS Cloud',
                'description' => 'Fasilitas aplikasi kasir kasir digital cloud gratis selama 1 tahun, integrasi QRIS dinamis, dan konektivitas internet bisnis berkecepatan tinggi.',
                'published_at' => $now,
                'archived_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'partner_profile_id' => 2,
                'title' => 'Pembiayaan Modal Usaha Mikro KUR Mandiri Digital',
                'description' => 'Program pinjaman modal kerja dengan subsidi bunga 6% p.a., plafon hingga Rp 100 juta dengan proses persetujuan cepat bagi UMKM terverifikasi.',
                'published_at' => $now,
                'archived_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'partner_profile_id' => 1,
                'title' => 'Workshop Pemasaran Digital & Optimasi Marketplace',
                'description' => 'Pelatihan intensif 3 sesi bersama praktisi e-commerce mengenai strategi konten visual produk, live streaming selling, dan manajemen pelanggan.',
                'published_at' => $now,
                'archived_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($offerings as $offering) {
            DB::table('digital_offerings')->updateOrInsert(['id' => $offering['id']], $offering);
        }
    }
}
