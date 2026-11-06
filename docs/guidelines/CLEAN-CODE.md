# 🧼 Panduan Clean Code & Arsitektur Anti-Regresi — Koneksa Platform

Dokumen ini memuat standar penulisan kode bersih (*Clean Code*) dan prinsip isolasi arsitektur untuk meminimalkan risiko kerusakan (*breaking changes/regressions*) pada fitur lain saat fitur baru ditambahkan ke **Koneksa Platform**.

---

## 1. Prinsip Utama: Isolasi Fitur & Anti-Regresi

Kekhawatiran terbesar saat proyek bertambah besar adalah timbulnya efek samping (*unintended side-effects*) yang merusak fungsionalitas yang sudah ada. Untuk mencegahnya, patuhi prinsip berikut:

1. **Prinsip Tanggung Jawab Tunggal (Single Responsibility Principle - SRP)**:
   - Satu class, function, atau komponen hanya boleh memiliki satu alasan untuk berubah.
2. **Prinsip Buka-Tutup (Open/Closed Principle - OCP)**:
   - Terbuka untuk penambahan fitur baru (*extensible*), tetapi tertutup untuk modifikasi liar pada kode fitur yang telah stabil (*closed for modification*).
3. **Isolasi Domain & Modularitas**:
   - Fitur baru tidak boleh menumpuk logika di dalam controller atau service milik fitur lain.
   - Gunakan pendekatan **Feature-Driven Structure** atau **Action/Service Layer**.
4. **Hindari State Global yang Berubah-ubah (No Mutable Global State)**:
   - Hindari berbagi objek global atau mutasi variabel runtime yang dapat mengubah perilaku modul lain di luar lingkup fitur.

---

## 2. Standar Clean Code di Sisi Backend (PHP / Laravel)

### 2.1 Controller Ramping (Thin Controllers)
Controller hanya bertugas menerima HTTP Request, mendelegasikan validasi ke Form Request, memanggil Service/Action, dan mengembalikan respon (Inertia Page atau JSON).
* ❌ **Dilarang**: Menulis query SQL/Eloquent kompleks, manipulasi string panjang, atau logika bisnis bertingkat di Controller.
* ✅ **Wajib**: Pindahkan logika bisnis ke `app/Services/{Feature}/{Feature}Service.php` atau `app/Actions/{Feature}/...`.

```php
// CONTOH CONTROLLER YANG BERSIH
namespace App\Http\Controllers\Initiative;

use App\Http\Controllers\Controller;
use App\Http\Requests\Initiative\StoreInitiativeRequest;
use App\Services\Initiative\InitiativeService;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class InitiativeController extends Controller
{
    public function __construct(
        protected InitiativeService $initiativeService
    ) {}

    public function store(StoreInitiativeRequest $request): RedirectResponse
    {
        $this->initiativeService->createInitiative($request->validated(), $request->user());

        return redirect()->route('initiatives.index')
            ->with('success', 'Inisiatif berhasil diajukan!');
    }
}
```

### 2.2 Validasi Terisolasi (Dedicated Form Requests)
Jangan melakukan `$request->validate([...])` langsung di controller jika terdapat validasi multi-field atau aturan kompleks.
* Selalu buat Form Request terpisah di `app/Http/Requests/{Feature}/`.
* Isolasi aturan validasi per aksi (misal: `StoreInitiativeRequest.php` dan `UpdateInitiativeRequest.php`).

### 2.3 Hilangkan Magic Numbers & Magic Strings (Gunakan PHP Enums)
Dilarang menggunakan string mentah untuk status, tipe, atau peran.
* Buat PHP 8.1+ Enums di `app/Enums/`.
* Contoh: `InitiativeStatus::OPEN`, `UserRole::STUDENT`.

```php
namespace App\Enums;

enum InitiativeStatus: string
{
    case DRAFT = 'draft';
    case OPEN = 'open';
    case CLOSED = 'closed';
    case COMPLETED = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Konsep',
            self::OPEN => 'Terbuka',
            self::CLOSED => 'Ditutup',
            self::COMPLETED => 'Selesai',
        };
    }
}
```

### 2.4 Komunikasi Antar-Fitur melalui Event & Listener (Loose Coupling)
Jika fitur baru membutuhkan pemicu dari aktivitas fitur yang sudah ada (contoh: Saat pendaftaran inisiatif disetujui, kirim notifikasi dan buat kanal grup):
* ❌ **Dilarang**: Menulis logika pembuatan notifikasi/kanal langsung di dalam service fitur lama.
* ✅ **Gunakan**: Laravel Events & Listeners (`InitiativeApproved` -> `SendApprovalNotificationListener`). Dengan begitu, jika fitur notifikasi dimodifikasi, fitur inisiatif tetap aman.

