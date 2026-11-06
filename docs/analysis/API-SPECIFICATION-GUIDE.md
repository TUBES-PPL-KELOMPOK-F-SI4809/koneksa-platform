# Kontrak Inertia dan REST

Aplikasi memakai Laravel + Inertia Vue sebagai modern monolith.
Jangan membuat REST API paralel untuk alur halaman internal tanpa kebutuhan.

## Halaman dan form Inertia

- GET halaman mengembalikan Inertia::render dengan props yang dibutuhkan.
- Mutasi form sukses mengembalikan redirect ke named route dan flash bila relevan.
- Validasi memakai Form Request dan error bag Laravel/Inertia melalui redirect.
  Jangan memaksakan envelope JSON atau respons 422 JSON pada form Inertia.
- Periksa Policy/Gate dan kepemilikan data di server; jangan mengandalkan tombol tersembunyi.

## REST eksplisit

Aturan berikut hanya untuk endpoint JSON yang memang diminta/dipakai integrasi:
- Prefix /api/v1, resource plural dan nama mengikuti ERD kanonis:
  sales, collaboration-proposals, verification-requests.
- Gunakan API Resource dan pola respons existing bila tersedia.
- Sukses resource memakai data; koleksi paginasi memakai data, links dan meta.
  Metadata message dapat ditambahkan bila diperlukan; success boolean tidak wajib.
- Validasi JSON memakai 422 dengan message dan errors standar Laravel.
  Gunakan 401 untuk belum autentikasi, 403 untuk akses ditolak, 404 untuk resource
  yang tidak tersedia sesuai kebijakan privasi, 201 untuk create, 204 untuk delete tanpa body.
- Tentukan kontrak request/response, izin, filter dan pagination pada tugas/PR;
  contoh endpoint di dokumen ini bukan janji endpoint sudah diimplementasikan.

Periksa routes aktual melalui php artisan route:list. Untuk detail data/invariant,
baca [ERD kanonis](RANCANGAN_ERD_KONEKSA.md).
