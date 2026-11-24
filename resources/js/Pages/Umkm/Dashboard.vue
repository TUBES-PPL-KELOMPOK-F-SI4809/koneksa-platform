<script setup>
import { computed, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import UmkmLayout from '@/Layouts/UmkmLayout.vue';
import StatCard from '@/Components/StatCard.vue';
import StockBadge from '@/Components/StockBadge.vue';

const props = defineProps({
    counts: Object,
    financials: {
        type: Object,
        default: () => ({ totalRevenue: 0, totalSalesCount: 0 }),
    },
    salesTrend: {
        type: Array,
        default: () => [],
    },
    lowStockVariants: Array,
    hasBusiness: Boolean,
});

const selectedTrendIndex = ref(props.salesTrend?.length ? props.salesTrend.length - 1 : 0);
const selectedTrend = computed(() => props.salesTrend[selectedTrendIndex.value] || { day: '-', amount: 0, count: 0, date: '-' });

const maxTrendAmount = computed(() => {
    const max = Math.max(...props.salesTrend.map((t) => t.amount), 1);
    return max > 0 ? max : 1;
});

const formatCurrency = (value) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
const formatNumber = (value) => new Intl.NumberFormat('id-ID').format(value);

const guideSteps = [
    { title: '1. Pantau omzet', description: 'Lihat ringkasan pendapatan dan tren penjualan harian Anda.' },
    { title: '2. Cek stok menipis', description: 'Perhatikan varian yang membutuhkan penambahan persediaan.' },
    { title: '3. Aksi operasional', description: 'Gunakan navigasi cepat untuk mencatat penjualan atau restock.' },
];
</script>

<template>
    <UmkmLayout title="Dashboard Usaha" subtitle="Ringkasan performa penjualan, omzet, dan status persediaan usaha Anda." guide-title="Panduan dashboard usaha" guide-description="Tiga langkah mengoptimalkan operasional usaha." :guide-steps="guideSteps" :inventory-count="counts.active">
        <Head title="Dashboard Usaha" />
        <template #header-actions>
            <div class="flex flex-wrap items-center gap-3">
                <Link :href="route('umkm.sales.create')" class="inline-flex min-h-11 items-center rounded-xl bg-primary-600 px-4 text-sm font-semibold text-white shadow-xs hover:bg-primary-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2">+ Catat Penjualan</Link>
                <Link :href="route('umkm.inventory.index')" class="inline-flex min-h-11 items-center rounded-xl border border-hairline bg-white px-4 text-sm font-semibold text-ink hover:bg-canvas focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2">Persediaan</Link>
            </div>
        </template>

        <div class="flex flex-col gap-6">
            <!-- 4 KPI StatCards (Modern Bento) -->
            <section aria-label="Ringkasan metrik utama" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <StatCard label="Total Omzet" :value="formatCurrency(financials.totalRevenue)" badge="Penjualan" delta="Akumulasi seluruh transaksi tercatat." />
                <StatCard label="Total Transaksi" :value="formatNumber(financials.totalSalesCount)" badge="Transaksi" tone="secondary" delta="Frekuensi pesanan yang berhasil dicatat." />
                <StatCard label="Varian Aktif" :value="formatNumber(counts.active)" badge="Katalog" delta="Varian siap jual yang tidak diarsipkan." />
                <StatCard label="Perlu Restock" :value="formatNumber(counts.low)" :tone="counts.low ? 'warning' : 'success'" :badge="counts.low ? 'Perhatian' : 'Aman'" delta="Varian stok habis atau batas minimum." />
            </section>

            <!-- Bento 8 + 4: Analisis Penjualan & Akses Cepat Operasional -->
            <div class="grid items-start gap-6 xl:grid-cols-12">
                <!-- Analitik Tren Penjualan 7 Hari -->
                <section aria-labelledby="trend-heading" class="min-w-0 rounded-2xl border border-hairline bg-white p-5 shadow-xs xl:col-span-8">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h2 id="trend-heading" class="text-base font-semibold text-ink">Tren Penjualan 7 Hari Terakhir</h2>
                            <p class="mt-1 text-sm text-ink-secondary">Pilih hari untuk melihat detail transaksi dan omzet harian.</p>
                        </div>
                        <div class="text-right">
                            <span class="text-xs uppercase text-ink-muted">Omzet terpilih ({{ selectedTrend.day }}, {{ selectedTrend.date }})</span>
                            <p class="text-base font-bold tabular-nums text-primary-700">{{ formatCurrency(selectedTrend.amount) }} <span class="text-xs font-normal text-ink-secondary">({{ selectedTrend.count }} transaksi)</span></p>
                        </div>
                    </div>

                    <!-- Interactive Bar Chart -->
                    <div class="mt-6 flex h-44 items-end justify-between gap-2 border-b border-hairline pb-3 sm:gap-4">
                        <button
                            v-for="(trend, index) in salesTrend"
                            :key="trend.date"
                            type="button"
                            class="group flex flex-1 flex-col items-center gap-2 focus-visible:outline-none"
                            :aria-label="`Penjualan ${trend.day} ${trend.date}: ${formatCurrency(trend.amount)}`"
                            @click="selectedTrendIndex = index"
                        >
                            <div class="relative flex h-32 w-full items-end justify-center">
                                <div
                                    class="w-full max-w-10 rounded-t-lg transition-all duration-200"
                                    :class="selectedTrendIndex === index ? 'bg-primary-600 ring-2 ring-primary-300' : 'bg-primary-100 hover:bg-primary-200'"
                                    :style="{ height: `${Math.max(12, Math.round((trend.amount / maxTrendAmount) * 100))}%` }"
                                ></div>
                            </div>
                            <span
                                class="text-xs font-medium"
                                :class="selectedTrendIndex === index ? 'font-bold text-primary-700' : 'text-ink-secondary'"
                            >
                                {{ trend.day }}
                            </span>
                        </button>
                    </div>

                    <div class="mt-4 flex items-center justify-between text-xs text-ink-muted">
                        <span>Grafik diperbarui secara otomatis dari transaksi kasir.</span>
                        <Link :href="route('umkm.sales.index')" class="font-semibold text-primary-700 hover:underline">Lihat Riwayat Lengkap →</Link>
                    </div>
                </section>

                <!-- Aksi Cepat Operasional UMKM (FR-08.2) -->
                <section aria-labelledby="quick-actions-heading" class="min-w-0 rounded-2xl border border-hairline bg-white p-5 shadow-xs xl:col-span-4">
                    <h2 id="quick-actions-heading" class="text-base font-semibold text-ink">Aksi Cepat Operasional</h2>
                    <p class="mt-1 text-sm text-ink-secondary">Navigasi langsung ke modul transaksi dan persediaan.</p>

                    <div class="mt-4 flex flex-col gap-3">
                        <Link :href="route('umkm.sales.create')" class="flex items-center gap-3 rounded-xl border border-hairline p-3 transition hover:border-primary-300 hover:bg-primary-50">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary-600 text-white">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-ink">Catat Penjualan Baru</p>
                                <p class="text-xs text-ink-secondary">Buka kasir POS dan potong stok</p>
                            </div>
                        </Link>

                        <Link :href="route('umkm.inventory.index')" class="flex items-center gap-3 rounded-xl border border-hairline p-3 transition hover:border-secondary-300 hover:bg-secondary-50">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-secondary-600 text-white">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m3 7 9-4 9 4-9 4-9-4Zm0 0v10l9 4 9-4V7M12 11v10" /></svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-ink">Restock Persediaan</p>
                                <p class="text-xs text-ink-secondary">Catat barang masuk & cek saldo</p>
                            </div>
                        </Link>

                        <Link :href="route('umkm.products.index')" class="flex items-center gap-3 rounded-xl border border-hairline p-3 transition hover:border-hairline hover:bg-canvas">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-slate-700 text-white">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M5 7V4h14v3M5 7v13h14V7M9 11h6" /></svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-ink">Katalog Produk</p>
                                <p class="text-xs text-ink-secondary">Kelola harga & varian produk</p>
                            </div>
                        </Link>

                        <Link :href="route('umkm.sales.index')" class="flex items-center gap-3 rounded-xl border border-hairline p-3 transition hover:border-hairline hover:bg-canvas">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-ink">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-ink">Daftar Nota Penjualan</p>
                                <p class="text-xs text-ink-secondary">Lihat riwayat transaksi kasir</p>
                            </div>
                        </Link>
                    </div>
                </section>
            </div>

            <!-- Peringatan Persediaan (FR-08.2) -->
            <section aria-labelledby="low-stock-heading" class="rounded-2xl border border-hairline bg-white p-5 shadow-xs sm:p-6">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 id="low-stock-heading" class="text-base font-semibold">Peringatan Persediaan</h2>
                        <p class="mt-1 text-sm text-ink-secondary">Varian dengan saldo stok terendah ditampilkan lebih dahulu.</p>
                    </div>
                    <Link :href="route('umkm.inventory.index')" class="text-sm font-semibold text-primary-700 underline focus-visible:ring-2 focus-visible:ring-primary-500">Kelola Semua Stok →</Link>
                </div>

                <div v-if="!hasBusiness" class="py-8 text-sm text-ink-secondary">
                    <p>Lengkapi profil usaha untuk mulai mengelola produk dan persediaan.</p>
                    <Link :href="route('business-profile.edit')" class="mt-3 inline-flex min-h-11 items-center rounded-lg font-semibold text-primary-700 underline focus-visible:ring-2 focus-visible:ring-primary-500">Lengkapi profil usaha</Link>
                </div>
                <p v-else-if="!counts.active" class="py-8 text-sm text-ink-secondary">Belum ada varian produk aktif. Tambahkan produk melalui Katalog Produk.</p>
                <p v-else-if="!lowStockVariants.length" class="py-8 text-sm text-success-dark">Stok aman. Seluruh varian aktif berada di atas batas minimum.</p>
                <ul v-else class="mt-4 divide-y divide-hairline">
                    <li v-for="variant in lowStockVariants" :key="variant.id" class="flex flex-wrap items-center justify-between gap-4 py-4">
                        <div class="min-w-0 flex-1 basis-48">
                            <h3 class="break-words text-sm font-semibold">{{ variant.product.name }}</h3>
                            <p class="mt-1 break-words text-sm text-ink-secondary">{{ variant.variant_key === 'default' ? 'Varian standar' : variant.variant_key }}</p>
                        </div>
                        <div class="flex flex-col gap-2 text-sm">
                            <StockBadge :status="variant.stock_status" />
                            <p class="text-ink-secondary">Sisa {{ variant.stock_quantity }} {{ variant.product.unit }} · Batas {{ variant.low_stock_threshold }}</p>
                        </div>
                    </li>
                </ul>
            </section>
        </div>
    </UmkmLayout>
</template>
