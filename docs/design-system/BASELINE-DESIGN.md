# 🎨 Baseline Desain Website — Koneksa Platform

Dokumen ini merupakan panduan acuan (*baseline design system*) untuk perancangan dan implementasi antarmuka web Koneksa Platform. Panduan ini disintesis berdasarkan setup keahlian desain agen (`taste-design`, `ui-ux-pro-max`, `shadcn-ui`, `stitch-*`) dan integrasi `styled-components` dengan Tailwind CSS.

---

## 1. Filosofi & Karakter Visual (Brand Identity)

**Koneksa Platform** adalah wadah kolaboratif dan konektivitas digital modern. Karakter antarmuka yang diusung harus mencerminkan nilai-nilai berikut:

1. **Anti-Generic & Purpose-Driven (Taste Design)**:
   - Hindari tampilan template korporat yang kaku atau kosong tanpa hirarki.
   - Gunakan tipografi yang tegas, ritme spasial yang presisi, dan aksen warna terkalibrasi.
2. **Kejelasan Fungsional (Clear Usability & UI-UX Pro Max)**:
   - Setiap elemen memiliki intensi interaksi yang jelas (*affordance*).
   - *State* interaktif (hover, active, focus-visible, disabled, loading) wajib memiliki umpan balik visual instan.
3. **Aksesibilitas Universal (WCAG 2.1 AA Compliance)**:
   - Rasio kontras teks terhadap latar belakang minimal **4.5:1** untuk teks reguler dan **3:1** untuk teks besar/elemen UI non-teks.
   - Dukungan navigasi keyboard penuh (focus ring terlihat jelas, tidak boleh dihilangkan tanpa pengganti).

---

## 2. Palet Warna Terkalibrasi (Calibrated Color System)

Sistem warna Koneksa menggunakan model token semantik untuk mendukung konsistensi tema:

| Kategori | Token | Kode Hex | Penggunaan Utama |
| :--- | :--- | :--- | :--- |
| **Brand Primary** | `color-primary-600` | `#4F46E5` (Indigo) | Warna aksen utama, tombol primer, link aktif |
| **Brand Primary Hover**| `color-primary-700` | `#4338CA` | Hover state tombol primer |
| **Brand Secondary** | `color-secondary-500` | `#06B6D4` (Cyan) | Aksen sekunder, badge fitur, visual highlight |
| **Neutral Base** | `color-bg-base` | `#FFFFFF` / `#0F172A` | Background kanvas (Light / Dark) |
| **Neutral Surface** | `color-bg-surface` | `#F8FAFC` / `#1E293B` | Background card, container, form panel |
| **Text Primary** | `color-text-main` | `#0F172A` / `#F8FAFC` | Judul, heading, teks konten utama |
| **Text Muted** | `color-text-muted` | `#64748B` / `#94A3B8` | Label pendukung, timestamp, placeholder |
| **Semantic Success** | `color-success-500` | `#10B981` (Emerald) | Notifikasi berhasil, indikator aktif |
| **Semantic Warning** | `color-warning-500` | `#F59E0B` (Amber) | Peringatan batas waktu, review pending |
| **Semantic Danger** | `color-danger-500` | `#EF4444` (Rose) | Aksi destruktif, pesan validasi error |

