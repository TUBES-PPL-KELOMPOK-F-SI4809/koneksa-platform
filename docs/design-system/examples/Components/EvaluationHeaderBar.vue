<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    activePage: {
        type: String,
        required: true,
        validator: (value) =>
            ['landing', 'umkm', 'mitra', 'pemda', 'admin', 'login', 'register'].includes(value),
    },
});

const evaluationRoutes = [
    { key: 'landing', label: 'Landing', routeName: 'landing' },
    { key: 'umkm', label: 'UMKM', routeName: 'evaluasi.umkm' },
    { key: 'mitra', label: 'Mitra', routeName: 'evaluasi.mitra' },
    { key: 'pemda', label: 'Pemda', routeName: 'evaluasi.pemda' },
    { key: 'admin', label: 'Admin', routeName: 'evaluasi.admin' },
];

const authRoutes = [
    { key: 'login', label: 'Login', routeName: 'login' },
    { key: 'register', label: 'Register', routeName: 'register' },
];
</script>

<template>
    <div
        class="sticky top-0 z-50 border-b border-neutral-800 bg-neutral-900 text-neutral-100"
        role="region"
        aria-label="Kontrol Mode Evaluasi UI"
    >
        <div class="mx-auto flex max-w-[1440px] flex-col gap-2 px-4 py-1.5 sm:px-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center justify-between gap-2.5">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 rounded-full border border-primary-500/40 bg-primary-900/60 px-2.5 py-0.5 font-mono text-[11px] font-medium text-primary-200">
                        <span class="h-1.5 w-1.5 rounded-full bg-secondary-500" aria-hidden="true"></span>
                        MODE EVALUASI UI
                    </span>
                    <span class="hidden text-xs text-neutral-400 md:inline">
                        Vercel + shadcn/ui + Breeze Colorful · 5 Views + Auth
                    </span>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-1.5">
                <nav class="flex flex-wrap items-center gap-1" aria-label="Pilih Halaman Evaluasi">
                    <Link
                        v-for="item in evaluationRoutes"
                        :key="item.key"
                        :href="route(item.routeName)"
                        :aria-current="activePage === item.key ? 'page' : undefined"
                        class="inline-flex min-h-[30px] items-center rounded-md px-2.5 py-1 text-xs font-medium transition duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                        :class="
                            activePage === item.key
                                ? 'bg-primary-600 text-white shadow-xs'
                                : 'text-neutral-300 hover:bg-neutral-800 hover:text-white'
                        "
                    >
                        {{ item.label }}
                    </Link>
                </nav>

                <span class="mx-1 hidden h-4 w-px bg-neutral-800 sm:inline-block" aria-hidden="true"></span>

                <nav class="flex items-center gap-1" aria-label="Halaman Autentikasi">
                    <Link
                        v-for="auth in authRoutes"
                        :key="auth.key"
                        :href="route(auth.routeName)"
                        :aria-current="activePage === auth.key ? 'page' : undefined"
                        class="inline-flex min-h-[30px] items-center rounded-md border px-2.5 py-1 text-xs font-medium transition duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                        :class="
                            activePage === auth.key
                                ? 'border-primary-500 bg-primary-600 text-white'
                                : 'border-neutral-700 bg-neutral-800/80 text-neutral-300 hover:border-neutral-600 hover:text-white'
                        "
                    >
                        {{ auth.label }}
                    </Link>
                </nav>
            </div>
        </div>
    </div>
</template>
