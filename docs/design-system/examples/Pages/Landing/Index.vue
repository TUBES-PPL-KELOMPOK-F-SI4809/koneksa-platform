<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import EvaluationHeaderBar from '@/Components/EvaluationHeaderBar.vue';
import KoneksaLogo from '@/Components/KoneksaLogo.vue';
import StatusBadge from '@/Components/StatusBadge.vue';

const props = defineProps({
    canLogin: {
        type: Boolean,
        default: true,
    },
    canRegister: {
        type: Boolean,
        default: true,
    },
    stats: {
        type: Array,
        required: true,
    },
    roleHighlights: {
        type: Array,
        required: true,
    },
    featuredBusinesses: {
        type: Array,
        required: true,
    },
    activePrograms: {
        type: Array,
        required: true,
    },
    digitalSolutions: {
        type: Array,
        required: true,
    },
});

const mobileNavOpen = ref(false);
const searchQuery = ref('');
const selectedCategory = ref('Semua');
const selectedBusiness = ref(null);

const categories = ['Semua', 'Kuliner', 'Fesyen & Kriya', 'Kerajinan'];

const roleToneMap = {
    umkm: { badge: 'primary', border: 'border-t-primary-500' },
    mitra: { badge: 'secondary', border: 'border-t-secondary-500' },
    pemda: { badge: 'success', border: 'border-t-success' },
    admin: { badge: 'info', border: 'border-t-accent-500' },
};

const filteredBusinesses = computed(() => {
    const q = searchQuery.value.trim().toLowerCase();

    return props.featuredBusinesses.filter((business) => {
        const matchesCategory =
            selectedCategory.value === 'Semua' ||
            business.category === selectedCategory.value;

        const matchesQuery =
            !q ||
            business.name.toLowerCase().includes(q) ||
            business.district.toLowerCase().includes(q) ||
            business.needs.some((n) => n.toLowerCase().includes(q));

        return matchesCategory && matchesQuery;
    });
});

function handleEscape(event) {
    if (event.key === 'Escape') {
        if (selectedBusiness.value) {
            selectedBusiness.value = null;
        }
        if (mobileNavOpen.value) {
            mobileNavOpen.value = false;
        }
    }
}

onMounted(() => document.addEventListener('keydown', handleEscape));
onUnmounted(() => document.removeEventListener('keydown', handleEscape));
</script>

