# Spesifikasi Design Tokens — Koneksa Platform (Modern Bento + shadcn/ui + Breeze Colorful)

Dokumen ini adalah spesifikasi visual kanonis untuk seluruh developer dan AI coding agent.
Periksa `resources/css/app.css` dan `tailwind.config.js` sebelum memakai token, dan rujuk konfigurasi referensi siap pakai di [`examples/tailwind.config.js`](examples/tailwind.config.js) serta [`examples/css/app.css`](examples/css/app.css) (lihat [FRONTEND-UI-EXAMPLES.md](FRONTEND-UI-EXAMPLES.md)).

---

## 1. Token Warna (Indigo & Cyan pada Kanvas Bento Lembut)

### 1.1 Permukaan & Netral (Soft Slate Canvas)
* `color.surface.white`: `#FFFFFF` *(Kartu bento utama, sidebar, header, modal)*
* `color.surface.canvas` (`neutral.50`): `#F4F6F9` *(Kanvas background aplikasi yang lembut)*
* `color.surface.muted` (`neutral.100`): `#F1F5F9` *(Permukaan sekunder, kotak detail yang di-extend, pill input)*
* `color.border.hairline` (`neutral.200`): `#E2E8F0` *(Border 1px kartu, divider, tabel, input)*
* `color.border.strong` (`neutral.300`): `#CBD5E1` *(Border hover, outline tombol sekunder)*
* `color.text.placeholder` (`neutral.400`): `#94A3B8` *(Placeholder input, ikon nonaktif)*
* `color.text.muted` (`neutral.500`): `#64748B` *(Sub-label perbandingan, timestamp, shortcut hint)*
* `color.text.secondary` (`neutral.600`): `#475569` *(Label kartu KPI, deskripsi sekunder, navigasi)*
* `color.text.label` (`neutral.700`): `#334155` *(Label form, sub-heading)*
* `color.ink.900` (`ink.DEFAULT`): `#0F172A` *(Heading utama, angka KPI, teks kontras tinggi)*
* `color.ink.950` (`ink.deep`): `#020617` *(Top bar evaluasi)*

### 1.2 Primary Brand & Aksi Utama (Indigo)
* `primary.50`: `#EEF2FF` *(Background pill aktif sekunder, highlight lembut)*
* `primary.100`: `#E0E7FF` *(Border pill aktif)*
* `primary.200`: `#C7D2FE`
* `primary.500`: `#6366F1` *(Focus ring input/button, hover tombol utama)*
* `primary.600`: `#4F46E5` *(Item aktif sidebar, `PrimaryButton`, batang grafik utama)*
* `primary.700`: `#4338CA` *(Teks badge primary, active state tombol)*

### 1.3 Secondary & Role Accents (Cyan & Ekosistem)
* `secondary.50`: `#ECFEFF` *(Highlight lembut Mitra/Kemitraan)*
* `secondary.100`: `#CFFAFE`
* `secondary.500`: `#06B6D4` *(Aksen Cyan Koneksa, grafik sekunder)*
* `secondary.600`: `#0891B2` *(Identitas role Mitra Industri)*
* `secondary.700`: `#0E7490`
* `accent.500`: `#0070F3` *(Status info & link interaktif)*

### 1.4 Semantic Feedback (Pill Trend & Status)
* **Success (Naik / Aman / Terverifikasi)**:
  * Light: `#ECFDF5` | Base: `#10B981` | Dark: `#047857`
* **Warning (Menipis / Menunggu Review)**:
  * Light: `#FFFBEB` | Base: `#F59E0B` | Dark: `#B45309`
* **Danger (Turun / Habis / Ditolak)**:
  * Light: `#FFF1F2` | Base: `#E11D48` | Dark: `#BE123C`
* **Info (Baru / Sistem)**:
  * Light: `#EFF6FF` | Base: `#0070F3` | Dark: `#1D4ED8`

### 1.5 Sidebar Tokens (`resources/css/app.css`)
* `--sidebar`: `#FFFFFF`
* `--sidebar-foreground`: `#0F172A`
* `--sidebar-primary`: `#4F46E5`
* `--sidebar-primary-foreground`: `#FFFFFF`
* `--sidebar-accent`: `#EEF2FF`
* `--sidebar-accent-foreground`: `#4338CA`
* `--sidebar-border`: `#E2E8F0`
* `--sidebar-ring`: `#6366F1`

---

## 2. Token Tipografi

* **Font Family**:
  * `font.sans`: `'Inter', 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif`
  * `font.mono`: `'JetBrains Mono', 'Fira Code', ui-monospace, SFMono-Regular, monospace`

* **Skala Ukuran Font**:
  | Token | Ukuran | Penggunaan |
  | :--- | :--- | :--- |
  | `text.xs` (`12px`) | `0.75rem` | Pill status, perbandingan bulan lalu, header tabel |
  | `text.sm` (`14px`) | `0.875rem` | Menu sidebar, tombol, sel tabel utama |
  | `text.base` (`16px`) | `1rem` | Judul kartu bento, deskripsi utama |
  | `text.xl` (`20px`) | `1.25rem` | Judul halaman panel (`Sales Overview` style) |
  | `text.2xl` (`24px`) | `1.5rem` | Angka KPI utama pada `StatCard` |

---

## 3. Token Elevasi, Border & Radius (Bento Style)

* **Border Default**: `1px solid #E2E8F0` (`border-hairline`).
* **Elevasi**:
  * `shadow-xs`: `0 1px 3px 0 rgb(15 23 42 / 0.04)` (kartu bento, tombol, pill header)
  * `shadow-paas`: `0 6px 16px -4px rgb(15 23 42 / 0.07)` (hover card, popover, modal)
* **Radius Sudut**:
  * `rounded-lg` (`8px`): Input form, kotak rincian dalam kartu
  * `rounded-xl` (`12px`): Item menu sidebar aktif, tombol aksi, baris item
  * `rounded-2xl` (`16px`): Kartu KPI (`StatCard`), kartu grafik, kontainer tabel utama
  * `rounded-full` (`9999px`): Input pencarian topbar, filter periode, pill persentase KPI, avatar