### 2.5 Strict Typing & Defensive Programming
* Selalu deklarasikan type hints untuk argumen dan return type eksplisit:
  ```php
  public function calculateQuota(int $currentParticipants, int $maxQuota): int
  ```
* Gunakan null-safe operator `?->` dan early returns (*guard clauses*) untuk mengurangi percabangan `if-else` bertingkat.

---

## 3. Standar Clean Code di Sisi Frontend (Vue 3 / Inertia)

### 3.1 Struktur Komponen Mandiri (Single Responsibility)
* Pisahkan tampilan halaman besar menjadi sub-komponen kecil (*partials*) pada folder `resources/js/Pages/{Feature}/Partials/`.
* Jangan menumpuk 800+ baris kode dalam satu file `.vue`.
* Pisahkan:
  - Form dialog -> `Partials/CreateInitiativeModal.vue`
  - Tabel rekap -> `Partials/InitiativeTable.vue`
  - Kartu ringkasan -> `Partials/InitiativeCard.vue`

### 3.2 Komposisi Vue 3 Modern (`<script setup>`)
* Gunakan Composition API dengan `<script setup>`.
* Definisikan props dan emits secara eksplisit dengan validasi tipe:
  ```vue
  <script setup>
  const props = defineProps({
      initiative: {
          type: Object,
          required: true,
      },
      canEdit: {
          type: Boolean,
          default: false,
      },
  });

  const emit = defineEmits(['close', 'submitted']);
  </script>
  ```

### 3.3 Konsistensi UI & Bebas Konflik
* Cek katalog **`shadcn-ui`** sebelum membuat komponen interaktif baru.
* Gunakan token desain di `docs/design-system/DESIGN-TOKENS.md`. Dilarang menggunakan nilai warna hex arbitrer di dalam template komponen.

---

## 4. Prinsip Ecosystem-First: Dilarang Membuat Ulang Roda (*No Reinventing The Wheel*)

Sebelum menulis fungsi utilitas kustom, cek apakah framework dan ekosistem sudah menyediakannya:

1. **Gunakan Fitur Bawaan Laravel**:
   - Manipulasi koleksi: `collect($items)->pluck('id')`, `where()`, `unique()`.
   - String & Array: `Str::slug()`, `Str::headline()`, `Arr::get()`, `Arr::only()`.
   - Tanggal & Waktu: `Carbon::parse()`, `now()->addDays(7)`.
   - Autentikasi: `Auth::id()`, `$request->user()->can('update', $initiative)`.
2. **Gunakan Fitur Bawaan Vue / Inertia**:
   - Penanganan form: `useForm` dari `@inertiajs/vue3` (sudah menyediakan state data, error handling, reset, submit helper, dan tracking loading).
   - Navigasi: Komponen `<Link :href="route('...')">`.
   - Komponen UI: Primitif dari `shadcn-ui`.

---

## 5. Prinsip CLI-First: Maksimalkan Tooling Bawaan

* Selalu manfaatkan generator CLI (`php artisan make:*`) daripada membuat file kelas secara manual dari nol untuk menjamin boilerplate mematuhi standar framework.
* Manfaatkan `npx shadcn@latest add` untuk instalasi komponen UI teruji.
* Manfaatkan script npm (`npm run build`, `npm run dev`) untuk verifikasi integritas frontend.

---

## 6. Checklist Pencegahan Error Sebelum Menyerahkan Fitur

Sebelum menandai penambahan fitur selesai, periksa:
- [ ] Apakah ada fungsi utilitas yang dibuat manual padahal sudah disediakan Laravel/Vue?
- [ ] Apakah seluruh berkas dibuat menggunakan generator CLI resmi (`php artisan make:*`)?
- [ ] Apakah ada file fitur lain yang dimodifikasi tanpa alasan jelas? (Periksa `git status` dan `git diff`).
- [ ] Apakah semua dependensi fitur baru telah terisolasi dalam Form Request dan Service layer miliknya sendiri?
- [ ] Apakah seluruh migrasi database bersifat non-destruktif (tidak menghapus kolom/tabel yang dipakai fitur lain)?
- [ ] Apakah seluruh test suite unit lulus tanpa ada regresi?
  ```bash
  php artisan test --testsuite=Unit
  ```

