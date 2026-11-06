<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VerificationRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $requests = [
            [
                'id' => 1,
                'business_id' => 1,
                'submitted_data' => json_encode([
                    'business_name' => 'Kopi Aroma Braga',
                    'category_name' => 'Kuliner & Makanan Minuman',
                    'owner_name' => 'Ahmad Fauzi',
                    'address' => 'Jl. Braga No. 12, Braga, Kec. Sumur Bandung',
                    'district' => 'Sumur Bandung',
                    'subdistrict' => 'Braga',
                    'contact_phone' => '081223344551',
                ]),
                'attachment_path' => null,
                'status' => 'approved',
                'reviewed_by' => 2,
                'review_note' => 'Pemeriksaan administratif dan verifikasi faktual lapangan selesai. Dokumen legalitas operasional dan keaslian tempat usaha di Kota Bandung sah.',
                'reviewed_at' => $now,
                'created_at' => $now->copy()->subDays(4),
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'business_id' => 3,
                'submitted_data' => json_encode([
                    'business_name' => 'Kerajinan Bambu Manglayang',
                    'category_name' => 'Kriya & Kerajinan Tangan',
                    'owner_name' => 'Dedi Kusnadi',
                    'address' => 'Jl. Cibiru Hilir No. 45, Pasirbiru, Kec. Cibiru',
                    'district' => 'Cibiru',
                    'subdistrict' => 'Pasirbiru',
                    'contact_phone' => '081223344553',
                ]),
                'attachment_path' => null,
                'status' => 'pending',
                'reviewed_by' => null,
                'review_note' => null,
                'reviewed_at' => null,
                'created_at' => $now->copy()->subDay(),
                'updated_at' => $now->copy()->subDay(),
            ],
        ];

        foreach ($requests as $request) {
            DB::table('verification_requests')->updateOrInsert(['id' => $request['id']], $request);
        }
    }
}
