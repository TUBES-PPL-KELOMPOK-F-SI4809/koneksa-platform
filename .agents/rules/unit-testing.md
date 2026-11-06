---
description: "Aturan wajib eksekusi dan pembuatan unit test setelah setiap pengerjaan fitur"
globs: ["**/*.php", "**/*.js", "**/*.vue", "**/*.jsx", "**/*.tsx"]
always_on: true
---

# Aturan Wajib: Unit Testing Pasca Pengerjaan Fitur

Setiap kali agent menyelesaikan pengerjaan fitur, refactoring logic, penambahan endpoint, atau komponen UI:

1. **Wajib Ada Unit Test**:
   - Fitur belum dianggap selesai ("Done") jika belum disertai unit test yang menguji fungsionalitas tersebut.
   - Buat file test baru jika fitur baru, atau update file test yang sudah ada di direktori `tests/Unit/` (backend) atau direktori test frontend.

2. **Eksekusi Test Secara Mandiri**:
   - Agent harus mengeksekusi test runner secara mandiri menggunakan `run_command`:
     - Backend: `php artisan test --testsuite=Unit` atau file spesifik.
     - Frontend: runner unit test (misal `npm test` atau `vitest`).
   - Agent wajib memeriksa output konsol hingga memperoleh status **PASSED** (exit code 0).

3. **Perbaiki Kegagalan**:
   - Jika test gagal (Failing/Error), telusuri penyebabnya, perbaiki kode implementasi atau assertion test, lalu jalankan kembali hingga lulus.

4. **Laporkan Hasil Test**:
   - Dalam respons ke user, sertakan ringkasan hasil eksekusi unit test (jumlah test, assertions, dan status pass).

