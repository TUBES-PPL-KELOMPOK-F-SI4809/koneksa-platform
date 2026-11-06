# 🎯 Spesifikasi Design Tokens — Koneksa Platform

Design tokens adalah variabel atomik visual terkecil yang digunakan untuk menjamin konsistensi di seluruh tampilan antarmuka Koneksa Platform.

---

## 1. Token Warna (Color Tokens)

### 1.1 Brand Primary (Indigo)
* `color.primary.50`: `#EEF2FF`
* `color.primary.100`: `#E0E7FF`
* `color.primary.200`: `#C7D2FE`
* `color.primary.300`: `#A5B4FC`
* `color.primary.400`: `#818CF8`
* `color.primary.500`: `#6366F1`
* `color.primary.600` *(Base Primary)*: `#4F46E5`
* `color.primary.700`: `#4338CA`
* `color.primary.800`: `#3730A3`
* `color.primary.900`: `#312E81`

### 1.2 Brand Secondary / Accent (Cyan / Sky)
* `color.secondary.50`: `#ECFEFF`
* `color.secondary.100`: `#CFFAFE`
* `color.secondary.500` *(Base Accent)*: `#06B6D4`
* `color.secondary.600`: `#0891B2`
* `color.secondary.700`: `#0E7490`

### 1.3 Neutrals & Greys (Slate)
* `color.neutral.50`: `#F8FAFC` *(Kanvas background terang)*
* `color.neutral.100`: `#F1F5F9` *(Surface card / muted container)*
* `color.neutral.200`: `#E2E8F0` *(Border & divider)*
* `color.neutral.300`: `#CBD5E1`
* `color.neutral.400`: `#94A3B8` *(Placeholder teks)*
* `color.neutral.500`: `#64748B` *(Teks sekunder / muted)*
* `color.neutral.700`: `#334155` *(Teks label)*
* `color.neutral.800`: `#1E293B` *(Card dark / teks kontras)*
* `color.neutral.900`: `#0F172A` *(Heading utama / background dark)*

### 1.4 Semantic Feedback
* **Success**:
  * Light: `#ECFDF5`
  * Base: `#10B981` (Emerald)
  * Dark: `#047857`
* **Warning**:
  * Light: `#FFFBEB`
  * Base: `#F59E0B` (Amber)
  * Dark: `#B45309`
* **Danger**:
  * Light: `#FEF2F2`
  * Base: `#EF4444` (Rose/Red)
  * Dark: `#B91C1C`
* **Info**:
  * Light: `#EFF6FF`
  * Base: `#3B82F6` (Blue)
  * Dark: `#1D4ED8`

---

## 2. Token Tipografi (Typography Tokens)

* **Font Family**:
  * `font.sans`: `'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif`
  * `font.mono`: `'JetBrains Mono', 'Fira Code', monospace`

* **Ukuran Font & Line Height**:
  | Token | Font Size | Line Height | Tracking | Penggunaan |
  | :--- | :--- | :--- | :--- | :--- |
  | `text.xs` | `0.75rem (12px)` | `1rem (16px)` | `0.025em` | Badges, metadata, timestamp |
  | `text.sm` | `0.875rem (14px)` | `1.25rem (20px)` | `0` | Form labels, table cells, secondary text |
  | `text.base` | `1rem (16px)` | `1.5rem (24px)` | `0` | Body paragraph, default inputs |
  | `text.lg` | `1.125rem (18px)` | `1.75rem (28px)` | `-0.01em` | Subheadings, card titles |
  | `text.xl` | `1.25rem (20px)` | `1.75rem (28px)` | `-0.015em` | Modal titles, Section headers (H3) |
  | `text.2xl` | `1.5rem (24px)` | `2rem (32px)` | `-0.02em` | Page section titles (H2) |
  | `text.3xl` | `1.875rem (30px)` | `2.25rem (36px)` | `-0.025em` | Main page titles (H1) |
  | `text.4xl` | `2.25rem (36px)` | `2.5rem (40px)` | `-0.03em` | Hero displays |

---

## 3. Token Elevasi & Bayangan (Shadows & Elevation)

* `elevation.none`: `none`
* `elevation.sm`: `0 1px 2px 0 rgb(0 0 0 / 0.05)` (Input fields, subtle cards)
* `elevation.base`: `0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1)`
* `elevation.md`: `0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1)` (Cards hover, dropdowns)
* `elevation.lg`: `0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1)` (Popovers, floating bars)
* `elevation.xl`: `0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1)` (Modals, dialogs)

---

## 4. Token Radius Sudut (Border Radius)

* `radius.none`: `0px`
* `radius.sm`: `4px` (Tags, chips kecil)
* `radius.md`: `8px` (Inputs, tombol standar)
* `radius.lg`: `12px` (Cards, panels)
* `radius.xl`: `16px` (Modals, banners)
* `radius.full`: `9999px` (Avatars, pills, circular icon buttons)

---

## 5. Token Z-Index (Layering Hierarchy)

* `z.hide`: `-1`
* `z.base`: `0`
* `z.docked`: `10` (Sticky elements)
* `z.dropdown`: `1000` (Dropdown menus, autocomplete)
* `z.sticky`: `1100` (Sticky navbar)
* `z.overlay`: `1200` (Modal backdrop)
* `z.modal`: `1300` (Modal dialog)
* `z.toast`: `1400` (Notification toasts, alerts)
* `z.tooltip`: `1500` (Tooltips)

