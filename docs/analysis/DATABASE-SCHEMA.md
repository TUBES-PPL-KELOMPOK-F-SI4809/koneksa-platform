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

## 3. Kontrak Restock (`FR-06.1`, `KSA-20`)

- `GET /umkm/inventory` (`umkm.inventory.index`) menampilkan stok varian aktif dan riwayat perubahan stok milik akun UMKM yang sedang login. Daftar stok dan riwayat dipaginasi terpisah melalui `stock_page` dan `history_page`; riwayat produk/varian yang diarsipkan tetap tersedia.
- `POST /umkm/inventory/{variant}/restocks` (`umkm.inventory.restocks.store`) menerima `quantity` (bilangan bulat positif) dan `notes` (opsional, maksimal 2.000 karakter). Role UMKM aktif dan kepemilikan produk diperiksa di server; produk/varian yang diarsipkan tidak menerima restock.
- `RestockService` mengunci produk lalu varian dengan `lockForUpdate()` di dalam `DB::transaction`. Penambahan saldo dan pembuatan ledger `type = restock` dilakukan atomik; `performed_by`, `product_variant_id`, dan `quantity_delta` ditentukan server. Kegagalan penyimpanan membatalkan seluruh perubahan.
- Saldo akhir dibatasi maksimal `2.147.483.647` sesuai kolom `INTEGER` bertanda pada migrasi aktif. Tidak ada perubahan skema untuk implementasi ini. Verifikasi konkurensi/penguncian tetap memerlukan database test MySQL; SQLite tidak membuktikan perilaku penguncian baris.
