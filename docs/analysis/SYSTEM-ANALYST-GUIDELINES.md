# 📐 Panduan Analisis Sistem (System Analyst Guidelines)

Dokumen ini memuat standar metodologi, pemodelan diagram UML 2.x, dan format spesifikasi analisis perangkat lunak bagi System Analyst pada proyek **KONEKSA Platform** sesuai Proposal PPL terbaru (**32 Kelompok Fitur / 64 Functional Requirements**).

---

## 1. Standar Pemodelan Berorientasi Objek (UML Standards)

Seluruh pemodelan sistem menggunakan Visual Paradigm dengan notasi UML standar:

1. **Use Case Diagram**: Memetakan interaksi **4 aktor utama terautentikasi** (`UMKM`, `Mitra Industri`, `Pemerintah Daerah`, `Superadmin / Admin`) dan **1 aktor pendukung** (`Pengunjung Publik`) terhadap **32 kelompok fitur (F01–F32 / 64 FR)**.
2. **Activity Diagram**: Menggambarkan alur kerja prosedural bisnis lintas role (misal: Registrasi & Onboarding, Transaksi Penjualan Atomik, Siklus Proposal–Kesepakatan–Progres–Hasil Kemitraan, Pendaftaran & Undangan VIP Program, Verifikasi UMKM, Mediasi, serta Audit Trail).
3. **Sequence Diagram**: Memvisualisasikan pertukaran pesan antar-layer (`Browser Vue (Inertia) -> Route + Middleware (CheckRole) -> Controller -> Form Request / Policy -> Service (DB Transaction & AuditLogger) -> Database MySQL`).
4. **Entity-Relationship Diagram (ERD)** / **Class Diagram**: Menjelaskan entitas data relasional dan model domain (lihat [RANCANGAN_ERD_KONEKSA.md](RANCANGAN_ERD_KONEKSA.md) dan [DATABASE-SCHEMA.md](DATABASE-SCHEMA.md)).

---

## 2. Template Spesifikasi Use Case (Use Case Specification)

```markdown
### UC-XXX: [Nama Use Case]

* **Aktor Utama**: [UMKM (`umkm`) / Mitra Industri (`mitra`) / Pemerintah Daerah (`pemda`) / Superadmin (`admin`) / Publik (`guest`)]
* **Referensi FR & Business Rule**: [FR-XX.1, FR-XX.2 | BR-XX]
* **Deskripsi Singkat**: [Tujuan dan fungsi use case]
* **Pre-Kondisi**: Aktor telah terautentikasi (`account_status = 'enabled'`), sesi valid, dan memenuhi Policy/Gate sesuai role serta kepemilikan resource.
* **Post-Kondisi**: Perubahan tersimpan secara atomik di basis data; aksi krusial tercatat pada `audit_logs` bila termasuk cakupan `BR-15`.

#### Alur Utama (Main Success Scenario):
1. Aktor membuka halaman [Fitur].
2. Sistem memverifikasi autentikasi, role, dan kepemilikan resource, lalu merender halaman Inertia Vue beserta props yang diizinkan.
3. Aktor mengisi formulir dan menekan tombol simpan/kirim.
4. Sistem memvalidasi payload request melalui Form Request dan hak akses melalui Policy/Gate.
5. Sistem menjalankan logika bisnis (menggunakan Service dan `DB::transaction` + `lockForUpdate()` untuk mutasi stok/kuota, serta `AuditLogger` untuk aksi administratif/keputusan penting).
6. Sistem mengembalikan redirect Inertia ke named route beserta flash message status.

#### Alur Alternatif (Alternative Flow):
* **4a. Validasi Form Gagal**:
  1. Sistem membatalkan mutasi dan mengembalikan error bag melalui redirect Inertia (atau HTTP 422 JSON hanya untuk endpoint REST eksplisit).
  2. Antarmuka menampilkan pesan galat inline pada field terkait.
* **4b. Otorisasi atau Kepemilikan Ditolak (`BR-01`)**:
  1. Sistem menolak request dengan HTTP 403 Forbidden (atau 404 sesuai kebijakan privasi data).
* **5a. Batas Kuota Terlampaui (`BR-10`) / Stok Tidak Cukup (`BR-02`)**:
  1. Sistem melakukan rollback transaksi database.
  2. Sistem menampilkan pesan kesalahan bisnis bahwa kuota kegiatan atau stok produk tidak mencukupi.
```

