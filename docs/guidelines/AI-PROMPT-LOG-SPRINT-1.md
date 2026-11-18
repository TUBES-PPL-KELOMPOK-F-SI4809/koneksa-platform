# Log Prompt AI Sprint 1 — KONEKSA Platform
**Integrasi GitHub dan Jira Tracking**

Dokumen ini mencatat seluruh rangkaian instruksi prompt AI per fase yang digunakan selama pengerjaan Sprint 1 KONEKSA Platform, termasuk konvensi Git Workflow, pelacakan tiket Jira, acuan arsitektur sistem, serta validasi skema database terhadap ERD kanonis.

---

## 1. Konteks dan Penanggung Jawab
- **Platform**: KONEKSA (Platform Kolaborasi Terintegrasi UMKM, Mitra Industri, Pemda)
- **Mata Kuliah**: Proyek Perangkat Lunak (PPL) — Kelompok F / SI4809
- **Penanggung Jawab (PIC)**: Nabil Fikry Khaidar
- **Lingkup User Story Sprint 1**:
  - `KSA-10`: Registrasi Akun UMKM dan Mitra Industri (`FR-01.1`)
  - `KSA-11`: Autentikasi Login, Logout, dan Pengelolaan Session (`FR-01.2`)
  - `KSA-12`: Role-Based Access Control (RBAC) dan Resource Ownership (`FR-01.3`)
  - `KSA-13`: Pengelolaan Profil Akun dan Ganti Password (`FR-02`)
  - `KSA-38`: Manajemen Akun Pengguna dan Penyediaan Akun Pemda (`FR-03`)
  - `KSA-148`: Supervisi dan Operasi Administratif Superadmin (`FR-31`)
  - `KSA-152`: Fondasi Audit Trail dan Pelacakan Tindakan Administratif (`FR-32`)
- **Acuan Kebutuhan Kanonis**: Proposal PPL revisi terbaru (13 November 2026, 64 FR / 32 Kelompok Fitur) dan [RANCANGAN_ERD_KONEKSA.md](../analysis/RANCANGAN_ERD_KONEKSA.md).

---

## 2. Aturan Git Workflow dan Pelacakan Jira
1. **Satu Branch per User Story**: Setiap pengerjaan User Story wajib memiliki feature branch khusus, commit dengan prefix issue key, dan Pull Request (PR) tersendiri menuju branch `dev`.
2. **Konvensi Penamaan Branch**: Menggunakan format semantik `feat/<Jira-Key>-<slug>` (contoh: `feat/KSA-10-user-registration`, `feat/KSA-11-auth-session`).
3. **Format Commit**: Memuat Jira issue key, misalnya `feat(auth): [KSA-10][KSA-29] implement user and role model foundation`.
4. **Target PR & Larangan Auto-Merge**: Seluruh PR ditargetkan ke branch integrasi `dev`. Agent dilarang melakukan auto-merge atau auto-deploy.
5. **Pemetaan 7 User Story dan 7 PR**:

| Fase | Jira Story | Subtask Terkait | Branch Feature | Status PR |
|---|---|---|---|---|
| **0. Audit** | Semua | - | `docs/align-erd-and-proposal` | PR Ready |
| **1. Database** | `KSA-10` | `KSA-29` | `feat/KSA-10-user-registration` | Disatukan di Story KSA-10 |
| **2. Registrasi** | `KSA-10` | `KSA-30`, `KSA-31`, `KSA-160` | `feat/KSA-10-user-registration` | PR Ready |
| **2. Login & Session** | `KSA-11` | `KSA-32`, `KSA-33`, `KSA-161` | `feat/KSA-11-auth-session` | PR Ready |
| **3. RBAC & Ownership** | `KSA-12` | `KSA-34`, `KSA-35`, `KSA-162` | `feat/KSA-12-rbac-ownership` | PR Ready |
| **4. Profil & Password** | `KSA-13` | `KSA-36`, `KSA-37`, `KSA-163` | `feat/KSA-13-account-profile` | PR Ready |
| **5. Audit Trail** | `KSA-152` | `KSA-153`, `KSA-154`, `KSA-155` | `feat/KSA-152-audit-trail` | PR Ready (`fe54205`) |
| **6. User Management** | `KSA-38` | `KSA-39`, `KSA-40`, `KSA-164` | `feat/KSA-38-user-management` | PR Ready (`f677499`) |
| **6. Superadmin** | `KSA-148` | `KSA-149`, `KSA-150`, `KSA-151` | `feat/KSA-148-superadmin` | PR Ready (`0b26cab`) |
| **7. Final Testing** | Semua | Integrasi & Regresi | Seluruh Branch (7 Story) | Terverifikasi Lulus |

