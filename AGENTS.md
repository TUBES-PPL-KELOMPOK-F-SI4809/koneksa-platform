# Koneksa Platform — Agent Instructions & Workflow Rules

Dokumen ini berisi aturan kerja wajib (Mandatory Operational Rules) untuk setiap AI Coding Agent (Antigravity, Cursor, Claude Code, dll) yang bekerja pada repositori **Koneksa Platform**.

---

## 0. Kewajiban Membaca Dokumentasi & Mengaktifkan Skills (Mandatory Pre-Flight Reading)

> **CRITICAL PRE-FLIGHT MANDATE**: Sebelum merancang, membuat, atau memodifikasi kode apapun, **Agent WAJIB membaca berkas acuan terkait di folder `docs/` serta memeriksa dan mengaktifkan skills yang relevan di `.agents/skills/` dan `.antigravity/skills/`. DILARANG membuat kode berdasarkan asumsi sendiri tanpa mengonfirmasi dokumentasi.**

### 1. Pemetaan Wajib Berkas `docs/` yang Harus Dibaca:
* **Pengembangan Fitur & Logika Bisnis (Backend/Frontend)**:
  - 📖 [docs/PRD/PRD-KONEKSA-PLATFORM.md](docs/PRD/PRD-KONEKSA-PLATFORM.md) — untuk memahami lingkup 21 fitur, aktor, dan aturan bisnis.
  - 📖 [docs/analysis/DATABASE-SCHEMA.md](docs/analysis/DATABASE-SCHEMA.md) — untuk memahami ERD dan struktur tabel terkait.
* **Perancangan Antarmuka & Komponen UI**:
  - 📖 [docs/design-system/BASELINE-DESIGN.md](docs/design-system/BASELINE-DESIGN.md) — filosofi visual dan ritme layout.
  - 📖 [docs/design-system/DESIGN-TOKENS.md](docs/design-system/DESIGN-TOKENS.md) — warna, tipografi, radius, elevasi.
  - 📖 [docs/design-system/STYLED-COMPONENTS.md](docs/design-system/STYLED-COMPONENTS.md) — panduan CSS-in-JS dan tema.
* **Struktur Kode, Pembuatan File/Folder, & Migrasi Database**:
  - 📖 [docs/guidelines/FEATURE-DEVELOPMENT-GUIDE.md](docs/guidelines/FEATURE-DEVELOPMENT-GUIDE.md) — SOP pembuatan file baru.
  - 📖 [docs/guidelines/CLEAN-CODE.md](docs/guidelines/CLEAN-CODE.md) — prinsip anti-regresi dan thin controller.
* **Pengujian Fitur**:
  - 📖 [docs/guidelines/AGENT-UNIT-TESTING.md](docs/guidelines/AGENT-UNIT-TESTING.md) — format AAA dan eksekusi test runner.
* **Penambahan Dependensi Baru**:
  - 📖 [docs/tech-stack/STACK-ADDITION-GUIDELINES.md](docs/tech-stack/STACK-ADDITION-GUIDELINES.md) — prosedur pengisian RFC.

### 2. Kewajiban Mengaktifkan Skills di `.agents/` dan `.antigravity/`:
Agent wajib membaca `SKILL.md` dan mengaktifkan keahlian berikut sesuai domain tugas:
* **Logika Backend Laravel**: Aktifkan skill `laravel-best-practices` (`.antigravity/skills/laravel-best-practices/SKILL.md`).
* **Halaman & Komponen Inertia Vue**: Aktifkan skill `inertia-vue-development` (`.antigravity/skills/inertia-vue-development/SKILL.md`).
* **Styling & CSS Tailwind**: Aktifkan skill `tailwindcss-development` (`.antigravity/skills/tailwindcss-development/SKILL.md`).
* **Komponen Aksesibel**: Aktifkan skill `shadcn-ui` (`.agents/skills/shadcn-ui/SKILL.md`).
* **Desain Visual & Layout Anti-Generic**: Aktifkan skill `taste-design` (`.agents/skills/taste-design/SKILL.md`) dan `ui-ux-pro-max` (`.agents/skills/ui-ux-pro-max/SKILL.md`).
* **Pengujian Unit & E2E**: Aktifkan skill `testing-best-practices` (`.antigravity/skills/testing-best-practices/SKILL.md`) dan `playwright` (`.agents/skills/playwright/SKILL.md`).

