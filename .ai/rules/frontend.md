# Frontend — Vue/Inertia

Ikuti [AGENTS.md](../../AGENTS.md) dan [baseline UI](../../docs/design-system/BASELINE-DESIGN.md).

- Vue 3 Composition API, script setup, satu root untuk komponen Inertia.
  Halaman di resources/js/Pages/{Feature}/, partial fitur di Partials/,
  komponen lintas fitur di resources/js/Components/.
- Gunakan Link/named routes dan useForm dari @inertiajs/vue3 untuk pola form proyek.
  Form bawaan Inertia boleh jika sesuai kebutuhan; jangan menambah state/error handling duplikat.
- Utamakan komponen Vue yang tersedia. Untuk komponen baru, baca skill shadcn-ui lokal
  yang mengarahkan ke ekosistem Vue. Jangan memasang CLI/komponen React ke aplikasi Vue.
- Tailwind v3 adalah pipeline aktif. Gunakan scoped CSS/CSS variables untuk styling khusus.
  styled-components terpasang tetapi belum terintegrasi ke Vue; bukan standar implementasi.
- Baca token desain; periksa mapping aktual di CSS/Tailwind sebelum menggunakannya.
  Jangan menganggap ThemeProvider atau token runtime sudah tersedia.
- Jaga keyboard/focus, label/error, loading/empty state, dan reduced motion.
  Deferred props perlu skeleton/fallback saat data belum tersedia.
- Token/brand proyek mengungguli saran estetika skills. Stitch/taste-design hanya untuk
  tugas desain terkait; jangan mengganti brand atau menghasilkan DESIGN.md tanpa kebutuhan.
- Ikon: periksa dependensi aktual. Lucide belum menjadi dependensi langsung proyek;
  jangan mengimpor paket yang belum ada atau memasangnya tanpa otorisasi.
