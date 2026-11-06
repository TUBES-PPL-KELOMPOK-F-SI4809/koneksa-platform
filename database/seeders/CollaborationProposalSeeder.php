<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CollaborationProposalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $proposals = [
            [
                'id' => 1,
                'business_id' => 1,
                'partner_profile_id' => 1,
                'initiated_by' => 3,
                'cooperation_type_id' => 3,
                'digital_offering_id' => 1,
                'title' => 'Penerapan POS Cloud & QRIS pada Kedai Kopi Aroma Braga',
                'description' => 'Kami bermaksud mengintegrasikan sistem kasir digital cloud dan koneksi internet bisnis guna mempercepat antrean pelayanan kedai fisik kami.',
                'attachment_path' => null,
                'status' => 'accepted',
                'response_note' => 'Proposal disetujui. Tim kemitraan Telkom akan menghubungi penanggung jawab untuk jadwal instalasi perangkat kasir.',
                'responded_at' => $now,
                'started_at' => $now,
                'completed_at' => null,
                'created_at' => $now->subDays(3),
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'business_id' => 2,
                'partner_profile_id' => 2,
                'initiated_by' => 4,
                'cooperation_type_id' => 1,
                'digital_offering_id' => 2,
                'title' => 'Pengajuan Fasilitas Modal Kerja KUR untuk Pembelian Bahan Baku Alami',
                'description' => 'Permohonan fasilitas modal usaha produktif senilai Rp 50.000.000 untuk pengadaan kain katun mori dan pewarna alami shibori skala besar.',
                'attachment_path' => null,
                'status' => 'pending',
                'response_note' => null,
                'responded_at' => null,
                'started_at' => null,
                'completed_at' => null,
                'created_at' => $now->subDays(1),
                'updated_at' => $now->subDays(1),
            ],
        ];

        foreach ($proposals as $proposal) {
            DB::table('collaboration_proposals')->updateOrInsert(['id' => $proposal['id']], $proposal);
        }
    }
}