---

## 1. Aturan Wajib Unit Testing (Mandatory Unit Testing Rule)

> **CRITICAL RULE**: Setiap kali Agent selesai mengimplementasikan atau memodifikasi sebuah fitur (baik backend logic, service, controller, model, API endpoint, maupun komponen frontend), **Agent WAJIB membuat atau memperbarui unit test dan menjalankannya hingga seluruh test berstatus PASS (green) sebelum tugas dianggap selesai.**

### Aturan Eksekusi Tes:
1. **Tidak Ada Fitur Tanpa Test**:
   - Dilarang menandai pekerjaan fitur selesai atau memberi tahu user bahwa fitur telah selesai tanpa membuat/menyesuaikan unit test yang relevan.
   - Setiap fitur baru harus memiliki cakupan:
     - **Happy path**: alur input dan logika yang diharapkan berjalan sukses.
     - **Edge cases**: input boundary, null values, invalid types, array kosong.
     - **Error handling & validation**: validasi gagal, exception handling, authorization check.
2. **Cara Menjalankan Test Backend (PHP / Laravel)**:
   - Gunakan PHPUnit atau Pest:
     ```bash
     php artisan test --testsuite=Unit
     # atau menjalankan file test spesifik:
     php artisan test tests/Unit/YourFeatureTest.php
     ```
   - Pastikan database test terisolasi (gunakan in-memory sqlite bila tersedia atau mock DB/repository).
3. **Cara Menjalankan Test Frontend (JavaScript / Vue / React)**:
   - Jalankan test runner unit/komponen yang terpasang:
     ```bash
     npm run test # atau npx vitest run
     ```
   - Untuk validasi alur tampilan kritis dan integrasi, manfaatkan skill `playwright` bila diperlukan.
4. **Verifikasi Output**:
   - Agent wajib memeriksa return code (`exit code 0`) dan memverifikasi assertion lulus. Jika ada failure atau error, Agent wajib memperbaiki kode/test secara mandiri sebelum menyerahkan ke user.

Detail panduan dan konvensi pengujian dapat dibaca pada [AGENT-UNIT-TESTING.md](docs/guidelines/AGENT-UNIT-TESTING.md).

---

## 2. Aturan Wajib Cek Ketersediaan Komponen UI `shadcn-ui` (Shadcn-First Rule)

> **CRITICAL UI RULE**: Sebelum membuat komponen antarmuka (UI component) baru secara manual dari nol (*from scratch*), **Agent WAJIB memeriksa ketersediaan komponen tersebut dalam pustaka `shadcn-ui` terlebih dahulu.**

### Alur Kerja Komponen UI:
1. **Cek Katalog `shadcn-ui` Terlebih Dahulu**:
   - Manfaatkan skill `shadcn-ui` (`.agents/skills/shadcn-ui/`) untuk mencari apakah komponen sudah tersedia (seperti *Button, Dialog/Modal, Dropdown, Table, Input, Select, Popover, Accordion, Sheet, Toast, Tabs, Card, Badge, Avatar, Form, dll*).
   - Telusuri katalog komponen melalui skill `shadcn-ui` atau CLI:
     ```bash
     npx shadcn@latest add <nama-komponen>
     ```
2. **Dilarang *Reinvent the Wheel***:
   - Dilarang membuat komponen interaktif kompleks dari nol jika komponen atau primitif aksesibilitasnya sudah disediakan oleh `shadcn-ui` / Radix UI.
   - Komponen dari `shadcn-ui` disalin ke dalam repositori kita, lalu disesuaikan (*customized*) dengan token desain Koneksa dan integrasi `styled-components` / Tailwind CSS.
3. **Pengecualian Pembuatan Komponen Mandiri (From Scratch)**:
   - Pembuatan komponen kustom dari nol **HANYA** diizinkan jika:
     - Komponen tersebut memang tidak ada di katalog `shadcn-ui`, ATAU
     - Komponen merupakan widget domain-spesifik yang sangat khusus untuk Koneksa Platform.
     - Agen wajib mencantumkan alasan singkat jika memilih membuat komponen dari nol.

