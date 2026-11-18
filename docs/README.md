# Dokumentasi Koneksa Platform

Mulai dari [AGENTS.md](../AGENTS.md) untuk workflow lintas agent di semua perangkat developer.
Setiap developer & AI coding agent wajib mematuhi **alur kerja 3-tahap**:
1. **Aturan AI & Pragmatisme**: Baca `AGENTS.md` dan `.ai/rules/index.md`. Terapkan prinsip `ponytail` (solusi paling sederhana, anti-overengineering, minimalkan diff).
2. **Dokumentasi Terkait**: Baca spesifikasi fitur di PRD dan rancangan data di ERD kanonis sebelum menulis kode.
3. **Contoh UI Frontend**: Wajib merujuk ke [FRONTEND-UI-EXAMPLES.md](design-system/FRONTEND-UI-EXAMPLES.md) dan cetak biru konkret di [`docs/design-system/examples/`](design-system/examples/) setiap kali mengimplementasikan UI.

| Kebutuhan | Acuan |
|---|---|
| Lingkup 21 fitur / 42 FR | [PRD](PRD/PRD-KONEKSA-PLATFORM.md) |
| Spesifikasi fitur baru | [Template PRD](PRD/PRD-TEMPLATE.md) |
| Rancangan data dan invariant kanonis | [ERD](analysis/RANCANGAN_ERD_KONEKSA.md) |
| Pengarah skema dan pemeriksaan implementasi | [Skema](analysis/DATABASE-SCHEMA.md) |
| Analisis/use case | [Analisis sistem](analysis/SYSTEM-ANALYST-GUIDELINES.md) |
| Batas Inertia dan REST | [API](analysis/API-SPECIFICATION-GUIDE.md) |
| UI, token, dan contoh konkret | [Baseline](design-system/BASELINE-DESIGN.md), [Tokens](design-system/DESIGN-TOKENS.md), [Contoh UI Frontend](design-system/FRONTEND-UI-EXAMPLES.md) di `docs/design-system/examples/` |
| Status styling Vue / styled-components | [Styling](design-system/STYLED-COMPONENTS.md) |
| Struktur fitur | [Pengembangan](guidelines/FEATURE-DEVELOPMENT-GUIDE.md), [Clean Code](guidelines/CLEAN-CODE.md) |
| Verifikasi | [Testing](guidelines/AGENT-UNIT-TESTING.md) |
| Commit, push dan PR | [Git workflow](guidelines/GIT-WORKFLOW.md) |
| Stack aktif dan keterbatasan | [Stack](tech-stack/CURRENT-STACK.md) |
| Dependensi baru | [Prosedur stack](tech-stack/STACK-ADDITION-GUIDELINES.md) |

Baca bagian yang terkait tugas. PRD/ERD menyatakan kebutuhan/rancangan, bukan bukti
bahwa seluruh fitur sudah selesai. Telusuri kode, migrasi dan test sebelum menyatakan status.
Perbarui sumber yang berubah, bukan menambahkan versi kontrak paralel.

## Memulai pada editor/agent berbeda

| Agent/editor | Entrypoint repo |
|---|---|
| Codex dan agent yang membaca AGENTS.md | AGENTS.md |
| Claude Code | CLAUDE.md → AGENTS.md |
| Gemini / integrasi yang membaca GEMINI.md | GEMINI.md → AGENTS.md |
| Antigravity / integrasi rules lokal | .agents/rules/mandatory-reading.md → AGENTS.md |
| Cursor | .cursor/rules/koneksa-preflight.mdc; .cursorrules sebagai adaptor kompatibilitas |
| Windsurf | .windsurfrules → AGENTS.md |
| Copilot | .github/copilot-instructions.md → AGENTS.md |

Nama berkas tidak menjamin semua versi editor memuatnya otomatis. Pada integrasi baru,
pastikan konteks sesi memuat AGENTS.md dari root repo; bila tidak, arahkan agent untuk
membacanya secara eksplisit. Jangan menyalin seluruh dokumen ke konfigurasi editor.
