# Koneksa Platform — Claude Code Instructions

> **CRITICAL PRE-FLIGHT MANDATE**: Sebelum membuat, merancang, atau memodifikasi kode apapun, Anda WAJIB:
> 1. Membaca aturan kerja lengkap di `AGENTS.md`.
> 2. Membaca dokumentasi domain terkait di folder `docs/`:
>    - `docs/PRD/PRD-KONEKSA-PLATFORM.md` (21 fitur & aturan bisnis)
>    - `docs/analysis/RANCANGAN_ERD_KONEKSA.md` & `docs/analysis/DATABASE-SCHEMA.md` (skema basis data)
>    - `docs/guidelines/FEATURE-DEVELOPMENT-GUIDE.md` (SOP hierarki kode & layer)
>    - `docs/guidelines/CLEAN-CODE.md` (prinsip anti-regresi)
>    - `docs/design-system/BASELINE-DESIGN.md` & `docs/design-system/DESIGN-TOKENS.md` (sistem desain)
> 3. Memeriksa dan mengaktifkan keahlian yang relevan di `.agents/skills/` dan `.antigravity/skills/`.

## Ringkasan Aturan Wajib
- **Zero Regression**: Jangan merusak fitur yang sudah ada. Dilarang mengubah file migrasi lama.
- **No Over-Engineering (KISS & YAGNI)**: Gunakan solusi paling ringkas dan langsung. Hindari over-abstraction (seperti generic repository pattern di atas Eloquent, interface tanpa kebutuhan polymorphism, atau layer indirection yang tidak perlu). Jangan menambahkan fitur/arsitektur spekulatif untuk masa depan.
- **Cek Dependensi Terpasang Terlebih Dahulu**: Periksa `composer.json` dan `package.json` sebelum membuat helper atau fungsi kustom. Manfaatkan fitur bawaan Laravel (`Str`, `Arr`, `Carbon`, `Collection`) dan ekosistem Vue/Inertia (`useForm`).
- **Mandatory Unit Testing**: Semua penambahan kode backend/frontend wajib disertai test dan lulus `php artisan test` (100% PASS).
- **Code Style**: Jalankan `vendor/bin/pint --dirty --format agent` sebelum submit.
- **CLI-First**: Gunakan `php artisan make:*` untuk scaffolding file baru.
- **Shadcn-First**: Periksa katalog `shadcn-ui` (`.agents/skills/shadcn-ui/SKILL.md`) sebelum membuat komponen UI baru dari nol.

