# Frontend — Vue/Inertia

Ikuti [AGENTS.md](../../AGENTS.md), [baseline UI](../../docs/design-system/BASELINE-DESIGN.md), dan **wajib merujuk [contoh implementasi UI](../../docs/design-system/FRONTEND-UI-EXAMPLES.md)** di direktori `docs/design-system/examples/`. Seluruh agent AI di perangkat developer mana pun memiliki acuan desain yang sama: *Modern Bento Dashboard* dengan primitif `ui/sidebar` shadcn, palet Indigo/Cyan Laravel Breeze, dan prinsip *progressive disclosure* yang ramah pengguna awam.

- **Kepatuhan Alur Implementasi UI**:
  - Sebelum membuat halaman atau komponen baru, **buka dan pelajari blueprint terkait di `docs/design-system/examples/`** (contoh: `EvaluationPanelLayout.vue` untuk shell bento, `Components/ui/sidebar/` untuk navigasi, `StatCard.vue` untuk KPI, `StatusBadge.vue` untuk indikator status, serta halaman role di `Pages/Evaluasi/`).
  - **Prinsip `ponytail` (Minimal Diff)**: Utamakan penggunaan ulang komponen yang sudah ada di `resources/js/Components/` sebelum membuat komponen baru. Hindari styling berlebihan atau dependensi eksternal tanpa kebutuhan nyata.
- **Standar Kode Vue 3 & Inertia v2**:
  - Vue 3 Composition API, `<script setup>`, satu root element untuk komponen Inertia.
  - Halaman di `resources/js/Pages/{Feature}/`, partial fitur di `Partials/`, komponen lintas fitur di `resources/js/Components/`.
  - Gunakan `Link` / named routes dan `useForm` dari `@inertiajs/vue3` untuk pola form proyek. Jangan menduplikasi state atau error handling bawaan Inertia.
- **Pemilihan Komponen & Styling**:
  - Utamakan komponen Vue yang tersedia. Untuk kebutuhan komponen baru, gunakan skill `shadcn-ui` lokal yang telah diadaptasi ke Vue 3. **Dilarang memasang komponen atau CLI React ke dalam aplikasi Vue**.
  - Tailwind v3 adalah styling pipeline aktif. Gunakan scoped CSS / CSS variables untuk kebutuhan styling khusus. `styled-components` belum terintegrasi ke Vue dan bukan standar implementasi.
  - Periksa token desain di `docs/design-system/DESIGN-TOKENS.md` dan pastikan mapping aktual di `tailwind.config.js` sebelum digunakan.
- **Aksesibilitas & User Experience**:
  - Jaga keyboard accessibility, focus state (`focus-visible:ring-primary-500`), kontras teks, label form, error feedback, loading skeleton, empty state, dan `prefers-reduced-motion`.
  - Terapkan *progressive disclosure*: tampilkan 3–4 informasi terpenting di kartu/tabel, simpan detail teknis lanjutan di modal atau expandable panel.
- **Dependensi & Paket Eksternal**:
  - Ikon: selalu verifikasi dependensi aktual di `package.json`. Jangan mengimpor library ikon yang belum terpasang tanpa otorisasi.
