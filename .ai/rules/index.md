# Indeks aturan domain

Mulai dari [AGENTS.md](../../AGENTS.md). Seluruh agent AI di semua perangkat developer wajib mematuhi **alur kerja 3-tahap**:
1. **Aturan AI & Pragmatisme**: Baca `AGENTS.md` dan aturan domain di sini. Utamakan prinsip `ponytail` (solusi paling sederhana, anti-overengineering, minimalkan diff).
2. **Dokumentasi Terkait**: Baca dokumentasi PRD (`docs/PRD/`) dan ERD kanonis (`docs/analysis/RANCANGAN_ERD_KONEKSA.md`) sebelum menulis kode.
3. **Contoh UI Frontend**: Jika tugas menyentuh UI/frontend, wajib merujuk ke [FRONTEND-UI-EXAMPLES.md](../../docs/design-system/FRONTEND-UI-EXAMPLES.md) dan contoh blueprint konkret di [`docs/design-system/examples/`](../../docs/design-system/examples/).

Path di tabel relatif terhadap root repo. Baca aturan yang cocok dengan dampak tugas:

| Path atau dampak | Aturan |
|---|---|
| Semua tugas | [preflight.md](preflight.md) |
| app/**, routes/**, database/**, config/**, bootstrap/** | [backend.md](backend.md) |
| resources/**, konfigurasi frontend, UI | [frontend.md](frontend.md) |
| tests/**, perilaku aplikasi, skema, konfigurasi test/CI | [testing.md](testing.md) |
| Dokumentasi/instruksi saja | AGENTS.md dan sumber dokumen terkait |
