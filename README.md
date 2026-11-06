# 🚀 KONEKSA — Platform Kolaborasi UMKM dan Inovasi Industri Digital Daerah

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="320" alt="Koneksa Logo">
</p>

<p align="center">
  <strong>Koneksi dan Akselerasi Usaha (Kota Bandung)</strong><br>
  Tugas Besar Pengembangan Perangkat Lunak (PPL) — Kelas SI-48-09 — Kelompok F
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-13.x-FF2D20?style=flat&logo=laravel" alt="Laravel 13">
  <img src="https://img.shields.io/badge/PHP-8.5-777BB4?style=flat&logo=php" alt="PHP 8.5">
  <img src="https://img.shields.io/badge/Inertia.js-v2.0-8B5CF6?style=flat&logo=inertia" alt="Inertia.js">
  <img src="https://img.shields.io/badge/Vue.js-3.x-4FC08D?style=flat&logo=vue.js" alt="Vue 3">
  <img src="https://img.shields.io/badge/Tailwind_CSS-3.x%2F4.x-38B2AC?style=flat&logo=tailwind-css" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/styled--components-6.x-DB7093?style=flat&logo=styled-components" alt="styled-components">
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat&logo=mysql" alt="MySQL 8">
  <img src="https://img.shields.io/badge/Testing-PHPUnit%20%7C%20Playwright-brightgreen" alt="Tests">
</p>

---

## 📌 1. Tentang KONEKSA

**KONEKSA (Koneksi dan Akselerasi Usaha)** adalah platform berbasis web yang dirancang untuk menghubungkan **pelaku UMKM**, **mitra industri**, dan **pemerintah daerah** dalam satu wadah kolaborasi terpadu guna mendorong pertumbuhan ekonomi daerah (dengan studi kasus Kota Bandung).

### Latar Belakang & Masalah
Pelaku Usaha Mikro, Kecil, dan Menengah (UMKM) sering kali menghadapi kendala:
1. **Pengelolaan Operasional Manual**: Pencatatan transaksi, stok, dan laporan penjualan yang rentan kesalahan.
2. **Keterbatasan Akses Pasar & Mitra**: Sulitnya menemukan mitra industri untuk kerja sama distribusi, pemasaran, dan peningkatan kapasitas bisnis.
3. **Keterisolasian Informasi**: Informasi mengenai program pelatihan, pembinaan, dan sertifikasi dari pemerintah daerah yang sulit dijangkau secara terstruktur.

### Kontribusi terhadap SDGs:
* **SDG 8**: Pekerjaan Layak dan Pertumbuhan Ekonomi (*Decent Work and Economic Growth*).
* **SDG 9**: Industri, Inovasi, dan Infrastruktur (*Industry, Innovation, and Infrastructure*).
* **SDG 17**: Kemitraan untuk Mencapai Tujuan (*Partnerships for the Goals*).

---

## 👥 2. Karakteristik Pengguna & Hak Akses (4 Aktor)

| Aktor | Peran Utama | Lingkup Hak Akses |
| :--- | :--- | :--- |
| **UMKM** | Pelaku usaha terdaftar di daerah | Kelola profil usaha, katalog produk, stok barang, catat transaksi penjualan, lihat laporan omzet, ajukan proposal kerja sama, dan daftar program pemerintah. |
| **Mitra Industri** | Organisasi / korporasi calon mitra | Kelola profil mitra, jelajahi direktori UMKM, ajukan proposal kolaborasi, publikasikan penawaran solusi teknologi digital. |
| **Pemerintah Daerah** | Petugas dinas berwenang tingkat kota | Kelola program pembinaan daerah, seleksi pendaftaran UMKM, verifikasi administratif UMKM, pantau dashboard dan ekspor rekap statistik daerah. |
| **Admin Platform** | Pengelola teknis sistem | Aktivasi/nonaktivasi akun pengguna, pembuatan akun petugas pemerintah, manajemen kontrol akses global. |

