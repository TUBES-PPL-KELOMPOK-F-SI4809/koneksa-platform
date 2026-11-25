<script setup>
import { computed, ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import KoneksaLogo from '@/Components/KoneksaLogo.vue';

const props = defineProps({
    businesses: {
        type: Object,
        required: true,
    },
    categories: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({
            search: '',
            category_id: null,
        }),
    },
    total_count: {
        type: Number,
        default: 0,
    },
});

const searchQuery = ref(props.filters.search || '');
const selectedCategoryId = ref(props.filters.category_id || null);
let debounceTimer = null;

function applyFilter() {
    router.get(
        route('businesses.index'),
        {
            search: searchQuery.value ? searchQuery.value : undefined,
            category_id: selectedCategoryId.value ? selectedCategoryId.value : undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    );
}

function handleSearchInput() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        applyFilter();
    }, 350);
}

function selectCategory(categoryId) {
    if (selectedCategoryId.value === categoryId) {
        selectedCategoryId.value = null;
    } else {
        selectedCategoryId.value = categoryId;
    }
    applyFilter();
}

function resetFilters() {
    searchQuery.value = '';
    selectedCategoryId.value = null;
    router.get(route('businesses.index'), {}, { preserveScroll: true, replace: true });
}

const hasActiveFilters = computed(() => {
    return Boolean(searchQuery.value || selectedCategoryId.value);
});

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
        <Head title="Direktori Publik UMKM — KONEKSA" />

        <!-- Header Navigasi Publik -->
        <header class="sticky top-0 z-40 border-b border-hairline bg-white/95 backdrop-blur-md">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
                <div class="flex items-center gap-6">
                    <Link href="/" class="flex items-center gap-2 rounded-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500">
                        <KoneksaLogo class="h-8 w-auto" />
                    </Link>
                    <nav class="hidden md:flex items-center gap-4 text-xs font-medium text-ink-secondary">
                        <Link href="/" class="hover:text-ink transition">Beranda</Link>
                        <span class="text-hairline">/</span>
                        <span class="text-ink font-semibold">Direktori UMKM</span>
                    </nav>
                </div>

                <div class="flex items-center gap-3 text-xs">
                    <template v-if="$page.props.auth?.user">
                        <Link
                            :href="route('dashboard')"
                            class="inline-flex h-9 items-center rounded-xl bg-canvas px-3.5 font-medium text-ink hover:bg-neutral-200 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                        >
                            Ke Dashboard ({{ $page.props.auth.user.name }})
                        </Link>
                    </template>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="inline-flex h-9 items-center rounded-xl px-3.5 font-medium text-ink-secondary hover:text-ink transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                        >
                            Masuk
                        </Link>
                        <Link
                            :href="route('register')"
                            class="inline-flex h-9 items-center rounded-xl bg-primary-600 px-3.5 font-semibold text-white shadow-xs hover:bg-primary-500 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                        >
                            Daftar Akun
                        </Link>
                    </template>
                </div>
            </div>
        </header>

        <!-- Hero Section & Pencarian (FR-10.1 & FR-10.2) -->
        <section class="border-b border-hairline bg-white py-10 sm:py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="max-w-2xl">
                    <span class="inline-flex items-center gap-1.5 rounded-full border border-primary-200 bg-primary-50 px-3 py-1 text-xs font-semibold text-primary-700">
                        <span class="h-1.5 w-1.5 rounded-full bg-primary-600"></span>
                        Direktori Usaha Terkurasi
                    </span>
                    <h1 class="mt-3 text-2xl font-bold tracking-tight text-ink sm:text-3xl">
                        Temukan Pelaku UMKM &amp; Peluang Kolaborasi
                    </h1>
                    <p class="mt-2 text-sm text-ink-secondary leading-relaxed">
                        Jelajahi profil publik UMKM Kota Bandung yang siap bermitra dengan industri, investor, dan masyarakat umum.
                    </p>
                </div>

                <!-- Input Pencarian & Filter Kategori (FR-10.2) -->
                <div class="mt-6 space-y-4">
                    <!-- Search Input -->
                    <div class="relative max-w-xl">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-ink-muted">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input
                            v-model="searchQuery"
                            type="search"
                            data-testid="search-input"
                            placeholder="Cari berdasarkan nama UMKM, produk, atau kecamatan..."
                            class="h-11 w-full rounded-xl border border-hairline bg-canvas pl-10 pr-10 text-xs text-ink placeholder-ink-muted focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary-500"
                            @input="handleSearchInput"
                        />
                        <button
                            v-if="searchQuery"
                            type="button"
                            class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-ink-muted hover:text-ink text-xs"
                            @click="searchQuery = ''; applyFilter();"
                        >
                            ✕
                        </button>
                    </div>

                    <!-- Category Pills Filter (FR-10.2) -->
                    <div class="flex flex-wrap items-center gap-2 pt-1">
                        <span class="text-xs font-semibold text-ink-muted mr-1">Kategori:</span>

                        <!-- All Categories Pill -->
                        <button
                            type="button"
                            data-testid="category-all-btn"
                            class="inline-flex h-8 items-center rounded-xl px-3.5 text-xs font-medium transition"
                            :class="selectedCategoryId === null
                                ? 'bg-primary-600 text-white shadow-xs'
                                : 'border border-hairline bg-white text-ink-secondary hover:bg-neutral-50 hover:text-ink'"
                            @click="selectCategory(null)"
                        >
                            Semua Kategori
                        </button>

                        <!-- Dynamic Categories -->
                        <button
                            v-for="cat in categories"
                            :key="cat.id"
                            type="button"
                            :data-testid="`category-btn-${cat.id}`"
                            class="inline-flex h-8 items-center rounded-xl px-3.5 text-xs font-medium transition"
                            :class="selectedCategoryId === cat.id
                                ? 'bg-primary-600 text-white shadow-xs'
                                : 'border border-hairline bg-white text-ink-secondary hover:bg-neutral-50 hover:text-ink'"
                            @click="selectCategory(cat.id)"
                        >
                            {{ cat.name }}
                        </button>

                        <!-- Reset Filter Button -->
                        <button
                            v-if="hasActiveFilters"
                            type="button"
                            data-testid="reset-filters-btn"
                            class="inline-flex h-8 items-center gap-1 text-xs font-semibold text-danger-500 hover:text-danger-600 px-2 underline"
                            @click="resetFilters"
                        >
                            Reset Filter
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Content: Daftar Kartu UMKM (FR-10.1) -->
        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Counter & Status Bar -->
            <div class="mb-6 flex flex-wrap items-center justify-between gap-3 text-xs text-ink-secondary border-b border-hairline pb-4">
                <div>
                    Menampilkan <strong class="text-ink">{{ businesses.data?.length ?? 0 }}</strong> dari <strong class="text-ink">{{ total_count }}</strong> profil UMKM terpublikasi
                    <span v-if="searchQuery" class="text-ink-muted ml-1">
                        untuk kata kunci "<em>{{ searchQuery }}</em>"
                    </span>
                </div>
            </div>

            <!-- Grid Kartu UMKM -->
            <div v-if="businesses.data && businesses.data.length > 0" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <article
                    v-for="b in businesses.data"
                    :key="b.id"
                    data-testid="business-card"
                    class="group flex flex-col justify-between rounded-2xl border border-hairline bg-white p-5 shadow-xs transition hover:border-primary-300 hover:shadow-md"
                >
                    <div>
                        <!-- Header Kartu: Logo/Avatar + Badge Kategori & Status Verifikasi -->
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-primary-100 text-primary-700 font-bold text-lg shadow-2xs">
                                <template v-if="b.logo_path">
                                    <img :src="b.logo_path" :alt="b.name" class="h-12 w-12 rounded-xl object-cover" />
                                </template>
                                <template v-else>
                                    {{ b.name?.charAt(0)?.toUpperCase() ?? 'U' }}
                                </template>
                            </div>

                            <div class="flex flex-col items-end gap-1.5">
                                <span class="rounded-full bg-primary-50 px-2.5 py-0.5 text-[11px] font-semibold text-primary-700">
                                    {{ b.category_name }}
                                </span>
                                <span
                                    v-if="b.is_verified"
                                    class="inline-flex items-center gap-1 rounded-full bg-success-light px-2 py-0.5 text-[11px] font-semibold text-success-dark"
                                >
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Terverifikasi
                                </span>
                            </div>
                        </div>

                        <!-- Info Usaha: Nama & Lokasi -->
                        <div class="mt-4">
                            <h2 class="text-base font-bold text-ink group-hover:text-primary-600 transition line-clamp-1">
                                <Link :href="getBusinessDetailUrl(b.id)" class="focus:outline-none">
                                    {{ b.name }}
                                </Link>
                            </h2>

                            <p class="mt-1 flex items-center gap-1 text-xs text-ink-muted">
                                <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="truncate">
                                    {{ b.district ? `Kec. ${b.district}` : 'Kota Bandung' }}{{ b.subdistrict ? `, Kel. ${b.subdistrict}` : '' }}
                                </span>
                            </p>
                        </div>

                        <!-- Deskripsi Singkat Usaha -->
                        <p class="mt-3 text-xs text-ink-secondary leading-relaxed line-clamp-2">
                            {{ b.description || 'Pelaku usaha mikro, kecil, dan menengah lokal Kota Bandung.' }}
                        </p>

                        <!-- Kebutuhan Kemitraan (jika ada) -->
                        <div v-if="b.business_needs" class="mt-3 rounded-xl border border-hairline bg-canvas/60 p-2.5">
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-ink-muted">Kebutuhan Kolaborasi:</p>
                            <p class="mt-0.5 text-xs font-medium text-ink line-clamp-2">
                                {{ b.business_needs }}
                            </p>
                        </div>
                    </div>

                    <!-- Footer Kartu: Tombol Aksi Detail Profil (FR-11) -->
                    <div class="mt-5 pt-3 border-t border-hairline flex items-center justify-between text-xs">
                        <span class="text-[11px] text-ink-muted">
                            Bergabung {{ b.published_date_human }}
                        </span>

                        <Link
                            :href="getBusinessDetailUrl(b.id)"
                            class="inline-flex items-center gap-1.5 font-semibold text-primary-600 hover:text-primary-700 transition"
                        >
                            <span>Lihat Profil</span>
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </Link>
                    </div>
                </article>
            </div>

            <!-- Empty State (Jika tidak ada hasil pencarian/filter) -->
            <div v-else class="rounded-2xl border border-dashed border-hairline bg-white p-12 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-canvas text-ink-muted">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <h3 class="mt-3 text-base font-bold text-ink">
                    Tidak ada UMKM yang ditemukan
                </h3>
                <p class="mt-1 text-xs text-ink-secondary max-w-sm mx-auto">
                    Tidak ada profil usaha yang cocok dengan kriteria pencarian Anda. Silakan coba kata kunci lain atau bersihkan filter.
                </p>
                <div class="mt-4">
                    <button
                        type="button"
                        class="inline-flex h-9 items-center rounded-xl bg-primary-600 px-4 text-xs font-semibold text-white shadow-xs hover:bg-primary-500 transition"
                        @click="resetFilters"
                    >
                        Reset Filter Pencarian
                    </button>
                </div>
            </div>

            <!-- Paginasi Responsif -->
            <div v-if="businesses.links && businesses.links.length > 3" class="mt-8 flex items-center justify-center gap-1 text-xs">
                <template v-for="(link, idx) in businesses.links" :key="idx">
                    <span
                        v-if="!link.url"
                        class="inline-flex h-9 min-w-9 items-center justify-center rounded-xl px-3 text-ink-muted opacity-50"
                        v-html="link.label"
                    />
                    <Link
                        v-else
                        :href="link.url"
                        class="inline-flex h-9 min-w-9 items-center justify-center rounded-xl px-3 transition font-medium"
                        :class="link.active
                            ? 'bg-primary-600 text-white font-semibold shadow-xs'
                            : 'border border-hairline bg-white text-ink hover:bg-neutral-50'"
                        v-html="link.label"
                    />
                </template>
            </div>
        </main>
    </div>
</template>

