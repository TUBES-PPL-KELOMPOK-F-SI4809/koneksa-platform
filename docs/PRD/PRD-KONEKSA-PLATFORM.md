# 📑 PRD: KONEKSA — Platform Kolaborasi UMKM dan Inovasi Industri Digital Daerah

* **Proyek**: KONEKSA (Koneksi dan Akselerasi Usaha)
* **Mata Kuliah**: Tugas Besar Pengembangan Perangkat Lunak (PPL) — SI-48-09 Kelompok F
* **Studi Kasus**: Kota Bandung
* **Target SDGs**: SDG 8 (Pekerjaan Layak & Pertumbuhan Ekonomi), SDG 9 (Industri, Inovasi, & Infrastruktur), SDG 17 (Kemitraan untuk Mencapai Tujuan)
* **Status**: `Approved Revision — Proposal 13 November 2026 (32 Kelompok Fitur / 64 Functional Requirements)`

---

## 1. Latar Belakang & Pendekatan Solusi

Usaha Mikro, Kecil, dan Menengah (UMKM) di Kota Bandung memegang peranan penting dalam pertumbuhan ekonomi daerah, namun masih menghadapi kendala:
1. **Pengelolaan Operasional Manual**: Pencatatan produk, persediaan barang, transaksi penjualan, dan laporan usaha masih terpisah atau manual.
2. **Keterbatasan Akses Kemitraan**: UMKM kesulitan memperkenalkan kapasitas usahanya, sementara Mitra Industri memerlukan informasi profil, kebutuhan pengembangan, dan indikator performa usaha yang relevan untuk menemukan calon mitra.
3. **Koordinasi Program & Monitoring Pemerintah Daerah**: Penyelenggaraan program pemberdayaan, pendaftaran, seleksi berkuota, undangan khusus/VIP, verifikasi administratif, serta fasilitasi/mediasi kendala kemitraan membutuhkan sistem terintegrasi.

**KONEKSA** menerapkan pendekatan **Matching & Monitoring Kemitraan**: platform tidak hanya mempertemukan UMKM dan Mitra Industri, tetapi juga mencatat kesepakatan, progres pelaksanaan, hasil kolaborasi, fasilitasi pemerintah, serta *audit trail* aktivitas penting sistem. Sistem tidak memproses pembayaran langsung, transfer dana investasi, maupun pembagian keuntungan otomatis di dalam platform.

---

## 2. Karakteristik Pengguna (4 Aktor Utama & 1 Aktor Pendukung)

| Kategori Pengguna | Role DB (`users.role`) | Karakteristik | Hak Akses Utama |
| :--- | :---: | :--- | :--- |
| **UMKM** | `umkm` | Pelaku usaha yang mengelola operasional bisnis dan mencari peluang pengembangan (1 akun = 1 profil usaha). | Registrasi, profil usaha, produk, stok, transaksi penjualan, laporan CSV, kontrol publikasi performa, kebutuhan kolaborasi, pengajuan/respons proposal, kesepakatan/progres/hasil kemitraan, pendaftaran program, respons undangan VIP, pengajuan verifikasi, dan permintaan mediasi. |
| **Mitra Industri** | `mitra` | Perusahaan, distributor, investor, atau organisasi penyedia dukungan bisnis/solusi digital (1 akun = 1 profil organisasi). | Registrasi, profil organisasi, pencarian/filter direktori UMKM & performa publik, pengajuan/respons proposal, kesepakatan/progres/hasil kemitraan, publikasi solusi digital, pendaftaran program, respons undangan VIP, dan permintaan mediasi. |
| **Pemerintah Daerah** | `pemda` | Petugas berwenang yang mengelola pemberdayaan dan monitoring ekosistem usaha (akun dibuat oleh Superadmin). | Pengelolaan program/event, seleksi peserta berkuota, pengiriman undangan VIP, manajemen peserta terpadu, verifikasi administratif UMKM, tindak lanjut fasilitasi/mediasi, monitoring kemitraan, dashboard daerah, dan ekspor rekap CSV. |
| **Superadmin** | `admin` | Pengelola teknis sistem dengan kewenangan administratif tertinggi. | Manajemen status akun pengguna (`enabled`/`disabled`), pembuatan akun petugas Pemerintah Daerah, akses modul administratif, tindakan administratif lintas role dengan pencatatan identitas pelaksana, dan penelusuran *audit trail*. |
| **Pengunjung Publik** | *(guest)* | Pengguna yang belum login atau belum memiliki akun. | Melihat landing page, direktori UMKM yang dipublikasikan, detail profil publik UMKM, dan informasi program pemerintah yang dipublikasikan. |