---

## 3. Kepatuhan Baseline Desain & Styling

1. **Gunakan Design Baseline & Tokens**:
   - Selalu rujuk [BASELINE-DESIGN.md](docs/design-system/BASELINE-DESIGN.md) dan [DESIGN-TOKENS.md](docs/design-system/DESIGN-TOKENS.md).
   - Dilarang menggunakan nilai warna atau spacing arbitrer (*hardcoded hex/magic numbers*) jika sudah didefinisikan dalam design tokens.
2. **Penggunaan `styled-components` & Tailwind CSS**:
   - Manfaatkan `styled-components` untuk isolated custom styling, dynamic prop-driven styles, theme provider consumption, dan reusable styled UI primitives.
   - Manfaatkan Tailwind CSS untuk utility layouting cepat (flex, grid, spacing, container).
   - Jangan menimbulkan style conflict antara inline utility dan styled definitions. Rujuk [STYLED-COMPONENTS.md](docs/design-system/STYLED-COMPONENTS.md).
3. **Pemanfaatan Skills**:
   - Gunakan skill `ui-ux-pro-max` saat mendesain layout, pemilihan kontras warna WCAG AA, micro-animations, dan struktur interaktif.
   - Gunakan skill `taste-design` untuk memastikan visual hierarchy modern, clean, anti-generic, dan proporsional.
   - Gunakan skill `shadcn-ui` untuk mengadopsi struktur komponen UI yang aksesibel dan teruji.

---

## 4. Dokumentasi & Struktur `docs/`

1. **Lokasi Dokumentasi**:
   - Semua dokumentasi analisis sistem, PRD, baseline desain, panduan arsitektur, dan penambahan stack harus ditempatkan di folder `docs/`.
2. **Aturan Penambahan Tech Stack Baru**:
   - Jika Agent atau tim mengusulkan penambahan dependensi baru (composer package atau npm package signifikan), Agent WAJIB memperbarui dokumentasi di `docs/tech-stack/` mengikuti [STACK-ADDITION-GUIDELINES.md](docs/tech-stack/STACK-ADDITION-GUIDELINES.md).

---

## 5. Aturan Wajib Clean Code & Arsitektur Anti-Regresi (Anti-Regression Mandate)

> **CRITICAL CODE INTEGRITY RULE**: Setiap kali Agent menambahkan atau mengembangkan fitur baru, **Agent WAJIB menerapkan prinsip Clean Code dan isolasi fitur guna mencegah timbulnya bug atau regresi pada fitur yang sudah ada.**

### Aturan Ketat untuk Agent:
1. **Dilarang Merusak Fitur Lain (Feature Isolation)**:
   - Dilarang menumpuk kode fitur baru ke dalam Controller, Model, atau Service milik fitur lain.
   - Setiap fitur baru harus memiliki **Controller**, **Form Request**, dan **Service Layer** tersendiri sesuai panduan [FEATURE-DEVELOPMENT-GUIDE.md](docs/guidelines/FEATURE-DEVELOPMENT-GUIDE.md).
   - Hubungkan antar-fitur hanya melalui *interface*, *event*, atau *listener* yang terisolasi.
2. **Pola Thin Controller & Dedicated Service**:
   - Controller dilarang memuat logika bisnis rumit atau raw queries.
   - Validasi data wajib menggunakan Form Request terpisah di `app/Http/Requests/{Feature}/`.
   - Logika pemrosesan wajib berada di `app/Services/{Feature}/{Feature}Service.php`.
3. **Aturan Integritas Skema Database**:
   - **DILARANG MENGUBAH FILE MIGRASI YANG SUDAH ADA**.
   - Perubahan skema basis data wajib dilakukan dengan membuat file migrasi baru (`php artisan make:migration ...`).
   - Penambahan kolom pada tabel yang sudah aktif wajib bersifat **`nullable()`** atau memiliki nilai **`default(...)`** agar tidak merusak kode atau query fitur sebelumnya.
4. **Kepatuhan Struktur Frontend**:
   - Halaman Vue diatur dalam folder `resources/js/Pages/{Feature}/`.
   - Komponen form/tabel yang panjang wajib dipecah menjadi modul kecil di `resources/js/Pages/{Feature}/Partials/`.
