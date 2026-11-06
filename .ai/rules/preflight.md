# Mandatory Pre-Flight Protocol (Wajib Baca docs/, .agents/, .antigravity/)

> **CRITICAL PRE-FLIGHT MANDATE**: Dilarang membuat, merancang, atau memodifikasi kode apapun tanpa membaca dokumen acuan di `docs/` serta memeriksa dan mengaktifkan skills di `.agents/skills/` dan `.antigravity/skills/` terlebih dahulu.

---

## 1. Berkas Acuan Wajib di `docs/`

Sebelum menyusun rencana (*planning*) atau menulis kode, Agent **WAJIB** membaca dokumen terkait:

1. **Logika Bisnis & Lingkup Fitur (FR-01 s/d FR-21)**:
   - 📖 `docs/PRD/PRD-KONEKSA-PLATFORM.md`
   - 📖 `docs/analysis/RANCANGAN_ERD_KONEKSA.md` & `docs/analysis/DATABASE-SCHEMA.md`
2. **Arsitektur Kode & Pembuatan File Baru**:
   - 📖 `docs/guidelines/FEATURE-DEVELOPMENT-GUIDE.md` (Pola Controller, Form Request, Service Layer)
   - 📖 `docs/guidelines/CLEAN-CODE.md` (Prinsip anti-regresi dan isolasi fitur)
3. **Desain & Komponen Antarmuka**:
   - 📖 `docs/design-system/BASELINE-DESIGN.md`
   - 📖 `docs/design-system/DESIGN-TOKENS.md`
   - 📖 `docs/design-system/STYLED-COMPONENTS.md`
4. **Standar Pengujian (Testing)**:
   - 📖 `docs/guidelines/AGENT-UNIT-TESTING.md`
5. **Penambahan Dependensi Baru**:
   - 📖 `docs/tech-stack/STACK-ADDITION-GUIDELINES.md`

---

## 2. Aktivasi Skills di `.agents/skills/` dan `.antigravity/skills/`

Agent **WAJIB** membaca `SKILL.md` dan mengaktifkan keahlian berikut sesuai domain tugas:
* **Logika Backend Laravel**: `.antigravity/skills/laravel-best-practices/SKILL.md`
* **Halaman & Komponen Inertia Vue**: `.antigravity/skills/inertia-vue-development/SKILL.md`
* **Styling & CSS Tailwind**: `.antigravity/skills/tailwindcss-development/SKILL.md`
* **Komponen Aksesibel**: `.agents/skills/shadcn-ui/SKILL.md` (Wajib Shadcn-First)
* **Desain UI/UX & Layout**: `.agents/skills/ui-ux-pro-max/SKILL.md` & `.agents/skills/taste-design/SKILL.md`
* **Pengujian Unit & E2E**: `.antigravity/skills/testing-best-practices/SKILL.md` & `.agents/skills/playwright/SKILL.md`

---

## 3. Aturan Integritas Kode (Non-Negotiable)

1. **Zero Regression**: Jangan merusak fitur yang sudah ada. DILARANG MENGUBAH FILE MIGRASI YANG SUDAH DILUNCURKAN.
2. **No Over-Engineering (KISS & YAGNI)**: Gunakan solusi terkecil, paling sederhana, dan paling langsung yang menyelesaikan masalah secara benar. Dilarang membuat abstraksi berlebihan (seperti generic repository pattern di atas Eloquent, interface tanpa multi-driver, atau layer indirection yang tidak perlu) dan dilarang menambahkan kode spekulatif untuk kebutuhan masa depan yang tidak diminta.
3. **Cek Dependensi Terpasang Terlebih Dahulu**: Sebelum membuat helper kustom, algoritma manual, atau fungsi baru, periksa pustaka yang sudah ada di `composer.json` dan `package.json`, serta fitur bawaan Laravel (`Str`, `Arr`, `Carbon`, `Collection`) dan Vue/Inertia (`useForm`). Dilarang membuat ulang roda (*no reinventing the wheel*).
4. **Shadcn-First**: Selalu gunakan komponen `shadcn-ui` sebelum berpikir untuk merancang komponen antarmuka dari nol.
5. **Mandatory Testing**: Setiap perubahan logika wajib disertai test dan dipastikan `php artisan test` berstatus 100% PASS (green) sebelum tugas dianggap selesai.
6. **Code Style**: Jalankan `vendor/bin/pint --dirty --format agent` setelah melakukan modifikasi pada file PHP.
7. **CLI-First**: Selalu gunakan `php artisan make:*`, `npm`, atau `composer` untuk scaffolding file baru.

