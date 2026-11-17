# Kontrak Inertia dan REST

Aplikasi memakai Laravel 13 + Inertia.js v2 + Vue 3 sebagai *modern monolith*.
Jangan membuat REST API paralel untuk alur halaman internal tanpa kebutuhan.

## 1. Halaman, Form Inertia & Otorisasi Server-Side

- **GET Halaman**: Mengembalikan `Inertia::render(...)` dengan props yang relevan dan sudah difilter sesuai hak akses role/kepemilikan (`NFR-04`). Jangan mengirim atribut sensitif seperti `password`, `remember_token`, `current_session_id`, atau data keuangan privat UMKM kepada pihak yang tidak berhak.
- **Mutasi Form (POST/PUT/PATCH/DELETE)**: Mengembalikan redirect ke named route beserta flash message status bila relevan.
- **Validasi**: Menggunakan Form Request dan error bag Laravel/Inertia melalui redirect. Jangan memaksakan envelope JSON atau respons 422 JSON pada form Inertia.
- **RBAC & Ownership (`BR-01`, `NFR-01`)**:
  - Proteksi tingkat rute memakai middleware `auth`, status akun aktif (`account_status = 'enabled'`), dan `role:<daftar_role>` (`umkm`, `mitra`, `pemda`, `admin`).
  - Proteksi tingkat resource/data memakai Laravel Policy atau Gate di sisi server; jangan hanya menyembunyikan tombol di Vue.
- **Audit Trail (`BR-15`, `NFR-08`, `FR-31.2`, `FR-32.1`)**:
  - Setiap mutasi yang mengubah status akun, membuat akun petugas Pemda, menetapkan keputusan verifikasi UMKM, menyeleksi peserta program, mengubah status kemitraan, atau menjalankan tindakan administratif lintas role oleh Superadmin wajib mencatat entri ke `audit_logs` beserta `actor_id` pelaksana.

## 2. REST Eksplisit (Jika Diperlukan Integrasi)

Aturan berikut hanya berlaku untuk endpoint JSON eksplisit yang memang diminta/dipakai integrasi:
- Prefix `/api/v1`, resource plural dengan penamaan mengikuti ERD kanonis: `sales`, `collaboration-proposals`, `collaboration-updates`, `mediation-requests`, `verification-requests`, `audit-logs`.
- Gunakan API Resource dan pola respons standar Laravel (`data`, serta `links` dan `meta` untuk paginasi).
- Validasi JSON memakai HTTP `422` dengan `message` dan `errors` standar Laravel.
- Gunakan HTTP `401` untuk belum terautentikasi, `403` untuk akses ditolak (role/ownership tidak sesuai), `404` untuk resource yang tidak ditemukan atau disembunyikan sesuai kebijakan privasi, `201` untuk pembuatan resource baru, dan `204` untuk penghapusan tanpa body.
- Tentukan kontrak request/response, izin, filter, dan pagination pada tugas/PR; contoh penamaan di dokumen ini bukan klaim bahwa endpoint REST tersebut sudah diimplementasikan.

Periksa daftar rute aktual melalui `php artisan route:list`. Untuk detail skema data dan invariant bisnis, baca [ERD kanonis](RANCANGAN_ERD_KONEKSA.md).
