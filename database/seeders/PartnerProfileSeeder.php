<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PartnerProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $profiles = [
            [
                'id' => 1,
                'user_id' => 6,
                'organization_name' => 'PT Telkom Indonesia (Persero) Tbk',
                'industry' => 'Teknologi Informasi & Telekomunikasi',
                'description' => 'BUMN penyedia solusi infrastruktur digital, inkubasi startup Indigo, dan program pendampingan digitalisasi UMKM nasional.',
                'address' => 'Jl. Japati No. 1, Sadang Serang, Kec. Coblong, Kota Bandung',
                'contact_phone' => '081198765001',
                'website_url' => 'https://telkom.co.id',
                'logo_path' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'user_id' => 7,
                'organization_name' => 'Bank Mandiri (Rumah BUMN Bandung)',
                'industry' => 'Perbankan & Jasa Keuangan',
                'description' => 'Lembaga pendampingan usaha dan penyedia fasilitas pembiayaan Kredit Usaha Rakyat (KUR) mikro untuk akselerasi bisnis.',
                'address' => 'Jl. Naripan No. 25, Braga, Kec. Sumur Bandung, Kota Bandung',
                'contact_phone' => '081198765002',
                'website_url' => 'https://rumahbumn.id',
                'logo_path' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($profiles as $profile) {
            DB::table('partner_profiles')->updateOrInsert(['id' => $profile['id']], $profile);
        }

        $cooperations = [
            ['partner_profile_id' => 1, 'cooperation_type_id' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['partner_profile_id' => 1, 'cooperation_type_id' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['partner_profile_id' => 2, 'cooperation_type_id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['partner_profile_id' => 2, 'cooperation_type_id' => 2, 'created_at' => $now, 'updated_at' => $now],
        ];

        foreach ($cooperations as $cooperation) {
            DB::table('partner_cooperation_types')->updateOrInsert(
                [
                    'partner_profile_id' => $cooperation['partner_profile_id'],
                    'cooperation_type_id' => $cooperation['cooperation_type_id'],
                ],
                $cooperation
            );
        }
    }
}
