<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import EvaluationPanelLayout from '@/Layouts/EvaluationPanelLayout.vue';
import StatCard from '@/Components/StatCard.vue';
import StatusBadge from '@/Components/StatusBadge.vue';

const props = defineProps({
    actor: {
        type: Object,
        required: true,
    },
    kpis: {
        type: Array,
        required: true,
    },
    salesTrend: {
        type: Array,
        required: true,
    },
    products: {
        type: Array,
        required: true,
    },
    incomingProposals: {
        type: Array,
        required: true,
    },
    programApplications: {
        type: Array,
        required: true,
    },
});

const activeSection = ref('ringkasan');
const stockFilter = ref('Semua');
const searchSku = ref('');
const searchInputRef = ref(null);
const selectedBarDay = ref('Sab');
const expandedProductSku = ref(null);
const expandedProposalId = ref(null);
const showProgramSection = ref(false);
const proposalList = ref(props.incomingProposals.map((item) => ({ ...item })));
const feedbackMessage = ref('');
const showPosModal = ref(false);
const posForm = ref({
    sku: props.products[0]?.sku || '',
    qty: 2,
});

const navItems = [
    { key: 'ringkasan', label: 'Dashboard' },
    { key: 'produk', label: 'Produk & Stok', badge: '3' },
    { key: 'proposal', label: 'Tawaran Mitra', badge: '2' },
    { key: 'program', label: 'Program Pemda', badge: '2' },
];

const filteredProducts = computed(() => {
    const q = searchSku.value.trim().toLowerCase();

    return props.products.filter((product) => {
        const matchesStatus = stockFilter.value === 'Semua' || product.status === stockFilter.value;
        const matchesQuery =
            !q ||
            product.name.toLowerCase().includes(q) ||
            product.sku.toLowerCase().includes(q);

        return matchesStatus && matchesQuery;
    });
});

function toggleProductDetail(sku) {
    expandedProductSku.value = expandedProductSku.value === sku ? null : sku;
}

function toggleProposalDetail(id) {
    expandedProposalId.value = expandedProposalId.value === id ? null : id;
}

function handleSelectSection(key) {
    activeSection.value = key;
    if (key === 'program') {
        showProgramSection.value = true;
    }
}

function stockTone(status) {
    if (status === 'Aman') {
        return 'success';
    }
    if (status === 'Menipis') {
        return 'warning';
    }
    return 'danger';
}

function proposalTone(status) {
    if (status === 'Diterima') {
        return 'success';
    }
    if (status === 'Ditolak') {
        return 'danger';
    }
    return 'info';
}

function decideProposal(id, decision) {
    proposalList.value = proposalList.value.map((item) =>
        item.id === id ? { ...item, status: decision } : item,
    );
    feedbackMessage.value = `Status tawaran kerja sama #${id} berhasil diubah menjadi "${decision}".`;
}

function submitPosPreview() {
    showPosModal.value = false;
    feedbackMessage.value = `Penjualan ${posForm.value.qty} item (${posForm.value.sku}) berhasil dicatat dan stok diperbarui.`;
}

function formatRupiah(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(amount);
}

function handleSlashShortcut(event) {
    const tag = event.target?.tagName;
    const isTyping = tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT' || event.target?.isContentEditable;
    if (!isTyping && event.key === '/') {
        event.preventDefault();
        searchInputRef.value?.focus();
    }
    if (event.key === 'Escape' && showPosModal.value) {
        showPosModal.value = false;
    }
}

onMounted(() => document.addEventListener('keydown', handleSlashShortcut));
onUnmounted(() => document.removeEventListener('keydown', handleSlashShortcut));
</script>

