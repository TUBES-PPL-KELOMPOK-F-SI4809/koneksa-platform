# 📚 Koneksa Platform — Dokumentasi Proyek

Selamat datang di pusat dokumentasi resmi **Koneksa Platform** (Proyek Tugas Besar Pengembangan Perangkat Lunak / Tubes PPL). Direktori `docs/` ini adalah sumber kebenaran tunggal (*single source of truth*) untuk seluruh aspek teknis, manajerial, analisis, desain antarmuka, dan arsitektur perangkat lunak.

---

## 🗂 Struktur Direktori Dokumentasi

```text
docs/
├── README.md                          # Indeks & panduan utama dokumentasi
│
├── design-system/                     # Baseline Desain Website & Styling
│   ├── BASELINE-DESIGN.md             # Baseline desain website, filosofi, & panduan skill
│   ├── STYLED-COMPONENTS.md           # Panduan integrasi styled-components & pola pemakaian
│   └── DESIGN-TOKENS.md               # Spesifikasi token desain (warna, tipografi, spacing, dsb.)
│
├── tech-stack/                        # Dokumentasi & Tata Kelola Stack Teknologi
│   ├── CURRENT-STACK.md               # Daftar lengkap stack yang sedang aktif digunakan
│   └── STACK-ADDITION-GUIDELINES.md   # Prosedur (RFC) jika ada penambahan stack/library baru
│
├── PRD/                               # Product Requirement Documents
│   ├── PRD-TEMPLATE.md                # Template standar PRD untuk setiap fitur baru
│   └── PRD-KONEKSA-PLATFORM.md        # PRD tingkat tinggi platform Koneksa (Inisial Baseline)
│
├── analysis/                          # Analisis Sistem & Rekayasa Perangkat Lunak
│   ├── SYSTEM-ANALYST-GUIDELINES.md   # Standar UML, Use Case, Sequence, & Activity Diagram
│   ├── DATABASE-SCHEMA.md             # Dokumentasi baseline skema basis data & konvensi ERD
│   └── API-SPECIFICATION-GUIDE.md     # Standar perancangan API (RESTful, payload, HTTP status)
│
└── guidelines/                        # Aturan Operasional & Panduan Pengembang
    ├── CLEAN-CODE.md                  # Panduan Clean Code & arsitektur anti-regresi
    ├── FEATURE-DEVELOPMENT-GUIDE.md   # SOP pembuatan file/folder fitur, package, & DB schema
    ├── AGENT-UNIT-TESTING.md          # Aturan & alur wajib unit testing bagi AI Agent & Dev
    └── GIT-WORKFLOW.md                # Standar percabangan (branching) & pesan commit Git
```

---

## 🚀 Prinsip Utama Pengembangan

1. **Clean Code & Isolasi Fitur (Anti-Regresi)**:
   - Dilarang menumpuk kode fitur baru ke dalam berkas fitur lain. Gunakan **Thin Controller**, **Dedicated Form Request**, dan **Service Layer** agar penambahan fitur tidak memicu bug pada fitur lain.
   - Baca [CLEAN-CODE.md](file:///home/naabilfikry/Documents/Punya NAFI/KULIAH/MATKUL/PPL/TUBES/koneksa-platform/docs/guidelines/CLEAN-CODE.md) dan [FEATURE-DEVELOPMENT-GUIDE.md](file:///home/naabilfikry/Documents/Punya NAFI/KULIAH/MATKUL/PPL/TUBES/koneksa-platform/docs/guidelines/FEATURE-DEVELOPMENT-GUIDE.md).

2. **Test-Driven & Verifikasi Mandiri**:
   - Setiap fitur yang selesai dikerjakan **WAJIB** disertai unit test yang lulus (green). AI Agent dan developer dilarang menutup tugas sebelum test lulus.
   - Baca [AGENT-UNIT-TESTING.md](file:///home/naabilfikry/Documents/Punya NAFI/KULIAH/MATKUL/PPL/TUBES/koneksa-platform/docs/guidelines/AGENT-UNIT-TESTING.md).

3. **Konsistensi Desain Antarmuka & Prinsip Shadcn-First**:
   - Pembangunan tampilan antarmuka wajib mengacu pada [BASELINE-DESIGN.md](file:///home/naabilfikry/Documents/Punya NAFI/KULIAH/MATKUL/PPL/TUBES/koneksa-platform/docs/design-system/BASELINE-DESIGN.md).
   - **Wajib Shadcn-First**: Selalu cek ketersediaan komponen di `shadcn-ui` sebelum membuat komponen sendiri dari nol. Padukan dengan `styled-components` dan Tailwind CSS sesuai token desain.

4. **Ecosystem-First & CLI-First**:
   - Manfaatkan fungsi, helper, dan komponen yang sudah ada di ekosistem Laravel (`Str`, `Arr`, `Carbon`, `Collection`) dan frontend (`useForm`, `shadcn-ui`) sebelum membuat sendiri.
   - Wajib memaksimalkan CLI bawaan (`php artisan make:*`, `npm`, `npx`) untuk scaffolding dan pengujian.

5. **Tertib Penambahan Stack & Skema Database**:
   - Dilarang mengedit migrasi lama. Penambahan kolom wajib *nullable* atau memiliki *default*.
   - Dilarang menambahkan dependensi/package sembarangan tanpa evaluasi RFC pada [STACK-ADDITION-GUIDELINES.md](file:///home/naabilfikry/Documents/Punya NAFI/KULIAH/MATKUL/PPL/TUBES/koneksa-platform/docs/tech-stack/STACK-ADDITION-GUIDELINES.md).

6. **Keterlacakan Analisis (Traceability)**:
   - Setiap kode yang dibangun harus dapat dirunut balik ke Use Case di [analysis/](file:///home/naabilfikry/Documents/Punya NAFI/KULIAH/MATKUL/PPL/TUBES/koneksa-platform/docs/analysis/) dan kebutuhan pengguna di [PRD/](file:///home/naabilfikry/Documents/Punya NAFI/KULIAH/MATKUL/PPL/TUBES/koneksa-platform/docs/PRD/).

