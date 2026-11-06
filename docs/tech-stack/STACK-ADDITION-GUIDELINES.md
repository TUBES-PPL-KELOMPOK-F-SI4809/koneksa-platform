# Perubahan dependensi dan stack

Ikuti [AGENTS.md](../../AGENTS.md). Instalasi/penghapusan/penggantian dependensi
memerlukan otorisasi pengguna yang mencakup perubahan tersebut.
Tidak perlu meminta ulang bila sudah diotorisasi secara eksplisit.

## Evaluasi sebelum instalasi

1. Periksa kode/helper, dependensi terpasang dan kemampuan native framework.
2. Jelaskan kebutuhan nyata, alternatif, kompatibilitas Laravel/Vue/PHP/Node,
   lisensi, pemeliharaan, keamanan dan dampak bundle yang relevan.
3. Catat keputusan pada tugas/PR. Perubahan stack signifikan memakai RFC ringkas
   pada tugas/PR atau dokumen tech-stack yang diminta; jangan membuat dokumen tambahan
   untuk setiap utilitas kecil.
4. Jika belum diotorisasi, siapkan usulan konkret lalu minta persetujuan sebelum
   menjalankan installer. CLI komponen UI dapat mengubah package.json dan termasuk aturan ini.
5. Setelah disetujui: gunakan Composer/npm CLI, periksa manifest dan lockfile,
   jalankan audit yang relevan dan pengujian integrasi yang bermakna.
6. Perbarui [CURRENT-STACK.md](CURRENT-STACK.md) untuk integrasi yang berubah.
   Bedakan terpasang, aktif, dan belum terintegrasi.

## Catatan keputusan

Gunakan format secukupnya pada tugas/PR:
- Kebutuhan dan FR terkait.
- Paket/versi target serta alternatif yang sudah dievaluasi.
- Dampak runtime, lisensi, keamanan, bundle dan alasan pilihan.
- Otorisasi yang sudah diberikan atau keputusan yang masih diperlukan.
- Pengujian dan hasil aktual; laporkan audit yang tidak dapat dijalankan.

Target runtime dan versi aktif mengikuti manifests/lockfiles serta CI, bukan angka
pada template. Jangan memakai @latest tanpa memeriksa kompatibilitas konfigurasi.
Jangan menganggap paket React cocok dengan Vue karena sama-sama memakai Tailwind.
