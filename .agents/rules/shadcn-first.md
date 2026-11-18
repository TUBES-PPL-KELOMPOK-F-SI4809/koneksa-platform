---
description: "Route Vue component selection to the project frontend rules"
globs: ["resources/**/*.vue","resources/**/*.js","resources/**/*.css"]
always_on: false
---

Ikuti [.ai/rules/frontend.md](../../.ai/rules/frontend.md), [baseline UI](../../docs/design-system/BASELINE-DESIGN.md), dan **wajib lihat contoh implementasi konkret** di [FRONTEND-UI-EXAMPLES.md](../../docs/design-system/FRONTEND-UI-EXAMPLES.md) (`docs/design-system/examples/`).
Gunakan contoh blueprint tersebut sebagai standar visual tunggal di semua device developer.
Terapkan prinsip minimal diff (skill `ponytail`), pertahankan komponen yang sudah ada di `resources/js/Components/`, dan gunakan skill `inertia-vue-development` serta `shadcn-ui` (adaptasi Vue).