---

## 3. Log Prompt AI Lengkap per Fase

### Fase 0: Audit Proposal, ERD, Repository dan Git Workflow
```markdown
# Goal
Audit seluruh fondasi Sprint 1 KONEKSA sebelum implementasi, termasuk proposal terbaru, ERD, source code, database, Jira, dan integrasi GitHub.

# Konteks
Penanggung jawab: Nabil Fikry Khaidar.
Jira: KSA-10, KSA-11, KSA-12, KSA-13, KSA-38, KSA-148, KSA-152.

# Scope
1. Inspeksi repository dan identifikasi framework, struktur proyek, dependency, migration, model, controller, route, middleware, frontend, dan testing existing.
2. Temukan proposal terbaru (revisi 13 November 2026 atau yang lebih baru) dan ERD yang saat ini digunakan.
3. Bandingkan proposal, ERD, schema database, implementasi existing, dan acceptance criteria Jira.
4. Identifikasi seluruh aktor, role, permission, ownership, dan kewenangan akses berdasarkan proposal.
5. Susun matriks RBAC berdasarkan role, resource, action, dan scope data.
6. Evaluasi apakah schema existing memerlukan perubahan.
7. Identifikasi potensi konflik, duplikasi schema, dan dependensi antarmodul.
8. Periksa Git workflow existing: branch utama, branch integrasi, konvensi penamaan, commit, PR, dan CI.
9. Periksa apakah Jira telah terintegrasi dengan GitHub dan apakah aktivitas branch/commit/PR dapat ditampilkan pada Development Jira.
10. Susun mapping Story Jira dengan unit implementasi dan branch GitHub.

# Git Workflow
Fase ini read-only. Jangan membuat branch atau commit kosong.

# Out of Scope
Tidak boleh mengubah file, database, Jira, permission, deployment, atau konfigurasi GitHub.

# Definition of Done
Tersedia laporan berisi:
- Dokumen yang berhasil diperiksa.
- Matriks proposal vs ERD vs kode vs Jira.
- Rekomendasi schema dan relasi final.
- Matriks RBAC dan ownership.
- Daftar gap dan risiko.
- Rencana implementasi per Story.
- Konvensi Git yang akan digunakan.
- Status integrasi GitHub dan Jira.
- Keputusan yang memerlukan persetujuan PM.

# Verifikasi
Inspeksi read-only. Jangan mengarang temuan yang tidak didukung file atau konfigurasi.
```

---

### Fase 1: Fondasi Database dan Role
```markdown
# Goal
Implementasikan fondasi database pengguna dan role berdasarkan desain Fase 0 yang telah disetujui PM.

# Jira
Story: KSA-10
Subtask: KSA-29

# Git Workflow
1. Inspeksi konvensi Git dan branch integrasi existing.
2. Buat branch feature untuk KSA-10, contoh feature/KSA-10-user-registration.
3. Pastikan branch berasal dari branch integrasi yang benar.
4. Implementasikan hanya schema dan migration yang sudah disetujui.
5. Jalankan verifikasi.
6. Commit dengan issue key KSA-10.
7. Push branch ke GitHub.
8. Jika pekerjaan KSA-10 belum lengkap, jangan membuat PR yang mengklaim keseluruhan Story selesai. Lanjutkan branch ini pada Fase 2.
9. Jangan merge atau deploy otomatis.

# Scope
- Inspeksi schema, migration, dan model existing.
- Reuse tabel pengguna yang sudah tersedia jika memenuhi requirement.
- Implementasikan perubahan schema dan role yang disetujui.
- Pastikan relasi akun dan profil sesuai desain ERD.
- Tambahkan constraint yang dibutuhkan.
- Tambahkan pengujian integritas data.

# Out of Scope
Form registrasi, login, middleware RBAC, serta fitur modul lain.

# Constraint
Minimal diff. Tidak boleh mengubah schema di luar keputusan PM. Tidak boleh menjalankan migration pada production atau menghapus data.

# Definition of Done
Schema sesuai desain yang disetujui, migration aman pada lingkungan yang diotorisasi, test relevan lulus, dan pekerjaan sudah di-commit serta di-push.

# Verifikasi
Jalankan script validasi database dan test existing yang relevan.
```

