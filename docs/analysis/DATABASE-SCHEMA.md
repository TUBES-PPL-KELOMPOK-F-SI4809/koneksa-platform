# Acuan Skema Database Koneksa

Sumber rancangan tunggal adalah [RANCANGAN_ERD_KONEKSA.md](RANCANGAN_ERD_KONEKSA.md) yang mencakup **32 Kelompok Fitur (FR-01–FR-32 / 64 FR)** sesuai Proposal PPL terbaru (revisi 13 November 2026). Dokumen ini merupakan pengarah teknis dan tidak mendefinisikan ERD atau status alternatif.

## 1. Prinsip Pemeriksaan Skema & Migrasi

1. Baca bagian ERD sesuai FR, lalu periksa migrasi aktual di `database/migrations/`.
2. Rancangan ERD menyatakan target kanonis; berkas migrasi menyatakan struktur yang sudah dikodekan saat ini.
3. Selisih antara kebutuhan fitur (`FR-22`–`FR-32`) dan migrasi baseline (`FR-01`–`FR-21`) wajib diselesaikan melalui **migrasi baru yang bersifat aditif dan backward-compatible**. **Jangan pernah mengedit migrasi lama yang sudah ada.**

## 2. Konvensi Penamaan Tabel & Kolom Kanonis

- **Role Pengguna (`users.role`)**: Menggunakan nilai `'umkm'`, `'mitra'`, `'pemda'`, dan `'admin'` (Superadmin). Jangan menggunakan istilah `'partner'` atau `'government'` sebagai nilai kolom `users.role`.
- **Status Akun (`users.account_status`)**: Menggunakan `'enabled'` dan `'disabled'` (default `'enabled'`), dilengkapi `deleted_at` (*soft delete*), `must_change_password`, dan `current_session_id`.
- **Tabel Domain Baseline (Sudah Dimigrasikan di `database/migrations/`)**:
  - Profil & Referensi: `business_categories`, `cooperation_types`, `businesses`, `partner_profiles`, `partner_cooperation_types`.
  - Operasional UMKM: `products`, `product_variants`, `sales`, `sale_items`, `stock_movements`.
  - Kolaborasi & Solusi Digital: `digital_offerings`, `collaboration_proposals`.
  - Program & Verifikasi Pemda: `programs`, `program_applications`, `verification_requests`.
- **Tabel & Kolom Perluasan Proposal 13 Nov (`FR-22`–`FR-32` via Migrasi Baru)**:
  - Audit & Superadmin (`FR-31`–`FR-32`): `audit_logs` (*append-only*, mencatat `actor_id`, `actor_role`, `module`, `action`, `auditable_type`, `auditable_id`, `description`, `metadata`, `ip_address`, `created_at`).
  - Progres & Mediasi Kemitraan (`FR-25`, `FR-28`): `collaboration_updates` dan `mediation_requests`, serta kolom kesepakatan/hasil pada `collaboration_proposals` (`FR-24`, `FR-30`).
  - Publikasi Performa UMKM (`FR-22`): kolom `public_performance_config` pada `businesses`.
  - Undangan VIP & Partisipasi Mitra pada Program (`FR-14`, `FR-26`, `FR-27`): kolom `target_participant` pada `programs` serta `partner_profile_id`, `entry_type`, dan `invited_by` pada `program_applications`.
- **Nama Non-Kanonis yang Tidak Dipakai**: Nama konseptual awal seperti `umkm_profiles`, `transactions`, `transaction_items`, `collaboration_requests`, `collaborations` (terpisah dari proposal), `collaboration_results` (terpisah), `business_needs` (tabel terpisah), atau `program_invitations` (tabel terpisah) telah dikonsolidasikan ke dalam struktur kanonis di atas agar bebas duplikasi dan tetap sederhana.

Untuk tata cara pembuatan migrasi dan model baru, ikuti [panduan pengembangan fitur](../guidelines/FEATURE-DEVELOPMENT-GUIDE.md).

## Profil Usaha (`FR-04.1`, `KSA-15`)