---

## 🎯 3. Ruang Lingkup Fitur (21 Fitur) & Pembagian Tim Pengembang

Pengembangan KONEKSA mencakup **21 Fitur Utama (42 Functional Requirements)** yang dikelola oleh 7 pengembang (3 fitur per anggota tim):

| No | Kode | Fitur Utama | Tanggung Jawab (Assignee) | Deskripsi Fungsional |
| :-: | :-: | :--- | :--- | :--- |
| 1 | **F01** | Autentikasi Sistem | **Nabil Fikry Khaidar** (102022430014) | Registrasi UMKM/Mitra, login, logout, sesi, dan guard otorisasi role. |
| 2 | **F02** | Profil Akun | **Nabil Fikry Khaidar** (102022430014) | Lihat & perbarui profil dasar, ubah password dengan verifikasi sandi lama. |
| 3 | **F03** | Admin Pengguna | **Nabil Fikry Khaidar** (102022430014) | Manajemen status akun pengguna, pembuatan akun petugas pemerintah daerah. |
| 4 | **F04** | Profil UMKM | **Muhammad Izaz Naufal** (102022400306) | Kelola profil identitas usaha, kebutuhan kolaborasi, dan publikasi profil. |
| 5 | **F05** | Katalog Produk | **Muhammad Izaz Naufal** (102022400306) | Tambah, ubah, arsipkan produk barang/jasa UMKM. |
| 6 | **F06** | Manajemen Stok | **Muhammad Izaz Naufal** (102022400306) | Restock, pemotongan stok otomatis saat penjualan, notifikasi stok menipis. |
| 7 | **F07** | Transaksi Penjualan | **Muhammad Amin Hazami** (102022400110) | Pencatatan transaksi atomik, kalkulasi total otomatis, riwayat penjualan. |
| 8 | **F08** | Dashboard UMKM | **Muhammad Amin Hazami** (102022400110) | Grafik omzet, metrik transaksi, ringkasan sisa stok, pintasan aksi cepat. |
| 9 | **F09** | Laporan Penjualan | **Muhammad Amin Hazami** (102022400110) | Rekap penjualan berdasarkan periode tanggal dan ekspor data ke CSV. |
| 10 | **F10** | Direktori UMKM | **Dewinda Salsabila Putri Haidir** (102022430028) | Direktori publik profil UMKM, pencarian cepat, dan filter multi-kategori. |
| 11 | **F11** | Peluang Usaha | **Dewinda Salsabila Putri Haidir** (102022430028) | Detail profil publik UMKM, status verifikasi resmi, dan pembatasan proposal. |
| 12 | **F12** | Pengajuan Proposal | **Dewinda Salsabila Putri Haidir** (102022430028) | Mitra mengirim proposal kerja sama; UMKM menerima atau menolak proposal. |
| 13 | **F13** | Program Pemerintah | **Syifa Ummayah** (102022400161) | Pembuatan/pengubahan program pelatihan daerah, pembukaan & penutupan pendaftaran. |
| 14 | **F14** | Pendaftaran Program | **Syifa Ummayah** (102022400161) | Eksplorasi detail program pembinaan, pendaftaran UMKM, pemantauan status lolos. |
| 15 | **F15** | Seleksi Peserta | **Syifa Ummayah** (102022400161) | Review pendaftar program daerah, terima/tolak berdasar batas kuota ketat. |
| 16 | **F16** | Profil Mitra Industri | **Rayula Samina** (102022400102) | Kelola identitas organisasi mitra dan preferensi kategori kerja sama. |
| 17 | **F17** | Monitoring Kemitraan | **Rayula Samina** (102022400102) | Pelacakan status proposal kemitraan aktif, perbarui progress berjalan/selesai. |
| 18 | **F18** | Solusi Inovasi Digital | **Rayula Samina** (102022400102) | Publikasi katalog penawaran solusi teknologi mitra, pengiriman minat kolaborasi. |
| 19 | **F19** | Dashboard Pemda | **Sepdaffa Raja Dwi Putra Basman** (102022400191) | Ringkasan statistik UMKM daerah, status verifikasi, rekap program & kolaborasi. |
| 20 | **F20** | Rekap Daerah & Ekspor| **Sepdaffa Raja Dwi Putra Basman** (102022400191) | Tabel agregat UMKM daerah per kategori, ekspor data terotorisasi ke CSV. |
| 21 | **F21** | Verifikasi UMKM | **Sepdaffa Raja Dwi Putra Basman** (102022400191) | Pengajuan verifikasi profil UMKM, review dokumen & keputusan oleh dinas. |

