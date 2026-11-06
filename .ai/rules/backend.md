# Backend — Laravel

Ikuti [AGENTS.md](../../AGENTS.md). Konfirmasi versi terpasang melalui Composer;
jangan menyimpulkan runtime dari contoh dokumentasi.

- Baca kode sekitar dan callers; pertahankan konvensi, kecuali ada cacat kebenaran/keamanan.
- Gunakan Form Request untuk validasi, middleware/Policy/Gate untuk akses.
  Uji role, kepemilikan resource, dan data privat pada batas HTTP.
- Controller tetap ringkas; service domain untuk transaksi/multi-langkah.
  Pemanggilan service sinkron diperbolehkan. Hindari repository/interface/event tanpa kebutuhan nyata.
- Gunakan Eloquent, eager loading, Collections, Str/Arr, dan Carbon bila sesuai.
  Loop sederhana tetap boleh; jangan mengganti kode yang jelas demi memaksakan helper.
- Baca [ERD](../../docs/analysis/RANCANGAN_ERD_KONEKSA.md) dan migrasi aktual.
  Mutasi stok/penjualan/kuota mengikuti transaksi dan locking pada ERD.
- Gunakan named routes dan Inertia::render untuk halaman; API Resource hanya untuk endpoint API.
- PHP: type hint parameter/return, kurung kurawal semua control structure,
  constructor promotion bila sesuai, enum case TitleCase, PHPDoc shape untuk array kompleks.
- Gunakan Artisan make:* --no-interaction. Feature test adalah default untuk HTTP/DB;
  --unit hanya untuk logika murni. Jalankan Pint bila PHP berubah.
- Boost search-docs untuk API sensitif versi; fallback ke sumber terpasang/resmi.
  route:list melalui CLI; baca config tanpa menampilkan secret.
