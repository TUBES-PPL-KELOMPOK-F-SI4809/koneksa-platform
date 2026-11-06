---
description: "Aturan wajib pemeriksaan ketersediaan komponen UI di library shadcn-ui sebelum membuat komponen sendiri"
globs: ["resources/js/**/*.vue", "resources/js/**/*.js", "resources/js/**/*.jsx", "resources/js/**/*.tsx", "resources/css/**/*.css"]
always_on: true
---

# Aturan Wajib: Prioritaskan Komponen `shadcn-ui` (Shadcn-First Rule)

Sebelum membuat atau merancang komponen UI baru:

1. **Wajib Cek Katalog `shadcn-ui`**:
   - Selalu periksa apakah komponen yang dibutuhkan (misal: Button, Dialog, Modal, Dropdown, Table, Input, Popover, Select, Accordion, Sheet, Card, Toast, dll.) sudah tersedia di `shadcn-ui`.
   - Gunakan skill `shadcn-ui` (`.agents/skills/shadcn-ui/`) untuk mencari metadata, demo, dan cara instalasinya.

2. **Gunakan atau Adaptasi dari `shadcn-ui`**:
   - Pasang atau salin komponen dari `shadcn-ui` (`npx shadcn@latest add <component-name>` atau manual integration).
   - Sesuaikan tampilan (*styling*) menggunakan token desain Koneksa di `docs/design-system/DESIGN-TOKENS.md` dan `styled-components` / Tailwind CSS.

3. **Larangan Membuat dari Nol Tanpa Alasan**:
   - Dilarang membuat komponen interaktif kompleks dari nol jika `shadcn-ui` telah menyediakannya.
   - Pembuatan dari nol hanya diperbolehkan jika komponen tersebut benar-benar belum tersedia di katalog `shadcn-ui` atau merupakan widget domain yang sangat spesifik.

