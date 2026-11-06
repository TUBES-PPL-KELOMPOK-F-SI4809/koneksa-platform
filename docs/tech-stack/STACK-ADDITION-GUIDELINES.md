# 📋 Panduan Penambahan Tech Stack & Dependensi Baru

Dokumen ini mengatur tata cara, kriteria evaluasi, dan protokol dokumentasi apabila tim pengembang atau AI Coding Agent hendak menambahkan pustaka (*library*), paket (*package*), atau teknologi baru (*stack*) ke dalam repositori **Koneksa Platform**.

---

## 1. Prinsip Penambahan Stack

Setiap penambahan dependensi membawa biaya jangka panjang (*maintenance cost*, potensi kerentanan keamanan, ukuran *bundle*, dan waktu kompilasi). Oleh karena itu, prinsip berikut wajib dipenuhi:

1. **Prinsip Kebutuhan Nyata (Real Necessity)**:
   - Apakah masalah dapat diselesaikan dengan pustaka bawaan framework (Laravel / Vue / Tailwind / styled-components)?
   - Jika ya, **utamakan implementasi bawaan** tanpa menambah package pihak ketiga.
2. **Kesehatan & Reputasi Dependensi**:
   - Pustaka harus aktif dirawat (update terakhir < 6 bulan).
   - Memiliki lisensi ramah open-source (MIT, Apache 2.0, BSD).
   - Memiliki riwayat keamanan yang baik (`npm audit` atau `composer audit` bebas dari kerentanan kritis).
3. **Dampak Performa & Ukuran Bundle**:
   - Untuk paket frontend (npm): periksa ukuran di `bundlephobia.com`. Hindari pustaka berbobot besar (> 50KB gzip) untuk fungsi sepele.
   - Untuk paket backend (composer): pastikan kompatibel dengan versi PHP 8.5 dan Laravel 13.

---

## 2. Alur Pengajuan Stack Baru (RFC Workflow)

```text
1. Identifikasi Kebutuhan ──► 2. Evaluasi Alternatif ──► 3. Buat Catatan RFC
                                                               │
                                                               ▼
6. Update Dokumentasi    ◄── 5. Jalankan Unit Test  ◄── 4. Instalasi Paket
   (CURRENT-STACK.md)
```

### Langkah-langkah:
1. **Buat Usulan (RFC)**: Isi template pengajuan pada Bagian 4 dokumen ini.
2. **Instalasi Formal**:
   - Backend: `composer require vendor/package-name` (atau `--dev` jika hanya untuk testing/dev).
   - Frontend: `npm install package-name` (atau `--save-dev`).
3. **Audit Keamanan**:
   - Jalankan `composer audit` atau `npm audit`.
4. **Verifikasi Unit Test**:
   - Tulis unit test untuk memvalidasi fungsi yang memanfaatkan pustaka baru tersebut.
5. **Pembaruan Berkas Dokumentasi**:
   - Catat paket baru di tabel riwayat di bawah ini.
   - Perbarui berkas [CURRENT-STACK.md](CURRENT-STACK.md).

---

## 3. Log Riwayat Penambahan Stack (Tech Stack Log)

Setiap penambahan paket wajib dicatat dalam tabel ini:

| Tanggal | Nama Paket / Stack | Versi | Kategori | Alasan & Penggunaan | Pengusul | Status |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| *Inisial* | `laravel/framework` | `13.x` | Backend Core | Framework utama aplikasi web | Tim PPL | Disetujui |
| *Inisial* | `@inertiajs/vue3` | `^2.0.0` | Frontend Adapter | Jembatan monolith SPA modern | Tim PPL | Disetujui |
| *Inisial* | `tailwindcss` | `^3.x / ^4.x`| Styling | Utility layouting & atomic styling | Tim PPL | Disetujui |
| *Inisial* | `styled-components` | `^6.6.0` | Styling | CSS-in-JS untuk komponen dinamis ber-props | User | Disetujui |
| *Inisial* | `.agents/skills/*` | `v1.0` | Agent Tooling | Skill sistem desain & pengujian otomatis | Tim PPL | Disetujui |

---

## 4. Template Formulir Pengajuan (RFC Template)

Salin template di bawah ini saat mendokumentasikan penambahan dependensi baru:

```markdown
### RFC-XXX: [Nama Pustaka / Stack yang Diajukan]

- **Nama Paket**: `vendor/package` atau `package-name`
- **Tipe Dependensi**: `dependencies` / `devDependencies` / `composer require`
- **Versi**: `x.y.z`
- **Lisensi**: MIT / Apache-2.0 / BSD

#### 1. Masalah yang Diselesaikan:
[Jelaskan kebutuhan fungsional yang memerlukan pustaka ini]

#### 2. Alternatif yang Telah Dipertimbangkan:
- Alternatif A: [Alasan mengapa ditolak]
- Alternatif B: [Alasan mengapa ditolak]

#### 3. Dampak Keamanan & Performa:
- Hasil audit (`npm audit` / `composer audit`): Clean / 0 vulnerabilities
- Ukuran bundle (jika frontend): ~XX KB gzipped
- Kompatibilitas versi PHP / Node: Kompatibel dengan PHP 8.5+ & Node 20+

#### 4. Dampak Pengujian (Testing Strategy):
[Rencana unit test untuk menguji integrasi modul ini]
```

