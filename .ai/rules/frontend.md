# Frontend Rules (Vue 3, Inertia.js v2, Tailwind CSS, styled-components)

1. **Struktur Halaman**:
   - Berkas Vue ditempatkan di `resources/js/Pages/{Feature}/`.
   - Pecah komponen panjang ke `resources/js/Pages/{Feature}/Partials/`.
2. **Anti Over-Engineering (KISS & YAGNI)**:
   - Hindari over-engineering pada state management: Gunakan Inertia props dan `useForm` bawaan. Jangan menambah global store rumit jika data flow lokal sudah cukup.
   - Pilih solusi UI dan interaksi paling sederhana dan mudah dipelihara.
3. **Cek Dependensi Terpasang Terlebih Dahulu**:
   - Cek `package.json` sebelum menginstal package NPM baru atau membuat utilitas JavaScript custom.
   - Manfaatkan library ikon (Lucide Icons) yang sudah ada daripada menggambar SVG kustom.
4. **Shadcn-First**:
   - Selalu periksa ketersediaan komponen di skill `shadcn-ui` (`.agents/skills/shadcn-ui/SKILL.md`) sebelum membuat komponen UI dari nol.
5. **Desain & Styling**:
   - Rujuk token di `docs/design-system/DESIGN-TOKENS.md` dan filosofi di `docs/design-system/BASELINE-DESIGN.md`.
   - Hindari magic numbers / hardcoded colors.
   - Aktifkan `.agents/skills/ui-ux-pro-max/SKILL.md` dan `.agents/skills/taste-design/SKILL.md`.
6. **Form Handling**:
   - Wajib gunakan `useForm` dari `@inertiajs/vue3`.

