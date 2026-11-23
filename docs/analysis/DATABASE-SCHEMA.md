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
- `PUT /business-profile` (`business-profile.update`) membuat atau memperbarui satu profil milik akun melalui `BusinessProfileService`, dengan validasi server dan penguncian dalam transaksi. Form hanya mengirim nama, kategori, deskripsi, alamat, kecamatan, kelurahan, serta telepon usaha.
- UI mengunci nama, kategori, dan alamat sesuai `identityLocked`; pemeriksaan server tetap berlaku. Pengaturan publikasi/kebutuhan usaha berada pada FR terpisah. Tidak ada perubahan skema untuk pembaruan UI ini.

## Katalog dan Arsip Produk (`FR-05.1`, `FR-05.2`, `KSA-19`)

- `GET /umkm/products` (`umkm.products.index`) menampilkan produk milik akun UMKM, dengan pencarian nama (`search`, maksimal 150 karakter), filter `status=active|archived|all` (default `active`), dan paginasi 12 produk. Ringkasan jumlah selalu mencakup katalog milik sendiri, terlepas dari filter pencarian.
- `POST /umkm/products` dan `PUT /umkm/products/{product}` (`umkm.products.store/update`) menerima `name` (maksimal 150), `description` (opsional, maksimal 10.000), `unit` (maksimal 50), serta 1–50 `variants`. Setiap varian memiliki `variant_key`, `selling_price` (nol atau positif, maksimal 13 digit dan 2 desimal), `low_stock_threshold` (integer 0–2.147.483.647), dan opsional `sku`, `size`, `color`. Gunakan `default` untuk produk tanpa varian ukuran/warna.
- `business_id` ditentukan dari akun login; harga mengikuti kolom kanonis `product_variants.selling_price`. Tidak ada kolom `umkm_id`, `products.price`, atau `is_active` baru, serta tidak ada dependensi/migrasi tambahan. Subtask Jira yang memakai nama tersebut mengacu pada konsep lama; implementasi mengikuti ERD dan migrasi aktif.
- `ProductPolicy` membatasi akses pada UMKM aktif, terverifikasi email, dan tidak wajib mengganti password. Produk akun lain mendapat 404. Akun tanpa profil usaha dapat membaca keadaan kosong, tetapi tidak dapat membuat produk. Branch ini berdiri di atas `dev`; penyelesaian profil usaha tetap pada PR KSA-15.
- `ProductService` menyimpan produk dan varian atomik. Pembaruan mengunci produk lalu varian, mempertahankan seluruh ID varian dan saldo stok. Nama varian tersimpan tidak dapat diganti; varian baru boleh ditambahkan, tetapi varian lama tidak dihapus melalui form. Stok varian baru selalu nol; barang masuk dicatat melalui restock, bukan form katalog.
- `POST /umkm/products/{product}/archive` (`umkm.products.archive`) mengisi `products.archived_at` secara idempoten di bawah penguncian produk. Produk arsip tidak dapat diedit atau direstock. Tidak ada penghapusan produk/varian, mutasi saldo, atau perubahan snapshot `sale_items` dan ledger `stock_movements`; riwayat tetap dapat dibaca.
- Kontrak pemilihan produk untuk transaksi baru disediakan oleh `ProductVariant::availableForSale()` dan policy `sell`, yang menolak produk maupun varian arsip serta kepemilikan lain. Query stok aktif menggunakan scope yang sama. Endpoint penjualan minimal KSA-21 di bawah memakai policy ini dan memeriksa status produk/varian lagi di dalam transaksi dengan urutan lock produk → varian. Layar kasir dan riwayat penjualan tetap pada FR-07.
- UI memakai `UmkmLayout`, `StatCard`, `StatusBadge`, modal dan form Inertia yang sudah tersedia: tiga ringkasan, daftar ringkas dengan rincian lipat, modal tambah/edit, serta konfirmasi arsip. Panduan katalog terdiri atas tiga langkah, dan panduan restock tetap menjadi default halaman Persediaan.
- Pengujian SQLite terisolasi tidak membuktikan konkurensi row-lock MySQL; CI MySQL memeriksa suite aplikasi, sedangkan verifikasi race antar-koneksi memerlukan pengujian konkurensi tersendiri.

## 3. Kontrak Restock (`FR-06.1`, `KSA-20`)

- `GET /umkm/inventory` (`umkm.inventory.index`) menampilkan stok varian aktif dan riwayat perubahan stok milik akun UMKM yang sedang login. Daftar stok dan riwayat dipaginasi terpisah melalui `stock_page` dan `history_page`; riwayat produk/varian yang diarsipkan tetap tersedia.
- `POST /umkm/inventory/{variant}/restocks` (`umkm.inventory.restocks.store`) menerima `quantity` (bilangan bulat positif) dan `notes` (opsional, maksimal 2.000 karakter). Role UMKM aktif dan kepemilikan produk diperiksa di server; produk/varian yang diarsipkan tidak menerima restock.
- `RestockService` mengunci produk lalu varian dengan `lockForUpdate()` di dalam `DB::transaction`. Penambahan saldo dan pembuatan ledger `type = restock` dilakukan atomik; `performed_by`, `product_variant_id`, dan `quantity_delta` ditentukan server. Kegagalan penyimpanan membatalkan seluruh perubahan.
- Saldo akhir dibatasi maksimal `2.147.483.647` sesuai kolom `INTEGER` bertanda pada migrasi aktif. Tidak ada perubahan skema untuk implementasi ini. Verifikasi konkurensi/penguncian tetap memerlukan database test MySQL; SQLite tidak membuktikan perilaku penguncian baris.

