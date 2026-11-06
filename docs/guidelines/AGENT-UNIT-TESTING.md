# Pengujian dan bukti penyelesaian

Kewajiban verifikasi ditetapkan di [AGENTS.md](../../AGENTS.md) bagian 4.
Panduan ini berlaku untuk PHPUnit proyek; Pest belum menjadi runner terpasang.

## Pilih cakupan berdasarkan perilaku

- Feature test: route, autentikasi, otorisasi, kepemilikan data, validasi dan persistensi.
- Unit test: fungsi/perhitungan murni tanpa bootstrap Laravel/DB.
- Tambah/perbarui regression test untuk keputusan bisnis yang berubah, happy path dan
  failure mode bernilai tinggi: input invalid, akses pihak lain, stok/kuota boundary,
  rollback atau submit berulang bila terkait.
- Jangan membuat test yang menyalin logika ke badan test. Assertion harus memanggil
  kode aplikasi dan memeriksa hasil yang dapat diamati.
- Gunakan Arrange–Act–Assert, factory dan fake sesuai konvensi test sekitar.
  Komentar AAA tidak wajib ketika alurnya sudah jelas.
- Jangan menguji ulang framework atau menambah assertion hanya untuk menaikkan jumlah test.

## Backend

~~~bash
# Saat iterasi: file yang terdampak
php artisan test --compact tests/Feature/ProfileTest.php

# Pemeriksaan akhir perubahan perilaku backend/skema: Unit dan Feature
php artisan test

# Format hanya ketika PHP diubah
vendor/bin/pint --dirty --format agent
~~~

Jalankan affected tests sebelum perubahan untuk mengetahui baseline, lalu sesudahnya.
phpunit.xml memakai SQLite :memory: lokal. CI backend memakai environment MySQL.
Gunakan database test terisolasi; jangan migrate:fresh pada database development/produksi.
Untuk perilaku locking, race condition atau constraint khusus MySQL, verifikasi tambahan
pada MySQL test terisolasi diperlukan; SQLite saja tidak membuktikan perilaku tersebut.
Suite Unit saat ini hanya memiliki contoh dasar, sehingga Unit saja tidak memadai.

## Frontend dan browser

package.json menyediakan build/dev, belum test runner komponen.
Jangan menjalankan npm test atau npx vitest sebagai langkah standar yang dianggap tersedia.
Perubahan perilaku Vue memerlukan test server terkait, build dan pemeriksaan browser;
component test ditambahkan bila runner tersedia/penambahannya diotorisasi.

~~~bash
npm run build
command -v npx
bash .agents/skills/playwright/scripts/playwright_cli.sh --help
~~~

Wrapper Playwright memanggil npx dan mungkin mengambil paket. Periksa ketersediaan
network/browser; gunakan skill untuk snapshot, interaksi dan assertion hasil alur.
Uji input/error/loading, keyboard/focus dan viewport yang relevan.
Build sukses hanya membuktikan kompilasi, bukan interaksi UI.

## Dokumentasi, layout dan kegagalan

Dokumentasi/instruksi saja: review diff, cek tautan lokal/path/perintah dan konsistensi.
Layout/copy saja: build dan inspeksi browser; tidak memerlukan test logika baru.

Periksa exit code, jumlah test/assertions, serta error/skip. Jangan mengubah ekspektasi
atau melewati test hanya untuk mendapatkan hijau. Pisahkan failure baseline dari regresi.
Jika prasyarat tidak tersedia, laporkan pemeriksaan yang terblokir dan langkah lanjut.
Tidak boleh mengklaim seluruh pemeriksaan lulus jika belum dijalankan.
