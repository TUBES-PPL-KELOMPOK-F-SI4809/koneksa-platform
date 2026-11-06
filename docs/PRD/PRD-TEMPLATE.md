# Template spesifikasi fitur

Isi pada tugas/PR atau dokumen fitur yang diminta; tidak wajib membuat berkas baru
untuk setiap perubahan. Gunakan ID Fxx / FR-xx.y dari
[PRD platform](PRD-KONEKSA-PLATFORM.md).

## Identitas dan lingkup

- Fitur/FR:
- Pemilik dan status: Draft / Approved / In Development / Released.
- Masalah pengguna dan hasil yang dibutuhkan:
- Termasuk / di luar lingkup:
- Aktor: UMKM / Mitra / Pemerintah / Admin sesuai fitur.
- Rujukan ERD dan keputusan bisnis terkait:

## Kontrak perilaku

- Precondition: autentikasi, status akun, kepemilikan resource, izin Policy/Gate.
- Input wajib/opsional, batas nilai dan validasi.
- Alur sukses dan hasil data yang dapat diamati.
- Transisi status yang diizinkan dan aktor yang boleh menjalankannya.
- Error penting: data invalid, akses pihak lain, data hilang, submit berulang,
  stok/kuota boundary atau concurrency bila terkait.
- Data publik/privat dan batas ekspor/upload bila terkait.
- Respons mengikuti [kontrak Inertia/REST](../analysis/API-SPECIFICATION-GUIDE.md):
  form Inertia menggunakan redirect/error bag; endpoint JSON eksplisit menggunakan 422.

## Acceptance criteria

Gunakan Given–When–Then untuk perilaku nyata, misalnya:
- Given pengguna berwenang dan input valid, When menyimpan, Then data tersimpan
  dan halaman tujuan/feedback sesuai kontrak.
- Given field wajib invalid, When submit, Then tidak ada mutasi dan error terkait
  muncul melalui mekanisme Inertia atau JSON sesuai jenis endpoint.
- Given pengguna lain tidak berhak, When mengakses/mengubah resource, Then akses ditolak
  dan data privat tidak bocor.

Tambahkan skenario domain terkait; jangan menambah notifikasi, queue, ilustrasi,
real-time validation atau target performa numerik tanpa kebutuhan yang disetujui.

## Keterlacakan dan verifikasi

| FR | Route/controller | Halaman/komponen | Test/perintah | Status aktual |
|---|---|---|---|---|
| Isi FR terkait | Isi implementasi terkait | Isi bila ada | Isi bukti aktual | Belum / sebagian / terverifikasi |

Feature test untuk HTTP/DB, Unit untuk logika murni. Ikuti
[panduan testing](../guidelines/AGENT-UNIT-TESTING.md) dan matriks AGENTS.md.
Catat perintah, exit code/hasil, keterbatasan dan keputusan terbuka pada handoff.
