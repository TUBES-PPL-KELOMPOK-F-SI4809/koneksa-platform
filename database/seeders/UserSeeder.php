<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        $password = Hash::make('password');

        $users = [
            [
                'id' => 1,
                'name' => 'Administrator Koneksa',
                'email' => 'admin@koneksa.id',
                'role' => 'admin',
                'password' => $password,
                'phone' => '081220000001',
                'avatar_path' => null,
                'account_status' => 'enabled',
                'email_verified_at' => $now,
                'must_change_password' => false,
                'current_session_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'name' => 'Dinas Koperasi dan UKM Kota Bandung',
                'email' => 'pemda@bandung.go.id',
                'role' => 'pemda',
                'password' => $password,
                'phone' => '081220000002',
                'avatar_path' => null,
                'account_status' => 'enabled',
                'email_verified_at' => $now,
                'must_change_password' => false,
                'current_session_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'name' => 'Ahmad Fauzi (Kopi Aroma Braga)',
                'email' => 'ahmad@kopiaroma.id',
                'role' => 'umkm',
                'password' => $password,
                'phone' => '081223344551',
                'avatar_path' => null,
                'account_status' => 'enabled',
                'email_verified_at' => $now,
                'must_change_password' => false,
                'current_session_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'name' => 'Siti Rahmawati (Batik Shibori Priangan)',
                'email' => 'siti@batikpriangan.com',
                'role' => 'umkm',
                'password' => $password,
                'phone' => '081223344552',
                'avatar_path' => null,
                'account_status' => 'enabled',
                'email_verified_at' => $now,
                'must_change_password' => false,
                'current_session_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 5,
                'name' => 'Dedi Kusnadi (Kerajinan Bambu Manglayang)',
                'email' => 'dedi@bambumanglayang.id',
                'role' => 'umkm',
                'password' => $password,
                'phone' => '081223344553',
                'avatar_path' => null,
                'account_status' => 'enabled',
                'email_verified_at' => $now,
                'must_change_password' => false,
                'current_session_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 6,
                'name' => 'Budi Santoso (Telkom Indonesia)',
                'email' => 'mitra@telkom.co.id',
                'role' => 'mitra',
                'password' => $password,
                'phone' => '081198765001',
                'avatar_path' => null,
                'account_status' => 'enabled',
                'email_verified_at' => $now,
                'must_change_password' => false,
                'current_session_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 7,
                'name' => 'Rina Anggraeni (Bank Mandiri Rumah BUMN)',
                'email' => 'mitra@bankmandiri.co.id',
                'role' => 'mitra',
                'password' => $password,
                'phone' => '081198765002',
                'avatar_path' => null,
                'account_status' => 'enabled',
                'email_verified_at' => $now,
                'must_change_password' => false,
                'current_session_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(['email' => $user['email']], $user);
        }
    }
}