---

### Fase 2: Registrasi, Login, Logout dan Session
```markdown
# Goal
Selesaikan registrasi UMKM/Mitra dan autentikasi KONEKSA menggunakan mekanisme existing sesuai desain yang disetujui.

# Jira
KSA-10:
- KSA-30: UI registrasi.
- KSA-31: Validasi dan hashing password.
- KSA-160: Feature test registrasi.

KSA-11:
- KSA-32: Session authentication.
- KSA-33: UI login/logout.
- KSA-161: Feature test authentication.

# Git Workflow
1. Kerjakan KSA-10 terlebih dahulu.
2. Gunakan kembali branch KSA-10 dari Fase 1.
3. Implementasikan registrasi beserta testing.
4. Buat commit dengan prefix KSA-10.
5. Push ke GitHub dan buat PR KSA-10 jika seluruh acceptance criteria telah dipenuhi.
6. Setelah KSA-10 tersedia pada branch integrasi, buat branch baru untuk KSA-11 mengikuti konvensi existing, contoh feature/KSA-11-auth-session.
7. Implementasikan login, logout, session, dan testing.
8. Commit dengan prefix KSA-11, push, dan buat PR khusus KSA-11.
9. Jangan menggabungkan kedua Story menjadi satu PR.
10. Jangan merge tanpa review dan persetujuan sesuai workflow tim.

# Scope KSA-10
- Registrasi publik UMKM dan Mitra.
- Validasi server-side.
- Hashing password.
- Pencegahan registrasi publik role Pemda dan Superadmin.
- UI registrasi.
- Feature test.

# Scope KSA-11
- Login dan logout.
- Session authentication.
- Penanganan kredensial invalid.
- Proteksi pengguna anonim.
- Invalidasi session ketika logout.
- UI login/logout.
- Feature test.

# Constraint
Reuse mekanisme autentikasi existing. Jangan membuat authentication system kedua.
Perubahan perilaku authentication harus sesuai rancangan yang sudah disetujui PM.

# Definition of Done
Registrasi dan autentikasi berjalan sesuai acceptance criteria. Test lulus. Kedua Story memiliki riwayat commit dan PR yang dapat ditelusuri secara terpisah.
```

---

### Fase 3: RBAC dan Ownership
```markdown
# Goal
Implementasikan fondasi RBAC dan resource ownership KONEKSA sesuai matriks permission hasil Fase 0 yang disetujui.

# Jira
Story: KSA-12
Subtask: KSA-34, KSA-35, KSA-162.

# Git Workflow
1. Pastikan dependensi autentikasi tersedia di branch integrasi.
2. Buat branch baru untuk KSA-12 sesuai konvensi existing, contoh feature/KSA-12-rbac-ownership.
3. Implementasikan RBAC dan ownership.
4. Jalankan seluruh test authorization.
5. Commit dengan prefix KSA-12.
6. Push branch ke GitHub.
7. Buat PR KSA-12 menuju branch integrasi.
8. Jangan merge atau deploy otomatis.

# Scope
- Audit route privat existing.
- Middleware pemeriksaan role.
- Resource Policy atau mekanisme authorization existing.
- Proteksi akses pengguna anonim.
- Pembatasan akses berdasarkan role.
- Ownership resource.
- Pembatasan scope Pemda sesuai proposal.
- Feature test lintas role dan kepemilikan.

# Constraint
Gunakan pola authorization native framework atau yang sudah tersedia. Jangan menambahkan library permission atau sistem permission dinamis tanpa kebutuhan yang disetujui.
Seluruh perubahan aturan akses harus mengikuti matriks permission yang telah disetujui PM.

# Definition of Done
- Route privat terlindungi.
- Akses lintas role ditolak sesuai aturan.
- Akses resource milik pengguna lain ditolak.
- Tidak ada privilege escalation melalui input client.
- Test positif dan negatif lulus.
- PR dan commit terhubung dengan KSA-12.
```

---

