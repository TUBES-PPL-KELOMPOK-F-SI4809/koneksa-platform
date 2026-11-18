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
  <img src="https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=flat&logo=php" alt="PHP 8.3+">
  <img src="https://img.shields.io/badge/Inertia.js-v2.0-8B5CF6?style=flat&logo=inertia" alt="Inertia.js">
  <img src="https://img.shields.io/badge/Vue.js-3.x-4FC08D?style=flat&logo=vue.js" alt="Vue 3">
  <img src="https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=flat&logo=tailwind-css" alt="Tailwind CSS">
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
| **Admin Platform / Superadmin** | Pengelola teknis sistem | Aktivasi/nonaktivasi akun pengguna, pembuatan akun petugas pemerintah, tindakan administratif lintas role, manajemen kontrol akses global, dan penelusuran *audit trail*. |

---

## 🎯 3. Ruang Lingkup Fitur (32 Fitur / 64 FR) & Pembagian Tim Pengembang

Pengembangan KONEKSA mencakup **32 Fitur Utama (64 Functional Requirements)** sesuai Proposal PPL revisi 13 November 2026 yang dikelola oleh 7 pengembang:

| No | Kode | Fitur Utama | Tanggung Jawab (Assignee) | Deskripsi Fungsional |
| :-: | :-: | :--- | :--- | :--- |
| 1 | **F01** | Autentikasi Sistem | **Nabil Fikry Khaidar** (102022430014) | Registrasi UMKM/Mitra, login, logout, sesi tunggal, dan guard otorisasi role. |
| 2 | **F02** | Profil Akun | **Nabil Fikry Khaidar** (102022430014) | Lihat & perbarui profil dasar, ubah password dengan verifikasi sandi lama. |
| 3 | **F03** | Admin Pengguna | **Nabil Fikry Khaidar** (102022430014) | Manajemen status akun pengguna (`enabled`/`disabled`), pembuatan akun petugas pemerintah daerah. |
| 4 | **F04** | Profil UMKM | **Muhammad Izaz Naufal** (102022400306) | Kelola profil identitas usaha, kebutuhan kolaborasi, dan publikasi profil. |
| 5 | **F05** | Katalog Produk | **Muhammad Izaz Naufal** (102022400306) | Tambah, ubah, arsipkan produk barang/jasa UMKM beserta varian. |
| 6 | **F06** | Manajemen Stok | **Muhammad Izaz Naufal** (102022400306) | Restock, pemotongan stok otomatis saat penjualan, notifikasi stok menipis. |
| 7 | **F07** | Transaksi Penjualan | **Muhammad Amin Hazami** (102022400110) | Pencatatan transaksi atomik, kalkulasi total otomatis, riwayat penjualan. |
| 8 | **F08** | Dashboard UMKM | **Muhammad Amin Hazami** (102022400110) | Grafik omzet, metrik transaksi, ringkasan sisa stok, pintasan aksi cepat. |
| 9 | **F09** | Laporan Penjualan | **Muhammad Amin Hazami** (102022400110) | Rekap penjualan berdasarkan periode tanggal dan ekspor data ke CSV. |
| 10 | **F10** | Direktori UMKM | **Dewinda Salsabila Putri Haidir** (102022430028) | Direktori publik profil UMKM, pencarian cepat, dan filter multi-kategori. |
| 11 | **F11** | Peluang Usaha | **Dewinda Salsabila Putri Haidir** (102022430028) | Detail profil publik UMKM, status verifikasi resmi, dan pembatasan proposal. |
| 12 | **F12** | Pengajuan Proposal | **Dewinda Salsabila Putri Haidir** (102022430028) | UMKM/Mitra mengirim proposal kerja sama; pihak penerima menerima atau menolak proposal. |
| 13 | **F13** | Program Pemerintah | **Syifa Ummayah** (102022400161) | Pembuatan/pengubahan program pelatihan daerah, sasaran peserta, pembukaan & penutupan pendaftaran. |
| 14 | **F14** | Pendaftaran Program | **Syifa Ummayah** (102022400161) | Eksplorasi detail program pembinaan, pendaftaran UMKM/Mitra, pemantauan status lolos. |
| 15 | **F15** | Seleksi Peserta | **Syifa Ummayah** (102022400161) | Review pendaftar program daerah, terima/tolak berdasar batas kuota ketat. |
| 16 | **F16** | Profil Mitra Industri | **Rayula Samina** (102022400102) | Kelola identitas organisasi mitra dan preferensi kategori kerja sama. |
| 17 | **F17** | Monitoring Kemitraan | **Rayula Samina** (102022400102) | Pelacakan status proposal kemitraan aktif, perbarui progress berjalan/selesai. |
| 18 | **F18** | Solusi Inovasi Digital | **Rayula Samina** (102022400102) | Publikasi katalog penawaran solusi teknologi mitra, pengiriman minat kolaborasi. |
| 19 | **F19** | Dashboard Pemda | **Sepdaffa Raja Dwi Putra Basman** (102022400191) | Ringkasan statistik UMKM daerah, status verifikasi, rekap program & kolaborasi. |
| 20 | **F20** | Rekap Daerah & Ekspor | **Sepdaffa Raja Dwi Putra Basman** (102022400191) | Tabel agregat UMKM daerah per kategori, ekspor data terotorisasi ke CSV. |
| 21 | **F21** | Verifikasi UMKM | **Sepdaffa Raja Dwi Putra Basman** (102022400191) | Pengajuan verifikasi profil UMKM, review dokumen & keputusan oleh dinas. |
| 22 | **F22** | Publikasi Performa UMKM | **Muhammad Amin Hazami** (102022400110) | Pengaturan indikator performa bisnis publik UMKM & peninjauan oleh Mitra Industri. |
| 23 | **F23** | Kebutuhan Kolaborasi | **Muhammad Izaz Naufal** (102022400306) | Publikasi kebutuhan kolaborasi UMKM & filter peluang kemitraan bagi Mitra. |
| 24 | **F24** | Kesepakatan Kemitraan | **Dewinda Salsabila Putri Haidir** (102022430028) | Pencatatan dan peninjauan ringkasan kesepakatan, bentuk kerja sama, periode, dan target. |
| 25 | **F25** | Progres Kemitraan | **Rayula Samina** (102022400102) | Penambahan pembaruan aktivitas, catatan perkembangan, dan riwayat progres kemitraan. |
| 26 | **F26** | Undangan Khusus (VIP) | **Syifa Ummayah** (102022400161) | Pengiriman undangan khusus program/event Pemda kepada UMKM/Mitra & konfirmasi kehadiran. |
| 27 | **F27** | Manajemen Peserta | **Syifa Ummayah** (102022400161) | Pengelolaan daftar peserta terpadu (umum & undangan VIP) serta pemantauan kuota kegiatan. |
| 28 | **F28** | Fasilitasi & Mediasi | **Sepdaffa Raja Dwi Putra Basman** (102022400191) | Pengajuan permintaan fasilitasi/mediasi kendala kerja sama & pencatatan tindak lanjut Pemda. |
| 29 | **F29** | Monitoring Kemitraan Pemda | **Sepdaffa Raja Dwi Putra Basman** (102022400191) | Pemantauan daftar kemitraan, kesepakatan, progres, dan hasil kolaborasi oleh Pemda. |
| 30 | **F30** | Evaluasi Hasil Kolaborasi | **Rayula Samina** (102022400102) | Pencatatan hasil/realisasi target kolaborasi oleh UMKM/Mitra & rekap evaluasi oleh Pemda. |
| 31 | **F31** | Administrasi Superadmin | **Nabil Fikry Khaidar** (102022430014) | Akses modul administratif penuh & pelaksanaan tindakan administratif lintas role terlacak. |
| 32 | **F32** | Audit Sistem (Audit Trail) | **Nabil Fikry Khaidar** (102022430014) | Pencatatan otomatis aktivitas penting sistem & penelusuran riwayat *audit trail*. |

