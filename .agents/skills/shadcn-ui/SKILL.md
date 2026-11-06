---
name: shadcn-ui
description: "Select, reuse, or integrate accessible Vue UI components for Koneksa. Use when adding or choosing UI components; adapt shadcn patterns to Vue and verify installed dependencies before integration."
---

# Komponen Vue Koneksa

Baca [AGENTS.md](../../../AGENTS.md) dan
[baseline UI](../../../docs/design-system/BASELINE-DESIGN.md).
Proyek ini Vue 3/Inertia dengan Tailwind v3. Skill lokal ini menggantikan alur
shadcn React upstream untuk implementasi Koneksa.

## Workflow

1. Cari komponen Vue existing di resources/js/Components dan partial fitur.
   Reuse komponen yang sesuai sebelum memperkenalkan toolkit baru.
2. Jika belum ada, evaluasi padanan Vue melalui dokumentasi resmi shadcn-vue
   (https://www.shadcn-vue.com/) dan primitif Vue yang sesuai.
   Verifikasi versi, dukungan Tailwind aktif, alias dan peer dependencies.
3. components.json dan shadcn-vue belum dikonfigurasi di proyek.
   Jelaskan paket/config yang diperlukan; penambahan dependensi mengikuti otorisasi
   AGENTS.md. Jangan menjalankan init/add React shadcn atau memasang React ke Vue.
   Gunakan CLI Vue hanya setelah kompatibilitas dan otorisasi jelas.
4. Pertahankan komponen sebagai .vue, script setup dan token proyek.
   Untuk form gunakan Inertia useForm/pola existing; jangan menyalin react-hook-form.
5. Widget domain sederhana boleh dibuat dengan Vue/native HTML. Untuk dialog/select/
   menu kompleks, prioritaskan primitif aksesibel yang kompatibel daripada membuat
   sendiri focus trapping, roving focus dan keyboard handling.
6. Uji label/error, keyboard/focus restoration, disabled/loading dan responsive
   sesuai perubahan; build tidak menggantikan pemeriksaan interaksi browser.

## Referensi upstream

README, resources/, examples/*.tsx dan scripts/verify-setup.sh pada folder ini
berasal dari skill React upstream dan bukan instruksi instalasi/validasi Vue.
Gunakan katalog sebagai inspirasi fungsi saja; jangan mengeksekusi script React
atau mengimpor contoh TSX pada aplikasi ini. Token/baseline proyek mengungguli contoh upstream.
Jika MCP katalog tidak tersedia, baca dokumentasi resmi; jangan menganggap tool tersedia.