---

## 3. Daftar Kebutuhan Fungsional (32 Fitur / 64 Functional Requirements)

### A. Pengelolaan Akun dan Profil
| Kode Fitur | FR | Role | Deskripsi Kebutuhan Fungsional | Penanggung Jawab | Jira |
| :---: | :---: | :--- | :--- | :--- | :---: |
| **F01** | **FR-01.1** | UMKM, Mitra Industri | Melakukan registrasi dengan mengisi informasi akun dan identitas awal usaha atau organisasi, serta login menggunakan kredensial terdaftar. | Nabil Fikry Khaidar | `KSA-10` |
| | **FR-01.2** | Semua role | Melakukan logout dan mengakses sistem melalui sesi yang dibatasi berdasarkan role serta kepemilikan data. | Nabil Fikry Khaidar | `KSA-11`, `KSA-12` |
| **F02** | **FR-02.1** | Semua role | Melihat dan memperbarui informasi dasar profil akun milik sendiri. | Nabil Fikry Khaidar | `KSA-13` |
| | **FR-02.2** | Semua role | Mengubah password setelah memverifikasi password lama. | Nabil Fikry Khaidar | `KSA-13` |
| **F03** | **FR-03.1** | Superadmin | Melihat daftar pengguna serta mengaktifkan atau menonaktifkan akun. | Nabil Fikry Khaidar | `KSA-38` |
| | **FR-03.2** | Superadmin | Membuat akun petugas Pemerintah Daerah dan menetapkan hak aksesnya. | Nabil Fikry Khaidar | `KSA-38` |
| **F04** | **FR-04.1** | UMKM | Membuat dan memperbarui profil usaha, termasuk identitas, kategori, lokasi, dan deskripsi bisnis. | Muhammad Izaz Naufal | `KSA-15` |
| | **FR-04.2** | UMKM | Mengelola kebutuhan pengembangan usaha dan menentukan informasi profil yang dipublikasikan. | Muhammad Izaz Naufal | `KSA-16` |

### B. Operasional UMKM
| Kode Fitur | FR | Role | Deskripsi Kebutuhan Fungsional | Penanggung Jawab | Jira |
| :---: | :---: | :--- | :--- | :--- | :---: |
| **F05** | **FR-05.1** | UMKM | Menambahkan dan memperbarui informasi produk, termasuk nama, harga, dan deskripsi. | Muhammad Izaz Naufal | `KSA-19` |
| | **FR-05.2** | UMKM | Melihat daftar produk dan mengarsipkan produk yang tidak lagi aktif. | Muhammad Izaz Naufal | `KSA-19` |
| **F06** | **FR-06.1** | UMKM | Mencatat penambahan persediaan atau restock produk. | Muhammad Izaz Naufal | `KSA-20` |
| | **FR-06.2** | UMKM | Mengurangi stok secara otomatis setelah transaksi dan menampilkan peringatan stok rendah. | Muhammad Izaz Naufal | `KSA-21` |
| **F07** | **FR-07.1** | UMKM | Mencatat transaksi penjualan dengan memilih produk, kuantitas, dan menghitung total transaksi. | Muhammad Amin Hazami | `KSA-23` |
| | **FR-07.2** | UMKM | Menyimpan transaksi serta perubahan stok secara atomik dan menampilkan riwayat penjualan. | Muhammad Amin Hazami | `KSA-24` |
| **F08** | **FR-08.1** | UMKM | Melihat ringkasan omzet dan jumlah transaksi melalui dashboard usaha. | Muhammad Amin Hazami | `KSA-25` |
| | **FR-08.2** | UMKM | Melihat ringkasan persediaan dan navigasi cepat menuju fitur operasional. | Muhammad Amin Hazami | `KSA-25` |
| **F09** | **FR-09.1** | UMKM | Melihat rekap penjualan berdasarkan tanggal atau periode tertentu. | Muhammad Amin Hazami | `KSA-26` |
| | **FR-09.2** | UMKM | Mengekspor laporan penjualan dalam format CSV. | Muhammad Amin Hazami | `KSA-26` |

