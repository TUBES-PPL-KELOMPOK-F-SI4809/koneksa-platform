# 🧪 Panduan & Aturan Unit Testing untuk AI Agent & Pengembang

Dokumen ini menjelaskan tata cara, standar penulisan, dan aturan kepatuhan pelaksanaan **Unit Testing** pada proyek **Koneksa Platform**. Aturan ini mengikat bagi seluruh AI Coding Agent dan pengembang tim.

---

## 1. Aturan Mutlak: Pengujian Pasca Fitur (Mandatory Post-Feature Testing)

> **ATURAN UTAMA**:
> Setiap kali pengembang atau AI Agent menyelesaikan penulisan kode untuk satu fitur (baik fungsionalitas backend, endpoint API, service, validasi, maupun komponen antarmuka), **WAJIB membuat/memperbarui unit test dan menjalankannya hingga seluruh pengujian berstatus PASS (green) sebelum fitur diserahkan ke pengguna.**

### Definisi Selesai (Definition of Done - DoD):
Pekerjaan suatu fitur **DILARANG** dinyatakan selesai kecuali memenuhi seluruh checklist berikut:
- [ ] Kode implementasi fitur selesai dan bersih dari debug/console log.
- [ ] Berkas unit test ditulis di `tests/Unit/` (atau test runner frontend) mencakup *Happy Path* dan *Edge Cases*.
- [ ] Test dijalankan di terminal dan menghasilkan **Status: PASSED (exit code 0)**.
- [ ] Tidak ada regresi (seluruh test sebelumnya tetap lulus).
- [ ] Bukti output tes dilampirkan dalam ringkasan kepada pengguna.

---

## 2. Pola Penulisan Unit Test (AAA Pattern)

Setiap unit test wajib ditulis mengikuti struktur **Arrange - Act - Assert**:

```php
<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class InitiativeValidationTest extends TestCase
{
    /**
     * Memastikan kuota inisiatif minimal bernilai 1.
     */
    public function test_initiative_quota_must_be_positive_integer(): void
    {
        // 1. Arrange (Persiapan data & input)
        $invalidQuota = 0;

        // 2. Act (Eksekusi logika yang diuji)
        $isValid = $invalidQuota >= 1;

        // 3. Assert (Verifikasi hasil)
        $this->assertFalse($isValid, 'Quota di bawah 1 seharusnya tidak valid.');
    }
}
```

---

## 3. Menjalankan Unit Test di Lingkungan Koneksa

### 3.1 Menjalankan Unit Test Backend (PHPUnit / Pest)
```bash
# Menjalankan seluruh test suite Unit
php artisan test --testsuite=Unit

# Menjalankan file test spesifik
php artisan test tests/Unit/ExampleTest.php

# Menjalankan test dengan filter method
php artisan test --filter=test_initiative_quota_must_be_positive_integer
```

### 3.2 Menjalankan Pengujian Frontend / Browser
```bash
# Eksekusi runner unit/komponen JS
npm run test # jika runner vitest/jest terkonfigurasi

# Eksekusi browser testing otomatis via Playwright
node .agents/skills/playwright/scripts/playwright-cli.js --help
```

---

## 4. Penanganan Kesalahan Uji (Troubleshooting Failures)

Jika saat menjalankan tes terjadi kegagalan (Failing / Error):
1. **Analisis Stack Trace**: Perhatikan file, baris kode, dan pesan error yang diberikan test runner.
2. **Koreksi Kode**: Perbaiki implementasi atau sesuaikan ekspektasi test jika terdapat perubahan spesifikasi yang valid.
3. **Re-run Test**: Jalankan ulang test hingga seluruh assertion berwarna hijau (*green*).
4. **Jangan Pernah Mengabaikan Tes Gagal**: Dilarang menghapus atau me-skip tes (`->markTestSkipped()` tanpa alasan teknis resmi) hanya demi meloloskan eksekusi.

