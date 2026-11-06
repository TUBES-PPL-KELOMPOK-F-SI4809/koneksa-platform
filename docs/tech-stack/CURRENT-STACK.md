# 💻 Current Tech Stack — Koneksa Platform

Dokumen ini memetakan seluruh ekosistem teknologi, pustaka (*libraries*), perkakas (*tooling*), dan keahlian agen (*skills*) yang aktif digunakan dalam proyek **Koneksa Platform**.

---

## 1. Ringkasan Arsitektur

Koneksa Platform dibangun menggunakan arsitektur **Modern Monolith** yang memanfaatkan **Laravel + Inertia.js**. Pola ini memberikan pengalaman pengguna Single Page Application (SPA) yang cepat dan reaktif tanpa kerumitan mengelola REST API terpisah secara berlebih untuk alur internal.

```text
┌─────────────────────────────────────────────────────────────┐
│                    Frontend Client (SPA)                    │
│   Vue 3 + Inertia.js Client + Tailwind CSS + styled-components│
└──────────────────────────────▲──────────────────────────────┘
                               │ JSON Protocol via Inertia
┌──────────────────────────────▼──────────────────────────────┐
│                    Backend Server (Laravel)                 │
│         Laravel 13 + Inertia Server Adapter + Eloquent      │
└──────────────────────────────▲──────────────────────────────┘
                               │ PDO
┌──────────────────────────────▼──────────────────────────────┐
│                  Basis Data & Infrastruktur                 │
│                 MySQL 8.0 / SQLite (Testing)                │
└─────────────────────────────────────────────────────────────┘
```

---

## 2. Komponen Teknis

### 2.1 Backend (Server-Side)
* **Framework**: Laravel 13.x
* **Runtime**: PHP 8.5+
* **Routing & Controllers**: Laravel Routing dengan Inertia Response Helper (`Inertia::render()`).
* **Autentikasi**: Laravel Breeze (Inertia Stack).
* **ORM & Database**: Eloquent ORM dengan driver MySQL (Production) dan SQLite in-memory (Unit Testing).
* **Package Manager**: Composer 2.x

### 2.2 Frontend (Client-Side)
* **Build Tool**: Vite 8.x + `laravel-vite-plugin`
* **UI Framework**: Vue 3 (Composition API / `<script setup>`)
* **SPA Adapter**: `@inertiajs/vue3` (v2.0+)
* **Styling & CSS**:
  * **Tailwind CSS**: Utility classes untuk struktur layout, grid, flexbox, dan kontainer.
  * **styled-components**: CSS-in-JS untuk enkapsulasi styling dinamis, varian berbasis props, dan token visual terisolasi.
* **Package Manager**: npm / Node.js

### 2.3 Lingkungan Pengujian (Testing Environment)
* **Unit & Feature Testing (Backend)**: PHPUnit 11 / Pest runner (`php artisan test`).
* **End-to-End / Browser Testing**: Playwright melalui CLI dan wrapper skill `.agents/skills/playwright`.

---

## 3. Ekosistem Keahlian Agen (Installed Skills)

Dalam repositori ini, telah disiapkan seperangkat *agent skills* pada direktori `.agents/skills/`:

| Skill | Sumber | Fungsi Utama dalam Proyek |
| :--- | :--- | :--- |
| `ui-ux-pro-max` | Local | Panduan UI/UX komprehensif (warna, tipografi, pola interaksi, aksesibilitas WCAG). |
| `taste-design` | Stitch Skills | Pembuatan DESIGN.md anti-generic, kalibrasi visual, ritme layout premium. |
| `shadcn-ui` | Stitch Skills | Pola integrasi dan adaptasi komponen UI modular modern. |
| `design-md` | Stitch Skills | Sintesis sistem desain semantik ke dalam berkas `DESIGN.md`. |
| `site-md` | Stitch Skills | Analisis spesifikasi proyek ke dalam konstitusi `SITE.md`. |
| `playwright` | OpenAI Skills | Otomatisasi browser nyata untuk testing flows, form input, dan visual snapshot. |
| `stitch-*` | Stitch Skills | Ekosistem penghubung kode dengan desain Stitch (generasi layar, ekstraksi HTML, sinkronisasi). |
| `enhance-prompt` | Stitch Skills | Optimasi deskripsi UI menjadi prompt desain beresolusi tinggi. |
| `remotion` | Stitch Skills | Pembuatan video demonstrasi alur antarmuka berbasis kode. |
| `react-vite-dashboard`| Stitch Skills | Referensi konversi dashboard berbasis Vite dan token desain. |

---

## 4. Skrip Eksekusi Proyek

```bash
# Menjalankan server backend
php artisan serve

# Menjalankan frontend Vite dev server (HMR)
npm run dev

# Menjalankan build aset produksi
npm run build

# Menjalankan pengujian unit backend
php artisan test --testsuite=Unit

# Menjalankan browser testing via playwright skill
node .agents/skills/playwright/scripts/playwright-cli.js --help
```

