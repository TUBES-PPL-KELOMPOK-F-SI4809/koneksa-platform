# 🛠️ Panduan Pembuatan Fitur, Package, & Skema Basis Data Baru

Dokumen ini adalah buku panduan operasional (*standard operating procedure*) teknis untuk membuat file, folder, package, dan skema basis data baru di repositori **Koneksa Platform**.

---

## 1. Panduan Pembuatan Fitur Baru (End-to-End Feature Creation)

Setiap penambahan fitur baru harus mengikuti hierarki berkas terstruktur berikut:

```text
koneksa-platform/
├── app/
│   ├── Enums/
│   │   └── [Feature]Status.php              # Enum status/tipe fitur
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── [Feature]/
│   │   │       └── [Feature]Controller.php  # Thin controller
│   │   └── Requests/
│   │       └── [Feature]/
│   │           ├── Store[Feature]Request.php
│   │           └── Update[Feature]Request.php
│   ├── Models/
│   │   └── [Feature].php                    # Eloquent Model
│   ├── Policies/
│   │   └── [Feature]Policy.php              # Otorisasi hak akses
│   └── Services/
│       └── [Feature]/
│           └── [Feature]Service.php         # Business Logic layer
│
├── database/
│   ├── factories/
│   │   └── [Feature]Factory.php             # Data dummy generator
│   ├── migrations/
│   │   └── YYYY_MM_DD_HHMMSS_create_[features]_table.php
│   └── seeders/
│       └── [Feature]Seeder.php              # Seeder data awal
│
├── resources/js/
│   └── Pages/
│       └── [Feature]/
│           ├── Index.vue                    # Halaman daftar
│           ├── Show.vue                     # Halaman detail
│           ├── Create.vue                   # Halaman form tambah
│           ├── Edit.vue                     # Halaman form ubah
│           └── Partials/                    # Sub-komponen modular
│               ├── [Feature]Card.vue
│               └── [Feature]Form.vue
│
├── routes/
│   └── web.php (atau grup route fitur)
│
└── tests/
    ├── Unit/
    │   └── Services/
    │       └── [Feature]ServiceTest.php     # Unit test logic bisnis
    └── Feature/
        └── [Feature]Test.php                # Integration/Feature test
```

### Langkah Kerja Pembuatan Fitur Baru (Step-by-Step):

#### Langkah 1: Generate Scaffolding Database & Model
Jalankan Artisan command dengan flag lengkap:
```bash
# Membuat Model, Migration, Factory, Seeder, dan Policy sekaligus:
php artisan make:model Initiative -mfs
php artisan make:policy InitiativePolicy --model=Initiative
```

#### Langkah 2: Buat Form Requests untuk Validasi
Jangan menaruh aturan validasi di dalam controller:
```bash
php artisan make:request Initiative/StoreInitiativeRequest
php artisan make:request Initiative/UpdateInitiativeRequest
```

#### Langkah 3: Buat Service Layer untuk Logika Bisnis
Buat file class baru di `app/Services/{Feature}/{Feature}Service.php`:
```php
<?php

namespace App\Services\Initiative;

use App\Models\Initiative;
use App\Models\User;

class InitiativeService
{
    /**
     * Membuat inisiatif baru dari data tervalidasi.
     */
    public function createInitiative(array $data, User $creator): Initiative
    {
        return $creator->initiatives()->create([
            'title' => $data['title'],
            'category_id' => $data['category_id'],
            'description' => $data['description'],
            'status' => $data['status'] ?? 'draft',
        ]);
    }
}
```

#### Langkah 4: Buat Controller Ramping (Thin Controller)
```bash
php artisan make:controller Initiative/InitiativeController
```
Inject `InitiativeService` melalui konstruktor, validasi request melalui `StoreInitiativeRequest`, lalu kembalikan respon `Inertia::render('Initiative/Index')`.

