# 📑 PRD: KONEKSA — Platform Kolaborasi UMKM dan Inovasi Industri Digital Daerah

* **Proyek**: KONEKSA (Koneksi dan Akselerasi Usaha)
* **Mata Kuliah**: Tugas Besar Pengembangan Perangkat Lunak (PPL) — SI-48-09 Kelompok F
* **Studi Kasus**: Kota Bandung
* **Target SDGs**: SDG 8 (Pekerjaan Layak & Pertumbuhan Ekonomi), SDG 9 (Industri, Inovasi, & Infrastruktur), SDG 17 (Kemitraan untuk Mencapai Tujuan)
* **Status**: `Approved Baseline (42 Functional Requirements / 21 Fitur)`

---

## 1. Latar Belakang & Usulan Solusi

Usaha Mikro, Kecil, dan Menengah (UMKM) memegang peranan krusial bagi perekonomian Kota Bandung, namun masih terhambat oleh:
* Pengelolaan transaksi, stok, dan pencatatan keuangan manual.
* Keterbatasan akses terhadap mitra industri untuk perluasan pemasaran dan rantai pasok.
* Minimnya sarana terpadu untuk mengakses program pembinaan serta fasilitas pemantauan dari pemerintah daerah.

**KONEKSA** hadir sebagai platform web terintegrasi yang menghubungkan UMKM, mitra industri, dan pemerintah daerah untuk memperluas kolaborasi, mendigitalkan operasional, dan mempercepat pertumbuhan ekosistem bisnis daerah.

---

## 2. Karakteristik Pengguna (4 Aktor)

1. **UMKM (Pemilik Usaha)**:
   - Mengelola profil usaha, katalog produk, stok barang, transaksi penjualan atomik, rekap laporan, mengajukan proposal, dan mendaftar program pembinaan daerah.
2. **Mitra Industri (Korporasi / Organisasi)**:
   - Mengelola profil kemitraan, menelusuri direktori UMKM, mengajukan proposal kerja sama, memantau kemitraan berjalan, serta mempublikasikan solusi teknologi/inovasi digital.
3. **Pemerintah Daerah (Petugas Dinas Kota Bandung)**:
   - Mengelola program pembinaan dan kuota pendaftar, menyeleksi peserta program, melakukan verifikasi administratif profil UMKM, serta memantau ringkasan statistik dan rekapitulasi daerah.
4. **Admin Platform (Administrator Teknis)**:
   - Mengelola status akun pengguna (aktivasi/blokir), membuat akun khusus petugas dinas pemerintah, dan mengawasi kesehatan sistem secara menyeluruh.

---

## 3. Matriks 21 Fitur Utama (F01 – F21) & 42 Functional Requirements

