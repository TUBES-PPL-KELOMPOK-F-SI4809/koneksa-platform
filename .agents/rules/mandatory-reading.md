---
description: "Route all agents to the canonical Koneksa workflow"
globs: ["**/*"]
always_on: true
---

Wajib ikuti alur 3-tahap Koneksa di setiap perangkat developer:
1. **Baca aturan AI**: [AGENTS.md](../../AGENTS.md) dan [.ai/rules/index.md](../../.ai/rules/index.md). Utamakan prinsip minimal diff & anti-overengineering (skill `ponytail`).
2. **Baca dokumentasi sebelum implementasi**: [PRD](../../docs/PRD/PRD-KONEKSA-PLATFORM.md) untuk fitur dan [ERD kanonis](../../docs/analysis/RANCANGAN_ERD_KONEKSA.md) untuk skema data.
3. **Wajib lihat contoh UI**: Jika mengerjakan frontend/UI, wajib merujuk [FRONTEND-UI-EXAMPLES.md](../../docs/design-system/FRONTEND-UI-EXAMPLES.md) dan blueprint di `docs/design-system/examples/`.