### C. Direktori dan Kolaborasi Bisnis
| Kode Fitur | FR | Role | Deskripsi Kebutuhan Fungsional | Penanggung Jawab | Jira |
| :---: | :---: | :--- | :--- | :--- | :---: |
| **F10** | **FR-10.1** | Publik, Mitra Industri | Melihat daftar UMKM yang profilnya telah dipublikasikan. | Dewinda Salsabila Putri Haidir | `KSA-156` |
| | **FR-10.2** | Publik, Mitra Industri | Mencari dan memfilter UMKM berdasarkan nama dan kategori usaha. | Dewinda Salsabila Putri Haidir | `KSA-156` |
| **F11** | **FR-11.1** | Publik, Mitra Industri | Melihat detail profil publik UMKM, produk, kebutuhan kerja sama, serta informasi performa yang dipublikasikan. | Dewinda Salsabila Putri Haidir | `KSA-68` |
| | **FR-11.2** | Mitra Industri | Melihat status verifikasi UMKM dan mengakses pengajuan proposal kerja sama. | Dewinda Salsabila Putri Haidir | `KSA-68` |
| **F12** | **FR-12.1** | UMKM, Mitra Industri | Mengajukan proposal kolaborasi kepada pihak lain berdasarkan jenis kerja sama dan rincian penawaran. | Dewinda Salsabila Putri Haidir | `KSA-88` |
| | **FR-12.2** | UMKM, Mitra Industri | Melihat proposal masuk serta menerima atau menolak pengajuan kerja sama. | Dewinda Salsabila Putri Haidir | `KSA-88` |
| **F16** | **FR-16.1** | Mitra Industri | Membuat dan memperbarui profil organisasi beserta kategori entitas bisnis. | Rayula Samina | `KSA-72` |
| | **FR-16.2** | Mitra Industri | Menentukan jenis kerja sama dan dukungan bisnis yang ditawarkan kepada UMKM. | Rayula Samina | `KSA-72` |
| **F17** | **FR-17.1** | UMKM, Mitra Industri | Melihat riwayat proposal, ringkasan kesepakatan, dan status kemitraan. | Rayula Samina | `KSA-92` |
| | **FR-17.2** | UMKM, Mitra Industri | Mengelola status kemitraan menjadi berjalan atau selesai berdasarkan persetujuan pihak terkait. | Rayula Samina | `KSA-92` |
| **F18** | **FR-18.1** | Mitra Industri | Mempublikasikan penawaran solusi digital atau layanan pendampingan usaha. | Rayula Samina | `KSA-76` |
| | **FR-18.2** | UMKM | Melihat penawaran Mitra Industri dan mengajukan minat melalui mekanisme proposal kolaborasi. | Rayula Samina | `KSA-76` |

### D. Program dan Event Pemerintah
| Kode Fitur | FR | Role | Deskripsi Kebutuhan Fungsional | Penanggung Jawab | Jira |
| :---: | :---: | :--- | :--- | :--- | :---: |
| **F13** | **FR-13.1** | Pemerintah Daerah | Membuat dan mengubah program atau event beserta kategori, jadwal, persyaratan, sasaran peserta, dan kuota. | Syifa Ummayah | `KSA-112` |
| | **FR-13.2** | Pemerintah Daerah | Mempublikasikan program/event serta membuka atau menutup pendaftaran. | Syifa Ummayah | `KSA-112` |
| **F14** | **FR-14.1** | Publik, UMKM, Mitra Industri | Melihat daftar dan detail kegiatan pemerintah sesuai ketentuan publikasi. | Syifa Ummayah | `KSA-116` |
| | **FR-14.2** | UMKM, Mitra Industri | Mendaftar pada kegiatan yang memenuhi persyaratan serta memantau status pendaftaran. | Syifa Ummayah | `KSA-116` |
| **F15** | **FR-15.1** | Pemerintah Daerah | Melihat daftar pendaftar program/event beserta jenis peserta dan statusnya. | Syifa Ummayah | `KSA-120` |
| | **FR-15.2** | Pemerintah Daerah | Menerima atau menolak peserta berdasarkan persyaratan dan kuota kegiatan. | Syifa Ummayah | `KSA-120` |