| ID Fitur | Modul | Kebutuhan Fungsional (FR) | Penanggung Jawab |
| :---: | :--- | :--- | :--- |
| **F01** | Autentikasi | **FR-01.1** Registrasi akun UMKM dan Mitra Industri serta login terautentikasi.<br>**FR-01.2** Logout, manajemen sesi, dan proteksi otorisasi berbasis 4 role. | Nabil Fikry Khaidar |
| **F02** | Profil Akun | **FR-02.1** Lihat dan perbarui profil akun dasar (nama, kontak, email).<br>**FR-02.2** Perbarui kata sandi dengan verifikasi kata sandi lama. | Nabil Fikry Khaidar |
| **F03** | Admin Pengguna | **FR-03.1** Kelola daftar akun dan ubah status aktif/nonaktif pengguna.<br>**FR-03.2** Pembuatan akun khusus petugas pemerintah daerah dengan hak akses terproteksi. | Nabil Fikry Khaidar |
| **F04** | Profil UMKM | **FR-04.1** Kelola identitas usaha (nama usaha, deskripsi, alamat, kontak, legalitas).<br>**FR-04.2** Tetapkan kebutuhan usaha dan kelola visibilitas publikasi profil. | Muhammad Izaz Naufal |
| **F05** | Katalog Produk | **FR-05.1** Tambah dan perbarui informasi produk (nama, deskripsi, harga, foto).<br>**FR-05.2** Tampilkan daftar katalog produk dan fasilitas pengarsipan produk nonaktif. | Muhammad Izaz Naufal |
| **F06** | Manajemen Stok | **FR-06.1** Catat restock/penambahan persediaan barang.<br>**FR-06.2** Pemotongan stok otomatis saat penjualan dan peringatan dini saat stok menipis. | Muhammad Izaz Naufal |
| **F07** | Transaksi Penjualan | **FR-07.1** Catat penjualan kasir/toko dengan multi-item dan hitung total otomatis.<br>**FR-07.2** Penyimpanan transaksi atomik (DB Transaction), update stok, & riwayat. | Muhammad Amin Hazami |
| **F08** | Dashboard UMKM | **FR-08.1** Ringkasan grafik omzet, jumlah transaksi, dan tren penjualan.<br>**FR-08.2** Ringkasan sisa stok barang kritis dan navigasi aksi cepat. | Muhammad Amin Hazami |
| **F09** | Laporan Penjualan | **FR-09.1** Rekapitulasi penjualan per rentang tanggal tertentu.<br>**FR-09.2** Ekspor laporan keuangan penjualan ke format CSV. | Muhammad Amin Hazami |
| **F10** | Direktori UMKM | **FR-10.1** Tampilkan direktori publik seluruh UMKM terverifikasi.<br>**FR-10.2** Pencarian instan dan filter berdasarkan nama, kategori usaha, dan wilayah. | Dewinda Salsabila Putri Haidir |
| **F11** | Peluang Usaha | **FR-11.1** Halaman detail publik UMKM beserta daftar kebutuhan kerja sama.<br>**FR-11.2** Tampilkan badge status verifikasi resmi dan batas akses formulir proposal. | Dewinda Salsabila Putri Haidir |
| **F12** | Proposal Kerja Sama | **FR-12.1** Mitra industri mengirim proposal kerja sama terperinci.<br>**FR-12.2** UMKM meninjau proposal masuk serta memberikan keputusan terima/tolak. | Dewinda Salsabila Putri Haidir |
| **F13** | Program Pemerintah | **FR-13.1** Petugas pemerintah membuat dan memperbarui program pembinaan/pelatihan.<br>**FR-13.2** Publikasikan program, tetapkan kuota peserta, dan tutup pendaftaran. | Syifa Ummayah |
| **F14** | Pendaftaran Program | **FR-14.1** Tampilkan detail persyaratan, jadwal, dan benefit program pembinaan.<br>**FR-14.2** Formulir pendaftaran program bagi UMKM dan pelacakan status seleksi. | Syifa Ummayah |
| **F15** | Seleksi Peserta | **FR-15.1** Daftar pendaftar program untuk direview oleh petugas dinas.<br>**FR-15.2** Keputusan terima/tolak pendaftar dengan penegakan batas kuota ketat. | Syifa Ummayah |
| **F16** | Profil Mitra | **FR-16.1** Kelola profil identitas korporasi/organisasi mitra industri.<br>**FR-16.2** Tetapkan preferensi bidang kolaborasi (distribusi, teknologi, pemasaran). | Rayula Samina |
| **F17** | Monitoring Kemitraan | **FR-17.1** Riwayat pengajuan proposal dan status kemitraan aktif.<br>**FR-17.2** Pembaruan tahapan kemitraan (sedang berjalan / selesai) secara transparan. | Rayula Samina |
| **F18** | Solusi Digital | **FR-18.1** Mitra industri mempublikasikan penawaran solusi teknologi digital.<br>**FR-18.2** UMKM menelusuri katalog solusi dan mengirimkan minat adopsi teknologi. | Rayula Samina |
| **F19** | Dashboard Pemda | **FR-19.1** Ringkasan statistik jumlah UMKM, status verifikasi, dan penyebaran sektor.<br>**FR-19.2** Metrik aktivitas program pembinaan, peserta lolos, dan proposal kemitraan. | Sepdaffa Raja Dwi Putra Basman |
| **F20** | Rekap Daerah & Ekspor | **FR-20.1** Tabel analitik rekapitulasi data UMKM per kategori dan status legalitas.<br>**FR-20.2** Ekspor data rekapitulasi daerah ke format CSV dengan otorisasi ketat. | Sepdaffa Raja Dwi Putra Basman |
| **F21** | Verifikasi UMKM | **FR-21.1** UMKM mengajukan permohonan verifikasi administratif profil usaha.<br>**FR-21.2** Petugas dinas meninjau berkas, memberikan catatan, dan menyetujui status. | Sepdaffa Raja Dwi Putra Basman |

---

## 4. Kebutuhan Non-Fungsional (NFR)
1. **NFR-01 (Keamanan Akses)**: Otorisasi berbasis role di sisi server (*Server-side enforcement*), hashing password via Bcrypt/Argon2, dan proteksi CSRF.
2. **NFR-02 (Integritas Data)**: Transaksi penjualan dan update stok berjalan atomik (`DB::transaction`), pencegahan pendaftaran ganda pada program, dan kuota tidak boleh terlampaui saat *race condition*.
3. **NFR-03 (Privasi Data)**: Pemisahan data privat (transaksi penjualan, dokumen internal) dari profil direktori publik; ekspor data hanya untuk pihak berwenang.
4. **NFR-04 (Responsivitas UI)**: Antarmuka adaptif untuk perangkat mobile (`>= 375px`) hingga desktop widescreen.
5. **NFR-05 (Keandalan)**: Penanganan error terstruktur tanpa membocorkan stack trace sensitif pada lingkungan produksi.

