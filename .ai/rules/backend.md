# Backend Rules (Laravel 13 & PHP 8.4+)

1. **Feature Development Guide**:
   - Ikuti hierarki `app/Http/Controllers/{Feature}/`, `app/Http/Requests/{Feature}/`, dan `app/Services/{Feature}/{Feature}Service.php`.
   - Thin Controller: Controller hanya memanggil Form Request dan Service Layer, lalu merender Inertia view.
2. **Anti Over-Engineering (KISS & YAGNI)**:
   - Pilih solusi paling sederhana dan langsung.
   - Dilarang membuat Repository Pattern generik; panggil Eloquent Model langsung di dalam Service Layer.
   - Dilarang membuat Interface atau Abstract Class jika hanya ada satu implementasi konkret saat ini.
   - Dilarang membuat layer indirection (seperti Action/Processor/Pipeline berantai) untuk logika yang sederhana.
3. **Cek Dependensi & Pustaka Bawaan Terlebih Dahulu**:
   - Cek `composer.json` sebelum membuat helper atau menginstal paket baru.
   - Gunakan Laravel Collections (`collect()`), `Str`, `Arr`, `Carbon` daripada menulis manual loop atau parsing tanggal.
4. **Database Migrations**:
   - Dilarang mengedit file migrasi lama.
   - Buat migrasi baru jika ada penyesuaian kolom (`php artisan make:migration ...`).
   - Kolom baru wajib `nullable()` atau memiliki `default()`.
5. **Best Practices**:
   - Aktifkan `.antigravity/skills/laravel-best-practices/SKILL.md`.
   - Gunakan Eloquent ORM, hindari N+1 query (`with(...)`).
   - Otorisasi menggunakan Laravel Gates / Policies, jangan hardcode role check di controller.
6. **Code Style**:
   - Jalankan `vendor/bin/pint --dirty --format agent` sebelum selesai.