---

## 🏗️ 4. Arsitektur & Tech Stack

Aplikasi dirancang menggunakan pola **Modern Monolith** yang menyatukan performa backend tangguh dengan responsivitas Single Page Application (SPA):

```text
┌─────────────────────────────────────────────────────────────┐
│                    Frontend Client (SPA)                    │
│   Vue 3 + Inertia.js Client + Tailwind v3 + scoped CSS       │
│          Reuse komponen Vue; evaluasi shadcn-vue              │
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

* **Backend**: Laravel 13 (Composer PHP ^8.3; CI PHP 8.4), Eloquent ORM, Laravel Breeze.
* **Frontend**: Inertia.js v2, Vue 3 (`<script setup>`), Tailwind CSS v3 dan scoped CSS. `styled-components` terpasang tetapi belum terintegrasi ke Vue.
* **UI Foundation**: Komponen Vue yang tersedia; evaluasi shadcn-vue untuk kebutuhan baru. Setup toolkit tersebut belum terpasang.
* **Database**: MySQL 8.0 (target aplikasi/CI), SQLite in-memory (pengujian lokal terisolasi).
* **Manajemen & Kolaborasi**: Git, GitHub, Jira (Agile/Scrum 4 Sprint), Figma, Visual Paradigm.

---

## ⚡ 5. Panduan Instalasi Lokal (Setup Guide)

### Prasyarat Sistem
* PHP sesuai Composer (`^8.3`; CI memakai `8.4`) dengan ekstensi yang diminta lockfile; periksa `composer check-platform-reqs`.
* Composer `>= 2.x`
* Node.js `^20.19.0` atau `>=22.12.0` & npm sesuai engines Vite terkunci
* MySQL Server `>= 8.0` (atau Docker)

### Langkah Instalasi:

1. **Clone Repositori**:
   ```bash
   git clone https://github.com/TUBES-PPL-KELOMPOK-F-SI4809/koneksa-platform.git
   cd koneksa-platform
   ```

2. **Instal Dependensi Backend & Frontend**:
   ```bash
   composer install
   npm ci
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

