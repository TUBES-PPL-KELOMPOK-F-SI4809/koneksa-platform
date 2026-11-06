   

# ***Proposal Pengembangan Produk***

# **KONEKSA — Platform Kolaborasi UMKM dan Inovasi Industri Digital Daerah**

**Disusun oleh** 

**102022430028 \- Dewinda Salsabila Putri Haidir**  
**102022400110 \- Muhammad Amin Hazami**  
**102022400306 \- Muhammad Izaz Naufal**  
**102022430014 \- Nabil Fikry Khaidar**  
**102022400102 \- Rayula Samina**  
**102022400191 \- Sepdaffa Raja Dwi Putra Basman**  
**102022400161 \- Syifa UmmayaH**

**13 Oktober 2026**

**DAFTAR ISI**

[**1\. Pendahuluan	1**](#pendahuluan)

[1.1 Latar Belakang	1](#latar-belakang)

[1.2 Tujuan	1](#tujuan)

[1.3 Output	1](#output)

[**2\. Deskripsi Produk	2**](#deskripsi-produk)

[2.1 Usulan Solusi	2](#usulan-solusi)

[2.2 Deskripsi Produk	2](#deskripsi-produk-1)

[2.3 Proses Bisnis	2](#proses-bisnis)

[**3\. Kebutuhan Sistem	1**](#kebutuhan-sistem)

[3.1 Kebutuhan Fungsional	1](#kebutuhan-fungsional)

[3.1.1 Daftar Kebutuhan	1](#daftar-kebutuhan)

[3.1.2 Karakteristik Pengguna	1](#karakteristik-pengguna)

[3.2 Kebutuhan Non Fungsional	2](#kebutuhan-non-fungsional)

[3.3 Kebutuhan Teknis	2](#kebutuhan-teknis)

[**4\. Rancangan Sistem	3**](#rancangan-sistem)

[4.1 Use Case Diagram	3](#use-case-diagram)

[4.2 Arsitektur Sistem	3](#arsitektur-sistem)

[4.3 Rancangan ERD	3](#rancangan-erd)

[4.4 Class Diagram	3](#class-diagram)

[4.5 Mockup	3](#mockup)

[**5\. Metode Pengembangan	4**](#metode-pengembangan)

[5.1 Jadwal Pengembangan	4](#jadwal-pengembangan)

[5.2 Tim Pengembang	4](#tim-pengembang)

1. # **Pendahuluan** {#pendahuluan}

   1. ## **Latar Belakang** {#latar-belakang}

Usaha Mikro, Kecil, dan Menengah (UMKM) memiliki peran penting dalam mendukung pertumbuhan ekonomi daerah, termasuk di Kota Bandung. Namun, pelaku UMKM masih berpotensi menghadapi berbagai kendala, seperti pengelolaan usaha yang belum terorganisasi, keterbatasan akses terhadap mitra industri, serta sulitnya memperoleh informasi mengenai program pembinaan dan pengembangan usaha.

Di sisi lain, mitra industri membutuhkan sarana untuk menemukan UMKM yang sesuai dengan peluang kerja sama yang ditawarkan. Pemerintah daerah juga memerlukan wadah untuk mendukung program pemberdayaan dan memantau perkembangan UMKM secara lebih terstruktur.

Berdasarkan permasalahan tersebut, diusulkan KONEKSA (Koneksi dan Akselerasi Usaha) sebagai platform yang menghubungkan UMKM, mitra industri, dan pemerintah daerah dalam satu ekosistem digital. KONEKSA diharapkan dapat mempermudah pengelolaan usaha, memperluas peluang kolaborasi, serta mendorong inovasi dan pemberdayaan UMKM daerah. Pengembangan ini turut mendukung SDG 8 (Pekerjaan Layak dan Pertumbuhan Ekonomi), SDG 9 (Industri, Inovasi, dan Infrastruktur), serta SDG 17 (Kemitraan untuk Mencapai Tujuan).

2. ## **Tujuan** {#tujuan}

Tujuan pengembangan KONEKSA adalah menyediakan platform kolaborasi untuk mendukung pengelolaan dan pengembangan UMKM. Secara khusus, tujuan tersebut meliputi:

1. Mempermudah UMKM dalam mengelola produk, persediaan barang, transaksi, dan laporan penjualan.  
2. Memperluas akses UMKM terhadap mitra industri dan peluang kerja sama.  
3. Memfasilitasi pengajuan proposal dan pengelolaan kolaborasi antara UMKM dan mitra industri.  
4. Mempermudah akses terhadap program pembinaan dan pemberdayaan pemerintah daerah.  
5. Mendorong inovasi digital serta mendukung pemantauan perkembangan ekosistem UMKM daerah.

   3. ## **Output** {#output}

Hasil akhir pengembangan adalah platform web KONEKSA yang mengintegrasikan kebutuhan UMKM, mitra industri, dan pemerintah daerah dengan empat kategori pengguna, yaitu UMKM, Mitra Industri, Pemerintah Daerah, dan Admin Platform. Platform ini menyediakan fasilitas pengelolaan usaha, direktori UMKM, pengajuan proposal kerja sama, program pembinaan, penawaran inovasi digital, serta pemantauan perkembangan UMKM. Pengembangan dibatasi pada 21 fitur utama yang dikerjakan oleh tujuh anggota tim selama dua bulan, dengan Kota Bandung sebagai wilayah studi kasus. Peran pemerintah daerah disimulasikan untuk keperluan akademik, sedangkan proses investasi dan pembayaran langsung tidak termasuk dalam ruang lingkup pengembangan.

2. # **Deskripsi Produk** {#deskripsi-produk}

   1. ## **Usulan Solusi** {#usulan-solusi}

Sebagai upaya menjawab permasalahan dalam pengelolaan dan pengembangan Usaha Mikro, Kecil, dan Menengah (UMKM), diusulkan pengembangan KONEKSA (Koneksi dan Akselerasi Usaha) sebagai platform kolaborasi yang menghubungkan pelaku UMKM, mitra industri, dan pemerintah daerah dalam satu wadah terpadu.

KONEKSA dirancang untuk membantu pelaku UMKM mengelola kegiatan usaha secara lebih terorganisasi, memperluas akses terhadap peluang kerja sama, serta mempermudah partisipasi dalam program pembinaan dan pengembangan usaha. Di sisi lain, platform ini memberikan ruang bagi mitra industri untuk menjalin kolaborasi dengan UMKM serta mendukung pemerintah daerah dalam memfasilitasi dan memantau kegiatan pemberdayaan usaha.

Untuk mencapai tujuan tersebut, KONEKSA menawarkan beberapa solusi utama sebagai berikut.

1. Digitalisasi Pengelolaan Operasional UMKM  
   KONEKSA menyediakan sarana bagi pelaku UMKM untuk mencatat transaksi penjualan, mengelola produk dan persediaan barang, serta melihat laporan penjualan secara lebih terstruktur. Solusi ini bertujuan membantu pelaku usaha mengurangi ketergantungan pada pencatatan manual yang berpotensi menimbulkan kesalahan dan kesulitan dalam memantau kondisi usaha.  
2. Penyediaan Profil dan Direktori UMKM  
   KONEKSA memfasilitasi pelaku UMKM untuk memperkenalkan identitas, produk, dan kebutuhan pengembangan usahanya melalui profil usaha yang dapat diakses oleh calon mitra. Melalui direktori UMKM, mitra industri dapat menemukan pelaku usaha berdasarkan kategori dan bidang usaha yang sesuai. Solusi ini diharapkan dapat meningkatkan keterlihatan UMKM dan memperluas kesempatan untuk menjalin hubungan bisnis.  
3. Fasilitasi Pengajuan dan Pengelolaan Kerja Sama  
   KONEKSA menyediakan sarana pengajuan proposal kerja sama antara mitra industri dan pelaku UMKM. Mitra industri dapat menawarkan bentuk kolaborasi, seperti pemasaran, distribusi, pendampingan, dan dukungan pengembangan usaha. Pelaku UMKM dapat meninjau serta memberikan tanggapan terhadap proposal yang diterima. Perkembangan kerja sama juga dapat dipantau sehingga proses kolaborasi menjadi lebih terarah dan terdokumentasi.  
4. Peningkatan Akses terhadap Program Pemberdayaan Pemerintah Daerah  
   KONEKSA menyediakan wadah bagi pemerintah daerah untuk memperkenalkan dan mengelola program pembinaan UMKM, seperti pelatihan kewirausahaan, pengembangan produk, dan pemasaran digital. Pelaku UMKM dapat memperoleh informasi program, melakukan pendaftaran, serta mengetahui hasil seleksi melalui platform. Solusi ini diharapkan dapat mempermudah penyampaian informasi dan meningkatkan keterjangkauan program pemberdayaan bagi pelaku usaha.  
5. Dukungan terhadap Inovasi dan Transformasi Digital UMKM  
   KONEKSA membuka kesempatan bagi mitra industri untuk memperkenalkan solusi dan pendampingan digital yang relevan dengan kebutuhan UMKM. Pelaku usaha dapat menemukan peluang pengembangan, seperti peningkatan kemampuan pemasaran digital, pemanfaatan teknologi dalam kegiatan usaha, serta pendampingan pengelolaan bisnis. Solusi ini bertujuan mendorong adaptasi UMKM terhadap perkembangan industri digital secara bertahap.  
6. Verifikasi dan Pemantauan Ekosistem UMKM Daerah  
   KONEKSA menyediakan fasilitas verifikasi informasi usaha serta penyajian ringkasan data mengenai UMKM, program pembinaan, dan kegiatan kemitraan. Informasi tersebut dapat dimanfaatkan oleh pihak pemerintah daerah untuk memperoleh gambaran mengenai aktivitas dan kebutuhan pengembangan UMKM. Dengan demikian, proses pemantauan dan evaluasi program pemberdayaan diharapkan menjadi lebih terstruktur dan mendukung pengambilan keputusan yang lebih tepat sasaran.

Melalui keenam solusi tersebut, KONEKSA diharapkan dapat menjadi sarana yang mendukung pengelolaan usaha sekaligus memperkuat hubungan antara UMKM, mitra industri, dan pemerintah daerah. Integrasi berbagai kebutuhan tersebut dalam satu platform diharapkan mampu menciptakan peluang kolaborasi yang lebih luas, meningkatkan kapasitas pelaku usaha, dan mendorong terbentuknya ekosistem ekonomi daerah yang lebih inovatif serta berkelanjutan.

2. ## **Deskripsi Produk** {#deskripsi-produk-1}

**KONEKSA (Koneksi dan Akselerasi Usaha)** merupakan platform berbasis web yang dirancang untuk menghubungkan pelaku UMKM, mitra industri, dan pemerintah daerah dalam satu wadah kolaborasi guna mendukung pengembangan usaha di Kota Bandung. KONEKSA membantu pelaku UMKM mengelola kegiatan usaha, seperti pencatatan penjualan, pengelolaan produk dan stok, serta pemantauan perkembangan usaha. Selain itu, platform ini menyediakan direktori UMKM dan fasilitas pengajuan proposal kerja sama untuk memperluas peluang kemitraan serta mendukung penerapan inovasi digital.

Bagi pemerintah daerah, KONEKSA menyediakan fasilitas pengelolaan program pembinaan, verifikasi UMKM, dan pemantauan perkembangan usaha. Melalui layanan tersebut, KONEKSA 

diharapkan dapat menciptakan ekosistem UMKM yang lebih terhubung, kolaboratif, dan mendukung pertumbuhan ekonomi daerah.

3. ## **Proses Bisnis** {#proses-bisnis}

3. # **Kebutuhan Sistem** {#kebutuhan-sistem}

   1. ## **Kebutuhan Fungsional** {#kebutuhan-fungsional}

      1. ### **Daftar Kebutuhan** {#daftar-kebutuhan}

Baseline *To-Be* terdiri atas 21 fitur (F01–F21), dengan masing-masing fitur mencakup dua *Functional Requirements* (FR) inti, sehingga total terdapat 42 FR. Tabel berikut merangkum fungsi-fungsi yang akan dikembangkan. Sementara itu, *acceptance criteria*, aturan validasi, dan penanganan alur kegagalan akan dirinci dalam backlog implementasi tanpa mengubah ruang lingkup utama yang telah ditetapkan.

Implementasi sistem mengacu pada beberapa prinsip utama, yaitu otorisasi dilakukan di sisi server, transaksi dan pembaruan stok harus berjalan secara atomik, kuota program tidak boleh terlampaui, serta akses terhadap proposal dibatasi hanya untuk pihak yang berkepentingan. Selain itu, status verifikasi tidak dapat disamakan dengan sertifikasi legal. Penetapan FR sebagai baseline implementasi juga tidak menghilangkan kewajiban untuk menerapkan mekanisme keamanan dan penanganan kegagalan secara memadai.

| ID | Kebutuhan Fungsional | Deskripsi |
| ----- | ----- | ----- |
| FR-01 | Autentikasi | FR-01.1 Registrasi UMKM/Mitra dan login; FR-01.2 Logout, sesi dan pembatasan role. |
| FR-02 | Profil akun | FR-02.1 Lihat/ubah profil dasar; FR-02.2 Ubah password dengan verifikasi lama. |
| FR-03 | Admin pengguna | FR-03.1 Daftar dan status akun; FR-03.2 Buat petugas pemerintah dan otorisasi. |
| FR-04 | Profil UMKM | FR-04.1 Kelola identitas usaha; FR-04.2 Kebutuhan usaha dan publikasi. |
| FR-05 | Katalog produk | FR-05.1 Tambah/ubah produk; FR-05.2 Daftar dan arsip produk. |
| FR-06 | Stok | FR-06.1 Restock; FR-06.2 Potong stok otomatis dan peringatan stok rendah. |
| FR-07 | Transaksi | FR-07.1 Catat penjualan dan hitung total; FR-07.2 Simpan atomik, ubah stok, tampilkan riwayat. |
| FR-08 | Dashboard UMKM | FR-08.1 Omzet/jumlah transaksi; FR-08.2 Ringkasan stok dan navigasi cepat. |
| FR-09 | Laporan | FR-09.1 Rekap penjualan per tanggal; FR-09.2 Ekspor CSV. |
| FR-10 | Direktori UMKM | FR-10.1 Daftar UMKM publik; FR-10.2 Cari/filter nama dan kategori. |
| FR-11 | Peluang usaha | FR-11.1 Detail publik dan kebutuhan; FR-11.2 Status verifikasi dan akses proposal. |
| FR-12 | Proposal | FR-12.1 Mitra mengirim proposal; FR-12.2 UMKM menerima/menolak. |
| FR-13 | Program pemerintah | FR-13.1 Buat/ubah program; FR-13.2 Publikasi/tutup pendaftaran. |
| FR-14 | Pendaftaran program | FR-14.1 Detail program; FR-14.2 Daftar dan cek status. |
| FR-15 | Seleksi peserta | FR-15.1 Daftar pendaftar; FR-15.2 Terima/tolak dengan kuota. |
| FR-16 | Profil Mitra | FR-16.1 Kelola organisasi; FR-16.2 Pilih jenis kerja sama. |
| FR-17 | Monitoring kemitraan | FR-17.1 Riwayat proposal dan status; FR-17.2 Ubah status berjalan/selesai. |
| FR-18 | Solusi digital | FR-18.1 Publikasikan penawaran digital; FR-18.2 Lihat penawaran dan kirim minat lewat proposal. |
| FR-19 | Dashboard pemerintah | FR-19.1 UMKM/validasi; FR-19.2 Program, peserta dan proposal. |
| FR-20 | Rekap daerah | FR-20.1 Tabel UMKM kategori/status; FR-20.2 Ekspor CSV terotorisasi. |
| FR-21 | Verifikasi UMKM | FR-21.1 Pengajuan dan daftar verifikasi; FR-21.2 Keputusan/catatan pemerintah. |

2. ### **Karakteristik Pengguna** {#karakteristik-pengguna}

Sistem melibatkan empat aktor utama, yaitu UMKM, Mitra Industri, Pemerintah Daerah, dan Admin Platform. Pengguna yang belum terautentikasi hanya dapat mengakses halaman publik yang tersedia. Pada tahap *Minimum Viable Product* (MVP), sistem tidak mencakup hierarki pemerintahan bertingkat maupun peran khusus bagi investor finansial.

**Tabel 1 Karakteristik Pengguna**

| Kategori Pengguna | Deskripsi | Hak Akses |
| ----- | ----- | ----- |
| UMKM | Pemilik usaha dalam satu kabupaten/kota | Profil usaha, produk, stok, transaksi, laporan, program, proposal masuk dan inisiatif digital. |
| Mitra Industri | Organisasi calon mitra UMKM | Profil organisasi, direktori, proposal, status kemitraan, katalog solusi digital. |
| Pemerintah Daerah | Petugas berwenang untuk kabupaten/kota sasaran | Program, seleksi peserta, verifikasi administratif, monitoring dan rekap. |
| Admin Platform | Pengelola teknis aplikasi | Aktivasi/nonaktivasi akun, pembuatan akun pemerintah, kontrol akses. |

2. ## **Kebutuhan Non Fungsional** {#kebutuhan-non-fungsional}

Persyaratan nonfungsional awal disusun sebagai usulan yang perlu divalidasi melalui proses pengujian. Target performa dan ketersediaan layanan belum ditetapkan karena spesifikasi infrastruktur dan estimasi jumlah pengguna belum diketahui.

Batasan utama yang perlu dipenuhi meliputi pembatasan akses data berdasarkan peran dan kepemilikan, penyimpanan kata sandi dalam bentuk *hash*, penggunaan HTTPS pada lingkungan produksi, serta validasi input di sisi server. Selain itu, respons kesalahan harus dirancang agar tidak membocorkan informasi sensitif, mekanisme pencadangan basis data harus disiapkan pada tahap deployment, dan antarmuka harus mendukung penggunaan melalui perangkat desktop maupun mobile. Sistem juga perlu menyediakan catatan aktivitas atau keputusan penting yang dapat digunakan untuk keperluan verifikasi dan pengajuan.

Beberapa aspek masih memerlukan konfirmasi lebih lanjut, yaitu target jumlah pengguna, pilihan infrastruktur hosting, dan kebijakan penyimpanan data.

| ID | Kebutuhan Non Fungsional | Deskripsi |
| ----- | ----- | ----- |
| NFR-01 | Keamanan akses  | Server menegakkan role dan kepemilikan data; password di-hash dan HTTPS produksi. |
| NFR-02 | Integritas data | Transaksi dan stok atomik; kuota/pendaftaran ganda dicegah. |
| NFR-03 | Privasi | Profil publik terpisah dari transaksi/detail privat; ekspor sesuai izin. |
| NFR-04 | Responsivitas | Antarmuka dapat digunakan di desktop dan mobile. |
| NFR-05 | Keandalan | Error API ditangani; backup database direncanakan sebelum rilis. |

3. ## **Kebutuhan Teknis** {#kebutuhan-teknis}

Tim memilih Laravel 13 sebagai backend sekaligus kerangka utama aplikasi web, Inertia.js dan Vue untuk integrasi frontend, Tailwind CSS untuk styling antarmuka, serta MySQL 8 sebagai sistem manajemen basis data. Arsitektur yang digunakan adalah monolit modular dengan satu basis data, tanpa mewajibkan penerapan *microservices* maupun REST API terpisah.

Untuk mendukung proses pengembangan, Figma digunakan dalam perancangan UI/UX, Visual Paradigm untuk pemodelan *use case*, BPMN, ERD, dan *class diagram*, serta Jira untuk pengelolaan backlog dan sprint. Git dan GitHub digunakan untuk *version control* dan kolaborasi tim, Visual Studio Code sebagai IDE, serta Docker untuk menjaga konsistensi lingkungan pengembangan dan mendukung deployment.

Beberapa aspek teknis masih memerlukan penetapan lebih lanjut, meliputi adaptor Inertia.js yang digunakan (React, Vue, atau Svelte), sistem operasi server, penyedia hosting, domain, serta konfigurasi HTTPS pada lingkungan produksi.

Rancangan teknis minimum mencakup entitas pengguna dan peran (*role*), profil UMKM, produk, transaksi dan item transaksi, profil Mitra Industri, proposal kolaborasi, inisiatif digital, program pemerintah, pendaftaran, serta verifikasi. Pengelolaan hak akses dan kepemilikan data dilakukan melalui validasi di sisi Laravel. Transaksi penjualan dan pembaruan stok menggunakan transaksi basis data untuk menjaga konsistensi data, sedangkan mekanisme pendaftaran program harus memastikan kuota tidak terlampaui, termasuk ketika terjadi pendaftaran secara bersamaan.

Docker digunakan untuk menjaga konsistensi lingkungan pengembangan dan mendukung proses deployment. Sementara itu, peralihan dari kode React/Vite yang telah tersedia ke arsitektur Laravel dengan Inertia.js tidak serta-merta memungkinkan penggunaan ulang seluruh komponen secara langsung. Oleh karena itu, diperlukan audit terhadap kode yang ada untuk mengidentifikasi bagian desain, logika bisnis, dan komponen yang dapat digunakan kembali berdasarkan adaptor Inertia.js yang dipilih.

| ID | Kebutuhan Teknis | Deskripsi |
| ----- | ----- | ----- |
| TEC-01 | Frontend | Inertia.js \+ Vue \+ Tailwind CSS untuk frontend; rancangan UI/UX menggunakan Figma. |
| TEC-02 | Backend         | Laravel 13 (PHP), satu aplikasi monolit; otorisasi, validasi, dan workflow bisnis di server. |
| TEC-03 | Database | MySQL 8, skema relasional dan transaksi database untuk penjualan serta kuota. |
| TEC-04 | Deployment | Docker lokal untuk pengembangan dan demonstrasi akademik; tidak mensyaratkan VPS/domain atau layanan online pada MVP. |
| TEC-05 | Tools & kolaborasi | Visual Paradigm, Jira, Git, GitHub, Visual Studio Code, Figma. |

4. # **Rancangan Sistem** {#rancangan-sistem}

   1. ## **Use Case Diagram** {#use-case-diagram}

\[TEMPAT DIAGRAM 4.1: USE CASE DIAGRAM — VISUAL PARADIGM\]  
Panduan: empat aktor (UMKM, Mitra Industri, Pemerintah Daerah, Admin). Peta 21 fitur F01–F21 menjadi kelompok use case; tandai pengajuan proposal, respons proposal, program, seleksi, dan verifikasi; jangan tambahkan pembayaran/ROI. Setelah diagram ditempel, uraikan minimal skenario: transaksi penjualan, pengajuan proposal, pendaftaran program, dan verifikasi UMKM (prasyarat, alur utama, alternatif, hasil).

2. ## **Arsitektur Sistem** {#arsitektur-sistem}

Arsitektur monolitik dipilih agar tujuh pengembang part-time dapat menerapkan satu aplikasi dan satu database tanpa overhead microservices. Browser menjalankan antarmuka Vue/Inertia, Laravel menangani routing, sesi, authorization, validasi, aturan bisnis, dan akses model MySQL. Docker menyatukan lingkungan lokal.  
\[TEMPAT DIAGRAM 4.2 — browser → Laravel \+ Inertia/Vue → MySQL; batas container Docker. Sertakan rujukan dokumentasi resmi untuk justifikasi setelah diverifikasi.\]. \[TEMPAT DIAGRAM 4.2: ARSITEKTUR SISTEM\]  
Panduan: Browse0...r pengguna (Vue \+ Inertia.js \+ Tailwind CSS) ↔ aplikasi monolit Laravel 13 (routing, autentikasi, validasi, business logic) ↔ MySQL 8\. Sertakan autentikasi/otorisasi Laravel, server-side validation, storage berkas bila diperlukan, dan deployment Docker. Jelaskan alasan memilih monolit untuk tim 7 part-time dan durasi 8 minggu. Jangan menggambar frontend React/Vite terpisah atau REST API terpisah sebagai keputusan final.

3. ## **Rancangan ERD** {#rancangan-erd}

*\[TEMPAT ERD 4.3 — usulan entitas: users, businesses, partner\_profiles, products, transactions, transaction\_items, collaboration\_requests, digital\_offerings, programs, program\_applications, verification\_requests. Tandai PK/FK, kardinalitas dan unique constraints.\]*  Satu akun UMKM memiliki satu profil usaha; usaha memiliki banyak produk dan transaksi; transaksi memiliki banyak item; mitra memiliki satu profil organisasi; proposal menghubungkan pihak pengaju dan penerima; program mempunyai banyak pendaftar; verifikasi terkait satu usaha. Struktur atribut final mengikuti hasil desain database. 

4. ## **Class Diagram** {#class-diagram}

*\[TEMPAT CLASS DIAGRAM 4.4 — model User, Business, PartnerProfile, Product, Transaction, TransactionItem, CollaborationRequest, DigitalOffering, Program, ProgramApplication, VerificationRequest. Tunjukkan relasi dan atribut/metode domain inti, tidak perlu menduplikasi seluruh controller.\]* Tampilkan model/domain utama Laravel, misalnya User, UmkmProfile, PartnerProfile, Product, Transaction, TransactionItem, CollaborationProposal, Program, ProgramApplication, VerificationRequest, DigitalInitiative beserta relasi. Tidak perlu menggambarkan seluruh class bawaan Laravel atau setiap controller/framework service.

5. ## **Mockup** {#mockup}

\[TEMPAT MOCKUP FIGMA\]  
Siapkan layar login/registrasi; dashboard dan transaksi UMKM; direktori/detail UMKM; formulir proposal; katalog/pendaftaran program; halaman seleksi/verifikasi pemerintah; profil mitra; manajemen akun Admin. Sertakan deskripsi interaksi utama serta tautan Figma setelah tersedia. Minimal tampilkan (1) login/register, (2) dashboard UMKM, (3) produk/transaksi, (4) direktori/detail UMKM, (5) form proposal dan status kemitraan, (6) katalog/pendaftaran program, (7) dashboard/verifikasi pemerintah, (8) manajemen akun admin. Lampirkan anotasi tujuan halaman, role, navigasi utama, state kosong, dan contoh error/validasi. Gunakan desain responsif sederhana.

5. # **Metode Pengembangan** {#metode-pengembangan}

Metode yang diusulkan adalah Scrum ringan/iteratif selama empat sprint dua mingguan dengan Jira untuk Product Backlog Item (PBI), Git/GitHub untuk kerja kolaboratif, Figma untuk rancangan UI, Visual Paradigm untuk analisis dan desain, serta pengujian integrasi rutin. Pembagian penanggung jawab 3 fitur/anggota ditetapkan pada Tabel Tim Pengembang. Definition of Done: alur utama bekerja end-to-end, otorisasi dan validasi server berjalan, integrasi database benar, serta pengujian dasar lulus. Hindari overhead Scrum formal yang tidak sesuai kapasitas tim part-time. Karena tanggal mulai belum ditetapkan, seluruh jadwal diuraikan relatif berdasarkan Minggu 1 sampai Minggu 8\.

1. ## **Jadwal Pengembangan** {#jadwal-pengembangan}

| SPRINT | Task / Aktivitas | Minggu 1 | Minggu 2 | Minggu 3 | Minggu 4 | Minggu 5 | Minggu 6 | Minggu 7 | Minggu 8 |
| ----- | ----- | ----- | ----- | ----- | ----- | ----- | ----- | ----- | ----- |
| **SPRINT 1** (Minggu 1-4) | Pematang FR |  |  |  |  |  |  |  |  |
|  | Desain data dan UI awal |  |  |  |  |  |  |  |  |
|  | Setup Laravel 13/[Inertia.js/vue/Tailwind](http://Inertia.js/vue/Tailwind) CSS/MySQL 8/ Docker lokal/Github |  |  |  |  |  |  |  |  |
|  | Autentikasi dan Profil Dasar |  |  |  |  |  |  |  |  |
|  | Produk |  |  |  |  |  |  |  |  |
|  | Stok |  |  |  |  |  |  |  |  |
|  | Transaksi |  |  |  |  |  |  |  |  |
|  | Profil Mitra |  |  |  |  |  |  |  |  |
|  | Direktori |  |  |  |  |  |  |  |  |
|  | Kerangka Program |  |  |  |  |  |  |  |  |
| **SPRINT 2** (Minggu 5-8) | Proposal/kemitraan |  |  |  |  |  |  |  |  |
|  | Pendaftaran \- seleksi |  |  |  |  |  |  |  |  |
|  | Inisiatif digital |  |  |  |  |  |  |  |  |
|  | Verifikasi |  |  |  |  |  |  |  |  |
|  | Dashboard dan ekspor |  |  |  |  |  |  |  |  |
|  | Integritasi lintas \- role |  |  |  |  |  |  |  |  |
|  | Pengujian |  |  |  |  |  |  |  |  |
|  | Perbaikan bug |  |  |  |  |  |  |  |  |
|  | Deployment |  |  |  |  |  |  |  |  |
|  | Demonstrasi |  |  |  |  |  |  |  |  |
|  | Demonstrasi |  |  |  |  |  |  |  |  |

   2. ## **Tim Pengembang** {#tim-pengembang}

Tim terdiri atas tujuh anggota sesuai daftar identitas sampul. Pembagian penanggung jawab utama dan fitur yang dikelola disajikan dalam tabel berikut:

| No | Role | Nama Pengembang | Fitur yang Dikelola |
| :---: | :---: | ----- | :---: |
| 1 | Dev 1 | Nabil Fikry Khaidar | F01 – F03 |
| 2 | Dev 2 | Muhammad Izaz Naufal | F04 – F06 |
| 3 | Dev 3 | Muhammad Amin Hazami | F07 – F09 |
| 4 | Dev 4 | Dewinda Salsabila Putri Haidir | F10 – F12 |
| 5 | Dev 5 | Syifa Ummayah | F13 – F15 |
| 6 | Dev 6 | Rayula Samina | F16 – F18 |
| 7 | Dev 7 | Sepdaffa Raja Dwi Putra Basman | F19 – F21 |

 