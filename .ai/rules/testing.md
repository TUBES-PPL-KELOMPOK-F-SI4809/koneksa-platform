# Testing

Acuan: [AGENTS.md](../../AGENTS.md) bagian 4 dan
[panduan testing](../../docs/guidelines/AGENT-UNIT-TESTING.md).

- Uji perilaku aplikasi, bukan salinan rumus/teks implementasi atau perilaku framework.
- Feature test untuk HTTP, autentikasi, Policy, validasi dan DB; Unit untuk logika murni.
- Gunakan factories dan database terisolasi. Jangan memakai database development/produksi.
- phpunit.xml memakai SQLite in-memory lokal; CI backend mengatur MySQL melalui environment.
  Jangan menganggap hasil SQLite membuktikan locking/constraint khusus MySQL.
- Jalankan affected tests saat iterasi dan seluruh php artisan test sebelum menyerahkan
  perubahan perilaku backend/skema. Styling/dokumentasi mengikuti matriks verifikasi utama.
- package.json saat ini tidak memiliki script test atau runner component test.
  Jangan mengklaim npm test/Vitest tersedia. Untuk alur Vue, uji server dan browser;
  penambahan runner memerlukan otorisasi dependensi.
- Browser CLI: bash .agents/skills/playwright/scripts/playwright_cli.sh --help
  dari root repo. Memerlukan npx, akses paket, dan browser yang tersedia.
  Kegagalan prasyarat harus dilaporkan, bukan diubah menjadi klaim lulus.
