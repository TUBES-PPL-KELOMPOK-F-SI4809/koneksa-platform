---
description: "Aturan wajib Clean Code, isolasi modul, dan anti-regresi saat menambahkan fitur atau skema database baru"
globs: ["app/**/*.php", "routes/**/*.php", "database/**/*.php", "resources/js/**/*.vue", "resources/js/**/*.js"]
always_on: true
---

# Aturan Wajib: Clean Code & Arsitektur Anti-Regresi

Saat merancang atau mengimplementasikan fitur baru:

1. **Isolasi Fitur & Anti-Efek Samping**:
   - Dilarang menambahkan logika fitur baru ke controller atau model milik fitur lain secara langsung.
   - Buat file terpisah mengikuti [FEATURE-DEVELOPMENT-GUIDE.md](../../docs/guidelines/FEATURE-DEVELOPMENT-GUIDE.md).
   - Controller wajib ramping (*Thin Controller*): delegasikan validasi ke Form Request dan logika bisnis ke Service Class (`app/Services/{Feature}/`).

2. **Integritas Basis Data**:
   - Dilarang mengedit berkas migrasi yang sudah ada.
   - Setiap perubahan atau penambahan tabel/kolom wajib melalui berkas migrasi baru.
   - Kolom baru pada tabel eksisting wajib bersifat `nullable()` atau memiliki `default(...)` agar tidak merusak fungsionalitas fitur yang sudah berjalan.

3. **Frontend Component Cleanliness**:
   - Letakkan halaman fitur di `resources/js/Pages/{Feature}/`.
   - Pecah komponen antarmuka yang kompleks ke dalam `resources/js/Pages/{Feature}/Partials/`.
   - Cek ketersediaan komponen di `shadcn-ui` sebelum membuat sendiri.
   - Gunakan token desain terpusat di `docs/design-system/DESIGN-TOKENS.md`.

4. **Verifikasi Bebas Regresi**:
   - Jalankan `php artisan test --testsuite=Unit` untuk memastikan fitur baru tidak mematahkan unit test yang sudah ada.

