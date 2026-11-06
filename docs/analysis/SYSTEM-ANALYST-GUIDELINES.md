# 📐 Panduan Analisis Sistem (System Analyst Guidelines)

Dokumen ini memuat standar metodologi, pemodelan diagram UML 2.x, dan format spesifikasi analisis perangkat lunak bagi System Analyst pada proyek **KONEKSA Platform**.

---

## 1. Standar Pemodelan Berorientasi Objek (UML Standards)

Seluruh pemodelan sistem menggunakan Visual Paradigm dengan notasi UML standar:

1. **Use Case Diagram**: Memetakan interaksi 4 aktor utama (UMKM, Mitra Industri, Pemerintah Daerah, Admin) terhadap 21 kelompok fitur inti.
2. **Activity Diagram**: Menggambarkan alur kerja prosedural bisnis (misal: Transaksi Penjualan, Pendaftaran Program, dan Verifikasi Berkas).
3. **Sequence Diagram**: Memvisualisasikan pertukaran pesan antar-layer (Browser Vue -> Route -> Controller -> Service -> Database MySQL).
4. **Entity-Relationship Diagram (ERD)** / **Class Diagram**: Menjelaskan entitas data relasional (lihat [DATABASE-SCHEMA.md](DATABASE-SCHEMA.md)).

---

## 2. Template Spesifikasi Use Case (Use Case Specification)

```markdown
### UC-XXX: [Nama Use Case]

* **Aktor Utama**: [UMKM / Mitra Industri / Pemerintah Daerah / Admin Platform]
* **Deskripsi Singkat**: [Tujuan dan fungsi use case]
* **Pre-Kondisi**: Aktor telah terautentikasi dan memiliki hak akses valid sesuai role.
* **Post-Kondisi**: Transaksi atomik berhasil dieksekusi dan status terbarui di basis data.

#### Alur Utama (Main Success Scenario):
1. Aktor membuka halaman [Fitur].
2. Sistem merender antarmuka Inertia Vue dan memuat data yang diperlukan.
3. Aktor menginput formulir dan menekan tombol simpan/kirim.
4. Sistem memvalidasi payload request via Form Request.
5. Sistem menjalankan logika bisnis; service dan transaksi atomik dipakai untuk operasi multi-langkah/invariant yang memerlukannya.
6. Sistem menampilkan notifikasi sukses dan mengarahkan ke halaman terkait.

#### Alur Alternatif (Alternative Flow):
* **4a. Validasi Form Gagal**:
  1. Sistem membatalkan proses dan mengembalikan error bag melalui redirect Inertia (422 JSON hanya untuk endpoint REST eksplisit).
  2. Tampilkan pesan galat inline pada input terkait.
* **5a. Batas Kuota Terlampaui / Stok Tidak Cukup**:
  1. Sistem melakukan rollback transaksi.
  2. Tampilkan pesan peringatan bahwa kuota program atau stok barang telah habis.
```

---

## 3. Matriks Keterlacakan (Traceability Matrix)

Setiap fitur pada tugas/PR dipetakan ke FR, route/controller, halaman, tabel dan test terkait. Gunakan format keterlacakan pada [template PRD](../PRD/PRD-TEMPLATE.md). Contoh berikut bukan bukti implementasi sudah selesai:

| Kode Fitur | Modul | Use Case ID | Tabel Terkait | Penanggung Jawab |
| :---: | :--- | :--- | :--- | :--- |
| **F01** | Autentikasi | `UC-AUTH-01` | `users` | Nabil Fikry Khaidar |
| **F04** | Profil UMKM | `UC-UMKM-01` | `businesses` | Muhammad Izaz Naufal |
| **F07** | Transaksi | `UC-TRX-01` | `sales`, `sale_items`, `stock_movements` | Muhammad Amin Hazami |
| **F10** | Direktori | `UC-DIR-01` | `businesses` | Dewinda Salsabila Putri Haidir |
| **F13** | Program | `UC-PROG-01` | `programs` | Syifa Ummayah |
| **F16** | Mitra | `UC-PART-01` | `partner_profiles` | Rayula Samina |
| **F19** | Dashboard Pemda | `UC-GOV-01` | `businesses`, `programs`, `verification_requests` | Sepdaffa Raja Dwi Putra Basman |

