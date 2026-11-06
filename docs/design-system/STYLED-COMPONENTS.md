# Status styled-components dan styling Vue

Dokumen ini menggantikan contoh React styled.button/ThemeProvider yang sebelumnya
diposisikan sebagai pola Vue. Ikuti [baseline UI](BASELINE-DESIGN.md).

## Status aktual

styled-components tercantum di package.json, tetapi aplikasi memakai Vue 3.
Belum ada integrasi ThemeProvider atau resources/js/Theme/tokens.js.
Paket terpasang tidak membuktikan adanya integrasi Vue yang dapat dipakai.

Untuk implementasi saat ini:
- Gunakan Tailwind v3 aktif untuk layout dan gaya umum.
- Gunakan style scoped, class bindings Vue dan CSS variables untuk styling khusus/dinamis.
- Rujuk [DESIGN-TOKENS.md](DESIGN-TOKENS.md), lalu periksa mapping aktual di CSS/Tailwind.
- Jangan mengimpor komponen React, menambah bridge/runtime, atau membuat ThemeProvider
  berdasarkan contoh dokumentasi lama.

Mempertahankan paket yang ada tidak berarti mewajibkan penggunaannya.
Penghapusan paket atau penggantian strategi styling adalah perubahan dependensi tersendiri
yang memerlukan otorisasi dan [evaluasi stack](../tech-stack/STACK-ADDITION-GUIDELINES.md).