### Fase 4: Profil Akun dan Ganti Password
```markdown
# Goal
Implementasikan pengelolaan profil akun dan ganti password pengguna KONEKSA.

# Jira
Story: KSA-13
Subtask: KSA-36, KSA-37, KSA-163.

# Git Workflow
1. Inspeksi branch integrasi dan pastikan dependensi authentication/RBAC tersedia.
2. Buat branch KSA-13 sesuai konvensi, contoh feature/KSA-13-account-profile.
3. Implementasikan fitur dan test.
4. Commit dengan prefix KSA-13.
5. Push branch ke GitHub.
6. Buat PR khusus KSA-13.
7. Jangan merge atau deploy otomatis.

# Scope
- Halaman pengaturan profil.
- Perubahan informasi dasar akun yang diizinkan.
- Form perubahan password.
- Verifikasi password lama.
- Hashing password baru.
- Validasi, authorization, dan error handling.
- Feature test.

# Constraint
Pengguna hanya dapat mengubah akun sendiri. Role dan field privileged tidak boleh dapat dimodifikasi melalui endpoint profil.
Gunakan komponen, controller, dan validation existing.

# Definition of Done
Pengguna dapat mengedit profilnya sendiri dan mengganti password dengan aman. Negative case diuji. PR dan commit mengandung KSA-13.
```

---

### Fase 5: Fondasi Audit Trail
```markdown
# Goal
Implementasikan mekanisme audit trail KONEKSA untuk tindakan penting yang tersedia pada Sprint 1.

# Jira
Story: KSA-152
Subtask: KSA-153, KSA-154, KSA-155.

# Git Workflow
1. Pastikan dependensi authentication dan RBAC sudah tersedia.
2. Buat branch KSA-152 sesuai konvensi, contoh feature/KSA-152-audit-trail.
3. Inspeksi logging existing.
4. Implementasikan schema dan mekanisme pencatatan yang telah disetujui PM.
5. Jalankan test dan verifikasi.
6. Commit dengan prefix KSA-152.
7. Push branch dan buat PR KSA-152.
8. Jangan merge atau deploy otomatis.

# Scope
- Audit schema existing.
- Schema audit_logs jika diperlukan dan disetujui.
- Pencatatan actor ID, action, resource, timestamp, dan informasi relevan.
- Integrasi dengan tindakan kritis yang sudah tersedia.
- Halaman penelusuran audit khusus Superadmin.
- Filter audit.
- Automated tests.

# Constraint
Tidak mencatat password, token, atau secret. Tidak menganggap operasi gagal sebagai operasi berhasil.
Jangan mengimplementasikan seluruh modul bisnis hanya untuk menghasilkan audit event.
Tidak boleh mengubah schema tanpa persetujuan PM.

# Definition of Done
Tindakan penting yang tersedia tercatat dan dapat ditelusuri. Akses log dibatasi sesuai RBAC. Test lulus.
```

---

### Fase 6: Manajemen Pengguna dan Superadmin
```markdown
# Goal
Implementasikan pengelolaan pengguna dan fondasi supervisi administratif Superadmin KONEKSA secara terpisah namun terintegrasi.

# Jira
KSA-38:
- KSA-39: Tabel pengguna.
- KSA-40: Pembuatan akun Pemda.
- KSA-164: Feature test.

KSA-148:
- KSA-149: Proteksi dan operasi administratif.
- KSA-150: Panel supervisi.
- KSA-151: Feature test.

# Git Workflow
1. Kerjakan KSA-38 pada branch khusus mengikuti konvensi existing, contoh feature/KSA-38-user-management.
2. Implementasikan daftar pengguna, pengaturan status akun, pembuatan akun Pemda, dan testing.
3. Commit dengan prefix KSA-38.
4. Push dan buat PR KSA-38.
5. Setelah dependensinya tersedia pada branch integrasi, buat branch KSA-148, contoh feature/KSA-148-superadmin.
6. Implementasikan fitur supervisi administratif tanpa menduplikasi fitur KSA-38.
7. Commit dengan prefix KSA-148.
8. Push dan buat PR KSA-148.
9. Jangan merge atau deploy otomatis.

# Scope KSA-38
- Tabel pengguna dan filter status.
- Pengaturan status akun sesuai aturan.
- Pembuatan akun petugas Pemda oleh Superadmin.
- Penetapan kewenangan sesuai matriks.
- Integrasi audit trail.
- Feature test.

# Scope KSA-148
- Proteksi operasi Superadmin.
- Panel supervisi entitas sistem.
- Tindakan administratif lintas modul yang tersedia dan disetujui.
- Pencatatan actor ID.
- Feature test authorization.

# Constraint
Reuse komponen dan mekanisme RBAC serta audit existing. Jangan membangun ulang modul bisnis tim lain.
Perubahan permission harus sesuai persetujuan PM.

# Definition of Done
Fitur manajemen pengguna dan supervisi berjalan sesuai acceptance criteria. Tindakan Superadmin terlindungi dan tercatat. Kedua Story memiliki PR masing-masing.
```

