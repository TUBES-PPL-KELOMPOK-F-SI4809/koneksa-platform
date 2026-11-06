---
description: "Aturan wajib membaca dokumen di docs/ dan memeriksa skills di .agents/ dan .antigravity/ sebelum membuat/mengubah kode"
globs: ["**/*"]
always_on: true
---

# Aturan Wajib: Pra-Eksekusi (Pre-Flight Context Reading)

Sebelum Agent mulai merancang, memodifikasi, atau menulis kode:

1. **Wajib Membaca Dokumen Terkait di `docs/`**:
   - Jika membuat/mengubah fitur: Wajib membaca [docs/PRD/PRD-KONEKSA-PLATFORM.md](file:///home/naabilfikry/Documents/Punya NAFI/KULIAH/MATKUL/PPL/TUBES/koneksa-platform/docs/PRD/PRD-KONEKSA-PLATFORM.md) dan [docs/analysis/DATABASE-SCHEMA.md](file:///home/naabilfikry/Documents/Punya NAFI/KULIAH/MATKUL/PPL/TUBES/koneksa-platform/docs/analysis/DATABASE-SCHEMA.md).
   - Jika membuat antarmuka UI: Wajib membaca [docs/design-system/BASELINE-DESIGN.md](file:///home/naabilfikry/Documents/Punya NAFI/KULIAH/MATKUL/PPL/TUBES/koneksa-platform/docs/design-system/BASELINE-DESIGN.md) dan [docs/design-system/DESIGN-TOKENS.md](file:///home/naabilfikry/Documents/Punya NAFI/KULIAH/MATKUL/PPL/TUBES/koneksa-platform/docs/design-system/DESIGN-TOKENS.md).
   - Jika membuat berkas/folder/service baru: Wajib membaca [docs/guidelines/FEATURE-DEVELOPMENT-GUIDE.md](file:///home/naabilfikry/Documents/Punya NAFI/KULIAH/MATKUL/PPL/TUBES/koneksa-platform/docs/guidelines/FEATURE-DEVELOPMENT-GUIDE.md) dan [docs/guidelines/CLEAN-CODE.md](file:///home/naabilfikry/Documents/Punya NAFI/KULIAH/MATKUL/PPL/TUBES/koneksa-platform/docs/guidelines/CLEAN-CODE.md).

2. **Wajib Memeriksa & Membaca Skill Terkait**:
   - Backend Laravel: Baca `.antigravity/skills/laravel-best-practices/SKILL.md`
   - Frontend Inertia + Vue: Baca `.antigravity/skills/inertia-vue-development/SKILL.md`
   - Styling: Baca `.antigravity/skills/tailwindcss-development/SKILL.md`
   - Komponen UI: Baca `.agents/skills/shadcn-ui/SKILL.md`
   - Testing: Baca `.antigravity/skills/testing-best-practices/SKILL.md`

3. **Larangan**:
   - Dilarang membuat asumsi sendiri tanpa mengonfirmasi acuan dokumentasi resmi proyek.

