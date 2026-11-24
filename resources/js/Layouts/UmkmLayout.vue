<script setup>
import { computed, nextTick, onMounted, onUnmounted, ref, useSlots } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import KoneksaLogo from '@/Components/KoneksaLogo.vue';
import {
    Sidebar, SidebarContent, SidebarFooter, SidebarGroup, SidebarGroupContent,
    SidebarGroupLabel, SidebarHeader, SidebarInset, SidebarMenu, SidebarMenuBadge,
    SidebarMenuButton, SidebarMenuItem, SidebarProvider, SidebarRail, SidebarTrigger,
} from '@/Components/ui/sidebar';

defineProps({
    title: String,
    subtitle: String,
    inventoryCount: Number,
    guideTitle: { type: String, default: 'Panduan restock' },
    guideDescription: { type: String, default: 'Tiga langkah untuk mencatat barang masuk.' },
    guideSteps: {
        type: Array,
        default: () => [
            { title: '1. Pilih produk', description: 'Klik Tambah stok pada varian produk yang menerima barang.' },
            { title: '2. Isi jumlah masuk', description: 'Masukkan jumlah barang dan catatan bila diperlukan.' },
            { title: '3. Simpan dan periksa', description: 'Simpan restock, lalu periksa stok dan riwayat yang diperbarui.' },
        ],
    },
});
const page = usePage();
const slots = useSlots();
const menuQuery = ref('');
const menuSearch = ref(null);
const showGuide = ref(false);
const guideHeading = ref(null);
const guideTrigger = ref(null);
const initials = computed(() => page.props.auth.user.name.trim().split(/\s+/)
    .slice(0, 2).map((part) => part[0]).join('').toUpperCase());