> Rincian kode hex dan varian tints lengkap dapat dilihat di [DESIGN-TOKENS.md](file:///home/naabilfikry/Documents/Punya NAFI/KULIAH/MATKUL/PPL/TUBES/koneksa-platform/docs/design-system/DESIGN-TOKENS.md).

---

## 3. Tipografi & Skala Teks (Typography Scale)

* **Font Utama (Primary Sans)**: `Plus Jakarta Sans` / `Inter`, dengan fallback `system-ui, -apple-system, sans-serif`.
* **Font Monospace**: `JetBrains Mono` / `Fira Code` (untuk kode, token, atau angka tabular).

### Skala Hirarki:
* **Display / Hero**: `36px` - `48px` (Font-weight: 700 / 800, Line-height: 1.15)
* **Heading 1 (H1)**: `30px` (Font-weight: 700, Line-height: 1.25)
* **Heading 2 (H2)**: `24px` (Font-weight: 600, Line-height: 1.3)
* **Heading 3 (H3)**: `20px` (Font-weight: 600, Line-height: 1.4)
* **Body Regular**: `16px` (Font-weight: 400 / 500, Line-height: 1.5)
* **Body Small / Caption**: `14px` (Font-weight: 400 / 500, Line-height: 1.5)
* **Micro / Badge**: `12px` (Font-weight: 600, Line-height: 1.4)

---

## 4. Sistem Grid & Spasi (Spacing & Layout Grid)

1. **Skala Spasi Berbasis 4px / 8px Grid**:
   - `space-1` = `4px`
   - `space-2` = `8px`
   - `space-3` = `12px`
   - `space-4` = `16px` (Default padding komponen)
   - `space-6` = `24px` (Default gap antar card)
   - `space-8` = `32px` (Section padding)
   - `space-12` = `48px` (Container vertical rhythm)
2. **Breakpoints Responsif**:
   - `sm`: `640px` (Ponsel landscape / tablet kecil)
   - `md`: `768px` (Tablet)
   - `lg`: `1024px` (Laptop standar)
   - `xl`: `1280px` (Desktop)
   - `2xl`: `1536px` (Widescreen)
3. **Maksimal Lebar Konten**:
   - Konten terpusat dengan `max-w-7xl` (`1280px`) dan margin otomatis `mx-auto px-4 sm:px-6 lg:px-8`.

---

## 5. Komposisi Komponen: `shadcn-ui` + `styled-components` + Tailwind CSS

Arsitektur antarmuka Koneksa menganut prinsip **"Shadcn-First"**:

> **ATURAN WAJIB**: Selalu periksa ketersediaan komponen di katalog **`shadcn-ui`** sebelum membuat komponen baru secara manual dari nol. Jangan *reinvent the wheel* untuk komponen interaktif dan aksesibel (Dialog, Dropdown, Table, Popover, Select, Sheet, Accordion, dll).

Pola integrasi antarmuka:
```text
┌────────────────────────────────────────────────────────────────────────┐
│                        Katalog shadcn-ui                               │
│      (Fondasi struktur aksesibel & interaktif: Radix / Base UI)        │
└───────────────────────────────────▲────────────────────────────────────┘
                                    │ Adaptasi & Customization
┌───────────────────────────────────▼────────────────────────────────────┐
│                    Arsitektur Komponen Koneksa                         │
├───────────────────────────┬────────────────────────────────────────────┤
│       Tailwind CSS        │             styled-components              │
│  (Struktur & Tata Letak)  │           (Komponen Dinamis UI)            │
├───────────────────────────┼────────────────────────────────────────────┤
│ • Layouting (Flex, Grid)  │ • Isolated custom widgets & styled wrapper │
│ • Container & Spacing     │ • Complex dynamic prop variations          │
│ • Responsive scaffolding  │ • State-driven animations & micro-motion   │
│ • Utility overrides       │ • Encapsulated Design Tokens consumption   │
└───────────────────────────┴────────────────────────────────────────────┘
```

Untuk petunjuk penulisan dan integrasi kode, pelajari [STYLED-COMPONENTS.md](file:///home/naabilfikry/Documents/Punya NAFI/KULIAH/MATKUL/PPL/TUBES/koneksa-platform/docs/design-system/STYLED-COMPONENTS.md).

---

## 6. Mikro-Interaksi & Transisi (Motion Principles)

Sesuai panduan `taste-design` dan `ui-ux-pro-max`:
* **Durasi Transisi**: `150ms` hingga `250ms` untuk interaksi mikro (hover, focus, toggles).
* **Easing Function**: `cubic-bezier(0.4, 0, 0.2, 1)` (Material standard ease-in-out).
* **Hover State**: Elevasi halus (shadow lembut) dan perubahan warna kontras, bukan transform zoom berlebihan.
* **Loading State**: Gunakan skeleton loader beranimasi shimmering daripada spinner kosong saat memuat data card/tabel.
* **Respect Reduced Motion**: Selalu sediakan fallback `@media (prefers-reduced-motion: reduce)` tanpa transisi animasi yang bergerak cepat.

---

## 7. Panduan Kerja Sama Tim Desain & Agen (Skills Workflow)

Dalam mengimplementasikan antarmuka baru:
1. **Rancang Ide / Layar Baru**:
   - Aktifkan skill `enhance-prompt` untuk mematangkan prompt antarmuka.
   - Gunakan skill `taste-design` untuk memastikan visual bebas dari klise template membosankan.
2. **Verifikasi Pola Desain**:
   - Terapkan standar dari skill `ui-ux-pro-max` untuk navigasi, tabel, form, modal, dan pesan kesalahan.
   - Bila mengadopsi komponen modular, rujuk skill `shadcn-ui`.
3. **Ekstraksi & Uji Desain**:
   - Gunakan skill `design-md` dan `stitch-code-to-design` jika ingin menyinkronkan aset desain dengan Stitch.
   - Lakukan uji tampilan interaktif menggunakan skill `playwright`.