#### Langkah 5: Buat Halaman Antarmuka (Vue 3 / Inertia)
1. Buat folder di `resources/js/Pages/{Feature}/`.
2. Sebelum merancang komponen form, modal, atau tabel, **periksa pustaka `shadcn-ui`**.
3. Gunakan token desain yang ada di `docs/design-system/DESIGN-TOKENS.md`.

#### Langkah 6: Daftarkan Rute di `routes/web.php`
Bungkus rute dalam middleware group yang sesuai:
```php
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('initiatives', \App\Http\Controllers\Initiative\InitiativeController::class);
});
```

#### Langkah 7: Tulis dan Jalankan Unit Test
Buat berkas tes di `tests/Unit/Services/InitiativeServiceTest.php` dan jalankan hingga pass:
```bash
php artisan test --testsuite=Unit
```

---

## 2. Panduan Penambahan Skema Basis Data Baru (Database Schema Guidelines)

Skema database adalah fondasi yang sangat rentan memicu error jika diubah sembarangan. Patuhi aturan migrasi berikut:

### 2.1 Aturan Emas Migrasi Basis Data
1. **Dilarang Mengedit File Migrasi Lama yang Sudah Pernah Dijalankan**:
   - Jika Anda perlu menambah kolom, mengubah tipe data, atau menambah index pada tabel yang sudah ada, **SELALU BUAT FILE MIGRASI BARU**.
2. **Prinsip Non-Destruktif (Backward Compatibility)**:
   - Saat menambahkan kolom baru ke tabel yang sudah aktif digunakan (misal tabel `users`), kolom tersebut **HARUS** bersifat `nullable()` atau memiliki nilai `default(...)`.
   - Hal ini memastikan fitur lama yang membuat record baru tanpa menyertakan kolom baru tersebut tidak mengalami crash (*SQL error: 1364 Field doesn't have a default value*).
3. **Simetri `up()` dan `down()`**:
   - Setiap operasi di `up()` wajib memiliki kebalikan yang akurat di `down()`.

### 2.2 Contoh Migrasi Tabel Baru
```bash
php artisan make:migration create_initiatives_table
```
```php
public function up(): void
{
    Schema::create('initiatives', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->string('title');
        $table->string('slug')->unique();
        $table->text('description');
        $table->string('status', 30)->default('draft')->index();
        $table->timestamps();
        $table->softDeletes();
    });
}

public function down(): void
{
    Schema::dropIfExists('initiatives');
}
```

### 2.3 Contoh Migrasi Penambahan Kolom pada Tabel Eksisting
```bash
php artisan make:migration add_category_id_to_initiatives_table --table=initiatives
```
```php
public function up(): void
{
    Schema::table('initiatives', function (Blueprint $table) {
        // Kolom nullable agar tidak merusak data lama
        $table->foreignId('category_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
    });
}

public function down(): void
{
    Schema::table('initiatives', function (Blueprint $table) {
        $table->dropForeign(['category_id']);
        $table->dropColumn('category_id');
    });
}
```

---

## 3. Panduan Penambahan Package / Library Baru

### 3.1 Penambahan Third-Party Package (Composer / NPM)
Sebelum menambahkan dependensi eksternal, ikuti protokol RFC di [STACK-ADDITION-GUIDELINES.md](../tech-stack/STACK-ADDITION-GUIDELINES.md):
1. Periksa apakah fitur tersebut benar-benar membutuhkan dependensi luar.
2. Jalankan audit keamanan (`composer audit` / `npm audit`).
3. Catat di tabel log [CURRENT-STACK.md](../tech-stack/CURRENT-STACK.md).

### 3.2 Pembuatan Internal Modular Package (Opsional)
Jika di kemudian hari tim memutuskan untuk memisahkan domain menjadi *local package* terpisah di dalam repositori:
1. Buat folder di `packages/{vendor}/{package-name}` (contoh: `packages/koneksa/collaboration`).
2. Buat `composer.json` lokal pada paket tersebut.
3. Daftarkan di root `composer.json` melalui bagian `"repositories"` dengan tipe `"path"`.
4. Buat Service Provider internal untuk mendaftarkan migrasi, rute, dan views.

