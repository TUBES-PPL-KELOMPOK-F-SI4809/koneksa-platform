# Clean Code dan batas domain

Aturan utama: [AGENTS.md](../../AGENTS.md). Panduan ini memperinci keputusan arsitektur.

## Pembagian tanggung jawab

- Controller: HTTP, koordinasi request, response/redirect.
- Form Request: validasi payload; Policy/Gate: hak akses.
- Model: relasi, casts, scopes dan representasi data.
- Service domain: operasi bisnis multi-langkah, perhitungan yang perlu diuji mandiri,
  transaksi dan invariant lintas model. CRUD sederhana boleh langsung menggunakan
  Eloquent dalam controller mengikuti pola yang ada.
- Hindari service yang hanya membungkus satu pemanggilan tanpa aturan bisnis.
  Jangan menambah Action, Repository, Manager atau interface untuk operasi yang sama.
- Layanan domain boleh dipanggil sinkron dari domain lain lewat method yang jelas.
  Event/listener untuk efek samping terpisah; queue bila benar-benar perlu proses latar.
  Operasi yang harus atomik tetap di satu batas transaksi.
- Perubahan relasi/cast/policy pada model bersama diperbolehkan bila diperlukan;
  periksa callers dan uji perilaku yang terdampak. Isolasi bukan larangan menyentuh kode bersama.

## Konvensi

Ikuti berkas sekitar dan aturan domain di .ai/rules/. Gunakan penamaan deskriptif,
type hints, guard clauses, PHPDoc array shape bila perlu, dan enum case TitleCase
(contoh Draft, Published, Closed). Nilai enum harus mengikuti ERD; jangan mengubah nilai DB
hanya untuk mengikuti gaya penamaan case.

Vue menggunakan script setup; pisahkan halaman yang sulit dibaca menjadi partial
berdasarkan tanggung jawab. Gunakan komponen bersama yang sudah ada sebelum membuat baru.
Lihat [baseline UI](../design-system/BASELINE-DESIGN.md).

## Integritas dan verifikasi

Migrasi lama tidak diedit. Tambahkan migrasi kompatibel, pertahankan data historis,
dan uji constraint/invariant yang berubah. Review diff untuk efek samping terhadap fitur lain.
Ikuti [matriks pengujian](AGENT-UNIT-TESTING.md); suite Unit saja bukan bukti bebas regresi.
