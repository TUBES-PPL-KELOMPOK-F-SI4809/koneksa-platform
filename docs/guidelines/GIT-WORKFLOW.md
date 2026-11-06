# 🌿 Standar Git Workflow & Kolaborasi Tim — Koneksa Platform

Dokumen ini memuat panduan percabangan (*branching strategy*), konvensi commit, dan alur peninjauan kode (*code review*) bagi tim pengembang Koneksa Platform.

---

## 1. Strategi Percabangan (Branching Strategy)

Kami menggunakan model adaptasi **GitHub Flow** yang terstruktur:

* `main`: Branch produksi stabil. Kode di branch ini selalu siap deploy ke production.
* `dev`: Branch integrasi utama seluruh tim untuk pengembangan fitur harian dan persiapan sprint release.
* `feat/<kode-fitur>-<deskripsi-singkat>`: Branch kerja untuk fitur baru (contoh: `feat/KSA-10-registrasi-multi-role`).
* `fix/<deskripsi-bug>`: Branch perbaikan cacat/bug (contoh: `fix/login-csrf-token`).
* `docs/<nama-dokumen>`: Khusus pembaruan dokumentasi (contoh: `docs/update-prd`).

---

## 2. Standar Pesan Commit (Conventional Commits)

Format penulisan commit wajib menggunakan format:
`<tipe>(<lingkup>): <deskripsi ringkas dalam kalimat aktif>`

### Tipe Commit yang Diizinkan:
* `feat`: Penambahan fitur baru bagi pengguna.
* `fix`: Perbaikan bug atau galat program.
* `docs`: Pembaruan atau penambahan berkas dokumentasi.
* `style`: Penyesuaian tampilan, formatting, titik koma (tidak mengubah logika kode).
* `refactor`: Restrukturisasi kode tanpa mengubah fungsionalitas eksternal.
* `test`: Penambahan atau perbaikan berkas unit/feature test.
* `chore`: Pembaruan dependensi, konfigurasi build, atau skrip internal.

### Contoh:
```bash
git commit -m "feat(auth): tambahkan validasi format email pada pendaftaran"
git commit -m "test(auth): tambahkan unit test untuk skenario password lemah"
git commit -m "docs(design): tambahkan spesifikasi token elevasi dan warna"
```

---

## 3. Checklist Sebelum Membuat Pull Request (PR)

- [ ] Seluruh unit test lokal lulus (`php artisan test --testsuite=Unit`).
- [ ] Kode lolos verifikasi standar format Laravel Pint (`vendor/bin/pint --test`).
- [ ] Aset frontend berhasil di-build tanpa galat (`npm run build`).
- [ ] Kode bersih dari `dd()`, `dump()`, atau `console.log()` sementara.
- [ ] Dokumentasi terkait (PRD / ERD / Tech Stack) telah diperbarui jika ada perubahan skema atau dependensi.
- [ ] Tidak ada konflik (*merge conflict*) dengan branch target (`dev`/`main`).

---

## 4. Otomatisasi CI / Code Quality Gate (GitHub Actions)

Setiap Pull Request dan Push yang menargetkan branch `dev` maupun `main` akan secara otomatis memicu alur kerja pengujian di GitHub Actions (`.github/workflows/ci.yml`):
1. **Code Style (Laravel Pint)**: Memverifikasi kode PHP mematuhi standar PSR-12 / Laravel Code Style (`vendor/bin/pint --test`).
2. **Frontend Build (Vite & Vue)**: Menguji integritas kompilasi dan bundling frontend (`npm ci` & `npm run build`).
3. **Backend Tests (PHPUnit & MySQL)**: Menjalankan seluruh Unit Test dan Feature Test menggunakan service container database MySQL terisolasi.

> **Catatan**: Status seluruh pengecekan di atas **WAJIB GREEN (PASS)** sebelum sebuah Pull Request diizinkan untuk di-merge ke branch `dev` maupun `main`.

