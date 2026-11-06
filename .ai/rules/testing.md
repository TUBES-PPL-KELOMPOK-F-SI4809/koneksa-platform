# Testing Rules (PHPUnit & Feature Testing)

1. **Mandatory Testing**:
   - Dilarang menandai tugas selesai tanpa unit/feature test.
   - Sediakan pengujian untuk Happy Path, Edge Cases, dan Error/Validation.
2. **Eksekusi Pengujian**:
   - Jalankan `php artisan test` untuk memastikan seluruh test suite 100% green.
   - Database testing menggunakan SQLite in-memory `:memory:` sesuai konfigurasi `phpunit.xml`.
3. **Best Practices**:
   - Rujuk `.antigravity/skills/testing-best-practices/SKILL.md` dan `docs/guidelines/AGENT-UNIT-TESTING.md`.

