# Stack aktif dan status integrasi

Sumber versi aktual: composer.lock/package-lock.json; manifest menyatakan rentang.
Snapshot berikut diperiksa 6 November 2026. Konfirmasi lagi sebelum memakai API;
jangan menganggap snapshot sebagai versi terbaru selamanya.

## Aktif

| Komponen | Versi terkunci / status |
|---|---|
| Laravel | 13.38.0 |
| Inertia Laravel dan Vue adapter | Major 2; lihat lockfile untuk patch |
| Vue | 3.5.44; Composition API, script setup |
| Vite | 8.3.5 |
| Tailwind | 3.4.19; tailwind.config.js + PostCSS + @tailwind directives |
| PHPUnit | 12.5.42; Unit dan Feature; Pest tidak terpasang |
| Laravel Pint | 1.32.1 |
| Breeze | Scaffold autentikasi/profil; status FR harus dibuktikan lewat kode/test |
| Database | Target MySQL 8; phpunit.xml lokal SQLite :memory:; CI backend memakai MySQL |

Composer proyek mensyaratkan PHP ^8.3; CI saat ini memakai PHP 8.4.
PHP 8.5 dapat digunakan jika memenuhi platform requirements dependensi.
Jangan memakai API khusus 8.5 tanpa menyesuaikan target yang diotorisasi.
Node harus memenuhi engines paket terkunci: Vite 8 memerlukan ^20.19.0 atau >=22.12.0.
Gunakan composer check-platform-reqs dan versi runtime lokal untuk memastikan prasyarat.

## Terpasang tetapi bukan pipeline/fitur aktif

- styled-components 6.6.0: belum ada integrasi Vue/ThemeProvider. Gunakan Tailwind,
  scoped CSS dan CSS variables; lihat [styling](../design-system/STYLED-COMPONENTS.md).
- @tailwindcss/vite major 4 ada di manifest, tetapi tidak digunakan dalam vite.config.js.
  Ini tidak berarti aplikasi sudah menggunakan Tailwind v4.
- shadcn-vue/components.json, Lucide dan runner component test belum dikonfigurasi
  sebagai dependensi langsung/tool proyek. Jangan mengimpornya seolah tersedia.
- Token/font desain adalah target; scaffold memakai Figtree dan mapping token belum lengkap.
- Playwright tersedia sebagai skill/wrapper npx, bukan suite E2E terpasang di package.json.
  Network, paket CLI dan browser harus tersedia pada mesin eksekusi.

Status ini bukan perintah memasang atau menghapus paket. Perubahan dependensi memerlukan
otorisasi dan [evaluasi](STACK-ADDITION-GUIDELINES.md).

## Perintah yang tersedia

~~~bash
php artisan serve
npm run dev
npm run build
php artisan test
vendor/bin/pint --dirty --format agent
bash .agents/skills/playwright/scripts/playwright_cli.sh --help
~~~

npm run test tidak tersedia saat ini. Unit saja bukan pemeriksaan seluruh backend.
Perintah serve/dev berjalan lama; wrapper Playwright dapat mengambil paket melalui npx.
Lihat [testing](../guidelines/AGENT-UNIT-TESTING.md) sebelum memilih pemeriksaan.

## Skills dan MCP

Pemetaan skill kanonis ada di [AGENTS.md](../../AGENTS.md).
.agents/skills adalah sumber proyek; .antigravity/skills menampung salinan kompatibilitas
skill framework. Sinkronkan kedua salinan bila skill terkait diubah.
Skill React/React Native/Stitch/video tersedia untuk tugas khusus, bukan stack aplikasi.

Konfigurasi .mcp.json dan .agents/mcp_config.json menunjuk php artisan boost:mcp.
Ketersediaan tool tetap harus diperiksa pada sesi agent; deklarasi bukan bukti koneksi.
Jika Bootstrap/Boost memperbarui instruksi otomatis, review diff dan pertahankan aturan
kanonis serta adaptor tipis; jangan menerima ulang kebijakan yang saling bertentangan.
