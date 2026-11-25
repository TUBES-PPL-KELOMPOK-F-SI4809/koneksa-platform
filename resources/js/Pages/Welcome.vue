<script setup>
import { onMounted, onUnmounted, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import KoneksaLogo from '@/Components/KoneksaLogo.vue';

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
        type: Object,
        default: () => ({
            businesses_count: 0,
            categories_count: 0,
        }),
    },
    featured_businesses: {
        type: Array,
        default: () => [],
    },
});

const mobileNavOpen = ref(false);

function handleEscape(event) {
    if (event.key === 'Escape' && mobileNavOpen.value) {
        mobileNavOpen.value = false;
    }
}

onMounted(() => document.addEventListener('keydown', handleEscape));
onUnmounted(() => document.removeEventListener('keydown', handleEscape));

function getDirectoryUrl() {
    if (typeof route === 'function' && route().has('businesses.index')) {
        return route('businesses.index');
    }
    return '/businesses';
}

function getBusinessDetailUrl(businessId) {
    if (typeof route === 'function' && route().has('businesses.show')) {
        return route('businesses.show', businessId);
    }
    if (typeof route === 'function' && route().has('public.businesses.show')) {
        return route('public.businesses.show', businessId);
    }
    return `/businesses/${businessId}`;
}
</script>