### E. Monitoring Pemerintah dan Verifikasi
| Kode Fitur | FR | Role | Deskripsi Kebutuhan Fungsional | Penanggung Jawab | Jira |
| :---: | :---: | :--- | :--- | :--- | :---: |
| **F19** | **FR-19.1** | Pemerintah Daerah | Melihat ringkasan jumlah UMKM, kategori usaha, dan status verifikasi. | Sepdaffa Raja Dwi Putra Basman | `KSA-132` |
| | **FR-19.2** | Pemerintah Daerah | Melihat ringkasan program, peserta, proposal, serta aktivitas kemitraan. | Sepdaffa Raja Dwi Putra Basman | `KSA-132` |
| **F20** | **FR-20.1** | Pemerintah Daerah | Melihat rekap UMKM berdasarkan kategori dan status usaha. | Sepdaffa Raja Dwi Putra Basman | `KSA-136` |
| | **FR-20.2** | Pemerintah Daerah | Mengekspor data rekap daerah dalam format CSV sesuai kewenangan. | Sepdaffa Raja Dwi Putra Basman | `KSA-136` |
| **F21** | **FR-21.1** | UMKM, Pemerintah Daerah | UMKM mengajukan verifikasi administratif, sedangkan pemerintah melihat daftar pengajuan. | Sepdaffa Raja Dwi Putra Basman | `KSA-140` |
| | **FR-21.2** | Pemerintah Daerah | Memberikan keputusan verifikasi beserta catatan atau alasan pemeriksaan. | Sepdaffa Raja Dwi Putra Basman | `KSA-140` |

### F. Publikasi Performa dan Peluang Kemitraan
| Kode Fitur | FR | Role | Deskripsi Kebutuhan Fungsional | Penanggung Jawab | Jira |
| :---: | :---: | :--- | :--- | :--- | :---: |
| **F22** | **FR-22.1** | UMKM | Menentukan indikator performa bisnis yang boleh ditampilkan pada profil publik. | Muhammad Amin Hazami | `KSA-80` |
| | **FR-22.2** | Mitra Industri | Melihat ringkasan performa bisnis UMKM yang telah diizinkan untuk dipublikasikan. | Muhammad Amin Hazami | `KSA-80` |
| **F23** | **FR-23.1** | UMKM | Mempublikasikan kebutuhan kolaborasi, seperti distribusi, pendanaan, produksi, atau perluasan pasar. | Muhammad Izaz Naufal | `KSA-84` |
| | **FR-23.2** | Mitra Industri | Mencari dan memfilter UMKM berdasarkan kebutuhan atau jenis peluang kemitraan. | Muhammad Izaz Naufal | `KSA-84` |

### G. Kesepakatan dan Monitoring Kemitraan
| Kode Fitur | FR | Role | Deskripsi Kebutuhan Fungsional | Penanggung Jawab | Jira |
| :---: | :---: | :--- | :--- | :--- | :---: |
| **F24** | **FR-24.1** | UMKM, Mitra Industri | Mencatat ringkasan kesepakatan, bentuk kerja sama, periode, dan target kolaborasi. | Dewinda Salsabila Putri Haidir | `KSA-96` |
| | **FR-24.2** | UMKM, Mitra Industri | Melihat detail kesepakatan yang tercatat pada kemitraan masing-masing. | Dewinda Salsabila Putri Haidir | `KSA-96` |
| **F25** | **FR-25.1** | UMKM, Mitra Industri | Menambahkan pembaruan aktivitas, catatan perkembangan, dan progres kemitraan. | Rayula Samina | `KSA-100` |
| | **FR-25.2** | UMKM, Mitra Industri, Pemerintah Daerah | Melihat riwayat perkembangan kemitraan sesuai kewenangan akses. | Rayula Samina | `KSA-100` |

