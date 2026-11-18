# Pemetaan konteks

[AGENTS.md](../../AGENTS.md) adalah aturan operasional kanonis. Seluruh agent wajib mengikuti:
1. **Aturan AI**: `AGENTS.md` & domain rules. Utamakan mode `ponytail` (solusi paling sederhana, anti-overengineering).
2. **Dokumentasi**: Baca PRD & ERD terkait sebelum menulis kode.
3. **Contoh UI Frontend**: Wajib merujuk ke [FRONTEND-UI-EXAMPLES.md](../../docs/design-system/FRONTEND-UI-EXAMPLES.md) dan [`docs/design-system/examples/`](../../docs/design-system/examples/) sebelum membuat halaman/komponen Vue.

Cari aturan domain dengan rg sesuai kata kunci tugas; jangan memuat seluruh katalog tanpa kebutuhan.

| Kebutuhan | Baca bagian terkait |
|---|---|
| Fitur/aturan bisnis | [PRD](../../docs/PRD/PRD-KONEKSA-PLATFORM.md) |
| Data/invariant/transaksi | [ERD kanonis](../../docs/analysis/RANCANGAN_ERD_KONEKSA.md), [Skema](../../docs/analysis/DATABASE-SCHEMA.md) |
| Struktur/arsitektur | [Pengembangan fitur](../../docs/guidelines/FEATURE-DEVELOPMENT-GUIDE.md), [Clean Code](../../docs/guidelines/CLEAN-CODE.md) |
| UI & Desain Frontend | [Contoh UI Frontend](../../docs/design-system/FRONTEND-UI-EXAMPLES.md) & [Blueprint examples/](../../docs/design-system/examples/), [Baseline UI](../../docs/design-system/BASELINE-DESIGN.md), [Tokens](../../docs/design-system/DESIGN-TOKENS.md) |
| Test | [Testing](../../docs/guidelines/AGENT-UNIT-TESTING.md) |
| Versi/tool tersedia | [Stack](../../docs/tech-stack/CURRENT-STACK.md), manifests dan lockfiles |
| Dependensi baru | [Prosedur dependensi](../../docs/tech-stack/STACK-ADDITION-GUIDELINES.md) |
