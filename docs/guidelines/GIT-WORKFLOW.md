# Git workflow dan serah terima

Ikuti [AGENTS.md](../../AGENTS.md), terutama batas otorisasi dan verifikasi.

## Branch dan commit

- main: rilis stabil; dev: integrasi tim.
- Pertahankan branch tugas yang sudah ditentukan pengguna.
- Untuk branch baru yang dibuat agent, default codex/<deskripsi> kecuali pengguna
  menentukan nama lain. Tim manusia dapat memakai feat/, fix/ atau docs/.
- Periksa status/diff sebelum mulai dan sebelum commit. Jangan menimpa perubahan
  pengguna atau menyertakannya tanpa hubungan/otorisasi.
- Conventional Commits: feat, fix, docs, style, refactor, test, chore;
  contoh docs(agents): align the canonical agent workflow.
- Jangan commit/push/merge tanpa permintaan/otorisasi yang mencakupnya.
  Permintaan push mengizinkan commit perubahan terkait dan push normal branch tugas,
  bukan force-push, merge, atau deploy.

## Sebelum publikasi

1. Review diff dan git diff --check.
2. Jalankan pemeriksaan sesuai matriks AGENTS.md. Perilaku backend/skema memerlukan
   seluruh php artisan test; perubahan frontend memerlukan build/browser sesuai dampak.
   Dokumentasi saja cukup pemeriksaan tautan/path/perintah dan konsistensi.
3. PHP berubah: jalankan vendor/bin/pint --dirty --format agent.
4. Perbarui kontrak/dokumen yang terdampak; jangan membuat dokumen status terpisah tanpa kebutuhan.
5. Periksa remote dan divergence. Jika push ditolak karena perubahan baru, fetch dan
   integrasikan dengan menjaga pekerjaan pengguna; jangan force-push.
6. Jika PR dibuat, tulis masalah/perilaku akhir, FR terkait, validasi aktual,
   dan keterbatasan. Jika platform menyediakan tool attachment PR, lampirkan PR tersebut.

## CI

.github/workflows/ci.yml berjalan pada push/PR ke main dan dev:
Pint --test, npm ci + build, dan backend tests dengan environment MySQL.
CI saat ini menjalankan semua job termasuk untuk dokumentasi; verifikasi lokal
berbasis dampak tidak mengubah konfigurasi tersebut.

Status CI harus diperiksa sebelum mengklaim CI lulus. Push sukses bukan bukti CI hijau.
Sebelum merge, seluruh checks wajib lulus; branch protection aktual perlu diperiksa
di GitHub dan tidak dibuktikan oleh dokumen ini.

## Handoff

Laporkan branch/commit/tautan, perubahan, perintah yang benar-benar dijalankan,
hasil dan pemeriksaan terblokir. Jika belum selesai, sertakan sisa pekerjaan,
keputusan terbuka dan lokasi kode/test agar agent berikut dapat melanjutkan.