### H. Undangan dan Manajemen Peserta
| Kode Fitur | FR | Role | Deskripsi Kebutuhan Fungsional | Penanggung Jawab | Jira |
| :---: | :---: | :--- | :--- | :--- | :---: |
| **F26** | **FR-26.1** | Pemerintah Daerah | Mengirimkan undangan khusus kepada UMKM atau Mitra Industri untuk mengikuti program/event tertentu. | Syifa Ummayah | `KSA-124` |
| | **FR-26.2** | UMKM, Mitra Industri | Melihat undangan serta mengonfirmasi atau menolak kehadiran. | Syifa Ummayah | `KSA-124` |
| **F27** | **FR-27.1** | Pemerintah Daerah | Mengelola daftar peserta dari pendaftaran umum maupun undangan khusus. | Syifa Ummayah | `KSA-128` |
| | **FR-27.2** | Pemerintah Daerah | Melihat jumlah peserta terdaftar, diterima, dan terkonfirmasi berdasarkan kuota kegiatan. | Syifa Ummayah | `KSA-128` |

### I. Fasilitasi dan Evaluasi Kemitraan
| Kode Fitur | FR | Role | Deskripsi Kebutuhan Fungsional | Penanggung Jawab | Jira |
| :---: | :---: | :--- | :--- | :--- | :---: |
| **F28** | **FR-28.1** | UMKM, Mitra Industri | Mengajukan permintaan fasilitasi atau mediasi atas kendala dalam kerja sama. | Sepdaffa Raja Dwi Putra Basman | `KSA-104` |
| | **FR-28.2** | Pemerintah Daerah | Meninjau permintaan, mencatat tindak lanjut, dan memperbarui status penanganan. | Sepdaffa Raja Dwi Putra Basman | `KSA-104` |
| **F29** | **FR-29.1** | Pemerintah Daerah | Melihat daftar kemitraan berdasarkan pihak yang terlibat, jenis, status, dan periode. | Sepdaffa Raja Dwi Putra Basman | `KSA-144` |
| | **FR-29.2** | Pemerintah Daerah | Melihat ringkasan kesepakatan, progres, dan hasil kemitraan sesuai kewenangan. | Sepdaffa Raja Dwi Putra Basman | `KSA-144` |
| **F30** | **FR-30.1** | UMKM, Mitra Industri | Mencatat hasil kolaborasi, realisasi kegiatan, dan pencapaian target kerja sama. | Rayula Samina | `KSA-108` |
| | **FR-30.2** | Pemerintah Daerah | Melihat rekap hasil kemitraan sebagai bahan evaluasi pemberdayaan UMKM. | Rayula Samina | `KSA-108` |

### J. Superadmin dan Audit Sistem
| Kode Fitur | FR | Role | Deskripsi Kebutuhan Fungsional | Penanggung Jawab | Jira |
| :---: | :---: | :--- | :--- | :--- | :---: |
| **F31** | **FR-31.1** | Superadmin | Mengakses seluruh modul administratif dan data yang diperlukan untuk pengelolaan platform. | Nabil Fikry Khaidar | `KSA-148` |
| | **FR-31.2** | Superadmin | Menjalankan tindakan administratif lintas role dengan pencatatan identitas dan aktivitas pelaksana. | Nabil Fikry Khaidar | `KSA-148` |
| **F32** | **FR-32.1** | Sistem | Mencatat aktivitas penting, termasuk perubahan akun, keputusan verifikasi, seleksi, dan status kemitraan. | Nabil Fikry Khaidar | `KSA-152` |
| | **FR-32.2** | Superadmin | Melihat riwayat aktivitas penting untuk pengawasan dan penelusuran perubahan sistem. | Nabil Fikry Khaidar | `KSA-152` |

