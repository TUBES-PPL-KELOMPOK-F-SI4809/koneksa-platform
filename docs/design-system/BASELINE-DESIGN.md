# Baseline antarmuka Koneksa

Acuan visual proyek untuk Vue/Inertia. Ikuti [AGENTS.md](../../AGENTS.md)
untuk workflow dan [DESIGN-TOKENS.md](DESIGN-TOKENS.md) untuk nilai token.
Preferensi estetika skill tidak boleh mengganti brand atau menambah animasi tanpa tujuan.

## Brand dan tata letak

- Antarmuka kolaborasi UMKM, Mitra dan Pemda: hierarki jelas, mudah dipindai,
  kepadatan sesuai tugas. Form dan tabel operasional mengutamakan keterbacaan.
- Primary indigo #4F46E5; hover #4338CA; secondary cyan #06B6D4.
  Warna semantik mengikuti tokens. Jangan memperkenalkan palet alternatif dari skill.
- Font target Plus Jakarta Sans / Inter, fallback system sans; monospace untuk angka/kode
  bila perlu. Implementasi scaffold masih memakai Figtree; jangan menganggap font target
  sudah dimuat atau melakukan restyle global pada tugas kecil.
- Spasi berbasis 4/8px; breakpoints sm 640, md 768, lg 1024, xl 1280, 2xl 1536.
  Konten umum max-w-7xl, mx-auto px-4 sm:px-6 lg:px-8.
- Responsif mulai 375px; pilih tabel scroll atau susunan alternatif yang tetap menjaga
  akses data. Jangan memaksakan layout asimetris pada alur operasional.

## Komponen dan styling

1. Cari komponen Vue di resources/js/Components dan partial fitur terlebih dahulu.
2. Untuk komponen baru, baca skill shadcn-ui lokal. Evaluasi padanan Vue (shadcn-vue)
   dan kompatibilitas Tailwind terpasang sebelum adopsi.
3. Belum ada setup shadcn-vue/components.json. Instalasi yang menambah dependensi
   memerlukan otorisasi; jangan memakai CLI React shadcn atau menyalin TSX ke Vue.
4. Bila kebutuhan domain tidak tercakup, buat komponen Vue sederhana dengan alasan singkat.
   Untuk widget interaktif kompleks, gunakan primitif aksesibel yang kompatibel
   setelah dependensinya diotorisasi.
5. Styling aktif: Tailwind v3, scoped CSS dan CSS variables. Baca
   [status styled-components](STYLED-COMPONENTS.md); paket tersebut belum terintegrasi ke Vue.

Tokens di Markdown adalah spesifikasi. Periksa CSS/Tailwind aktual; mapping runtime
belum lengkap. Tambahkan mapping yang dibutuhkan dalam lingkup tugas tanpa membuat
sistem tema baru atau menduplikasi nilai yang sudah tersedia.

## Interaksi dan aksesibilitas

- Label input eksplisit, error terkait input, keyboard navigation dan focus-visible.
- Kontras minimum 4.5:1 untuk teks biasa, 3:1 untuk teks besar/elemen UI terkait.
  Pasangan foreground/background harus diperiksa; token warna sendiri tidak menjamin kontras.
- Tampilkan feedback hover, disabled, processing, sukses/gagal sesuai interaksi.
  Sediakan empty state dan skeleton untuk data yang belum tersedia bila relevan.
- Transisi mikro 150–250ms; dukung prefers-reduced-motion. Tidak wajib animasi
  terus-menerus, spring engine, ilustrasi atau shimmer pada semua komponen.
- Verifikasi browser sesuai [panduan testing](../guidelines/AGENT-UNIT-TESTING.md).

## Skills desain

Gunakan ui-ux-pro-max untuk keputusan layout/aksesibilitas yang memerlukannya.
taste-design, enhance-prompt, design-md dan stitch-* hanya untuk tugas desain/Stitch
yang sesuai. Jangan menghasilkan DESIGN.md atau memakai MCP eksternal pada setiap
perubahan UI. Baseline/tokens proyek tetap acuan visual utama.