5. **Verifikasi Anti-Regresi (Zero Regression Verification)**:
   - Sebelum dan sesudah mengimplementasikan kode, Agent wajib memastikan tidak ada tes terdahulu yang rusak dengan menjalankan:
     ```bash
     php artisan test --testsuite=Unit
     ```

Detail lengkap baca di [CLEAN-CODE.md](docs/guidelines/CLEAN-CODE.md) dan [FEATURE-DEVELOPMENT-GUIDE.md](docs/guidelines/FEATURE-DEVELOPMENT-GUIDE.md).

---

## 6. Aturan Wajib Ekosistem Eksisting (Ecosystem-First: No Reinventing The Wheel)

> **CRITICAL ECOSYSTEM RULE**: Sebelum menulis fungsi manual, helper kustom, atau algoritma buatan sendiri, **Agent WAJIB memeriksa dan memanfaatkan pustaka/dependensi yang sudah ada dalam ekosistem Laravel maupun React/Vue/NPM.**

### Kewajiban Pemanfaatan Ekosistem:
1. **Pemanfaatan Ekosistem Laravel & PHP**:
   - **Data Manipulation**: Selalu gunakan Laravel Collections (`collect()->map()`, `filter()`, `pluck()`, `groupBy()`) daripada loop manual `foreach` yang panjang.
   - **String & Array Helpers**: Gunakan `Illuminate\Support\Str` (misal: `Str::slug()`, `Str::uuid()`, `Str::limit()`) dan `Illuminate\Support\Arr`.
   - **Manipulasi Waktu/Tanggal**: Wajib gunakan `Carbon\Carbon` atau helper bawaan Laravel (`now()`, `today()`), dilarang memanipulasi string tanggal dengan fungsi mentah PHP `date()` atau hitungan manual unix timestamp.
   - **Autentikasi & Otorisasi**: Gunakan middleware, Gates, dan Policies bawaan Laravel, dilarang membuat sistem pengecekan role manual di controller.
   - **Database Query**: Manfaatkan fitur Eloquent ORM (Relationships, Query Scopes, Casts, Mutators) daripada menulis raw query atau logic filter berulang.
2. **Pemanfaatan Ekosistem Frontend (React / Vue / Inertia / NPM)**:
   - **Manajemen Form**: Wajib gunakan `useForm` dari `@inertiajs/vue3` untuk mengelola state form, error bag, reset, dan status processing. Dilarang membuat state reaktif form manual dari nol jika useForm mencukupi.
   - **Komponen UI**: Cek katalog `shadcn-ui` terlebih dahulu (Lihat Aturan 2).
   - **Iconography**: Manfaatkan library ikon yang terpasang (seperti Lucide Icons) daripada membuat SVG manual.
   - **Styling Dinamis**: Gunakan utility classes Tailwind dan `styled-components` theme tokens yang sudah tersedia.

---

## 7. Aturan Wajib Memaksimalkan Command Line (CLI-First Mandate)

> **CRITICAL CLI MANDATE**: Agent **WAJIB memaksimalkan perkakas Command Line Interface (CLI)** seperti `php artisan`, `npm`, `npx`, dan `composer` untuk segala kebutuhan pembuatan berkas (*scaffolding*), manajemen dependensi, inspeksi rute, dan pengujian. Dilarang membuat berkas boilerplate secara manual jika perintah CLI penyedianya tersedia.

### Panduan Eksekusi Perintah CLI:
1. **Pembuatan Berkas Laravel (Selalu gunakan `php artisan make:*`)**:
   - Model + Migration + Factory + Seeder: `php artisan make:model Feature -mfs`
   - Policy: `php artisan make:policy FeaturePolicy --model=Feature`
   - Controller: `php artisan make:controller Feature/FeatureController`
   - Form Request: `php artisan make:request Feature/StoreFeatureRequest`
   - Service / Class Generik: `php artisan make:class Services/Feature/FeatureService`
   - Enum: `php artisan make:enum Enums/FeatureStatus`
   - API Resource: `php artisan make:resource Feature/FeatureResource`
   - Unit Test: `php artisan make:test --unit Services/FeatureServiceTest`
   - Event / Listener: `php artisan make:event FeatureEvent` & `php artisan make:listener FeatureListener`