---

### Fase 7: Integration Testing, Final Review dan Jira Tracking
```markdown
# Goal
Verifikasi seluruh implementasi Sprint 1 tanggung jawab Nabil Fikry Khaidar serta pastikan keterlacakan issue Jira dengan aktivitas GitHub.

# Jira
KSA-10, KSA-11, KSA-12, KSA-13, KSA-38, KSA-148, KSA-152.

# Git Workflow
1. Inspeksi seluruh branch dan PR masing-masing Story.
2. Pastikan setiap Story memiliki referensi issue key pada branch, commit, dan PR.
3. Periksa bahwa seluruh commit sudah berhasil di-push.
4. Verifikasi bahwa perubahan yang direview tersedia pada PR yang benar.
5. Periksa apakah Jira Development menampilkan branch, commit, dan PR dari GitHub.
6. Jika aktivitas tidak muncul di Jira, analisis status integrasi dan konfigurasi secara read-only.
7. Jangan mengubah konfigurasi integrasi tanpa persetujuan PM.
8. Untuk bug, lakukan perbaikan di branch Story terkait atau branch fix yang memuat issue key sesuai workflow.
9. Commit, push, dan perbarui PR setelah perbaikan serta pengujian.
10. Jangan merge, deploy, atau mengubah status Jira menjadi Done secara otomatis.

# Scope
- Review diff setiap Story.
- Verifikasi terhadap proposal, ERD, dan acceptance criteria Jira.
- Uji registrasi, login, logout, session, RBAC, ownership, profil, password, manajemen pengguna, Superadmin, dan audit.
- Periksa regresi dan dependensi antarfitur.
- Verifikasi keterlacakan GitHub dan Jira.

# Definition of Done
Setiap Story memiliki bukti pengujian, PR yang dapat direview, dan referensi GitHub yang dapat ditelusuri. Status Jira Development diverifikasi secara nyata.
```

---

## 4. Sinkronisasi Skema Database dengan Update ERD

Berdasarkan analisis komparatif antara migrasi aktual di `database/migrations/` dan [RANCANGAN_ERD_KONEKSA.md](../analysis/RANCANGAN_ERD_KONEKSA.md) per 13 November 2026:

### A. Tabel Baseline yang Sudah Aktif & Kompatibel
1. `users`: Memuat kolom `role` (`umkm`, `mitra`, `pemda`, `admin`), `phone`, `avatar_path`, `account_status` (`enabled`, `disabled`), `must_change_password`, `current_session_id`, serta `deleted_at` (*soft deletes*).
2. `businesses`: Berelasi `1:1` unik ke `users.id` via `user_id`, memuat relasi ke `business_categories.id`.
3. `partner_profiles`: Berelasi `1:1` unik ke `users.id` via `user_id`.
4. Operasional, Transaksi, & Program: `products`, `product_variants`, `sales`, `sale_items`, `stock_movements`, `digital_offerings`, `collaboration_proposals`, `programs`, `program_applications`, dan `verification_requests` seluruhnya sesuai dengan ERD baseline.

### B. Tabel & Migrasi Baru Sprint 1
Sesuai Bagian 4.3 dan 7.2 pada dokumen ERD kanonis, satu-satunya penambahan skema baru untuk **Sprint 1** adalah:
- **Tabel**: `audit_logs` (diimplementasikan pada Story `KSA-152` / `FR-32`)
- **Nama File Migrasi**: `2026_11_18_000001_create_audit_logs_table.php`
- **Atribut**: `id`, `actor_id` (FK `users`, nullable), `actor_role` (VARCHAR 20, nullable), `module` (VARCHAR 50), `action` (VARCHAR 80), `auditable_type` (VARCHAR 100, nullable), `auditable_id` (BIGINT, nullable), `description` (VARCHAR 255), `metadata` (JSON, nullable), `ip_address` (VARCHAR 45, nullable), `created_at` (TIMESTAMP, immutable).
- **Indeks**: `(module, action, created_at)`, `(actor_id, created_at)`, `(auditable_type, auditable_id)`.

Tidak ada perubahan skema destruktif atau pengubahan migrasi lama; seluruh migrasi aditif baru mengikuti aturan **PRD & ERD Kanonis**.
