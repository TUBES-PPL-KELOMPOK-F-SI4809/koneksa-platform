# Acuan skema database Koneksa

Sumber rancangan tunggal adalah [RANCANGAN_ERD_KONEKSA.md](RANCANGAN_ERD_KONEKSA.md).
Dokumen ini merupakan pengarah; tidak mendefinisikan ERD atau status alternatif.

Baca bagian ERD sesuai FR, lalu periksa migrasi aktual di database/migrations/.
Rancangan menyatakan target; migrasi menyatakan struktur yang sudah dikodekan.
Inspeksi database target melalui Boost database-schema bila tersedia.
Selisih harus diidentifikasi sebelum menulis query/model; jangan mengganti migrasi lama.

Nama kanonis antara lain sales, sale_items, collaboration_proposals,
product_variants, dan stock_movements. Status akun memakai account_status.
Status verifikasi diturunkan sesuai alur verification_requests pada ERD.
Nama lama transactions, transaction_items, collaboration_requests, dan kolom
verification_status pada baseline sebelumnya tidak menjadi kontrak implementasi.

Untuk perubahan skema, ikuti
[panduan pengembangan fitur](../guidelines/FEATURE-DEVELOPMENT-GUIDE.md).