2. **Inspeksi & Utilitas Laravel**:
   - Cek rute: `php artisan route:list --path=nama-fitur`
   - Bersihkan cache konfigurasi: `php artisan optimize:clear`
3. **Eksekusi Frontend & Aset**:
   - Tambah komponen UI: `npx shadcn@latest add <component>`
   - Build dan verifikasi aset: `npm run build`
   - Menjalankan dev server: `npm run dev`
4. **Audit & Dependensi**:
   - Instal dependensi PHP: `composer require vendor/package`
   - Instal dependensi JS: `npm install package-name`
   - Audit keamanan: `composer audit` dan `npm audit`

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

## Foundational Context

This application is a Laravel application running on PHP 8.5. Always use the APIs that match the installed major version of each package — do not assume a version.

Before relying on a package's API, confirm its installed version:
- PHP packages: run `composer show --direct` to list direct dependencies with versions, or `composer show <vendor/package>` for a single package.
- JS packages: check `package.json` for the installed versions.

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If a frontend change doesn't show in the UI or you get a "Unable to locate file in Vite manifest" error, run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Use `search-docs` before changes that depend on Laravel ecosystem APIs, behavior, configuration, or version-specific syntax. Skip it for copy-only edits and other changes where package documentation is irrelevant. Reuse sufficient results already in context instead of searching again.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Project Rules

- This project contains committed, area-grouped rules in `.ai/rules` when that directory exists, including path-scoped framework guidelines under `.ai/rules/boost`. Before you enter plan mode or create/edit any file, you MUST first: open @.ai/rules/index.md (it maps file globs to rule files), read every rule file whose globs cover the path(s) in scope, and run `grep -rin 'keyword' .ai/rules` to catch what a path match alone misses. Do not write code until you have read and are following every matching rule. If `.ai/rules` does not exist, continue without it.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.
- Activate the `deploying-to-cloud` skill whenever deploying to Laravel Cloud, configuring Cloud environments or resources, using the Cloud CLI, or troubleshooting Cloud deployments.

=== tests rules ===

# Test Enforcement

- Add or update tests for behavior and logic changes when a test provides meaningful regression coverage.
- Pure copy, styling, and layout-only changes do not require new or updated tests.
- When test coverage applies, run the affected tests and ensure they pass.
- Test the changed behavior and its important failure modes, but do not add tests beyond them.
- Read the `testing-best-practices` skill before writing tests.

=== inertia-laravel/core rules ===

# Inertia

- Inertia creates fully client-side rendered SPAs without modern SPA complexity, leveraging existing server-side patterns.
- Components live in `resources/js/Pages` (unless specified in `vite.config.js`). Use `Inertia::render()` for server-side routing instead of Blade views.
- ALWAYS use `search-docs` tool for version-specific Inertia documentation and updated code examples.
- IMPORTANT: Activate `inertia-vue-development` when working with Inertia Vue client-side patterns.

# Inertia v2

- Use all Inertia features from v1 and v2. Check the documentation before making changes to ensure the correct approach.
- New features: deferred props, infinite scroll, merging props, polling, prefetching, once props, flash data.
- When using deferred props, add an empty state with a pulsing or animated skeleton.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== phpunit/core rules ===

# PHPUnit

- This project uses PHPUnit. Create tests with `php artisan make:test --phpunit {name}`.
- Do not include the test suite directory in `{name}`. Use `SomeFeatureTest`, not `Feature/SomeFeatureTest`.
- Read the `testing-best-practices` skill for guidance on coverage, naming, structure, dependency isolation, and review.

## Running Tests

- Run the narrowest set of tests that covers the change. Pass a file path or `--filter=testName` to `php artisan test --compact`.
- Rerun a test after each change to it.
- Run `vendor/bin/phpunit` to call the test runner directly. It accepts the same file path and `--filter=testName` arguments.

=== inertia-vue/core rules ===

# Inertia + Vue

Vue components must have a single root element.
- IMPORTANT: Activate `inertia-vue-development` when working with Inertia Vue client-side patterns.

</laravel-boost-guidelines>
