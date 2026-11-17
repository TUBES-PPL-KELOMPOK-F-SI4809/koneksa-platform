# Baseline Antarmuka Koneksa (Modern Bento Dashboard + shadcn/ui + Breeze Colorful)

Acuan visual dan interaksi kanonis proyek Koneksa untuk developer dan AI coding agent (Vue 3 / Inertia / Tailwind v3).
Ikuti [AGENTS.md](../../AGENTS.md) untuk workflow, [DESIGN-TOKENS.md](DESIGN-TOKENS.md) untuk nilai token, dan [FRONTEND-UI-EXAMPLES.md](FRONTEND-UI-EXAMPLES.md) ([`docs/design-system/examples/`](examples/)) untuk kode contoh implementasi konkret (Landing, Auth, `ui/sidebar`, serta 4 panel role).
Jaga implementasi tetap sederhana (*anti over-engineering*): gunakan komponen Vue dan token Tailwind yang sudah ada tanpa menambah dependensi baru.

## 1. Arah Visual & Karakteristik Modern Bento Dashboard

- **Soft Bento Cards & Clean Hierarchy (Referensi: Finexy Dashboard + shadcn/ui)**:
  - **Kanvas Aplikasi**: `#F4F6F9` / `#F8FAFC` (`bg-canvas`), memberikan kontras lembut terhadap kartu putih bersih.
  - **Kartu & Panel (`rounded-2xl`)**: Permukaan `#FFFFFF`, border halus `1px solid #E2E8F0` (`border-hairline`), `shadow-xs`, dan padding lega (`p-5`).
  - **Palet Warna Tetap Konsisten (Indigo & Cyan)**:
    - **Primary (Indigo)**: `#4F46E5` (`primary-600`), `#6366F1` (`primary-500`), `#EEF2FF` (`primary-50`).
    - **Secondary (Cyan)**: `#06B6D4` (`secondary-500`), `#0891B2` (`secondary-600`), `#ECFEFF` (`secondary-50`).
    - **Identitas Role**: UMKM (`Indigo`), Mitra Industri (`Cyan`), Pemda (`Emerald`), Admin (`Violet`).
    - **Status Semantik**: Emerald (`#10B981`), Amber (`#F59E0B`), Rose (`#E11D48`), Blue (`#0070F3`).

## 2. Tata Letak Shell & Komponen Utama

1. **Top Utility Header (aya Finexy + Breeze)**:
   - Kiri: `SidebarTrigger` + identitas brand/workspace ringkas.
   - Tengah: Input pencarian cepat berbentuk pill (`rounded-full border border-hairline bg-canvas px-4 h-9`) dengan petunjuk `<kbd>/</kbd>`.
   - Kanan: Tombol **Panduan Cepat (`?`)** untuk pengguna baru + pill profil/role pengguna (`rounded-full border border-hairline bg-canvas px-2.5 py-1`).
2. **Composable shadcn-Adapted Sidebar (`resources/js/Components/ui/sidebar/`)**:
   - Tetap menggunakan primitif `SidebarProvider`, `Sidebar` (`collapsible="icon"`, `Ctrl+B`), `SidebarHeader`, `SidebarContent`, `SidebarGroup`, `SidebarMenu`, `SidebarMenuItem`, `SidebarMenuButton`, `SidebarMenuBadge`, `SidebarFooter`, `SidebarInset`, `SidebarTrigger`, dan `SidebarRail`.
   - **Item Menu Aktif**: Berbentuk pill solid berwarna (`bg-primary-600 text-white rounded-xl shadow-xs font-medium`), sedangkan item non-aktif memiliki hover lembut (`hover:bg-neutral-100 rounded-xl`) dan badge angka bulat (`rounded-full`).
3. **Kartu KPI (`StatCard.vue`)**:
   - Baris atas: Label metrik yang mudah dipahami + ikon lingkaran (`h-8 w-8 rounded-full bg-canvas border border-hairline`) di kanan atas.
   - Baris tengah: Nilai utama berukuran besar (`text-2xl font-bold`) berdampingan langsung dengan pill indikator tren/status (`↑ 12,4%` dalam pill `rounded-full`).
   - Baris bawah: 1 baris konteks perbandingan singkat (`Bulan lalu: ...` atau target singkat).
4. **Komposisi Bento 3 Baris**:
   - **Baris 1**: 4 kartu KPI utama (`grid-cols-1 sm:grid-cols-2 xl:grid-cols-4`).
   - **Baris 2**: Grid asimetris (`lg:col-span-7` atau `8` untuk grafik/aktivitas utama + `lg:col-span-5` atau `4` untuk ringkasan sekunder/aksi cepat). Grafik batang menggunakan bentuk kapsul membulat (`rounded-full` / `rounded-xl`) dengan tooltip nilai puncak yang jelas.
   - **Baris 3**: Tabel/daftar utama dengan header berisi pencarian pill (`rounded-full`) dan filter ringkas di kanan atas.

## 3. Prinsip UI/UX & Interaction Design (IxD) untuk Pengguna Awam

1. **Minimalisasi Informasi Awal (*Progressive Disclosure*)**:
   - Tampilkan hanya **3–4 informasi terpenting** pada tampilan awal kartu atau baris tabel agar pengguna awam tidak kewalahan (*cognitive overload*).
   - Informasi tambahan/teknis (seperti nomor NIB/SKU lengkap, rincian legalitas, riwayat waktu, atau opsi lanjutan) wajib disembunyikan di balik tombol **"Rincian ▾"** (*inline expandable panel*) atau modal detail.
2. **Bahasa Manusiawi & Onboarding Kontekstual**:
   - Gunakan istilah sehari-hari yang langsung dimengerti pelaku UMKM/petugas baru (mis. *"Catat Kasir"*, *"Stok Hampir Habis"*, *"Tawaran Kerja Sama"*), bukan kode teknis PRD di judul utama.
   - Sediakan panel **Panduan Langkah Awal** yang dapat dibuka/ditutup dari header (`?`) agar pengguna baru langsung tahu 3 hal pertama yang harus dilakukan.
3. **Alur Aksi 1-Klik & Umpan Balik Instan**:
   - Setiap tugas utama memiliki 1 tombol aksi yang jelas di bagian atas atau di baris item, disertai umpan balik status (*banner/toast*) yang mudah ditutup.
4. **Aksesibilitas & Responsivitas**:
   - Semua input wajib memiliki `<label>`, focus ring `focus-visible:ring-2 focus-visible:ring-primary-500`, kontras minimum `4.5:1`, aman di layar `375px` tanpa overflow horizontal, serta menghormati `prefers-reduced-motion`.
