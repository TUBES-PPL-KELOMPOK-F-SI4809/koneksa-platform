# Contoh Implementasi UI Frontend — Koneksa Platform

Dokumen ini memetakan **contoh implementasi konkret UI frontend** yang tersimpan di direktori [`docs/design-system/examples/`](examples/) sebagai blueprint langsung bagi developer dan AI coding agent saat mengimplementasikan halaman maupun komponen Vue 3 / Inertia / Tailwind v3 pada proyek Koneksa.

> **Status Repositori**: Kode aplikasi aktif di `resources/` dan `routes/web.php` tetap dipertahankan pada baseline awal proyek (Laravel Breeze). Saat mengimplementasikan fitur nyata sesuai PRD, salin atau adaptasi konfigurasi, komponen, layout, dan halaman dari [`docs/design-system/examples/`](examples/) ke lokasi produksi terkait.

---

## 1. Peta Berkas Contoh (`docs/design-system/examples/` → Lokasi Produksi)

### 1.1 Konfigurasi Token, CSS & Blade Root
| Berkas Contoh | Target Produksi | Fungsi & Cakupan |
| :--- | :--- | :--- |
| [`examples/tailwind.config.js`](examples/tailwind.config.js) | `tailwind.config.js` | Definisi token warna (`canvas: #F4F6F9`, `hairline: #E2E8F0`, `primary` Indigo, `secondary` Cyan, `ink`, status semantik, `sidebar`), font `Inter` & `JetBrains Mono`, serta `shadow-xs` / `shadow-paas`. |
| [`examples/css/app.css`](examples/css/app.css) | `resources/css/app.css` | CSS variables `:root` untuk token warna, `--sidebar-*` shadcn, utilitas `.bg-grid-paas`, serta aturan aksesibilitas `prefers-reduced-motion`. |
| [`examples/views/app.blade.php`](examples/views/app.blade.php) | `resources/views/app.blade.php` | Pemuatan font `Inter` & `JetBrains Mono` serta class dasar `bg-canvas font-sans text-ink antialiased`. |

### 1.2 Komponen UI & Primitif `ui/sidebar` shadcn (Adaptasi Vue 3)
| Berkas Contoh | Target Produksi | Fungsi & Cakupan |
| :--- | :--- | :--- |
| [`examples/Components/ui/sidebar/`](examples/Components/ui/sidebar/) | `resources/js/Components/ui/sidebar/` | 16 primitif composable sidebar shadcn/ui untuk Vue 3 (`SidebarProvider`, `Sidebar`, `SidebarHeader`, `SidebarContent`, `SidebarGroup`, `SidebarGroupLabel`, `SidebarGroupAction`, `SidebarGroupContent`, `SidebarMenu`, `SidebarMenuItem`, `SidebarMenuButton`, `SidebarMenuAction`, `SidebarMenuBadge`, `SidebarMenuSub`, `SidebarMenuSubItem`, `SidebarFooter`, `SidebarRail`, `SidebarInset`, `SidebarTrigger`, `useSidebar.js`, `utils.js`). Mendukung collapse ke ikon (`collapsible="icon"`), pintasan `Ctrl/Cmd+B`, dan drawer mobile. |
| [`examples/Components/KoneksaLogo.vue`](examples/Components/KoneksaLogo.vue) | `resources/js/Components/KoneksaLogo.vue` | Logo SVG Koneksa bergradasi Indigo–Cyan dengan dukungan menyembunyikan wordmark saat sidebar terlipat (`:show-wordmark="false"`). |
| [`examples/Components/StatCard.vue`](examples/Components/StatCard.vue) | `resources/js/Components/StatCard.vue` | Kartu KPI bergaya *Modern Bento / Finexy* (`rounded-2xl border border-hairline bg-white p-5 shadow-xs`): judul di kiri atas, ikon lingkaran beraksen di kanan atas, angka `text-2xl` + pill tren/status di tengah, dan teks perbandingan di bawah. |
| [`examples/Components/StatusBadge.vue`](examples/Components/StatusBadge.vue) | `resources/js/Components/StatusBadge.vue` | Pill badge semantik (`rounded-full`) dengan indikator titik (`dot`) untuk `neutral`, `primary`, `secondary`, `success`, `warning`, `danger`, dan `info`. |
| [`examples/Components/EvaluationHeaderBar.vue`](examples/Components/EvaluationHeaderBar.vue) | `resources/js/Components/EvaluationHeaderBar.vue` | Top switcher bar khusus mode pratinjau/evaluasi lintas role. |
| [`examples/Components/PrimaryButton.vue`](examples/Components/PrimaryButton.vue), [`SecondaryButton.vue`](examples/Components/SecondaryButton.vue), [`TextInput.vue`](examples/Components/TextInput.vue), [`InputLabel.vue`](examples/Components/InputLabel.vue) | `resources/js/Components/*.vue` | Komponen form & tombol bawaan Breeze yang telah diselaraskan ke token `primary-600`, `border-hairline`, dan `focus:ring-primary-500`. |