---

## 🏗️ 4. Arsitektur & Tech Stack

Aplikasi dirancang menggunakan pola **Modern Monolith** yang menyatukan performa backend tangguh dengan responsivitas Single Page Application (SPA):

```text
┌─────────────────────────────────────────────────────────────┐
│                    Frontend Client (SPA)                    │
│   Vue 3 + Inertia.js Client + Tailwind CSS + styled-components│
│             Komponen Aksesibel berbasis shadcn-ui           │
└──────────────────────────────▲──────────────────────────────┘
                               │ JSON Protocol via Inertia
┌──────────────────────────────▼──────────────────────────────┐
│                    Backend Server (Laravel)                 │
│         Laravel 13 + Inertia Server Adapter + Eloquent      │
│          Otorisasi Server, Transaksi Atomik, Form Requests  │
└──────────────────────────────▲──────────────────────────────┘
                               │ PDO / Database Driver
┌──────────────────────────────▼──────────────────────────────┐
│                  Basis Data & Infrastruktur                 │
│              MySQL 8.0 (Dev/Prod) | SQLite (Testing)        │
│                    Docker Containerization                  │
└─────────────────────────────────────────────────────────────┘
```

* **Backend**: Laravel 13 (PHP 8.5+), Eloquent ORM, Laravel Breeze.
* **Frontend**: Inertia.js v2, Vue 3 (`<script setup>`), Tailwind CSS, `styled-components` (CSS-in-JS).
* **UI Foundation**: Pola komponen modular dari katalog `shadcn-ui`.
* **Database**: MySQL 8.0 (Aplikasi), SQLite in-memory (Unit Testing terisolasi).
* **Manajemen & Kolaborasi**: Git, GitHub, Jira (Agile/Scrum 4 Sprint), Figma, Visual Paradigm.

---

## ⚡ 5. Panduan Instalasi Lokal (Setup Guide)

### Prasyarat Sistem
* PHP `>= 8.2` (Disarankan PHP `8.5`) dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`.
* Composer `>= 2.x`
* Node.js `>= 20.x` & npm
* MySQL Server `>= 8.0` (atau Docker)

### Langkah Instalasi:

1. **Clone Repositori**:
   ```bash
   git clone https://github.com/your-org/koneksa-platform.git
   cd koneksa-platform
   ```

2. **Instal Dependensi Backend & Frontend**:
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Sesuaikan pengaturan koneksi basis data di `.env`:*
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=koneksa_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Jalankan Migrasi & Database Seeder**:
   ```bash
   php artisan migrate --seed
   ```

5. **Jalankan Aplikasi (Development Server)**:
   ```bash
   # Terminal 1: Backend Laravel server
   php artisan serve

   # Terminal 2: Frontend Vite HMR server
   npm run dev
   ```
   Akses aplikasi pada peramban melalui: `http://localhost:8000`

---

## 🧪 6. Pengujian Sistem (Testing Standards)

Setiap pengembang dan AI Coding Agent diwajibkan menulis dan menjalankan unit test setelah menyelesaikan pengerjaan fitur:

```bash
# Menjalankan Unit Test suite
php artisan test --testsuite=Unit

# Menjalankan pengujian fitur spesifik
php artisan test tests/Unit/Services/YourFeatureServiceTest.php

# Menjalankan browser testing via Playwright
node .agents/skills/playwright/scripts/playwright-cli.js --help
```