<template>
    <div class="min-h-screen bg-canvas text-ink">
        <Head title="Koneksa — Platform Kolaborasi UMKM & Industri Digital Bandung" />

        <!-- Top Header Mode Evaluasi -->
        <EvaluationHeaderBar active-page="landing" />

        <!-- Public Navbar (Vercel-style Hairline Header) -->
        <header class="border-b border-hairline bg-white/95 backdrop-blur">
            <div class="mx-auto flex h-14 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                <Link
                    :href="route('landing')"
                    class="rounded-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ink"
                >
                    <KoneksaLogo />
                </Link>

                <nav class="hidden items-center gap-6 md:flex" aria-label="Navigasi Utama">
                    <a
                        href="#ekosistem"
                        class="text-[13px] font-medium text-ink-secondary transition hover:text-ink focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ink"
                    >
                        Ekosistem
                    </a>
                    <a
                        href="#direktori"
                        class="text-[13px] font-medium text-ink-secondary transition hover:text-ink focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ink"
                    >
                        Direktori UMKM
                    </a>
                    <a
                        href="#program"
                        class="text-[13px] font-medium text-ink-secondary transition hover:text-ink focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ink"
                    >
                        Program &amp; Solusi
                    </a>
                </nav>

                <div class="hidden items-center gap-2 md:flex">
                    <Link
                        v-if="$page.props.auth?.user"
                        :href="route('dashboard')"
                        class="inline-flex h-9 items-center rounded-md bg-primary-600 px-3.5 text-[13px] font-medium text-white shadow-xs transition hover:bg-primary-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2"
                    >
                        Dashboard
                    </Link>
                    <template v-else-if="canLogin">
                        <Link
                            :href="route('login')"
                            class="inline-flex h-9 items-center rounded-md border border-hairline bg-white px-3.5 text-[13px] font-medium text-ink-secondary transition hover:border-neutral-300 hover:text-ink focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                        >
                            Masuk
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="inline-flex h-9 items-center rounded-md bg-primary-600 px-3.5 text-[13px] font-medium text-white shadow-xs transition hover:bg-primary-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2"
                        >
                            Daftar
                        </Link>
                    </template>
                </div>

                <!-- Mobile Menu Toggle -->
                <button
                    type="button"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-hairline text-ink-secondary hover:bg-canvas hover:text-ink focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ink md:hidden"
                    :aria-expanded="mobileNavOpen"
                    aria-label="Buka menu navigasi publik"
                    @click="mobileNavOpen = !mobileNavOpen"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path
                            v-if="!mobileNavOpen"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"
                        />
                        <path
                            v-else
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M6 18 18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>

            <!-- Mobile Nav Drawer -->
            <div v-if="mobileNavOpen" class="border-t border-hairline bg-white px-4 py-3 md:hidden">
                <nav class="flex flex-col gap-1" aria-label="Navigasi Mobile">
                    <a
                        href="#ekosistem"
                        class="rounded-md px-3 py-2 text-sm font-medium text-ink-secondary hover:bg-neutral-100 hover:text-ink"
                        @click="mobileNavOpen = false"
                    >
                        Ekosistem 4 Role
                    </a>
                    <a
                        href="#direktori"
                        class="rounded-md px-3 py-2 text-sm font-medium text-ink-secondary hover:bg-neutral-100 hover:text-ink"
                        @click="mobileNavOpen = false"
                    >
                        Direktori UMKM
                    </a>
                    <a
                        href="#program"
                        class="rounded-md px-3 py-2 text-sm font-medium text-ink-secondary hover:bg-neutral-100 hover:text-ink"
                        @click="mobileNavOpen = false"
                    >
                        Program &amp; Solusi
                    </a>
                    <div class="mt-2 flex flex-col gap-2 border-t border-hairline pt-3">
                        <Link
                            v-if="canLogin && !$page.props.auth?.user"
                            :href="route('login')"
                            class="inline-flex h-10 items-center justify-center rounded-md border border-hairline bg-white px-4 text-sm font-medium text-ink"
                        >
                            Masuk
                        </Link>
                        <Link
                            v-if="canRegister && !$page.props.auth?.user"
                            :href="route('register')"
                            class="inline-flex h-10 items-center justify-center rounded-md bg-primary-600 px-4 text-sm font-medium text-white"
                        >
                            Daftar Koneksa
                        </Link>
                    </div>
                </nav>
            </div>
        </header>

        <main>
            <!-- Hero Section (Vercel + Breeze Colorful Grid) -->
            <section class="border-b border-hairline bg-gradient-to-b from-primary-50/70 via-white to-secondary-50/30">
                <div class="mx-auto grid max-w-7xl grid-cols-1 gap-10 px-4 py-12 sm:px-6 sm:py-16 lg:grid-cols-12 lg:items-center lg:gap-12 lg:px-8 lg:py-20">
                    <div class="min-w-0 lg:col-span-7">
                        <StatusBadge tone="primary" dot size="md">
                            Kota Bandung · SDG 8, 9 &amp; 17
                        </StatusBadge>

                        <h1 class="mt-4 text-3xl font-semibold tracking-tight text-ink sm:text-4xl lg:text-[2.75rem] lg:leading-[1.12]">
                            Infrastruktur kolaborasi
                            <span class="bg-gradient-to-r from-primary-600 to-secondary-600 bg-clip-text text-transparent">
                                UMKM, mitra industri,
                            </span>
                            dan pembinaan daerah.
                        </h1>

                        <p class="mt-4 max-w-2xl text-base leading-relaxed text-ink-secondary">
                            Koneksa menyatukan pencatatan kasir &amp; stok UMKM, kurasi kemitraan industri digital, dan program pembinaan resmi Pemerintah Kota Bandung dalam satu platform modern yang tenang dan terukur.
                        </p>

                        <div class="mt-7 flex flex-wrap items-center gap-3">
                            <Link
                                :href="route('evaluasi.umkm')"
                                class="inline-flex h-10 items-center justify-center gap-2 rounded-md bg-primary-600 px-4 text-sm font-medium text-white shadow-xs transition hover:bg-primary-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2"
                            >
                                Buka Workspace UMKM
                                <span aria-hidden="true">→</span>
                            </Link>
                            <a
                                href="#direktori"
                                class="inline-flex h-10 items-center justify-center rounded-md border border-hairline bg-white px-4 text-sm font-medium text-ink shadow-xs transition hover:border-neutral-300 hover:bg-canvas focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                            >
                                Jelajahi Direktori
                            </a>
                        </div>

                        <div class="mt-10 grid grid-cols-2 gap-4 border-t border-hairline pt-6 sm:grid-cols-4">
                            <div v-for="stat in stats" :key="stat.label">
                                <p class="font-mono text-2xl font-semibold tracking-tight text-primary-700 tabular-nums">
                                    {{ stat.value }}
                                </p>
                                <p class="mt-0.5 text-xs font-medium text-ink">
                                    {{ stat.label }}
                                </p>
                                <p class="text-[11px] text-ink-muted">
                                    {{ stat.helper }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- PaaS Workspace Preview Card -->
                    <div class="min-w-0 lg:col-span-5">
                        <div class="rounded-xl border border-hairline border-t-2 border-t-primary-500 bg-white p-4 shadow-paas sm:p-5">
                            <div class="flex items-center justify-between border-b border-hairline pb-3">
                                <div>
                                    <p class="font-mono text-[11px] font-medium uppercase tracking-wider text-primary-600">
                                        Quick Launch
                                    </p>
                                    <h2 class="mt-0.5 text-sm font-semibold text-ink">
                                        4 Panel Role Ekosistem
                                    </h2>
                                </div>
                                <StatusBadge tone="primary" dot>
                                    Breeze + shadcn
                                </StatusBadge>
                            </div>

                            <div class="mt-3 divide-y divide-hairline overflow-hidden rounded-lg border border-hairline bg-canvas/60">
                                <div
                                    v-for="item in roleHighlights"
                                    :key="item.role"
                                    class="flex items-center justify-between gap-3 p-3.5 transition hover:bg-white"
                                >
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-medium text-ink">
                                            {{ item.title }}
                                        </p>
                                        <p class="truncate text-xs text-ink-muted">
                                            {{ item.subtitle }} · <span class="font-mono text-primary-600">{{ item.badge }}</span>
                                        </p>
                                    </div>
                                    <Link
                                        :href="route(item.routeName)"
                                        class="inline-flex h-8 shrink-0 items-center rounded-md bg-primary-600 px-3 text-xs font-medium text-white shadow-xs transition hover:bg-primary-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                                    >
                                        Buka
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 4 Role Architecture Section -->
            <section id="ekosistem" class="mx-auto max-w-7xl px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
                <div class="max-w-2xl">
                    <p class="font-mono text-xs uppercase tracking-wider text-ink-muted">
                        Arsitektur Multi-Role
                    </p>
                    <h2 class="mt-1.5 text-2xl font-semibold tracking-tight text-ink sm:text-3xl">
                        Empat Ruang Kerja Terisolasi
                    </h2>
                    <p class="mt-2 text-sm leading-relaxed text-ink-secondary">
                        Setiap aktor memiliki otorisasi dan modul operasional sesuai PRD (F01–F21), dirancang dengan pola antarmuka yang seragam.
                    </p>
                </div>

                <div class="mt-8 grid grid-cols-1 gap-4 md:grid-cols-2">
                    <article
                        v-for="role in roleHighlights"
                        :key="role.role"
                        class="flex flex-col justify-between rounded-lg border border-hairline border-t-2 bg-white p-5 shadow-xs transition hover:border-neutral-300"
                        :class="roleToneMap[role.role]?.border || 'border-t-primary-500'"
                    >
                        <div>
                            <div class="flex items-center justify-between gap-2">
                                <StatusBadge :tone="roleToneMap[role.role]?.badge || 'primary'" dot>
                                    {{ role.badge }}
                                </StatusBadge>
                                <span class="font-mono text-[11px] font-semibold uppercase text-ink-muted">
                                    {{ role.role }}
                                </span>
                            </div>

                            <h3 class="mt-4 text-lg font-semibold tracking-tight text-ink">
                                {{ role.title }}
                            </h3>
                            <p class="mt-0.5 text-xs font-medium text-ink-secondary">
                                {{ role.subtitle }}
                            </p>
                            <p class="mt-2.5 text-sm leading-relaxed text-ink-secondary">
                                {{ role.description }}
                            </p>

                            <ul class="mt-4 flex flex-wrap gap-1.5">
                                <li
                                    v-for="point in role.highlights"
                                    :key="point"
                                    class="rounded-md border border-hairline bg-canvas px-2 py-0.5 text-xs text-ink-secondary"
                                >
                                    {{ point }}
                                </li>
                            </ul>
                        </div>

                        <div class="mt-5 flex items-center justify-between border-t border-hairline pt-4">
                            <span class="text-xs text-ink-muted">Evaluasi UI</span>
                            <Link
                                :href="route(role.routeName)"
                                class="inline-flex h-8 items-center rounded-md border border-primary-200 bg-primary-50 px-3 text-xs font-medium text-primary-700 transition hover:bg-primary-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                            >
                                Lihat Panel →
                            </Link>
                        </div>
                    </article>
                </div>
            </section>

            <!-- Public Verified Directory Preview (F10 & F11) -->
            <section id="direktori" class="border-y border-hairline bg-white">
                <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                        <div>
                            <p class="font-mono text-xs uppercase tracking-wider text-ink-muted">
                                Direktori Publik (F10 – F11)
                            </p>
                            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink">
                                UMKM Kota Bandung Terverifikasi
                            </h2>
                        </div>

                        <div class="flex flex-col gap-2 sm:flex-row sm:items-end">
                            <div>
                                <label for="landing-umkm-search" class="sr-only">
                                    Cari UMKM atau Wilayah
                                </label>
                                <input
                                    id="landing-umkm-search"
                                    v-model="searchQuery"
                                    type="search"
                                    placeholder="Cari nama usaha, kecamatan, atau kebutuhan..."
                                    class="h-9 w-full rounded-md border border-hairline bg-canvas px-3 text-sm text-ink placeholder-neutral-400 focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary-500 sm:w-72"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 flex flex-wrap gap-1.5" role="group" aria-label="Filter kategori usaha">
                        <button
                            v-for="cat in categories"
                            :key="cat"
                            type="button"
                            class="inline-flex h-8 items-center rounded-md border px-3 text-xs font-medium transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                            :class="
                                selectedCategory === cat
                                    ? 'border-primary-600 bg-primary-600 text-white shadow-xs'
                                    : 'border-hairline bg-white text-ink-secondary hover:border-neutral-300 hover:text-ink'
                            "
                            @click="selectedCategory = cat"
                        >
                            {{ cat }}
                        </button>
                    </div>

                    <div v-if="filteredBusinesses.length" class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                        <article
                            v-for="biz in filteredBusinesses"
                            :key="biz.id"
                            class="flex flex-col justify-between rounded-lg border border-hairline bg-canvas p-5 transition hover:border-neutral-300 hover:bg-white"
                        >
                            <div>
                                <div class="flex flex-wrap items-start justify-between gap-2">
                                    <div>
                                        <span class="text-xs font-medium text-ink-muted">
                                            {{ biz.category }} · Kec. {{ biz.district }}
                                        </span>
                                        <h3 class="mt-1 text-base font-semibold text-ink">
                                            {{ biz.name }}
                                        </h3>
                                    </div>
                                    <StatusBadge tone="success" dot>
                                        Terverifikasi
                                    </StatusBadge>
                                </div>

                                <p class="mt-2.5 text-sm leading-relaxed text-ink-secondary">
                                    {{ biz.summary }}
                                </p>

                                <div class="mt-4 flex flex-wrap items-center gap-1.5">
                                    <span
                                        v-for="need in biz.needs"
                                        :key="need"
                                        class="rounded-md border border-hairline bg-white px-2 py-0.5 text-xs text-ink-secondary"
                                    >
                                        {{ need }}
                                    </span>
                                </div>
                            </div>

                            <div class="mt-5 flex items-center justify-between border-t border-hairline pt-3.5">
                                <span class="font-mono text-xs text-ink-muted">
                                    {{ biz.legalStatus }}
                                </span>
                                <button
                                    type="button"
                                    class="inline-flex h-8 items-center rounded-md border border-hairline bg-white px-3 text-xs font-medium text-ink transition hover:border-neutral-300 hover:bg-neutral-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ink"
                                    @click="selectedBusiness = biz"
                                >
                                    Detail Peluang
                                </button>
                            </div>
                        </article>
                    </div>

                    <div
                        v-else
                        class="mt-6 rounded-lg border border-dashed border-neutral-300 bg-canvas p-10 text-center"
                    >
                        <p class="text-sm font-medium text-ink">
                            Tidak ada UMKM cocok dengan filter "{{ searchQuery || selectedCategory }}"
                        </p>
                        <button
                            type="button"
                            class="mt-3 inline-flex h-8 items-center rounded-md bg-primary-600 px-3 text-xs font-medium text-white hover:bg-primary-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                            @click="searchQuery = ''; selectedCategory = 'Semua'"
                        >
                            Reset Filter
                        </button>
                    </div>
                </div>
            </section>

            <!-- Programs & Digital Solutions Section -->
            <section id="program" class="mx-auto max-w-7xl px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div class="rounded-lg border border-hairline border-t-2 border-t-success bg-white p-5 shadow-xs">
                        <div class="flex items-center justify-between gap-3 border-b border-hairline pb-4">
                            <div>
                                <p class="font-mono text-[11px] font-medium uppercase tracking-wider text-success-dark">
                                    Pembinaan Pemda (F13 – F14)
                                </p>
                                <h2 class="mt-0.5 text-lg font-semibold text-ink">
                                    Program Pembinaan Terbuka
                                </h2>
                            </div>
                            <Link
                                :href="route('evaluasi.pemda')"
                                class="text-xs font-medium text-primary-600 hover:underline"
                            >
                                Panel Pemda →
                            </Link>
                        </div>

                        <div class="mt-4 space-y-3">
                            <div
                                v-for="prog in activePrograms"
                                :key="prog.id"
                                class="rounded-lg border border-hairline bg-canvas p-4"
                            >
                                <div class="flex flex-wrap items-start justify-between gap-2">
                                    <h3 class="text-sm font-semibold text-ink">
                                        {{ prog.title }}
                                    </h3>
                                    <StatusBadge tone="success" dot>
                                        {{ prog.status }}
                                    </StatusBadge>
                                </div>
                                <p class="mt-1 text-xs text-ink-muted">
                                    {{ prog.organizer }} · {{ prog.schedule }}
                                </p>
                                <div class="mt-3">
                                    <div class="flex items-center justify-between text-xs text-ink-secondary">
                                        <span>Kuota Peserta</span>
                                        <span class="font-mono font-medium text-ink">{{ prog.quotaFilled }}/{{ prog.quotaMax }}</span>
                                    </div>
                                    <div class="mt-1.5 h-1.5 w-full overflow-hidden rounded-full bg-neutral-200">
                                        <div
                                            class="h-full rounded-full bg-gradient-to-r from-primary-600 to-secondary-500"
                                            :style="{ width: `${Math.round((prog.quotaFilled / prog.quotaMax) * 100)}%` }"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-lg border border-hairline border-t-2 border-t-secondary-500 bg-white p-5 shadow-xs">
                        <div class="flex items-center justify-between gap-3 border-b border-hairline pb-4">
                            <div>
                                <p class="font-mono text-[11px] font-medium uppercase tracking-wider text-secondary-700">
                                    Inovasi Industri (F18)
                                </p>
                                <h2 class="mt-0.5 text-lg font-semibold text-ink">
                                    Solusi Digital Mitra
                                </h2>
                            </div>
                            <Link
                                :href="route('evaluasi.mitra')"
                                class="text-xs font-medium text-primary-600 hover:underline"
                            >
                                Panel Mitra →
                            </Link>
                        </div>

                        <div class="mt-4 space-y-3">
                            <div
                                v-for="sol in digitalSolutions"
                                :key="sol.id"
                                class="rounded-lg border border-hairline bg-canvas p-4"
                            >
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <StatusBadge tone="secondary">
                                        {{ sol.category }}
                                    </StatusBadge>
                                    <span class="font-mono text-xs text-ink-secondary">
                                        {{ sol.adoptionCount }} UMKM berminat
                                    </span>
                                </div>
                                <h3 class="mt-2.5 text-sm font-semibold text-ink">
                                    {{ sol.title }}
                                </h3>
                                <p class="mt-1 text-xs text-ink-muted">
                                    Penyedia: {{ sol.provider }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="border-t border-hairline bg-white">
            <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-8 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
                <div class="flex items-center gap-3">
                    <KoneksaLogo size="sm" />
                    <span class="text-xs text-ink-muted">
                        Studi Kasus Kota Bandung · Baseline Desain PaaS Modern
                    </span>
                </div>

                <div class="flex flex-wrap gap-4 text-xs text-ink-secondary">
                    <Link :href="route('landing')" class="hover:text-ink">Landing</Link>
                    <Link :href="route('evaluasi.umkm')" class="hover:text-ink">UMKM</Link>
                    <Link :href="route('evaluasi.mitra')" class="hover:text-ink">Mitra</Link>
                    <Link :href="route('evaluasi.pemda')" class="hover:text-ink">Pemda</Link>
                    <Link :href="route('evaluasi.admin')" class="hover:text-ink">Admin</Link>
                    <Link :href="route('login')" class="hover:text-ink">Login</Link>
                    <Link :href="route('register')" class="hover:text-ink">Register</Link>
                </div>
            </div>
        </footer>

        <!-- Accessible Business Detail Modal -->
        <div
            v-if="selectedBusiness"
            class="fixed inset-0 z-50 flex items-center justify-center bg-ink-deep/50 p-4 backdrop-blur-[1px]"
            role="dialog"
            aria-modal="true"
            :aria-labelledby="`modal-biz-${selectedBusiness.id}`"
            @click.self="selectedBusiness = null"
        >
            <div class="w-full max-w-lg rounded-xl border border-hairline bg-white p-6 shadow-lg">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <StatusBadge tone="success" dot>Terverifikasi Pemda</StatusBadge>
                        <h3 :id="`modal-biz-${selectedBusiness.id}`" class="mt-2 text-lg font-semibold text-ink">
                            {{ selectedBusiness.name }}
                        </h3>
                        <p class="text-xs text-ink-muted">
                            {{ selectedBusiness.owner }} · Kec. {{ selectedBusiness.district }}
                        </p>
                    </div>
                    <button
                        type="button"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-md text-ink-muted hover:bg-neutral-100 hover:text-ink focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ink"
                        aria-label="Tutup detail UMKM"
                        @click="selectedBusiness = null"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <p class="mt-4 text-sm leading-relaxed text-ink-secondary">
                    {{ selectedBusiness.summary }}
                </p>

                <div class="mt-4 rounded-lg border border-hairline bg-canvas p-3.5 text-xs">
                    <p class="font-medium text-ink-muted">Legalitas</p>
                    <p class="mt-0.5 font-mono text-ink">{{ selectedBusiness.legalStatus }}</p>
                    <p class="mt-3 font-medium text-ink-muted">Kebutuhan Kolaborasi</p>
                    <div class="mt-1.5 flex flex-wrap gap-1.5">
                        <span
                            v-for="need in selectedBusiness.needs"
                            :key="need"
                            class="rounded-md border border-hairline bg-white px-2 py-0.5 font-medium text-ink-secondary"
                        >
                            {{ need }}
                        </span>
                    </div>
                </div>

                <div class="mt-5 flex flex-wrap justify-end gap-2">
                    <button
                        type="button"
                        class="h-9 rounded-md border border-hairline bg-white px-3.5 text-xs font-medium text-ink hover:bg-canvas focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ink"
                        @click="selectedBusiness = null"
                    >
                        Tutup
                    </button>
                    <Link
                        :href="route('evaluasi.mitra')"
                        class="inline-flex h-9 items-center rounded-md bg-primary-600 px-3.5 text-xs font-medium text-white hover:bg-primary-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                    >
                        Ajukan Proposal di Panel Mitra →
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
