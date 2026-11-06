<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $programs = [
            [
                'id' => 1,
                'created_by' => 2,
                'title' => 'Bandung Creative Preneur & Digital Incubator 2026',
                'description' => 'Program inkubasi intensif 3 bulan oleh Dinas Koperasi dan UKM Kota Bandung untuk akselerasi brand lokal, efisiensi rantai pasok, dan persiapan ekspor.',
                'requirements' => "1. Usaha aktif dan berdomisili operasional di wilayah Kota Bandung.\n2. Memiliki omzet usaha minimal 3 bulan berturut-turut.\n3. Berkomitmen mengirimkan penanggung jawab usaha pada seluruh sesi pelatihan.",
                'location' => 'Bandung Creative Hub (BCH), Jl. Laswi No. 7, Kota Bandung',
                'quota' => 50,
                'registration_opens_at' => $now->copy()->subDays(5),
                'registration_closes_at' => $now->copy()->addDays(20),
                'starts_at' => $now->copy()->addDays(25),
                'ends_at' => $now->copy()->addDays(115),
                'status' => 'published',
                'created_at' => $now->copy()->subDays(6),
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'created_by' => 2,
                'title' => 'Fasilitasi Sertifikasi Halal Gratis (SEHATI) UMKM Kota Bandung 2026',
                'description' => 'Program pendampingan audit dan penerbitan sertifikat halal resmi BPJPH tanpa dipungut biaya bagi pelaku usaha mikro makanan dan minuman.',
                'requirements' => "1. Produk makanan/minuman dengan bahan yang sudah terkonfirmasi kehalalannya.\n2. Bersedia mengikuti proses verifikasi lapangan oleh pendamping halal Diskop UKM.",
                'location' => 'Gedung Sentra UMKM Diskop UKM Kota Bandung, Jl. Kawaluyaan No. 2',
                'quota' => 100,
                'registration_opens_at' => $now->copy()->subDays(2),
                'registration_closes_at' => $now->copy()->addDays(15),
                'starts_at' => $now->copy()->addDays(18),
                'ends_at' => $now->copy()->addDays(60),
                'status' => 'published',
                'created_at' => $now->copy()->subDays(3),
                'updated_at' => $now,
            ],
        ];

        foreach ($programs as $program) {
            DB::table('programs')->updateOrInsert(['id' => $program['id']], $program);
        }

        $applications = [
            [
                'id' => 1,
                'program_id' => 1,
                'business_id' => 1,
                'application_note' => 'Kami mendaftar untuk memperkuat manajemen rantai pasok biji kopi dan standardisasi kemasan ritel modern.',
                'attachment_path' => null,
                'status' => 'accepted',
                'reviewed_by' => 2,
                'review_note' => 'Profil usaha dan portofolio produk sangat layak untuk masuk ke kelas akselerasi ekspor BCH.',
                'reviewed_at' => $now,
                'created_at' => $now->copy()->subDays(3),
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'program_id' => 1,
                'business_id' => 3,
                'application_note' => 'Ingin belajar teknik branding internasional dan efisiensi produksi kerajinan bambu berstandar ekspor.',
                'attachment_path' => null,
                'status' => 'pending',
                'reviewed_by' => null,
                'review_note' => null,
                'reviewed_at' => null,
                'created_at' => $now->copy()->subDay(),
                'updated_at' => $now->copy()->subDay(),
            ],
        ];

        foreach ($applications as $application) {
            DB::table('program_applications')->updateOrInsert(['id' => $application['id']], $application);
        }
    }
}