---

## 📚 7. Direktori Dokumentasi Proyek (`docs/`)

Seluruh dokumentasi teknis dan perancangan tersimpan secara terpusat di folder [`docs/`](file:///home/naabilfikry/Documents/Punya NAFI/KULIAH/MATKUL/PPL/TUBES/koneksa-platform/docs):

* [**`docs/PRD/`**](file:///home/naabilfikry/Documents/Punya NAFI/KULIAH/MATKUL/PPL/TUBES/koneksa-platform/docs/PRD): Product Requirement Document (PRD Baseline & Template).
* [**`docs/analysis/`**](file:///home/naabilfikry/Documents/Punya NAFI/KULIAH/MATKUL/PPL/TUBES/koneksa-platform/docs/analysis): Spesifikasi Use Case, Diagram Relasi Basis Data (ERD), Panduan REST API.
* [**`docs/design-system/`**](file:///home/naabilfikry/Documents/Punya NAFI/KULIAH/MATKUL/PPL/TUBES/koneksa-platform/docs/design-system): Baseline Desain Website, Panduan `styled-components`, dan Token Desain.
* [**`docs/guidelines/`**](file:///home/naabilfikry/Documents/Punya NAFI/KULIAH/MATKUL/PPL/TUBES/koneksa-platform/docs/guidelines): Panduan Clean Code, SOP Pembuatan Fitur/Folder/Database, Aturan Unit Test, Standar Git.
* [**`docs/tech-stack/`**](file:///home/naabilfikry/Documents/Punya NAFI/KULIAH/MATKUL/PPL/TUBES/koneksa-platform/docs/tech-stack): Inventaris Tech Stack Aktif dan Protokol RFC Penambahan Stack Baru.
* [**`AGENTS.md`**](file:///home/naabilfikry/Documents/Punya NAFI/KULIAH/MATKUL/PPL/TUBES/koneksa-platform/AGENTS.md): Aturan Operasional Wajib bagi AI Coding Agent.

---

## 👨‍💻 8. Anggota Tim Pengembang (Kelompok F — SI-48-09)

| Nama Pengembang | NIM | Peran Utama | Modul / Fitur yang Dikelola |
| :--- | :---: | :---: | :--- |
| **Nabil Fikry Khaidar** | `102022430014` | Dev 1 | F01 (Autentikasi), F02 (Profil Akun), F03 (Admin Pengguna) |
| **Muhammad Izaz Naufal** | `102022400306` | Dev 2 | F04 (Profil UMKM), F05 (Katalog Produk), F06 (Manajemen Stok) |
| **Muhammad Amin Hazami** | `102022400110` | Dev 3 | F07 (Transaksi Penjualan), F08 (Dashboard UMKM), F09 (Laporan Penjualan) |
| **Dewinda Salsabila Putri Haidir** | `102022430028` | Dev 4 | F10 (Direktori UMKM), F11 (Peluang Usaha), F12 (Pengajuan Proposal) |
| **Syifa Ummayah** | `102022400161` | Dev 5 | F13 (Program Pemerintah), F14 (Pendaftaran Program), F15 (Seleksi Peserta) |
| **Rayula Samina** | `102022400102` | Dev 6 | F16 (Profil Mitra Industri), F17 (Monitoring Kemitraan), F18 (Solusi Inovasi Digital) |
| **Sepdaffa Raja Dwi Putra Basman** | `102022400191` | Dev 7 | F19 (Dashboard Pemda), F20 (Rekap Daerah & Ekspor), F21 (Verifikasi UMKM) |

---

<p align="center">
  Dibuat dengan ❤️ untuk kemajuan ekosistem UMKM dan Inovasi Digital Daerah Kota Bandung.<br>
  © 2026 Kelompok F — S1 Sistem Informasi, Telkom University.
</p>