## Stok Otomatis dan Peringatan (`FR-06.2`, `KSA-21`)

- Sumber stok dan ambang tetap `product_variants.stock_quantity` dan `low_stock_threshold`, bukan kolom stok pada `products`. `stock_status` adalah atribut turunan (`out` jika nol, `low` jika positif dan `<=` ambang, `healthy` jika di atas ambang, `archived` untuk varian arsip), tanpa kolom baru. Ambang nol tetap memperingatkan stok habis. Scope `lowStock()` hanya mencakup produk/varian aktif dengan stok `<=` ambang.
- Katalog menampilkan badge ringkas pada produk yang memiliki varian aktif rendah/habis dan status per varian pada Rincian. Persediaan menampilkan status dan ambang setiap varian. Ambang dapat diubah melalui Edit produk yang sudah tersedia; restock atau perubahan ambang langsung tercermin pada pembacaan halaman berikutnya.
- `GET /dashboard` bagi UMKM dan `GET /umkm/dashboard` menampilkan ringkasan jumlah varian aktif, varian yang perlu ditambah (termasuk habis), dan varian habis. Daftar peringatan dibatasi lima varian, diurutkan stok terendah lalu ID, dengan tautan ke Persediaan dan Katalog. Seluruh query dibatasi pemilik; arsip dikecualikan. Dashboard role lain tetap menggunakan perilaku sebelumnya. Dashboard UMKM memakai policy kelayakan pengelolaan produk.
- Karena FR-07 belum tersedia pada basis `dev`, endpoint minimal berikut ditambahkan dalam lingkup KSA-21 yang telah disetujui: `POST /umkm/inventory/sales` (`umkm.inventory.sales.store`). Ini menerima JSON/form `items` berisi 1–50 objek `{product_variant_id, quantity}`; setiap varian hanya sekali dan kuantitas integer positif maksimal 2.147.483.647. `notes` opsional, maksimal 2.000 karakter. Autentikasi sesi dan CSRF tetap berlaku; akun wajib UMKM aktif, email terverifikasi, tidak wajib mengganti password, dan memiliki profil usaha.
- `SaleStockService::record()` merupakan satu pintu pencatatan atomik untuk endpoint ini dan integrasi FR-07 selanjutnya. Service mengunci seluruh produk menurut ID dahulu, lalu seluruh varian menurut ID, mengecek policy `sell` dan stok terbaru, baru menyimpan `sales`, snapshot `sale_items`, saldo, dan ledger `stock_movements.type = sale` dengan delta negatif, ID penjualan, serta pelaksana. Kegagalan mana pun me-rollback seluruh cart; transaksi database mencoba ulang deadlock maksimal tiga kali. Jangan mengurangi stok lagi melalui observer/listener atau writer FR-07 lain.
- Nomor invoice unik dan waktu penjualan ditentukan server. Harga berasal dari varian terkunci; total dihitung dalam satuan sen integer dan dibatasi sesuai `DECIMAL(15,2)` sebelum perkalian untuk menghindari overflow. Payload pemilik/total/status/waktu dari klien tidak digunakan. Harga dalam objek item ditolak. Snapshot nama, varian, satuan, dan harga mempertahankan keadaan saat transaksi.
- Respons sukses: 201 dengan `message` dan `sale` beserta `items`; stok tidak cukup/input invalid: 422; varian hilang: 404; kepemilikan/arsip/kelayakan akun: 403; kegagalan database: 500 dengan pesan umum yang aman. Endpoint ini tidak menyediakan layar kasir, daftar penjualan, pembatalan, atau idempotency key; setiap POST valid merupakan penjualan baru. KSA-23/KSA-24 dapat memakai endpoint/service ini untuk UI dan riwayat tanpa menggandakan mutasi stok.
- Contoh payload: `{"items":[{"product_variant_id":1,"quantity":2}],"notes":"Penjualan toko"}`. Dari browser yang sudah login, `window.axios.post('/umkm/inventory/sales', payload)` menggunakan sesi dan CSRF aplikasi; ID varian diperoleh dari data katalog/persediaan milik sendiri. Muat ulang dashboard/katalog/persediaan setelah respons sukses untuk melihat saldo dan peringatan baru.
- Tidak ada migrasi atau dependensi baru. Test HTTP SQLite mencakup threshold, otorisasi, harga server, stok habis, oversell, rollback ledger, dan batas total. Dua test concurrency memerlukan database MySQL `testing` pada loopback serta `pcntl`; lingkungan CI menyediakan keduanya. Pengujian simultan memeriksa perebutan stok terakhir dan urutan cart terbalik pada koneksi/proses berbeda. Keduanya dilewati pada SQLite/Windows, sehingga hasil lokal tidak mengklaim bukti row-lock MySQL.
