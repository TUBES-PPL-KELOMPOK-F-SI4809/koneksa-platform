# Dokumentasi Koneksa Platform

Mulai dari [AGENTS.md](../AGENTS.md) untuk workflow lintas agent.
Dokumen di sini menjelaskan kontrak domain; aturan operasional tidak disalin per editor.

| Kebutuhan | Acuan |
|---|---|
| Lingkup 32 fitur / 64 FR | [PRD](PRD/PRD-KONEKSA-PLATFORM.md) |
| Spesifikasi fitur baru | [Template PRD](PRD/PRD-TEMPLATE.md) |
| Rancangan data dan invariant kanonis | [ERD](analysis/RANCANGAN_ERD_KONEKSA.md) |
| Pengarah skema dan pemeriksaan implementasi | [Skema](analysis/DATABASE-SCHEMA.md) |
| Analisis/use case | [Analisis sistem](analysis/SYSTEM-ANALYST-GUIDELINES.md) |
| Batas Inertia dan REST | [API](analysis/API-SPECIFICATION-GUIDE.md) |
| UI, token, dan contoh konkret | [Baseline](design-system/BASELINE-DESIGN.md), [Tokens](design-system/DESIGN-TOKENS.md), [Contoh UI Frontend](design-system/FRONTEND-UI-EXAMPLES.md) |
| Status styling Vue / styled-components | [Styling](design-system/STYLED-COMPONENTS.md) |
| Struktur fitur | [Pengembangan](guidelines/FEATURE-DEVELOPMENT-GUIDE.md), [Clean Code](guidelines/CLEAN-CODE.md) |
| Verifikasi | [Testing](guidelines/AGENT-UNIT-TESTING.md) |
| Commit, push dan PR | [Git workflow](guidelines/GIT-WORKFLOW.md) |
| Log Prompt AI Sprint 1 | [Log Prompt AI](guidelines/AI-PROMPT-LOG-SPRINT-1.md) |
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