Mulai dari [AGENTS.md](AGENTS.md). Pilih pemeriksaan menurut dampak pada [panduan testing](docs/guidelines/AGENT-UNIT-TESTING.md); dokumentasi saja tidak memerlukan test aplikasi.

```bash
# Pemeriksaan seluruh Unit + Feature untuk perubahan backend/skema
php artisan test

# Menjalankan pengujian fitur spesifik
php artisan test --compact tests/Feature/ProfileTest.php

# Menjalankan browser testing via Playwright
bash .agents/skills/playwright/scripts/playwright_cli.sh --help
```

---

## 📚 7. Direktori Dokumentasi Proyek (`docs/`)

Untuk mempermudah koordinasi seluruh anggota tim pengembang (Dev 1 s/d Dev 7) dan memastikan standardisasi pengerjaan, seluruh dokumen acuan teknis disimpan secara terpusat pada direktori [`docs/`](docs/):

| Direktori / Dokumen | Fungsi & Relevansi bagi Tim Pengembang | Tautan Dokumen |
| :--- | :--- | :--- |
| **`docs/PRD/`** | **Kebutuhan Fitur & Bisnis**: Berisi spesifikasi detail 32 fitur utama (F01–F32), 64 functional requirements, 15 business rules, karakteristik aktor, batasan sistem, dan template penulisan PRD per sprint. | • [PRD Baseline KONEKSA](docs/PRD/PRD-KONEKSA-PLATFORM.md)<br>• [Template PRD Fitur](docs/PRD/PRD-TEMPLATE.md) |
| **`docs/analysis/`** | **Rekayasa Sistem & Basis Data**: Berisi acuan skema relasional ERD (MySQL 8), aturan transaksi atomik penjualan & kuota, spesifikasi use case UML, dan standar response REST API. | • [Skema Database & ERD](docs/analysis/DATABASE-SCHEMA.md)<br>• [Panduan System Analyst](docs/analysis/SYSTEM-ANALYST-GUIDELINES.md)<br>• [Kontrak REST API](docs/analysis/API-SPECIFICATION-GUIDE.md) |
| **`docs/design-system/`** | **UI/UX & Styling Antarmuka**: Acuan wajib bagi pengerjaan frontend Vue 3, reuse komponen Vue, evaluasi shadcn-vue, status `styled-components`, serta spesifikasi token warna, tipografi, dan elevasi. | • [Baseline Desain Website](docs/design-system/BASELINE-DESIGN.md)<br>• [Token Desain Visual](docs/design-system/DESIGN-TOKENS.md)<br>• [Panduan styled-components](docs/design-system/STYLED-COMPONENTS.md) |
| **`docs/guidelines/`** | **Standar Koding & Kualitas (SOP)**: SOP langkah demi langkah membuat file/folder fitur baru, aturan Clean Code & isolasi modul anti-regresi, panduan unit testing wajib (AAA pattern), dan konvensi commit Git. | • [SOP Pembuatan Fitur & Folder](docs/guidelines/FEATURE-DEVELOPMENT-GUIDE.md)<br>• [Panduan Clean Code](docs/guidelines/CLEAN-CODE.md)<br>• [Panduan Unit Testing](docs/guidelines/AGENT-UNIT-TESTING.md)<br>• [Standar Git Workflow](docs/guidelines/GIT-WORKFLOW.md) |
| **`docs/tech-stack/`** | **Tata Kelola Dependensi**: Inventaris dependensi aktif dan status integrasi (Laravel, Inertia, Vue, Tailwind) dan prosedur pengajuan RFC jika anggota tim ingin menambah library/package baru. | • [Inventaris Tech Stack](docs/tech-stack/CURRENT-STACK.md)<br>• [Prosedur RFC Stack Baru](docs/tech-stack/STACK-ADDITION-GUIDELINES.md) |
| **`AGENTS.md`** | **Pedoman AI Coding Assistant**: Aturan wajib jika anggota tim menggunakan asisten AI (Antigravity, Cursor, Copilot, Claude) saat coding agar kode yang di-generate mematuhi arsitektur tim. | • [Instruksi Operasional AI Agent](AGENTS.md) |