- `GET /business-profile` (`business-profile.edit`) menampilkan profil milik akun UMKM yang sedang login, daftar kategori, dan `identityLocked`. Nilai ini mengikuti pemeriksaan backend yang sudah ada: identitas terkunci jika profil tersebut pernah memiliki pengajuan verifikasi berstatus `approved`.
- `PUT /business-profile` (`business-profile.update`) membuat atau memperbarui satu profil milik akun melalui `BusinessProfileService`, dengan validasi server dan penguncian dalam transaksi. Data identitas mencakup nama, kategori, deskripsi, alamat, kecamatan, kelurahan, serta telepon usaha.
- UI mengunci nama, kategori, dan alamat sesuai `identityLocked`; pemeriksaan server tetap berlaku. Kebutuhan dan publikasi mengikuti kontrak FR-04.2 di bawah.

## Kebutuhan dan Publikasi Profil (`FR-04.2`, `KSA-16`)

- Form profil yang sama menerima `business_needs` (opsional, teks maksimal 10.000 karakter; kosong menjadi `null`) dan `is_published` (boolean). Jika `is_published` tidak dikirim, status publikasi lama dipertahankan. Profil baru berstatus draft secara default.
- `is_published` hanya merupakan input form: `true` mengisi `businesses.published_at` dengan waktu server ketika pertama dipublikasikan, `false` mengosongkannya. Penyimpanan ulang profil publik mempertahankan waktu publikasi. Klien tidak boleh menentukan `user_id`, ID usaha, atau `published_at` melalui form ini.
- Kolom `business_needs` dan `published_at` sudah ada pada migrasi baseline. Permintaan kolom `is_published` pada subtiket KSA-41 mengacu pada konsep publikasi; tidak dibuat kolom boolean kedua atau tabel kebutuhan terpisah. **FR-23 / KSA-84 wajib memakai `businesses.business_needs` dan status `published_at` yang sama** untuk pencarian/filter peluang; kebutuhan tetap satu sumber data.
- UI menampilkan status tersimpan, ringkasan kebutuhan, daftar informasi yang dibagikan, serta dialog konfirmasi sebelum perubahan publik/draft disimpan. Konfirmasi ikut menyimpan seluruh isian form. Kebutuhan/publikasi tetap dapat diubah setelah identitas terverifikasi; perubahan identitas terkunci menggagalkan seluruh penyimpanan.
- Endpoint baca yang sudah ada, `GET /umkm/business/{business}`, mengembalikan model lengkap hanya kepada pemilik. Pembaca UMKM lain hanya menerima `id`, `business_category_id`, `name`, `description`, `address`, `district`, `subdistrict`, `contact_phone`, `logo_path`, dan `business_needs`. Draft dan profil pemilik nonaktif/terhapus ditolak oleh policy. Akses monitoring Pemda tetap melalui endpoint berwenangnya.
- Publikasi profil tidak membuka data akun, transaksi, keuangan, proposal, atau izin performa. Izin indikator performa berada pada FR-22 / KSA-80. Direktori/detail publik guest (FR-10/FR-11) dan filter kebutuhan (FR-23) belum diimplementasikan oleh tiket ini; integrasinya harus mengikuti status publikasi, daftar field aman, serta syarat pemilik aktif dan belum terhapus.
- Tidak ada migrasi atau dependensi baru. Test lokal memakai SQLite terisolasi; hasilnya tidak membuktikan konkurensi row-lock MySQL.

## Katalog dan Arsip Produk (`FR-05.1`, `FR-05.2`, `KSA-19`)