### 1.3 Layout Aplikasi & Autentikasi
| Berkas Contoh | Target Produksi | Fungsi & Cakupan |
| :--- | :--- | :--- |
| [`examples/Layouts/EvaluationPanelLayout.vue`](examples/Layouts/EvaluationPanelLayout.vue) | `resources/js/Layouts/AuthenticatedLayout.vue` (atau layout panel per role) | Shell dashboard utama berbasis `SidebarProvider` + `Sidebar` + `SidebarInset`: berisi pengelompokan menu **Menu** & **Umum**, top utility bar dengan pencarian kapsul (`rounded-full`, pintasan `F`), tombol **Panduan (`?`)** untuk membuka kartu onboarding 3 langkah bagi pengguna awam, serta pill profil pengguna. |
| [`examples/Layouts/GuestLayout.vue`](examples/Layouts/GuestLayout.vue) | `resources/js/Layouts/GuestLayout.vue` | Layout halaman autentikasi bergaya Breeze Colorful (`rounded-xl border border-hairline bg-white p-6 shadow-paas`) dengan aksen garis gradasi Indigo–Cyan di bagian atas kartu. |

### 1.4 Halaman Contoh (Landing, Auth, dan 4 Panel Role)
| Berkas Contoh | Target Produksi | Cakupan Kebutuhan (FR) & Pola UI/UX |
| :--- | :--- | :--- |
| [`examples/Pages/Landing/Index.vue`](examples/Pages/Landing/Index.vue) | `resources/js/Pages/Landing/Index.vue` | Landing page publik: hero dengan pratinjau metrik ekosistem, direktori 4 peran pengguna, dan penjelasan kapabilitas platform. |
| [`examples/Pages/Auth/Login.vue`](examples/Pages/Auth/Login.vue) & [`Register.vue`](examples/Pages/Auth/Register.vue) | `resources/js/Pages/Auth/Login.vue` & `Register.vue` | Halaman masuk & daftar akun dengan pemilih peran interaktif (Pelaku UMKM / Mitra Industri) pada pendaftaran (`FR-AUTH-01`, `FR-AUTH-02`). |
| [`examples/Pages/Evaluasi/UmkmPanel.vue`](examples/Pages/Evaluasi/UmkmPanel.vue) | `resources/js/Pages/Umkm/Dashboard.vue` | **Panel UMKM** (`FR-UMKM-01..06`): 4 `StatCard`, bento 7+5 (*Analitik Omzet Harian* dengan grafik batang kapsul interaktif & floating callout nilai Rupiah + *Tawaran Kerja Sama* dengan rincian lipat & *Status Pendaftaran Program Pemda* yang dapat dibuka/tutup), serta tabel *Daftar Produk & Stok* dengan pencarian `/` dan tombol **`Rincian ▾`** per baris (membuka SKU, batas minimum, dan tombol cepat *Catat Penjualan*). |
| [`examples/Pages/Evaluasi/MitraPanel.vue`](examples/Pages/Evaluasi/MitraPanel.vue) | `resources/js/Pages/Mitra/Dashboard.vue` | **Panel Mitra Industri** (`FR-MITRA-01..04`): pencarian & filter sektor UMKM, kartu katalog UMKM ringkas dengan **`Rincian ▾`** (membuka legalitas NIB & kebutuhan kolaborasi), panel lipat *Fokus Kolaborasi Korporasi*, modal pengajuan kemitraan, dan tabel monitoring. |
| [`examples/Pages/Evaluasi/PemdaPanel.vue`](examples/Pages/Evaluasi/PemdaPanel.vue) | `resources/js/Pages/Pemda/Dashboard.vue` | **Panel Pemerintah Daerah** (`FR-PEMDA-01..04`): bento 5+7 (*Sebaran Sektor UMKM* & *Kuota Program Pembinaan*), serta tabel *Antrean Verifikasi UMKM* 4 kolom inti dengan tombol **`Lihat NIB ▾`** (membuka nama pemilik, sektor, nomor NIB, dan waktu pengajuan). |
| [`examples/Pages/Evaluasi/AdminPanel.vue`](examples/Pages/Evaluasi/AdminPanel.vue) | `resources/js/Pages/Admin/Dashboard.vue` | **Panel Super Admin** (`FR-AUTH-03`, `FR-ADM-01..03`): tabel *Daftar Akun Pengguna* ringkas dengan **`Rincian ▾`** (membuka email & waktu login terakhir), form *Tambah Petugas Pemda*, serta lipatan *Riwayat Log Keamanan*. |

---

## 2. Panduan Pemakaian untuk AI Coding Agent

1. **Jangan Mengubah Contoh Tanpa Permintaan**: Berkas di `docs/design-system/examples/` berfungsi sebagai referensi arsitektur visual dan interaksi. Saat mengerjakan tiket fitur (mis. `FR-UMKM-02` atau `FR-PEMDA-01`), baca berkas contoh yang relevan lalu terapkan polanya pada komponen/halaman di `resources/js/`.
2. **Aktifkan Token Saat Fitur UI Dimulai**: Pastikan token di [`examples/tailwind.config.js`](examples/tailwind.config.js) dan [`examples/css/app.css`](examples/css/app.css) sudah disinkronkan ke `tailwind.config.js` dan `resources/css/app.css` sebelum merender komponen dari `examples/`.
3. **Pertahankan Progressive Disclosure & IxD Ramah Awam**:
   - Jaga tampilan awal kartu dan tabel tetap minimalis (maksimal 4 KPI di baris atas, 4–5 kolom tabel utama).
   - Gunakan pola state `expandedId` / `expandedSku` dengan tombol `Rincian ▾` / `Tutup ▴` (`:aria-expanded="..."`) untuk menyembunyikan informasi teknis sekunder hingga diminta pengguna.
   - Pertahankan istilah Bahasa Indonesia yang ramah pengguna awam dan panduan 3 langkah pada shell aplikasi.