---

## 3. Matriks Keterlacakan (Traceability Matrix F01–F32)

Setiap fitur pada tugas/PR dipetakan ke FR, issue Jira, modul, tabel utama, dan penanggung jawab sesuai Proposal 13 November 2026. Gunakan format keterlacakan detail pada [template PRD](../PRD/PRD-TEMPLATE.md) saat menyerahkan implementasi:

| Kode Fitur | FR | Jira Story | Modul / Use Case Utama | Tabel Utama | Penanggung Jawab |
| :---: | :---: | :---: | :--- | :--- | :--- |
| **F01** | `FR-01.1`, `FR-01.2` | `KSA-10`, `KSA-11`, `KSA-12` | Registrasi UMKM/Mitra, Login, Logout, Sesi & RBAC | `users`, `sessions`, `password_reset_tokens` | Nabil Fikry Khaidar |
| **F02** | `FR-02.1`, `FR-02.2` | `KSA-13` | Profil Akun Dasar & Ganti Password | `users` | Nabil Fikry Khaidar |
| **F03** | `FR-03.1`, `FR-03.2` | `KSA-38` | Manajemen Status Pengguna & Pembuatan Akun Pemda | `users`, `sessions`, `audit_logs` | Nabil Fikry Khaidar |
| **F04** | `FR-04.1`, `FR-04.2` | `KSA-15`, `KSA-16` | Identitas Profil UMKM, Kebutuhan Usaha & Publikasi | `businesses`, `business_categories` | Muhammad Izaz Naufal |
| **F05** | `FR-05.1`, `FR-05.2` | `KSA-19` | Katalog Produk, Varian & Pengarsipan Produk | `products`, `product_variants` | Muhammad Izaz Naufal |
| **F06** | `FR-06.1`, `FR-06.2` | `KSA-20`, `KSA-21` | Pencatatan Restock, Potong Stok Otomatis & Peringatan Stok Rendah | `product_variants`, `stock_movements` | Muhammad Izaz Naufal |
| **F07** | `FR-07.1`, `FR-07.2` | `KSA-23`, `KSA-24` | Transaksi Penjualan Kasir Atomik & Riwayat Penjualan | `sales`, `sale_items`, `stock_movements` | Muhammad Amin Hazami |
| **F08** | `FR-08.1`, `FR-08.2` | `KSA-25` | Dashboard Omzet, Jumlah Transaksi & Ringkasan Persediaan UMKM | `sales`, `sale_items`, `product_variants` | Muhammad Amin Hazami |
| **F09** | `FR-09.1`, `FR-09.2` | `KSA-26` | Rekap Penjualan per Periode & Ekspor CSV | `sales`, `sale_items` | Muhammad Amin Hazami |
| **F10** | `FR-10.1`, `FR-10.2` | `KSA-156` | Direktori Publik UMKM & Pencarian/Filter Kategori | `businesses`, `business_categories` | Dewinda Salsabila Putri Haidir |
| **F11** | `FR-11.1`, `FR-11.2` | `KSA-68` | Detail Profil Publik UMKM, Badge Verifikasi & Akses Proposal | `businesses`, `verification_requests` | Dewinda Salsabila Putri Haidir |
| **F12** | `FR-12.1`, `FR-12.2` | `KSA-88` | Pengajuan & Keputusan (Terima/Tolak) Proposal Kolaborasi | `collaboration_proposals`, `cooperation_types` | Dewinda Salsabila Putri Haidir |
| **F13** | `FR-13.1`, `FR-13.2` | `KSA-112` | Pembuatan, Publikasi & Pengaturan Kuota Program/Event Pemda | `programs` | Syifa Ummayah |
| **F14** | `FR-14.1`, `FR-14.2` | `KSA-116` | Eksplorasi Kegiatan & Pendaftaran Program (UMKM/Mitra) | `programs`, `program_applications` | Syifa Ummayah |
| **F15** | `FR-15.1`, `FR-15.2` | `KSA-120` | Daftar Pendaftar & Seleksi Peserta Berbasis Kuota | `program_applications`, `programs`, `audit_logs` | Syifa Ummayah |
| **F16** | `FR-16.1`, `FR-16.2` | `KSA-72` | Profil Organisasi Mitra Industri & Jenis Dukungan Kerja Sama | `partner_profiles`, `cooperation_types`, `partner_cooperation_types` | Rayula Samina |
| **F17** | `FR-17.1`, `FR-17.2` | `KSA-92` | Riwayat Proposal & Pengelolaan Status Kemitraan (`ongoing`/`completed`) | `collaboration_proposals`, `audit_logs` | Rayula Samina |
| **F18** | `FR-18.1`, `FR-18.2` | `KSA-76` | Publikasi Solusi Digital Mitra & Pengajuan Minat Kolaborasi UMKM | `digital_offerings`, `collaboration_proposals` | Rayula Samina |
| **F19** | `FR-19.1`, `FR-19.2` | `KSA-132` | Dashboard Monitoring Ekosistem UMKM, Verifikasi, Program & Kemitraan | `businesses`, `verification_requests`, `programs`, `collaboration_proposals` | Sepdaffa Raja Dwi Putra Basman |
| **F20** | `FR-20.1`, `FR-20.2` | `KSA-136` | Rekapitulasi UMKM Daerah per Kategori/Status & Ekspor CSV | `businesses`, `business_categories`, `verification_requests` | Sepdaffa Raja Dwi Putra Basman |
| **F21** | `FR-21.1`, `FR-21.2` | `KSA-140` | Pengajuan & Keputusan Verifikasi Administratif UMKM | `verification_requests`, `businesses`, `audit_logs` | Sepdaffa Raja Dwi Putra Basman |
| **F22** | `FR-22.1`, `FR-22.2` | `KSA-80` | Pengaturan Izin & Penampilan Indikator Performa Bisnis Publik UMKM | `businesses`, `sales` | Muhammad Amin Hazami |
| **F23** | `FR-23.1`, `FR-23.2` | `KSA-84` | Publikasi Kebutuhan Kolaborasi UMKM & Filter Peluang bagi Mitra | `businesses`, `cooperation_types` | Muhammad Izaz Naufal |
| **F24** | `FR-24.1`, `FR-24.2` | `KSA-96` | Pencatatan & Peninjauan Ringkasan Kesepakatan, Periode & Target | `collaboration_proposals` | Dewinda Salsabila Putri Haidir |
| **F25** | `FR-25.1`, `FR-25.2` | `KSA-100` | Pencatatan & Riwayat Perkembangan Progres Kemitraan | `collaboration_updates`, `collaboration_proposals` | Rayula Samina |
| **F26** | `FR-26.1`, `FR-26.2` | `KSA-124` | Pengiriman Undangan Khusus (VIP) Program & Konfirmasi Kehadiran | `program_applications`, `programs` | Syifa Ummayah |
| **F27** | `FR-27.1`, `FR-27.2` | `KSA-128` | Manajemen Terpadu Peserta (Umum & VIP) serta Monitoring Kuota | `program_applications`, `programs` | Syifa Ummayah |
| **F28** | `FR-28.1`, `FR-28.2` | `KSA-104` | Pengajuan & Tindak Lanjut Fasilitasi/Mediasi Kendala Kemitraan | `mediation_requests`, `collaboration_proposals` | Sepdaffa Raja Dwi Putra Basman |
| **F29** | `FR-29.1`, `FR-29.2` | `KSA-144` | Monitoring Daftar, Kesepakatan, Progres & Hasil Kemitraan oleh Pemda | `collaboration_proposals`, `collaboration_updates` | Sepdaffa Raja Dwi Putra Basman |
| **F30** | `FR-30.1`, `FR-30.2` | `KSA-108` | Pencatatan Hasil Kolaborasi & Rekap Evaluasi Pemberdayaan | `collaboration_proposals` | Rayula Samina |
| **F31** | `FR-31.1`, `FR-31.2` | `KSA-148` | Akses Modul Administratif Superadmin & Tindakan Lintas Role Terlacak | `users`, `audit_logs` (+ modul terkait) | Nabil Fikry Khaidar |
| **F32** | `FR-32.1`, `FR-32.2` | `KSA-152` | Pencatatan Otomatis Aktivitas Penting & Penelusuran Audit Trail | `audit_logs` | Nabil Fikry Khaidar |
