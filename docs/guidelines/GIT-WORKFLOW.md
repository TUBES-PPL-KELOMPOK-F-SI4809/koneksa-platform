# 🌿 Standar Git Workflow & Kolaborasi Tim — Koneksa Platform

Dokumen ini memuat panduan percabangan (*branching strategy*), konvensi commit, dan alur peninjauan kode (*code review*) bagi tim pengembang Koneksa Platform.

---

## 1. Strategi Percabangan (Branching Strategy)

Kami menggunakan model adaptasi **GitHub Flow** yang terstruktur:

* `main`: Branch produksi stabil. Kode di branch ini selalu siap deploy.
* `staging` / `develop`: Branch integrasi bersama untuk persiapan rilis sprint.
* `feature/<kode-fitur>-<deskripsi-singkat>`: Branch kerja untuk fitur baru (contoh: `feature/MOD-01-auth-page`).
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
- [ ] Kode bersih dari `dd()`, `dump()`, atau `console.log()` sementara.
- [ ] Dokumentasi terkait (PRD / ERD / Tech Stack) telah diperbarui jika ada perubahan skema atau dependensi.
- [ ] Tidak ada konflik (*merge conflict*) dengan branch target (`staging`/`main`).

