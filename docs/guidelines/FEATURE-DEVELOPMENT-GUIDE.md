# Workflow pengembangan fitur

Mulai dari [AGENTS.md](../../AGENTS.md); baca bagian FR dan ERD terkait.
Buat hanya berkas yang dibutuhkan. Pola berikut bukan checklist membuat semua layer.

## 1. Pahami kontrak

Telusuri route → controller/request/policy → model/service → halaman → test.
Nyatakan aktor, izin, input, hasil, validasi dan failure mode yang relevan.
Gunakan acceptance criteria pada PRD/tugas; keputusan bisnis yang belum jelas ditanyakan,
sedangkan detail implementasi rutin diselesaikan mengikuti pola proyek.

## 2. Struktur dan scaffolding

| Tanggung jawab | Lokasi |
|---|---|
| Controller fitur baru | app/Http/Controllers/{Feature}/ |
| Validasi | app/Http/Requests/{Feature}/ |
| Bisnis multi-langkah/transaksi | app/Services/{Feature}/ |
| Model/Policy/Enum | app/Models/, app/Policies/, app/Enums/ |
| Halaman/partial | resources/js/Pages/{Feature}/ dan Partials/ |
| Komponen lintas fitur | resources/js/Components/ |
| HTTP/DB test | tests/Feature/ |
| Logika murni | tests/Unit/ |

Pertahankan lokasi kode lama kecuali tugas membutuhkan pemindahan.
Jangan membuat interface, event, service atau package internal sebagai boilerplate.

Contoh generator (nama disesuaikan fitur, bukan instruksi menjalankan semuanya):

~~~bash
php artisan make:controller Program/ProgramController --no-interaction
php artisan make:request Program/StoreProgramRequest --no-interaction
php artisan make:class Services/Program/ProgramService --no-interaction
php artisan make:test --phpunit ProgramTest --no-interaction
~~~

Gunakan make:model, make:factory, make:seeder, make:policy atau make:migration hanya sesuai
kebutuhan. Periksa --help untuk opsi versi terpasang. Vue/Markdown dibuat langsung.

## 3. Implementasikan kebutuhan

Controller tetap ringkas; service hanya untuk aturan bisnis yang memerlukannya.
Gunakan Form Request dan Policy/Gate. UI memakai komponen Vue tersedia, Inertia,
Tailwind aktif dan token proyek; baca [baseline](../design-system/BASELINE-DESIGN.md).

Untuk database, baca [ERD kanonis](../analysis/RANCANGAN_ERD_KONEKSA.md) dan migrasi:
- Jangan ubah migrasi lama; buat migrasi baru.
- Kolom baru pada tabel aktif nullable atau memiliki default yang sah.
- Jaga foreign key dan riwayat; down() harus membalik perubahan skema secara tepat.
- Transaksi/locking/constraint mengikuti invariant ERD.
- Jangan menjalankan migrasi terhadap data bersama/produksi tanpa otorisasi.

Dependensi baru mengikuti [prosedur stack](../tech-stack/STACK-ADDITION-GUIDELINES.md).

## 4. Verifikasi dan serah terima

Ikuti [panduan testing](AGENT-UNIT-TESTING.md). Catat FR, perilaku yang berubah,
route/halaman/test terkait, perintah dan hasil aktual pada laporan tugas/PR.
Perbarui dokumen kontrak bila terdampak; jangan membuat dokumen status baru per tugas.
Jika pekerjaan terputus, tulis sisa langkah dan keputusan terbuka pada handoff chat/PR.