<template>
    <div>
        <Head title="Panel UMKM — Evaluasi UI Koneksa" />

        <EvaluationPanelLayout
            active-role="umkm"
            role-label="UMKM"
            :actor-name="actor.businessName"
            :actor-subtitle="`${actor.name} · ${actor.district}`"
            page-title="Ringkasan Usaha & Penjualan"
            page-subtitle="Pantau omzet harian, stok produk, dan tawaran kerja sama dengan mudah."
            :nav-items="navItems"
            :active-section="activeSection"
            @select-section="handleSelectSection"
        >
            <template #header-actions>
                <span class="inline-flex h-9 items-center gap-2 rounded-full border border-hairline bg-white px-3.5 text-xs font-medium text-ink-secondary shadow-xs">
                    <svg class="h-3.5 w-3.5 text-ink-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                    </svg>
                    1 – 12 Nov 2026
                </span>

                <button
                    type="button"
                    class="inline-flex h-9 items-center rounded-full border border-hairline bg-white px-3.5 text-xs font-medium text-ink shadow-xs transition hover:bg-neutral-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                    @click="feedbackMessage = 'Laporan penjualan bulan ini siap diunduh dalam format CSV.'"
                >
                    Unduh Laporan
                </button>

                <button
                    type="button"
                    class="inline-flex h-9 items-center rounded-full bg-primary-600 px-4 text-xs font-medium text-white shadow-xs transition hover:bg-primary-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2"
                    @click="showPosModal = true"
                >
                    + Catat Kasir
                </button>
            </template>

            <!-- Instant Action Feedback Banner -->
            <div
                v-if="feedbackMessage"
                role="status"
                class="mb-5 flex items-center justify-between gap-4 rounded-2xl border border-primary-200 bg-primary-50 px-4 py-3 text-xs font-medium text-primary-900"
            >
                <span class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-primary-600" aria-hidden="true"></span>
                    {{ feedbackMessage }}
                </span>
                <button
                    type="button"
                    class="rounded-lg px-2 py-1 text-xs font-semibold text-primary-700 hover:bg-primary-100"
                    @click="feedbackMessage = ''"
                >
                    Tutup
                </button>
            </div>

            <!-- Row 1: 4 Finexy-style KPI Cards -->
            <section aria-label="Metrik Utama UMKM" class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <StatCard
                    v-for="kpi in kpis"
                    :key="kpi.label"
                    :label="kpi.label"
                    :value="kpi.value"
                    :delta="kpi.delta"
                    :tone="kpi.tone"
                />
            </section>

            <!-- Row 2: Finexy 7+5 Bento Split (Analytics Chart + Actionable Proposals) -->
            <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-12">
                <!-- Left Card: Capsule Bar Chart with Peak Callout -->
                <section class="min-w-0 rounded-2xl border border-hairline bg-white p-5 shadow-xs lg:col-span-7">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div>
                            <h2 class="text-base font-semibold text-ink">
                                Analitik Omzet Harian
                            </h2>
                            <p class="text-xs text-ink-muted">
                                Klik batang hari untuk melihat nilai penjualan
                            </p>
                        </div>
                        <span class="inline-flex items-center rounded-full border border-hairline bg-canvas px-3 py-1 text-xs font-medium text-ink-secondary">
                            Minggu Ini
                        </span>
                    </div>

                    <div class="mt-10 grid grid-cols-7 items-end gap-2 sm:gap-4" aria-label="Grafik batang omzet harian">
                        <button
                            v-for="bar in salesTrend"
                            :key="bar.day"
                            type="button"
                            class="group relative flex min-w-0 flex-col items-center gap-2 focus-visible:outline-none"
                            @click="selectedBarDay = bar.day"
                        >
                            <!-- Floating Value Callout (Finexy Peak Badge) -->
                            <span
                                v-if="selectedBarDay === bar.day"
                                class="pointer-events-none absolute -top-8 left-1/2 z-10 -translate-x-1/2 whitespace-nowrap rounded-lg bg-primary-600 px-2 py-0.5 font-mono text-[10px] font-semibold text-white shadow-xs"
                            >
                                {{ formatRupiah(bar.amount) }}
                            </span>

                            <div class="flex h-36 w-full max-w-[42px] items-end rounded-full bg-canvas p-1 ring-1 ring-inset ring-hairline">
                                <div
                                    class="w-full rounded-full transition-all duration-200"
                                    :class="
                                        selectedBarDay === bar.day
                                            ? 'bg-gradient-to-t from-primary-600 to-secondary-500 shadow-xs'
                                            : 'bg-primary-500/75 group-hover:bg-primary-600'
                                    "
                                    :style="{ height: `${bar.percent}%` }"
                                ></div>
                            </div>
                            <span
                                class="text-xs font-medium"
                                :class="selectedBarDay === bar.day ? 'font-semibold text-primary-700' : 'text-ink-muted'"
                            >
                                {{ bar.day }}
                            </span>
                        </button>
                    </div>
                </section>

                <!-- Right Card: Incoming Proposals (Minimal upfront + Extendable Detail) -->
                <section class="flex min-w-0 flex-col justify-between rounded-2xl border border-hairline bg-white p-5 shadow-xs lg:col-span-5">
                    <div>
                        <div class="flex items-center justify-between gap-2">
                            <div>
                                <h2 class="text-base font-semibold text-ink">
                                    Tawaran Kerja Sama
                                </h2>
                                <p class="text-xs text-ink-muted">
                                    Ajukan atau terima kolaborasi mitra industri
                                </p>
                            </div>
                            <StatusBadge tone="primary" dot>
                                {{ proposalList.filter((p) => p.status === 'Menunggu').length }} Menunggu
                            </StatusBadge>
                        </div>

                        <div class="mt-4 space-y-2.5">
                            <div
                                v-for="proposal in proposalList"
                                :key="proposal.id"
                                class="rounded-xl border border-hairline bg-canvas/60 p-3.5 transition hover:bg-canvas"
                            >
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <p class="truncate text-xs font-semibold text-ink">
                                            {{ proposal.partner }}
                                        </p>
                                        <p class="mt-0.5 truncate text-xs text-ink-secondary">
                                            {{ proposal.title }}
                                        </p>
                                    </div>
                                    <StatusBadge :tone="proposalTone(proposal.status)" dot>
                                        {{ proposal.status }}
                                    </StatusBadge>
                                </div>

                                <div class="mt-3 flex flex-wrap items-center justify-between gap-2">
                                    <button
                                        type="button"
                                        class="text-[11px] font-medium text-primary-600 hover:underline focus-visible:outline-none"
                                        :aria-expanded="expandedProposalId === proposal.id"
                                        @click="toggleProposalDetail(proposal.id)"
                                    >
                                        {{ expandedProposalId === proposal.id ? 'Tutup rincian ▴' : 'Lihat rincian ▾' }}
                                    </button>

                                    <div v-if="proposal.status === 'Menunggu'" class="flex items-center gap-1.5">
                                        <button
                                            type="button"
                                            class="h-7 rounded-lg border border-hairline bg-white px-2.5 text-xs font-medium text-danger-dark transition hover:bg-danger-light"
                                            @click="decideProposal(proposal.id, 'Ditolak')"
                                        >
                                            Tolak
                                        </button>
                                        <button
                                            type="button"
                                            class="h-7 rounded-lg bg-primary-600 px-3 text-xs font-medium text-white shadow-xs transition hover:bg-primary-500"
                                            @click="decideProposal(proposal.id, 'Diterima')"
                                        >
                                            Terima
                                        </button>
                                    </div>
                                </div>

                                <!-- Extended Details (Progressive Disclosure) -->
                                <div
                                    v-if="expandedProposalId === proposal.id"
                                    class="mt-2.5 border-t border-hairline pt-2.5 text-[11px] text-ink-secondary"
                                >
                                    <p><span class="font-medium text-ink">Jenis Program:</span> {{ proposal.type }}</p>
                                    <p class="mt-0.5"><span class="font-medium text-ink">Tanggal Masuk:</span> {{ proposal.submittedAt }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Progressive Disclosure for Pemda Programs -->
                    <div class="mt-4 border-t border-hairline pt-3">
                        <button
                            type="button"
                            class="flex w-full items-center justify-between rounded-xl bg-canvas px-3 py-2 text-xs font-medium text-ink-secondary transition hover:bg-neutral-100 hover:text-ink"
                            :aria-expanded="showProgramSection"
                            @click="showProgramSection = !showProgramSection"
                        >
                            <span>Status Pendaftaran Program Pemda ({{ programApplications.length }})</span>
                            <span class="font-mono text-[11px] text-primary-600">
                                {{ showProgramSection ? 'Sembunyikan ▴' : 'Buka ▾' }}
                            </span>
                        </button>

                        <ul v-if="showProgramSection" class="mt-2.5 space-y-2">
                            <li
                                v-for="app in programApplications"
                                :key="app.program"
                                class="flex items-center justify-between gap-2 rounded-xl border border-hairline bg-white p-2.5 text-xs"
                            >
                                <div class="min-w-0">
                                    <p class="truncate font-medium text-ink">{{ app.program }}</p>
                                    <p class="text-[11px] text-ink-muted">{{ app.organizer }}</p>
                                </div>
                                <StatusBadge :tone="app.status === 'Lolos Seleksi' ? 'success' : 'warning'" dot>
                                    {{ app.status }}
                                </StatusBadge>
                            </li>
                        </ul>
                    </div>
                </section>
            </div>

            <!-- Row 3: Finexy-style Recent/Inventory Table Card with Pill Search & Expandable Rows -->
            <section class="mt-6 rounded-2xl border border-hairline bg-white p-5 shadow-xs">
                <div class="flex flex-col gap-3 pb-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-ink">
                            Daftar Produk &amp; Stok
                        </h2>
                        <p class="text-xs text-ink-muted">
                            Informasi utama ditampilkan ringkas; klik "Rincian" untuk melihat kode SKU &amp; batas stok.
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <div class="relative flex-1 sm:flex-initial">
                            <label for="umkm-stock-search" class="sr-only">Cari Produk atau SKU</label>
                            <svg
                                class="pointer-events-none absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-ink-muted"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                                aria-hidden="true"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m0 0A7.5 7.5 0 1 0 6.1 6.1a7.5 7.5 0 0 0 10.55 10.55Z" />
                            </svg>
                            <input
                                id="umkm-stock-search"
                                ref="searchInputRef"
                                v-model="searchSku"
                                type="search"
                                placeholder="Cari produk..."
                                class="h-9 w-full rounded-full border border-hairline bg-canvas pl-8 pr-8 text-xs text-ink placeholder-neutral-400 focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary-500 sm:w-52"
                            />
                            <kbd
                                class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 rounded-full border border-hairline bg-white px-1.5 py-0.5 font-mono text-[10px] text-ink-muted"
                                aria-hidden="true"
                            >
                                /
                            </kbd>
                        </div>

                        <div class="inline-flex rounded-full border border-hairline bg-canvas p-0.5" role="group" aria-label="Filter status stok">
                            <button
                                v-for="st in ['Semua', 'Menipis', 'Habis']"
                                :key="st"
                                type="button"
                                class="h-8 rounded-full px-3 text-xs font-medium transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                                :class="
                                    stockFilter === st
                                        ? 'bg-white text-primary-700 shadow-xs'
                                        : 'text-ink-muted hover:text-ink'
                                "
                                @click="stockFilter = st"
                            >
                                {{ st }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-hairline text-left text-[13px]">
                        <thead class="bg-canvas/80 text-xs font-medium text-ink-muted">
                            <tr>
                                <th scope="col" class="rounded-l-xl px-4 py-3">Nama Produk</th>
                                <th scope="col" class="px-4 py-3">Kategori</th>
                                <th scope="col" class="px-4 py-3">Status</th>
                                <th scope="col" class="px-4 py-3">Sisa Stok</th>
                                <th scope="col" class="px-4 py-3">Harga</th>
                                <th scope="col" class="rounded-r-xl px-4 py-3 text-right">Detail</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-hairline bg-white">
                            <template v-for="product in filteredProducts" :key="product.sku">
                                <tr class="transition hover:bg-canvas/50">
                                    <td class="px-4 py-3.5 font-medium text-ink">
                                        {{ product.name }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3.5 text-ink-secondary">
                                        {{ product.category }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3.5">
                                        <StatusBadge :tone="stockTone(product.status)" dot>
                                            {{ product.status }}
                                        </StatusBadge>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3.5 font-mono text-xs text-ink">
                                        {{ product.stock }} unit
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3.5 font-semibold text-ink">
                                        {{ product.price }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3.5 text-right">
                                        <button
                                            type="button"
                                            class="inline-flex h-7 items-center rounded-full border border-hairline bg-canvas px-3 text-xs font-medium text-ink-secondary transition hover:border-primary-300 hover:text-primary-700"
                                            :aria-expanded="expandedProductSku === product.sku"
                                            @click="toggleProductDetail(product.sku)"
                                        >
                                            {{ expandedProductSku === product.sku ? 'Tutup ▴' : 'Rincian ▾' }}
                                        </button>
                                    </td>
                                </tr>

                                <!-- Expandable Row for Secondary Technical Info -->
                                <tr v-if="expandedProductSku === product.sku" class="bg-canvas/70">
                                    <td colspan="6" class="px-4 py-3 text-xs text-ink-secondary">
                                        <div class="flex flex-wrap items-center justify-between gap-3">
                                            <div class="flex flex-wrap items-center gap-4">
                                                <span><strong class="text-ink">Kode SKU:</strong> <code class="font-mono text-primary-700">{{ product.sku }}</code></span>
                                                <span><strong class="text-ink">Batas Aman Minimum:</strong> {{ product.minStock }} unit</span>
                                                <span><strong class="text-ink">Rekomendasi:</strong> {{ product.stock <= product.minStock ? 'Segera tambah stok produksi' : 'Stok mencukupi' }}</span>
                                            </div>
                                            <button
                                                type="button"
                                                class="rounded-full bg-primary-600 px-3 py-1 text-xs font-medium text-white hover:bg-primary-500"
                                                @click="posForm.sku = product.sku; showPosModal = true"
                                            >
                                                Catat Penjualan Produk Ini
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>

                            <tr v-if="!filteredProducts.length">
                                <td colspan="6" class="px-4 py-8 text-center text-xs text-ink-muted">
                                    Tidak ada produk yang cocok dengan pencarian Anda.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- POS Modal -->
            <div
                v-if="showPosModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-ink-deep/50 p-4 backdrop-blur-[1px]"
                role="dialog"
                aria-modal="true"
                aria-labelledby="pos-modal-title"
                @click.self="showPosModal = false"
            >
                <form
                    class="w-full max-w-md rounded-2xl border border-hairline bg-white p-6 shadow-lg"
                    @submit.prevent="submitPosPreview"
                >
                    <h3 id="pos-modal-title" class="text-base font-semibold text-ink">
                        Catat Penjualan Kasir
                    </h3>
                    <p class="mt-1 text-xs text-ink-muted">
                        Pilih produk yang terjual; sisa stok akan otomatis dikurangi.
                    </p>

                    <div class="mt-4 space-y-4">
                        <div>
                            <label for="pos-sku-select" class="block text-xs font-medium text-ink-secondary">
                                Pilih Produk
                            </label>
                            <select
                                id="pos-sku-select"
                                v-model="posForm.sku"
                                class="mt-1 block h-10 w-full rounded-xl border-hairline text-sm focus:border-primary-500 focus:ring-primary-500"
                            >
                                <option v-for="item in products" :key="item.sku" :value="item.sku">
                                    {{ item.name }} ({{ item.price }})
                                </option>
                            </select>
                        </div>

                        <div>
                            <label for="pos-qty-input" class="block text-xs font-medium text-ink-secondary">
                                Jumlah Terjual
                            </label>
                            <input
                                id="pos-qty-input"
                                v-model.number="posForm.qty"
                                type="number"
                                min="1"
                                max="99"
                                class="mt-1 block h-10 w-full rounded-xl border-hairline font-mono text-sm focus:border-primary-500 focus:ring-primary-500"
                            />
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-2">
                        <button
                            type="button"
                            class="h-9 rounded-full border border-hairline bg-white px-4 text-xs font-medium text-ink hover:bg-canvas"
                            @click="showPosModal = false"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="h-9 rounded-full bg-primary-600 px-4 text-xs font-medium text-white shadow-xs hover:bg-primary-500"
                        >
                            Simpan Transaksi
                        </button>
                    </div>
                </form>
            </div>
        </EvaluationPanelLayout>
    </div>
</template>
