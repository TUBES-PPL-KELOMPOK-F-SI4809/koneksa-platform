<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import EvaluationHeaderBar from '@/Components/EvaluationHeaderBar.vue';
import KoneksaLogo from '@/Components/KoneksaLogo.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarGroup,
    SidebarGroupContent,
    SidebarGroupLabel,
    SidebarHeader,
    SidebarInset,
    SidebarMenu,
    SidebarMenuBadge,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarProvider,
    SidebarRail,
    SidebarTrigger,
} from '@/Components/ui/sidebar';

const props = defineProps({
    activeRole: {
        type: String,
        required: true,
        validator: (value) => ['umkm', 'mitra', 'pemda', 'admin'].includes(value),
    },
    roleLabel: {
        type: String,
        required: true,
    },
    actorName: {
        type: String,
        required: true,
    },
    actorSubtitle: {
        type: String,
        required: true,
    },
    pageTitle: {
        type: String,
        required: true,
    },
    pageSubtitle: {
        type: String,
        default: '',
    },
    navItems: {
        type: Array,
        required: true,
    },
    activeSection: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['select-section']);

const sidebarSearch = ref('');
const findInputRef = ref(null);
const showQuickGuide = ref(false);

const roleTheme = computed(() => {
    const map = {
        umkm: {
            avatar: 'bg-primary-600 text-white',
            pill: 'bg-primary-50 text-primary-700 ring-primary-700/15',
            guideSteps: [
                { title: '1. Catat Penjualan', desc: 'Klik tombol "+ Catat Kasir" di kanan atas saat ada pembeli.' },
                { title: '2. Cek Stok Hampir Habis', desc: 'Lihat daftar produk bertanda kuning/merah agar tidak kehabisan barang.' },
                { title: '3. Balas Tawaran Mitra', desc: 'Klik "Terima" pada tawaran kerja sama yang cocok untuk usaha Anda.' },
            ],
        },
        mitra: {
            avatar: 'bg-secondary-600 text-white',
            pill: 'bg-secondary-50 text-secondary-700 ring-secondary-700/15',
            guideSteps: [
                { title: '1. Cari UMKM Binaan', desc: 'Pilih kategori usaha untuk menemukan UMKM yang sudah diverifikasi Pemda.' },
                { title: '2. Kirim Proposal', desc: 'Klik "Ajukan Kerjasama" pada UMKM yang sesuai dengan program Anda.' },
                { title: '3. Pantau Progres', desc: 'Lihat perkembangan kemitraan yang sedang berjalan di bagian bawah.' },
            ],
        },
        pemda: {
            avatar: 'bg-emerald-600 text-white',
            pill: 'bg-emerald-50 text-emerald-700 ring-emerald-700/15',
            guideSteps: [
                { title: '1. Periksa Antrean UMKM', desc: 'Cek nomor NIB usaha baru yang mendaftar di tabel verifikasi.' },
                { title: '2. Setujui atau Revisi', desc: 'Klik "Setujui" agar usaha tampil di direktori resmi Kota Bandung.' },
                { title: '3. Pantau Kuota Program', desc: 'Pastikan kuota pelatihan tidak melebihi batas peserta.' },
            ],
        },
        admin: {
            avatar: 'bg-violet-600 text-white',
            pill: 'bg-violet-50 text-violet-700 ring-violet-700/15',
            guideSteps: [
                { title: '1. Kelola Status Akun', desc: 'Gunakan filter role untuk memeriksa dan mengaktifkan/menonaktifkan akun.' },
                { title: '2. Tambah Petugas Pemda', desc: 'Isi form singkat di kanan untuk membuat akun resmi dinas.' },
                { title: '3. Cek Riwayat Keamanan', desc: 'Buka rincian log audit bila perlu memeriksa aktivitas terbaru.' },
            ],
        },
    };

    return map[props.activeRole] || map.umkm;
});

const filteredNavItems = computed(() => {
    const q = sidebarSearch.value.trim().toLowerCase();
    if (!q) {
        return props.navItems;
    }

    return props.navItems.filter(
        (item) =>
            item.label.toLowerCase().includes(q) ||
            (item.badge && item.badge.toLowerCase().includes(q)),
    );
});

const actorInitials = computed(() =>
    props.actorName
        .split(' ')
        .slice(0, 2)
        .map((part) => part[0])
        .join('')
        .toUpperCase(),
);

const iconPaths = {
    ringkasan: 'M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z',
    produk: 'M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z',
    proposal: 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z',
    program: 'M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342',
    direktori: 'M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z',
    monitoring: 'M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5',
    solusi: 'M14.25 9.75L16.5 12l-2.25 2.25m-4.5 0L7.5 12l2.25-2.25M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z',
    profil: 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z',
    verifikasi: 'M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z',
    rekap: 'M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z',
    pengguna: 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z',
    provisioning: 'M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z',
    audit: 'M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z',
};

function selectMenu(key, setOpenMobile) {
    emit('select-section', key);
    setOpenMobile(false);
}

function handleFindShortcut(event) {
    const tag = event.target?.tagName;
    const isTyping =
        tag === 'INPUT' ||
        tag === 'TEXTAREA' ||
        tag === 'SELECT' ||
        event.target?.isContentEditable;

    if (!isTyping && !event.metaKey && !event.ctrlKey && (event.key === 'f' || event.key === 'F')) {
        event.preventDefault();
        findInputRef.value?.focus();
    }
}

onMounted(() => document.addEventListener('keydown', handleFindShortcut));
onUnmounted(() => document.removeEventListener('keydown', handleFindShortcut));
</script>

<template>
    <div class="flex min-h-screen flex-col bg-canvas text-ink">
        <!-- Top Header Mode Evaluasi -->
        <EvaluationHeaderBar :active-page="activeRole" />

        <!-- Shadcn Composable Sidebar + Modern Bento Dashboard Shell -->
        <SidebarProvider
            :default-open="true"
            v-slot="{ state, isMobile, setOpenMobile }"
        >
            <Sidebar collapsible="icon" variant="sidebar">
                <SidebarHeader>
                    <div
                        class="flex items-center gap-2.5 py-1"
                        :class="state === 'collapsed' && !isMobile ? 'justify-center px-0' : 'px-1'"
                    >
                        <Link
                            :href="route('landing')"
                            class="inline-flex items-center rounded-xl bg-canvas px-2.5 py-1.5 ring-1 ring-inset ring-hairline transition hover:bg-neutral-100"
                        >
                            <KoneksaLogo size="sm" :show-wordmark="state !== 'collapsed' || isMobile" />
                        </Link>
                    </div>
                </SidebarHeader>

                <SidebarContent>
                    <SidebarGroup>
                        <SidebarGroupLabel>Menu</SidebarGroupLabel>
                        <SidebarGroupContent>
                            <SidebarMenu>
                                <template v-if="filteredNavItems.length">
                                    <SidebarMenuItem
                                        v-for="item in filteredNavItems"
                                        :key="item.key"
                                    >
                                        <SidebarMenuButton
                                            :is-active="activeSection === item.key"
                                            :tooltip="item.label"
                                            @click="selectMenu(item.key, setOpenMobile)"
                                        >
                                            <svg
                                                class="h-4 w-4 shrink-0"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                stroke-width="1.75"
                                                aria-hidden="true"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    :d="iconPaths[item.key] || iconPaths.ringkasan"
                                                />
                                            </svg>
                                            <span
                                                v-if="state !== 'collapsed' || isMobile"
                                                class="truncate"
                                            >
                                                {{ item.label }}
                                            </span>
                                            <SidebarMenuBadge v-if="item.badge">
                                                {{ item.badge }}
                                            </SidebarMenuBadge>
                                        </SidebarMenuButton>
                                    </SidebarMenuItem>
                                </template>
                                <li v-else class="px-2 py-3 text-xs text-ink-muted">
                                    Menu tidak ditemukan.
                                </li>
                            </SidebarMenu>
                        </SidebarGroupContent>
                    </SidebarGroup>

                    <SidebarGroup class="mt-auto pt-2">
                        <SidebarGroupLabel>Umum</SidebarGroupLabel>
                        <SidebarGroupContent>
                            <SidebarMenu>
                                <SidebarMenuItem>
                                    <SidebarMenuButton
                                        tooltip="Panduan Pengguna Baru"
                                        :is-active="showQuickGuide"
                                        @click="showQuickGuide = !showQuickGuide"
                                    >
                                        <svg
                                            class="h-4 w-4 shrink-0"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            stroke-width="1.75"
                                            aria-hidden="true"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z"
                                            />
                                        </svg>
                                        <span v-if="state !== 'collapsed' || isMobile">Panduan Pemula</span>
                                    </SidebarMenuButton>
                                </SidebarMenuItem>
                                <SidebarMenuItem>
                                    <SidebarMenuButton
                                        :href="route('login')"
                                        tooltip="Halaman Login"
                                    >
                                        <svg
                                            class="h-4 w-4 shrink-0"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            stroke-width="1.75"
                                            aria-hidden="true"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"
                                            />
                                        </svg>
                                        <span v-if="state !== 'collapsed' || isMobile">Ganti Akun</span>
                                    </SidebarMenuButton>
                                </SidebarMenuItem>
                            </SidebarMenu>
                        </SidebarGroupContent>
                    </SidebarGroup>
                </SidebarContent>

                <SidebarFooter>
                    <Link
                        :href="route('landing')"
                        class="flex h-9 items-center gap-2.5 rounded-xl bg-danger-light/80 px-3 text-xs font-medium text-danger-dark transition hover:bg-danger-light"
                        :class="state === 'collapsed' && !isMobile ? 'justify-center px-0' : ''"
                        title="Kembali ke Beranda Publik"
                    >
                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                        </svg>
                        <span v-if="state !== 'collapsed' || isMobile">Ke Beranda</span>
                    </Link>
                </SidebarFooter>

                <SidebarRail />
            </Sidebar>

            <SidebarInset>
                <!-- Finexy-style Top Utility Header -->
                <header class="flex h-16 items-center justify-between gap-3 border-b border-hairline bg-white px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center gap-2.5">
                        <SidebarTrigger />

                        <!-- Pill Search in Top Bar -->
                        <div class="relative hidden sm:block">
                            <label for="sidebar-find-input" class="sr-only">Cari menu panel</label>
                            <svg
                                class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-muted"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.75"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="m21 21-4.35-4.35m0 0A7.5 7.5 0 1 0 6.1 6.1a7.5 7.5 0 0 0 10.55 10.55Z"
                                />
                            </svg>
                            <input
                                id="sidebar-find-input"
                                ref="findInputRef"
                                v-model="sidebarSearch"
                                type="search"
                                placeholder="Cari menu cepat..."
                                class="h-9 w-56 rounded-full border border-hairline bg-canvas pl-9 pr-10 text-xs text-ink placeholder-neutral-400 focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary-500 lg:w-72"
                            />
                            <kbd
                                class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 rounded-full border border-hairline bg-white px-1.5 py-0.5 font-mono text-[10px] font-medium text-ink-muted"
                                aria-hidden="true"
                            >
                                F
                            </kbd>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <!-- Novice Help Button -->
                        <button
                            type="button"
                            class="inline-flex h-9 items-center gap-1.5 rounded-full border px-3 text-xs font-medium transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                            :class="
                                showQuickGuide
                                    ? 'border-primary-200 bg-primary-50 text-primary-700'
                                    : 'border-hairline bg-canvas text-ink-secondary hover:bg-neutral-100 hover:text-ink'
                            "
                            :aria-expanded="showQuickGuide"
                            aria-label="Tampilkan panduan langkah awal"
                            @click="showQuickGuide = !showQuickGuide"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                            </svg>
                            <span class="hidden sm:inline">Panduan</span>
                        </button>

                        <!-- Finexy-style Profile Pill -->
                        <div class="flex items-center gap-2.5 rounded-full border border-hairline bg-canvas py-1 pl-1.5 pr-3">
                            <span
                                class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full font-mono text-[11px] font-semibold shadow-xs"
                                :class="roleTheme.avatar"
                            >
                                {{ actorInitials }}
                            </span>
                            <div class="hidden min-w-0 leading-tight sm:block">
                                <p class="max-w-[140px] truncate text-xs font-semibold text-ink">
                                    {{ actorName }}
                                </p>
                                <p class="text-[10px] font-medium text-ink-muted">
                                    {{ roleLabel }}
                                </p>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Main Workspace Canvas -->
                <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
                    <div class="mx-auto max-w-6xl">
                        <!-- Novice-Friendly Expandable Onboarding Guide -->
                        <section
                            v-if="showQuickGuide"
                            aria-label="Panduan Pemula"
                            class="mb-6 rounded-2xl border border-primary-200 bg-primary-50/70 p-4 sm:p-5"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h2 class="text-sm font-semibold text-primary-900">
                                        Baru pertama kali membuka panel {{ roleLabel }}?
                                    </h2>
                                    <p class="mt-0.5 text-xs text-primary-700">
                                        Ikuti 3 langkah sederhana berikut untuk mulai bekerja:
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    class="rounded-lg px-2 py-1 text-xs font-medium text-primary-700 hover:bg-primary-100"
                                    @click="showQuickGuide = false"
                                >
                                    Tutup
                                </button>
                            </div>

                            <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-3">
                                <div
                                    v-for="step in roleTheme.guideSteps"
                                    :key="step.title"
                                    class="rounded-xl border border-primary-100 bg-white p-3.5 shadow-xs"
                                >
                                    <p class="text-xs font-semibold text-ink">{{ step.title }}</p>
                                    <p class="mt-1 text-xs leading-relaxed text-ink-secondary">{{ step.desc }}</p>
                                </div>
                            </div>
                        </section>

                        <!-- Finexy-style Overview Title Row -->
                        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                            <div class="min-w-0">
                                <h1 class="text-xl font-bold tracking-tight text-ink sm:text-2xl">
                                    {{ pageTitle }}
                                </h1>
                                <p v-if="pageSubtitle" class="mt-0.5 text-xs text-ink-secondary sm:text-[13px]">
                                    {{ pageSubtitle }}
                                </p>
                            </div>

                            <div class="flex flex-wrap items-center gap-2">
                                <slot name="header-actions" />
                            </div>
                        </div>

                        <slot />
                    </div>
                </main>
            </SidebarInset>
        </SidebarProvider>
    </div>
</template>