---

## 👨‍💻 8. Anggota Tim Pengembang (Kelompok F — SI-48-09)

| Nama Pengembang | NIM | Peran Utama | Modul / Fitur yang Dikelola (FR) |
| :--- | :---: | :---: | :--- |
| **Nabil Fikry Khaidar** | `102022430014` | Dev 1 | F01–F03 (Autentikasi, Profil Akun, Admin Pengguna), F31–F32 (Superadmin & Audit Trail) |
| **Muhammad Izaz Naufal** | `102022400306` | Dev 2 | F04–F06 (Profil UMKM, Katalog Produk, Manajemen Stok), F23 (Kebutuhan Kolaborasi) |
| **Muhammad Amin Hazami** | `102022400110` | Dev 3 | F07–F09 (Transaksi Penjualan, Dashboard UMKM, Laporan Penjualan), F22 (Publikasi Performa) |
| **Dewinda Salsabila Putri Haidir** | `102022430028` | Dev 4 | F10–F12 (Direktori UMKM, Peluang Usaha, Proposal), F24 (Kesepakatan Kemitraan) |
| **Syifa Ummayah** | `102022400161` | Dev 5 | F13–F15 (Program Pemerintah, Pendaftaran, Seleksi), F26–F27 (Undangan VIP & Manajemen Peserta) |
| **Rayula Samina** | `102022400102` | Dev 6 | F16–F18 (Profil Mitra, Monitoring Kemitraan, Solusi Digital), F25 (Progres), F30 (Hasil Kolaborasi) |
| **Sepdaffa Raja Dwi Putra Basman** | `102022400191` | Dev 7 | F19–F21 (Dashboard Pemda, Rekap & Ekspor, Verifikasi UMKM), F28–F29 (Mediasi & Monitoring Kemitraan) |

---

<p align="center">
  Dibuat dengan ❤️ untuk kemajuan ekosistem UMKM dan Inovasi Digital Daerah Kota Bandung.<br>
  © 2026 Kelompok F — S1 Sistem Informasi, Telkom University.
</p>
