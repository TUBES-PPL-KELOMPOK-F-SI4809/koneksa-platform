---
description: "Aturan wajib pemanfaatan ekosistem library eksisting (Laravel/React/Vue) dan kewajiban memaksimalkan CLI (php artisan, npm, npx, composer)"
globs: ["**/*"]
always_on: true
---

# Aturan Wajib: Ecosystem-First & CLI-First

Saat mengembangkan kode di repositori ini:

## 1. Pemanfaatan Ekosistem Eksisting (No Reinventing the Wheel)
- **Larangan Membuat Ulang Fungsi Dasar**: Dilarang membuat fungsi manual atau helper utilitas dari nol jika sudah disediakan oleh ekosistem Laravel (`Str::*`, `Arr::*`, `collect()`, `Carbon`, `Auth`, `Gate`) maupun ekosistem frontend (`useForm`, `Inertia`, `shadcn-ui`, `styled-components`).
- **Data Manipulation**: Utamakan Eloquent ORM dan Laravel Collections daripada array looping mentah berulang.
- **Form State**: Gunakan `useForm` dari `@inertiajs/vue3` untuk semua alur form submit dan penanganan error responsif.
- **Komponen UI**: Cek ketersediaan di katalog `shadcn-ui` sebelum merancang dari nol.

## 2. Maksimalkan Penggunaan Command Line (CLI-First)
- **Scaffolding Berkas**: Dilarang membuat berkas class atau boilerplate secara manual jika ada generator Artisan.
  - Model & Migration: `php artisan make:model <Name> -mfs --policy`
  - Controller: `php artisan make:controller <Name>Controller`
  - Form Request: `php artisan make:request <Name>Request`
  - Service: `php artisan make:class Services/<Path>/<Name>Service`
  - Enum: `php artisan make:enum Enums/<Name>`
  - Resource: `php artisan make:resource <Name>Resource`
  - Test: `php artisan make:test --unit <Name>Test`
- **UI Components**: Gunakan `npx shadcn@latest add <component>` untuk menambahkan komponen siap pakai.
- **Manajemen & Eksekusi**: Selalu gunakan `php artisan test --testsuite=Unit`, `npm run build`, `composer audit`, `php artisan route:list`.