<template>
    <div class="min-h-screen bg-canvas font-sans text-ink antialiased">
        <Head title="KONEKSA — Platform Kolaborasi Terpadu UMKM Kota Bandung" />

        <!-- Header Navigasi Publik (Vercel-style Hairline Navbar) -->
        <header class="sticky top-0 z-40 border-b border-hairline bg-white/95 backdrop-blur-md">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-6">
                    <Link
                        href="/"
                        class="flex items-center gap-2 rounded-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                    >
                        <KoneksaLogo class="h-8 w-auto" />
                    </Link>

                    <nav class="hidden md:flex items-center gap-6 text-xs font-medium text-ink-secondary" aria-label="Navigasi Utama">
                        <a href="#tentang" class="hover:text-ink transition">Tentang</a>
                        <a href="#manfaat" class="hover:text-ink transition">Manfaat 3 Peran</a>
                        <Link :href="getDirectoryUrl()" class="hover:text-ink transition font-semibold text-primary-600">
                            Direktori UMKM
                        </Link>
                        <a href="#program" class="hover:text-ink transition">Program &amp; Solusi</a>
                    </nav>
                </div>

                <div class="hidden md:flex items-center gap-3 text-xs">
                    <template v-if="$page.props.auth?.user">
                        <Link
                            :href="route('dashboard')"
                            class="inline-flex h-9 items-center rounded-xl bg-primary-600 px-4 font-semibold text-white shadow-xs hover:bg-primary-500 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                        >
                            Ke Dashboard ({{ $page.props.auth.user.name }})
                        </Link>
                    </template>
                    <template v-else>
                        <Link
                            v-if="canLogin"
                            :href="route('login')"
                            class="inline-flex h-9 items-center rounded-xl px-3.5 font-medium text-ink-secondary hover:text-ink transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                        >
                            Masuk
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="inline-flex h-9 items-center rounded-xl bg-primary-600 px-4 font-semibold text-white shadow-xs hover:bg-primary-500 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                        >
                            Daftar Akun
                        </Link>
                    </template>
                </div>

                <!-- Mobile Menu Button -->
                <button
                    type="button"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-hairline text-ink-secondary hover:bg-canvas hover:text-ink focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 md:hidden"
                    :aria-expanded="mobileNavOpen"
                    aria-label="Buka menu navigasi"
                    @click="mobileNavOpen = !mobileNavOpen"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path v-if="!mobileNavOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Mobile Nav Drawer -->
            <div v-if="mobileNavOpen" class="border-t border-hairline bg-white px-4 py-4 md:hidden">
                <nav class="flex flex-col gap-2 text-xs" aria-label="Navigasi Mobile">
                    <a href="#tentang" class="rounded-xl px-3 py-2 font-medium text-ink-secondary hover:bg-canvas hover:text-ink" @click="mobileNavOpen = false">
                        Tentang KONEKSA
                    </a>
                    <a href="#manfaat" class="rounded-xl px-3 py-2 font-medium text-ink-secondary hover:bg-canvas hover:text-ink" @click="mobileNavOpen = false">
                        Manfaat 3 Peran
                    </a>
                    <Link :href="getDirectoryUrl()" class="rounded-xl px-3 py-2 font-semibold text-primary-600 hover:bg-canvas" @click="mobileNavOpen = false">
                        Direktori UMKM
                    </Link>
                    <a href="#program" class="rounded-xl px-3 py-2 font-medium text-ink-secondary hover:bg-canvas hover:text-ink" @click="mobileNavOpen = false">
                        Program &amp; Solusi
                    </a>

                    <div class="mt-3 flex flex-col gap-2 border-t border-hairline pt-3">
                        <template v-if="$page.props.auth?.user">
                            <Link :href="route('dashboard')" class="inline-flex h-10 items-center justify-center rounded-xl bg-primary-600 px-4 font-semibold text-white">
                                Dashboard ({{ $page.props.auth.user.name }})
                            </Link>
                        </template>
                        <template v-else>
                            <Link v-if="canLogin" :href="route('login')" class="inline-flex h-10 items-center justify-center rounded-xl border border-hairline bg-white px-4 font-medium text-ink">
                                Masuk
                            </Link>
                            <Link v-if="canRegister" :href="route('register')" class="inline-flex h-10 items-center justify-center rounded-xl bg-primary-600 px-4 font-semibold text-white">
                                Daftar Akun Baru
                            </Link>
                        </template>
                    </div>
                </nav>
            </div>
        </header>

        <main>
            <!-- Hero Section (FR-33.1 & FR-33.2) -->
            <section id="tentang" class="border-b border-hairline bg-gradient-to-b from-primary-50/70 via-white to-canvas py-14 sm:py-20">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="max-w-3xl">
                        <span class="inline-flex items-center gap-2 rounded-full border border-primary-200 bg-primary-50 px-3.5 py-1 text-xs font-semibold text-primary-700 shadow-2xs">
                            <span class="h-2 w-2 rounded-full bg-primary-600 animate-pulse"></span>
                            Infrastruktur Kolaborasi Bisnis Kota Bandung
                        </span>

                        <h1 class="mt-5 text-3xl font-extrabold tracking-tight text-ink sm:text-4xl lg:text-5xl leading-tight">
                            Menghubungkan
                            <span class="bg-gradient-to-r from-primary-600 to-secondary-600 bg-clip-text text-transparent">
                                UMKM, Mitra Industri,
                            </span>
                            dan Sinergi Daerah.
                        </h1>

                        <p class="mt-4 text-sm leading-relaxed text-ink-secondary sm:text-base">
                            KONEKSA adalah platform kolaborasi terpadu yang memfasilitasi pencatatan usaha mandiri bagi pelaku UMKM, kurasi kemitraan siap pasok bagi mitra industri, serta transparansi pembinaan potensi ekonomi lokal Kota Bandung secara terukur.
                        </p>

                        <!-- CTA Buttons (FR-33.2) -->
                        <div class="mt-8 flex flex-wrap items-center gap-3">
                            <Link
                                v-if="canRegister && !$page.props.auth?.user"
                                :href="route('register')"
                                data-testid="hero-register-cta"
                                class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-primary-600 px-6 text-xs font-bold text-white shadow-sm transition hover:bg-primary-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                            >
                                Mulai Kolaborasi — Daftar Sekarang
                                <span aria-hidden="true">&rarr;</span>
                            </Link>

                            <Link
                                :href="getDirectoryUrl()"
                                data-testid="hero-directory-cta"
                                class="inline-flex h-11 items-center justify-center rounded-xl border border-hairline bg-white px-5 text-xs font-semibold text-ink shadow-xs transition hover:bg-neutral-50 hover:border-neutral-300 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                            >
                                Jelajahi Direktori UMKM
                            </Link>
                        </div>

                        <!-- Metric Summary Strip (Data Agregat Aman Non-Privat) -->
                        <div class="mt-10 grid grid-cols-2 gap-4 border-t border-hairline pt-6 sm:grid-cols-3 max-w-xl">
                            <div>
                                <p class="text-2xl font-extrabold text-primary-600 tabular-nums">
                                    {{ stats.businesses_count }}+
                                </p>
                                <p class="text-xs font-medium text-ink">UMKM Terpublikasi</p>
                                <p class="text-[11px] text-ink-muted">Terdaftar di platform</p>
                            </div>
                            <div>
                                <p class="text-2xl font-extrabold text-secondary-600 tabular-nums">
                                    {{ stats.categories_count }}
                                </p>
                                <p class="text-xs font-medium text-ink">Sektor Usaha Aktif</p>
                                <p class="text-[11px] text-ink-muted">Kuliner, Fashion &amp; Kriya</p>
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <p class="text-2xl font-extrabold text-ink tabular-nums">
                                    Bandung
                                </p>
                                <p class="text-xs font-medium text-ink">Cakupan Wilayah</p>
                                <p class="text-[11px] text-ink-muted">Kecamatan se-Kota Bandung</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Nilai & Manfaat 3 Pemangku Kepentingan (FR-33.1) -->
            <section id="manfaat" class="mx-auto max-w-7xl px-4 py-14 sm:px-6 sm:py-20 lg:px-8">
                <div class="max-w-2xl">
                    <span class="rounded-full bg-secondary-50 px-3 py-1 text-xs font-semibold text-secondary-700">
                        Pilar Ekosistem
                    </span>
                    <h2 class="mt-3 text-2xl font-bold tracking-tight text-ink sm:text-3xl">
                        Manfaat Bagi Tiga Pilar Kolaborasi
                    </h2>
                    <p class="mt-2 text-xs sm:text-sm leading-relaxed text-ink-secondary">
                        Dirancang khusus untuk menjawab kebutuhan spesifik setiap pihak dalam ekosistem pengembangan ekonomi daerah.
                    </p>
                </div>

                <div class="mt-10 grid grid-cols-1 gap-6 md:grid-cols-3">
                    <!-- Pilar 1: UMKM -->
                    <article class="flex flex-col justify-between rounded-2xl border border-hairline border-t-4 border-t-primary-500 bg-white p-6 shadow-xs transition hover:shadow-md">
                        <div>
                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary-50 text-primary-600">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <span class="mt-4 inline-block text-[11px] font-semibold uppercase tracking-wider text-primary-600">
                                Pilar Pelaku Usaha
                            </span>
                            <h3 class="mt-1 text-base font-bold text-ink">
                                Pelaku UMKM
                            </h3>
                            <p class="mt-2 text-xs text-ink-secondary leading-relaxed">
                                Kelola persediaan stok barang secara terstruktur, miliki etalase profil usaha publik terverifikasi, dan terima tawaran kerja sama bisnis tanpa perantara.
                            </p>
                            <ul class="mt-4 space-y-2 text-xs text-ink">
                                <li class="flex items-center gap-2">
                                    <span class="text-success-dark font-bold">✓</span> Pencatatan stok dan restock otomatis
                                </li>
                                <li class="flex items-center gap-2">
                                    <span class="text-success-dark font-bold">✓</span> Profil usaha publik &amp; katalog produk
                                </li>
                                <li class="flex items-center gap-2">
                                    <span class="text-success-dark font-bold">✓</span> Inbox proposal kemitraan masuk
                                </li>
                            </ul>
                        </div>
                        <div class="mt-6 pt-4 border-t border-hairline">
                            <Link :href="canRegister ? route('register') : route('login')" class="text-xs font-semibold text-primary-600 hover:text-primary-700">
                                Daftar Sebagai UMKM &rarr;
                            </Link>
                        </div>
                    </article>

                    <!-- Pilar 2: Mitra Industri -->
                    <article class="flex flex-col justify-between rounded-2xl border border-hairline border-t-4 border-t-secondary-500 bg-white p-6 shadow-xs transition hover:shadow-md">
                        <div>
                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-secondary-50 text-secondary-600">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <span class="mt-4 inline-block text-[11px] font-semibold uppercase tracking-wider text-secondary-600">
                                Pilar Korporasi &amp; Mitra
                            </span>
                            <h3 class="mt-1 text-base font-bold text-ink">
                                Mitra Industri
                            </h3>
                            <p class="mt-2 text-xs text-ink-secondary leading-relaxed">
                                Temukan supplier lokal terkurasi, ketahui rincian kebutuhan usaha yang dibuka UMKM, dan ajukan proposal kolaborasi bisnis resmi melalui platform.
                            </p>
                            <ul class="mt-4 space-y-2 text-xs text-ink">
                                <li class="flex items-center gap-2">
                                    <span class="text-success-dark font-bold">✓</span> Akses direktori UMKM terverifikasi
                                </li>
                                <li class="flex items-center gap-2">
                                    <span class="text-success-dark font-bold">✓</span> Pengajuan proposal kerja sama terstandar
                                </li>
                                <li class="flex items-center gap-2">
                                    <span class="text-success-dark font-bold">✓</span> Transparansi status respon proposal
                                </li>
                            </ul>
                        </div>
                        <div class="mt-6 pt-4 border-t border-hairline">
                            <Link :href="getDirectoryUrl()" class="text-xs font-semibold text-secondary-700 hover:text-secondary-800">
                                Jelajahi Peluang Mitra &rarr;
                            </Link>
                        </div>
                    </article>

                    <!-- Pilar 3: Pemerintah Daerah -->
                    <article class="flex flex-col justify-between rounded-2xl border border-hairline border-t-4 border-t-success bg-white p-6 shadow-xs transition hover:shadow-md">
                        <div>
                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-success-light text-success-dark">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <span class="mt-4 inline-block text-[11px] font-semibold uppercase tracking-wider text-success-dark">
                                Sinergi Pembinaan
                            </span>
                            <h3 class="mt-1 text-base font-bold text-ink">
                                Pemerintah Daerah
                            </h3>
                            <p class="mt-2 text-xs text-ink-secondary leading-relaxed">
                                Memfasilitasi verifikasi administratif, pemantauan sebaran sektor usaha per wilayah, serta mendukung penyelarasan program pendampingan ekonomi lokal.
                            </p>
                            <ul class="mt-4 space-y-2 text-xs text-ink">
                                <li class="flex items-center gap-2">
                                    <span class="text-success-dark font-bold">✓</span> Data agregat sebaran usaha wilayah
                                </li>
                                <li class="flex items-center gap-2">
                                    <span class="text-success-dark font-bold">✓</span> Verifikasi administratif terstandar
                                </li>
                                <li class="flex items-center gap-2">
                                    <span class="text-success-dark font-bold">✓</span> Sinergi program pembinaan UMKM
                                </li>
                            </ul>
                        </div>
                        <div class="mt-6 pt-4 border-t border-hairline">
                            <span class="text-[11px] text-ink-muted">Inisiatif Sinergi Wilayah</span>
                        </div>
                    </article>
                </div>
            </section>

            <!-- Cuplikan Direktori UMKM Terpublikasi (FR-33.1) -->
            <section class="border-y border-hairline bg-white py-14 sm:py-20">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                        <div>
                            <span class="rounded-full bg-primary-50 px-3 py-1 text-xs font-semibold text-primary-700">
                                Pratinjau Direktori
                            </span>
                            <h2 class="mt-3 text-2xl font-bold tracking-tight text-ink sm:text-3xl">
                                Profil Publik UMKM Terverifikasi
                            </h2>
                            <p class="mt-2 text-xs sm:text-sm text-ink-secondary">
                                Cuplikan pelaku UMKM terpublikasi yang siap menjalin kolaborasi dan kemitraan.
                            </p>
                        </div>

                        <Link
                            :href="getDirectoryUrl()"
                            class="inline-flex h-10 items-center gap-1.5 font-semibold text-xs text-primary-600 hover:text-primary-700 transition"
                        >
                            <span>Lihat Seluruh Direktori</span>
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </Link>
                    </div>

                    <!-- Grid Preview Kartu UMKM -->
                    <div v-if="featured_businesses && featured_businesses.length > 0" class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <article
                            v-for="b in featured_businesses"
                            :key="b.id"
                            class="flex flex-col justify-between rounded-2xl border border-hairline bg-canvas/30 p-5 shadow-xs transition hover:border-primary-300 hover:bg-white hover:shadow-md"
                        >
                            <div>
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary-100 text-primary-700 font-bold text-base">
                                        {{ b.name?.charAt(0)?.toUpperCase() ?? 'U' }}
                                    </div>
                                    <div class="flex flex-col items-end gap-1">
                                        <span class="rounded-full bg-primary-50 px-2 py-0.5 text-[10px] font-semibold text-primary-700">
                                            {{ b.category_name }}
                                        </span>
                                        <span v-if="b.is_verified" class="inline-flex items-center gap-1 rounded-full bg-success-light px-2 py-0.5 text-[10px] font-semibold text-success-dark">
                                            Terverifikasi
                                        </span>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <h3 class="text-sm font-bold text-ink line-clamp-1">
                                        {{ b.name }}
                                    </h3>
                                    <p class="text-[11px] text-ink-muted mt-0.5">
                                        {{ b.district ? `Kec. ${b.district}` : 'Kota Bandung' }}
                                    </p>
                                    <p class="mt-2 text-xs text-ink-secondary line-clamp-2 leading-relaxed">
                                        {{ b.description || 'Pelaku UMKM binaan Kota Bandung.' }}
                                    </p>
                                </div>
                            </div>

                            <div class="mt-4 pt-3 border-t border-hairline flex items-center justify-between">
                                <span class="text-[11px] text-ink-muted">Siap Kolaborasi</span>
                                <Link :href="getBusinessDetailUrl(b.id)" class="text-xs font-semibold text-primary-600 hover:text-primary-700">
                                    Detail Profil &rarr;
                                </Link>
                            </div>
                        </article>
                    </div>

                    <!-- Fallback bila belum ada usaha terpublikasi -->
                    <div v-else class="mt-8 rounded-2xl border border-dashed border-hairline p-8 text-center text-xs text-ink-secondary">
                        Belum ada data UMKM terpublikasi. Pelaku UMKM dapat mempublikasikan profil usahanya melalui panel kendali.
                    </div>
                </div>
            </section>

            <!-- Sinergi Program & Solusi Publik (FR-33.1 / KSA-176) -->
            <section id="program" class="mx-auto max-w-7xl px-4 py-14 sm:px-6 sm:py-20 lg:px-8">
                <div class="max-w-2xl">
                    <span class="rounded-full bg-success-light px-3 py-1 text-xs font-semibold text-success-dark">
                        Program Publik
                    </span>
                    <h2 class="mt-3 text-2xl font-bold tracking-tight text-ink sm:text-3xl">
                        Program Pendampingan &amp; Solusi Kemitraan
                    </h2>
                    <p class="mt-2 text-xs sm:text-sm text-ink-secondary leading-relaxed">
                        Inisiatif fasilitasi pengembangan usaha yang disinergikan melalui ekosistem KONEKSA.
                    </p>
                </div>

                <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div class="rounded-2xl border border-hairline bg-white p-5 shadow-xs">
                        <span class="inline-block rounded-md bg-primary-50 px-2 py-0.5 text-[10px] font-bold text-primary-700">
                            Akses Pasar
                        </span>
                        <h3 class="mt-3 text-sm font-bold text-ink">
                            Integrasi Pasokan Ritel Modern
                        </h3>
                        <p class="mt-1 text-xs text-ink-secondary leading-relaxed">
                            Penghubung kurasi produk UMKM ke jaringan minimarket dan pasar modern Kota Bandung.
                        </p>
                    </div>

                    <div class="rounded-2xl border border-hairline bg-white p-5 shadow-xs">
                        <span class="inline-block rounded-md bg-secondary-50 px-2 py-0.5 text-[10px] font-bold text-secondary-700">
                            Pemberdayaan
                        </span>
                        <h3 class="mt-3 text-sm font-bold text-ink">
                            Pelatihan Manajemen &amp; Restock
                        </h3>
                        <p class="mt-1 text-xs text-ink-secondary leading-relaxed">
                            Pendampingan digitalisasi pembukuan kasir, pengendalian persediaan, dan mitigasi stok rendah.
                        </p>
                    </div>

                    <div class="rounded-2xl border border-hairline bg-white p-5 shadow-xs">
                        <span class="inline-block rounded-md bg-success-light px-2 py-0.5 text-[10px] font-bold text-success-dark">
                            Legalitas
                        </span>
                        <h3 class="mt-3 text-sm font-bold text-ink">
                            Fasilitasi Sertifikasi &amp; NIB
                        </h3>
                        <p class="mt-1 text-xs text-ink-secondary leading-relaxed">
                            Verifikasi administratif profil usaha guna memudahkan pengurusan izin edar dan keabsahan usaha.
                        </p>
                    </div>
                </div>
            </section>

            <!-- CTA Banner Final (FR-33.2) -->
            <section class="border-t border-hairline bg-primary-900 py-14 text-white sm:py-16">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center">
                    <h2 class="text-2xl font-bold tracking-tight sm:text-3xl text-white">
                        Siap Memulai Kolaborasi Usaha Anda?
                    </h2>
                    <p class="mx-auto mt-3 max-w-xl text-xs sm:text-sm text-primary-200 leading-relaxed">
                        Bergabunglah bersama ratusan pelaku UMKM dan mitra industri di Kota Bandung. Buka akun sekarang secara mudah dan terintegrasi.
                    </p>

                    <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                        <Link
                            v-if="canRegister && !$page.props.auth?.user"
                            :href="route('register')"
                            class="inline-flex h-11 items-center justify-center rounded-xl bg-white px-6 text-xs font-bold text-primary-900 shadow-sm transition hover:bg-primary-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white"
                        >
                            Daftar Akun Baru
                        </Link>
                        <Link
                            v-if="canLogin && !$page.props.auth?.user"
                            :href="route('login')"
                            class="inline-flex h-11 items-center justify-center rounded-xl border border-primary-700 bg-primary-800 px-6 text-xs font-semibold text-white transition hover:bg-primary-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white"
                        >
                            Masuk ke Akun
                        </Link>
                        <Link
                            v-if="$page.props.auth?.user"
                            :href="route('dashboard')"
                            class="inline-flex h-11 items-center justify-center rounded-xl bg-white px-6 text-xs font-bold text-primary-900 shadow-sm transition hover:bg-primary-50"
                        >
                            Buka Dashboard Saya
                        </Link>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer Publik -->
        <footer class="border-t border-hairline bg-white py-8 text-xs text-ink-muted">
            <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-4 px-4 sm:flex-row sm:px-6 lg:px-8">
                <div class="flex items-center gap-2">
                    <KoneksaLogo class="h-6 w-auto" />
                    <span>&copy; 2026 KONEKSA Platform · Kota Bandung</span>
                </div>
                <div class="flex items-center gap-4 text-ink-secondary">
                    <a href="#tentang" class="hover:text-ink">Tentang</a>
                    <Link :href="getDirectoryUrl()" class="hover:text-ink">Direktori UMKM</Link>
                    <a href="#program" class="hover:text-ink">Program</a>
                </div>
            </div>
        </footer>
    </div>
</template>