---

## 4. Aturan Bisnis Utama (`BR-01` – `BR-15`)

| ID | Aturan Bisnis |
| :---: | :--- |
| **BR-01** | Pengguna hanya dapat mengelola data miliknya atau data yang diberikan akses berdasarkan role. |
| **BR-02** | Transaksi penjualan dan pengurangan stok harus berlangsung secara atomik serta tidak menghasilkan stok negatif. |
| **BR-03** | Produk yang diarsipkan tidak dapat digunakan pada transaksi baru, tetapi riwayat transaksi tetap tersimpan. |
| **BR-04** | Informasi performa UMKM hanya ditampilkan sesuai pengaturan publikasi. Data keuntungan deklaratif tidak dianggap terverifikasi. |
| **BR-05** | Proposal dapat diajukan oleh UMKM maupun Mitra Industri dan hanya dapat direspons oleh pihak penerima. |
| **BR-06** | Kemitraan hanya dapat dilanjutkan dari proposal yang diterima. Perubahan status berjalan dan selesai mengikuti persetujuan pihak terkait. |
| **BR-07** | Pencatatan kemitraan tidak mencakup pembayaran, transfer investasi, atau pembagian keuntungan otomatis. |
| **BR-08** | Program/event dapat ditujukan kepada UMKM, Mitra Industri, atau keduanya dengan mekanisme pendaftaran dan undangan. |
| **BR-09** | Satu organisasi tidak dapat memiliki pendaftaran ganda pada kegiatan yang sama. |
| **BR-10** | Jumlah peserta diterima atau terkonfirmasi tidak boleh melebihi kapasitas kegiatan. |
| **BR-11** | Undangan VIP tidak secara otomatis mengabaikan persyaratan atau batas kuota kegiatan. |
| **BR-12** | Verifikasi UMKM merupakan pemeriksaan administratif, bukan sertifikasi legal usaha. |
| **BR-13** | Pemerintah hanya dapat mengakses data monitoring dan kemitraan sesuai kewenangan yang ditetapkan. |
| **BR-14** | Permintaan mediasi hanya dapat diajukan oleh pihak yang terlibat dalam kemitraan terkait. |
| **BR-15** | Tindakan administratif penting oleh Superadmin dan keputusan pemerintah harus memiliki catatan aktivitas yang dapat ditelusuri. |

---

## 5. Kebutuhan Non-Fungsional (`NFR-01` – `NFR-10`)

| ID | Kategori | Deskripsi |
| :---: | :--- | :--- |
| **NFR-01** | Keamanan akses | Autentikasi dan otorisasi ditegakkan di sisi server berdasarkan role dan kepemilikan data. |
| **NFR-02** | Keamanan kredensial | Password disimpan menggunakan hash yang aman dan sesi pengguna dikelola secara terproteksi. |
| **NFR-03** | Integritas data | Transaksi dan pembaruan stok menggunakan transaksi database; pendaftaran dan kuota kegiatan harus konsisten. |
| **NFR-04** | Privasi data | Informasi publik dipisahkan dari data bisnis privat, dengan pembatasan akses terhadap proposal, keuangan, dan ekspor. |
| **NFR-05** | Validasi & penanganan kesalahan | Seluruh input divalidasi di sisi server dan respons kesalahan tidak membocorkan informasi sensitif. |
| **NFR-06** | Responsivitas | Antarmuka dapat digunakan melalui perangkat desktop dan mobile. |
| **NFR-07** | Keandalan | Sistem menangani kegagalan operasi dan memiliki mekanisme backup database sebelum digunakan pada lingkungan produksi. |
| **NFR-08** | Auditabilitas | Keputusan dan perubahan penting dicatat dengan identitas pelaksana, waktu, dan jenis aktivitas. |
| **NFR-09** | Keamanan komunikasi | Penggunaan HTTPS diwajibkan apabila sistem diakses melalui lingkungan produksi. |
| **NFR-10** | Kemudahan penggunaan | Navigasi dan formulir dirancang konsisten sesuai kebutuhan masing-masing role. |