- `GET /umkm/products` (`umkm.products.index`) menampilkan produk milik akun UMKM, dengan pencarian nama (`search`, maksimal 150 karakter), filter `status=active|archived|all` (default `active`), dan paginasi 12 produk. Ringkasan jumlah selalu mencakup katalog milik sendiri, terlepas dari filter pencarian.
- `POST /umkm/products` dan `PUT /umkm/products/{product}` (`umkm.products.store/update`) menerima `name` (maksimal 150), `description` (opsional, maksimal 10.000), `unit` (maksimal 50), serta 1–50 `variants`. Setiap varian memiliki `variant_key`, `selling_price` (nol atau positif, maksimal 13 digit dan 2 desimal), `low_stock_threshold` (integer 0–2.147.483.647), dan opsional `sku`, `size`, `color`. Gunakan `default` untuk produk tanpa varian ukuran/warna.
- `business_id` ditentukan dari akun login; harga mengikuti kolom kanonis `product_variants.selling_price`. Tidak ada kolom `umkm_id`, `products.price`, atau `is_active` baru, serta tidak ada dependensi/migrasi tambahan. Subtask Jira yang memakai nama tersebut mengacu pada konsep lama; implementasi mengikuti ERD dan migrasi aktif.
- `ProductPolicy` membatasi akses pada UMKM aktif, terverifikasi email, dan tidak wajib mengganti password. Produk akun lain mendapat 404. Akun tanpa profil usaha dapat membaca keadaan kosong, tetapi tidak dapat membuat produk. Branch ini berdiri di atas `dev`; penyelesaian profil usaha tetap pada PR KSA-15.
- `ProductService` menyimpan produk dan varian atomik. Pembaruan mengunci produk lalu varian, mempertahankan seluruh ID varian dan saldo stok. Nama varian tersimpan tidak dapat diganti; varian baru boleh ditambahkan, tetapi varian lama tidak dihapus melalui form. Stok varian baru selalu nol; barang masuk dicatat melalui restock, bukan form katalog.
- `POST /umkm/products/{product}/archive` (`umkm.products.archive`) mengisi `products.archived_at` secara idempoten di bawah penguncian produk. Produk arsip tidak dapat diedit atau direstock. Tidak ada penghapusan produk/varian, mutasi saldo, atau perubahan snapshot `sale_items` dan ledger `stock_movements`; riwayat tetap dapat dibaca.
- Kontrak pemilihan produk untuk transaksi baru disediakan oleh `ProductVariant::availableForSale()` dan policy `sell`, yang menolak produk maupun varian arsip serta kepemilikan lain. Query stok aktif menggunakan scope yang sama. **Endpoint pencatatan penjualan FR-07 belum tersedia pada `dev` saat KSA-19 dikerjakan**; ketika diimplementasikan, writer penjualan wajib memakai policy ini dan memeriksa status produk/varian lagi di dalam transaksi dengan urutan lock produk → varian. Test KSA-19 membuktikan eligibility, penolakan restock, dan preservasi riwayat; bukan pengujian endpoint penjualan yang belum ada.
- UI memakai `UmkmLayout`, `StatCard`, `StatusBadge`, modal dan form Inertia yang sudah tersedia: tiga ringkasan, daftar ringkas dengan rincian lipat, modal tambah/edit, serta konfirmasi arsip. Panduan katalog terdiri atas tiga langkah, dan panduan restock tetap menjadi default halaman Persediaan.
- Pengujian SQLite terisolasi tidak membuktikan konkurensi row-lock MySQL; CI MySQL memeriksa suite aplikasi, sedangkan verifikasi race antar-koneksi memerlukan pengujian konkurensi tersendiri.

## 3. Kontrak Restock (`FR-06.1`, `KSA-20`)

- `GET /umkm/inventory` (`umkm.inventory.index`) menampilkan stok varian aktif dan riwayat perubahan stok milik akun UMKM yang sedang login. Daftar stok dan riwayat dipaginasi terpisah melalui `stock_page` dan `history_page`; riwayat produk/varian yang diarsipkan tetap tersedia.
- `POST /umkm/inventory/{variant}/restocks` (`umkm.inventory.restocks.store`) menerima `quantity` (bilangan bulat positif) dan `notes` (opsional, maksimal 2.000 karakter). Role UMKM aktif dan kepemilikan produk diperiksa di server; produk/varian yang diarsipkan tidak menerima restock.
- `RestockService` mengunci produk lalu varian dengan `lockForUpdate()` di dalam `DB::transaction`. Penambahan saldo dan pembuatan ledger `type = restock` dilakukan atomik; `performed_by`, `product_variant_id`, dan `quantity_delta` ditentukan server. Kegagalan penyimpanan membatalkan seluruh perubahan.
- Saldo akhir dibatasi maksimal `2.147.483.647` sesuai kolom `INTEGER` bertanda pada migrasi aktif. Tidak ada perubahan skema untuk implementasi ini. Verifikasi konkurensi/penguncian tetap memerlukan database test MySQL; SQLite tidak membuktikan perilaku penguncian baris.
