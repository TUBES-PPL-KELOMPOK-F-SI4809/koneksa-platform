<script setup>
import { computed, nextTick, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import KoneksaLogo from '@/Components/KoneksaLogo.vue';
import {
    Sidebar, SidebarContent, SidebarFooter, SidebarGroup, SidebarGroupContent,
    SidebarGroupLabel, SidebarHeader, SidebarInset, SidebarMenu, SidebarMenuBadge,
    SidebarMenuButton, SidebarMenuItem, SidebarProvider, SidebarRail, SidebarTrigger,
} from '@/Components/ui/sidebar';

defineProps({ title: String, subtitle: String, inventoryCount: Number });
const page = usePage();
const showGuide = ref(false);
const guideHeading = ref(null);
const guideTrigger = ref(null);
const initials = computed(() => page.props.auth.user.name.trim().split(/\s+/)
    .slice(0, 2).map((part) => part[0]).join('').toUpperCase());
const navigation = [
    { label: 'Dashboard', name: 'dashboard', icon: 'M3.75 3.75h6v6h-6zm10.5 0h6v6h-6zm-10.5 10.5h6v6h-6zm10.5 0h6v6h-6z' },
    { label: 'Persediaan', name: 'umkm.inventory.index', icon: 'm3 7 9-4 9 4-9 4-9-4Zm0 0v10l9 4 9-4V7M12 11v10' },
    { label: 'Profil akun', name: 'profile.edit', icon: 'M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20a7.5 7.5 0 0 1 15 0' },
];

async function toggleGuide(setOpenMobile) {
    setOpenMobile?.(false);
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
                    <SidebarGroup>
                        <SidebarGroupLabel>Ruang usaha</SidebarGroupLabel>
                        <SidebarGroupContent>
                            <nav aria-label="Menu UMKM">
                                <SidebarMenu>
                                    <SidebarMenuItem v-for="item in navigation" :key="item.name">
                                        <SidebarMenuButton :href="route(item.name)" :is-active="route().current(item.name)" :tooltip="item.label" @click="setOpenMobile(false)">
                                            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" :d="item.icon" /></svg>
                                            <span v-if="state !== 'collapsed' || isMobile" class="truncate">{{ item.label }}</span>
                                            <SidebarMenuBadge v-if="item.name === 'umkm.inventory.index'">{{ inventoryCount }}</SidebarMenuBadge>
                                        </SidebarMenuButton>
                                    </SidebarMenuItem>
                                </SidebarMenu>
                            </nav>
                        </SidebarGroupContent>
                    </SidebarGroup>
                    <SidebarGroup class="mt-auto">
                        <SidebarGroupLabel>Bantuan</SidebarGroupLabel>
                        <SidebarGroupContent>
                            <SidebarMenu>
                                <SidebarMenuItem>
                                    <SidebarMenuButton tooltip="Panduan restock" :aria-expanded="showGuide" aria-controls="quick-guide" @click="toggleGuide(setOpenMobile)">
                                        <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><circle cx="12" cy="12" r="9" /><path stroke-linecap="round" d="M9.5 9a2.5 2.5 0 1 1 4 2c-1 .5-1.5 1-1.5 2M12 16h.01" /></svg>
                                        <span v-if="state !== 'collapsed' || isMobile">Panduan restock</span>
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
                        <div class="hidden sm:block"><slot name="search" /></div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button ref="guideTrigger" type="button" class="flex h-11 items-center gap-2 rounded-full border border-hairline bg-canvas px-3 text-sm font-medium text-ink-secondary hover:bg-primary-50 hover:text-primary-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500" :aria-expanded="showGuide" aria-controls="quick-guide" aria-label="Tampilkan panduan restock" @click="toggleGuide()">
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
                                    <p class="mt-1 text-sm text-primary-700">Tiga langkah untuk mencatat barang masuk.</p>
                                </div>
                                <button type="button" class="min-h-11 rounded-xl px-3 text-sm font-semibold text-primary-700 hover:bg-primary-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500" @click="toggleGuide()">Tutup</button>
                            </div>
                            <ol class="mt-4 grid gap-3 sm:grid-cols-3">
                                <li class="rounded-xl border border-primary-100 bg-white p-4"><h3 class="text-sm font-semibold">1. Pilih produk</h3><p class="mt-1 text-sm text-ink-secondary">Klik Tambah stok pada varian produk yang menerima barang.</p></li>
                                <li class="rounded-xl border border-primary-100 bg-white p-4"><h3 class="text-sm font-semibold">2. Isi jumlah masuk</h3><p class="mt-1 text-sm text-ink-secondary">Masukkan jumlah barang dan catatan bila diperlukan.</p></li>
                                <li class="rounded-xl border border-primary-100 bg-white p-4"><h3 class="text-sm font-semibold">3. Simpan dan periksa</h3><p class="mt-1 text-sm text-ink-secondary">Simpan restock, lalu periksa stok dan riwayat yang diperbarui.</p></li>
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