const navigation = [
    { label: 'Dashboard', name: 'dashboard', icon: 'M3.75 3.75h6v6h-6zm10.5 0h6v6h-6zm-10.5 10.5h6v6h-6zm10.5 0h6v6h-6z' },
    { label: 'Profil usaha', name: 'business-profile.edit', icon: 'M3 21h18M5 21V4h14v17M9 8h1m4 0h1M9 12h1m4 0h1M10 21v-5h4v5' },
    { label: 'Produk', name: 'umkm.products.index', icon: 'M3 7h18M5 7V4h14v3M5 7v13h14V7M9 11h6' },
    { label: 'Persediaan', name: 'umkm.inventory.index', icon: 'm3 7 9-4 9 4-9 4-9-4Zm0 0v10l9 4 9-4V7M12 11v10' },
    { label: 'Catat Penjualan', name: 'umkm.sales.create', icon: 'M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z' },
    { label: 'Riwayat Penjualan', name: 'umkm.sales.index', icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
    { label: 'Rekap & Ekspor Laporan', name: 'umkm.sales.report', icon: 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z' },
    { label: 'Proposal kolaborasi', name: 'proposals.index', icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
    { label: 'Profil akun', name: 'profile.edit', icon: 'M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20a7.5 7.5 0 0 1 15 0' },
];
const filteredNavigation = computed(() => navigation.filter((item) =>
    item.label.toLocaleLowerCase('id-ID').includes(menuQuery.value.trim().toLocaleLowerCase('id-ID'))));

function focusMenuSearch(event) {
    if (!slots.search && event.key === '/' && !event.ctrlKey && !event.metaKey
        && !event.target?.matches('input, textarea, select, [contenteditable="true"]')
        && menuSearch.value?.offsetParent) {
        event.preventDefault();
        menuSearch.value.focus();
    }
}
onMounted(() => document.addEventListener('keydown', focusMenuSearch));
onUnmounted(() => document.removeEventListener('keydown', focusMenuSearch));

async function toggleGuide(setOpenMobile) {
    if (setOpenMobile) {
        setOpenMobile(false);
        await nextTick();
    }
    showGuide.value = !showGuide.value;
    await nextTick();
    if (showGuide.value) {
        guideHeading.value?.focus();
    } else {
        guideTrigger.value?.focus();
    }
}
</script>

<template>
    <div class="flex min-h-screen flex-col bg-canvas font-sans text-ink">
        <a href="#main-content" class="sr-only z-50 rounded-xl bg-white p-3 text-primary-700 focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:ring-2 focus:ring-primary-500">Lewati ke konten utama</a>
        <SidebarProvider v-slot="{ state, isMobile, setOpenMobile }">
            <Sidebar collapsible="icon">
                <SidebarHeader>
                    <Link :href="route('dashboard')" aria-label="Koneksa — Dashboard" class="flex min-h-11 items-center rounded-xl focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500" :class="state === 'collapsed' && !isMobile ? 'justify-center' : 'px-1'">
                        <KoneksaLogo size="sm" :show-wordmark="state !== 'collapsed' || isMobile" />
                    </Link>
                </SidebarHeader>
                <SidebarContent>
                    <label v-if="!slots.search && (state !== 'collapsed' || isMobile)" class="mx-2 mb-1 block sm:hidden">
                        <span class="sr-only">Cari menu UMKM</span>
                        <input v-model="menuQuery" type="search" placeholder="Cari menu…" class="h-11 w-full rounded-full border-hairline bg-canvas px-4 text-sm focus:border-primary-500 focus:ring-primary-500" />
                    </label>
                    <SidebarGroup>
                        <SidebarGroupLabel>Ruang usaha</SidebarGroupLabel>
                        <SidebarGroupContent>
                            <nav aria-label="Menu UMKM">
                                <SidebarMenu>
                                    <SidebarMenuItem v-for="item in filteredNavigation" :key="item.name">
                                        <SidebarMenuButton :href="route(item.name)" :is-active="route().current(item.name)" :tooltip="item.label" @click="setOpenMobile(false)">
                                            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" :d="item.icon" /></svg>
                                            <span v-if="state !== 'collapsed' || isMobile" class="truncate">{{ item.label }}</span>
                                            <SidebarMenuBadge v-if="item.name === 'umkm.inventory.index' && inventoryCount !== undefined">{{ inventoryCount }}</SidebarMenuBadge>
                                        </SidebarMenuButton>
                                    </SidebarMenuItem>
                                    <li v-if="!filteredNavigation.length && (state !== 'collapsed' || isMobile)" role="status" class="p-3 text-sm text-ink-muted">Menu tidak ditemukan.</li>
                                </SidebarMenu>
                            </nav>
                        </SidebarGroupContent>
                    </SidebarGroup>
                    <SidebarGroup class="mt-auto">
                        <SidebarGroupLabel>Bantuan</SidebarGroupLabel>
                        <SidebarGroupContent>
                            <SidebarMenu>
                                <SidebarMenuItem>
                                    <SidebarMenuButton :tooltip="guideTitle" :aria-expanded="showGuide" aria-controls="quick-guide" @click="toggleGuide(setOpenMobile)">
                                        <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><circle cx="12" cy="12" r="9" /><path stroke-linecap="round" d="M9.5 9a2.5 2.5 0 1 1 4 2c-1 .5-1.5 1-1.5 2M12 16h.01" /></svg>
                                        <span v-if="state !== 'collapsed' || isMobile">{{ guideTitle }}</span>
                                    </SidebarMenuButton>
                                </SidebarMenuItem>
                            </SidebarMenu>
                        </SidebarGroupContent>
                    </SidebarGroup>
                </SidebarContent>
                <SidebarFooter>
                    <Link :href="route('logout')" method="post" as="button" aria-label="Keluar dari akun" class="flex min-h-11 w-full items-center gap-2.5 rounded-xl px-3 text-sm font-medium text-danger-dark hover:bg-danger-light focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500" :class="state === 'collapsed' && !isMobile ? 'justify-center px-0' : ''">
                        <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 4H4v16h5m6-12 4 4-4 4m-6-4h10" /></svg>
                        <span v-if="state !== 'collapsed' || isMobile">Keluar</span>
                    </Link>
                </SidebarFooter>
                <SidebarRail />
            </Sidebar>

            <SidebarInset>
                <header class="flex flex-wrap items-center justify-between gap-3 border-b border-hairline bg-white px-4 py-3 sm:px-6 lg:px-8">
                    <div class="flex min-w-0 items-center gap-3">
                        <SidebarTrigger />
                        <span class="text-sm font-semibold text-ink-secondary sm:hidden">Ruang UMKM</span>
                        <div class="hidden sm:block">
                            <slot name="search">
                                <label class="relative block">
                                    <span class="sr-only">Cari menu UMKM</span>
                                    <input ref="menuSearch" v-model="menuQuery" type="search" placeholder="Cari menu cepat…" class="h-11 w-56 rounded-full border-hairline bg-canvas pl-4 pr-10 text-sm placeholder:text-ink-muted focus:border-primary-500 focus:ring-primary-500" />
                                    <kbd class="pointer-events-none absolute right-4 top-3 font-mono text-sm text-ink-muted" aria-hidden="true">/</kbd>
                                </label>
                            </slot>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button ref="guideTrigger" type="button" class="flex h-11 items-center gap-2 rounded-full border border-hairline bg-canvas px-3 text-sm font-medium text-ink-secondary hover:bg-primary-50 hover:text-primary-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500" :aria-expanded="showGuide" aria-controls="quick-guide" :aria-label="`Tampilkan ${guideTitle.toLocaleLowerCase('id-ID')}`" @click="toggleGuide()">
                            <span aria-hidden="true" class="flex h-5 w-5 items-center justify-center rounded-full border border-current font-semibold">?</span>
                            <span class="hidden lg:inline">Panduan</span>
                        </button>
                        <Link :href="route('profile.edit')" :aria-label="`Profil akun ${page.props.auth.user.name}`" class="flex min-h-11 items-center gap-2 rounded-full border border-hairline bg-canvas py-1 pl-1 pr-2 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 sm:pr-3">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-600 text-xs font-semibold text-white" aria-hidden="true">{{ initials }}</span>
                            <span class="hidden min-w-0 sm:block">
                                <span class="block max-w-36 truncate text-xs font-semibold">{{ page.props.auth.user.name }}</span>
                                <span class="block text-left text-xs text-ink-muted">UMKM</span>
                            </span>
                        </Link>
                    </div>
                </header>

                <main id="main-content" class="flex-1 p-4 sm:p-6 lg:p-8" tabindex="-1">
                    <div class="mx-auto max-w-6xl">
                        <section v-if="showGuide" id="quick-guide" aria-labelledby="guide-heading" class="mb-6 rounded-2xl border border-primary-200 bg-primary-50 p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h2 id="guide-heading" ref="guideHeading" tabindex="-1" class="rounded text-base font-semibold text-primary-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500">Panduan langkah awal</h2>
                                    <p class="mt-1 text-sm text-primary-700">{{ guideDescription }}</p>
                                </div>
                                <button type="button" class="min-h-11 rounded-xl px-3 text-sm font-semibold text-primary-700 hover:bg-primary-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500" @click="toggleGuide()">Tutup</button>
                            </div>
                            <ol class="mt-4 grid gap-3 sm:grid-cols-3">
                                <li v-for="step in guideSteps" :key="step.title" class="rounded-xl border border-primary-100 bg-white p-4"><h3 class="text-sm font-semibold">{{ step.title }}</h3><p class="mt-1 text-sm text-ink-secondary">{{ step.description }}</p></li>
                            </ol>
                        </section>
                        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="mb-1 text-xs font-semibold uppercase tracking-wider text-primary-700">Operasional usaha</p>
                                <h1 class="text-2xl font-bold tracking-tight">{{ title }}</h1>
                                <p class="mt-2 max-w-2xl text-sm leading-relaxed text-ink-secondary">{{ subtitle }}</p>
                            </div>
                            <slot name="header-actions" />
                        </div>
                        <slot />
                    </div>
                </main>
            </SidebarInset>
        </SidebarProvider>
    </div>
</template>
