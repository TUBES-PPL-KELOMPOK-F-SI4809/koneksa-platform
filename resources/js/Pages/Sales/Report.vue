<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import UmkmLayout from '@/Layouts/UmkmLayout.vue';
import StatCard from '@/Components/StatCard.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    sales: Object,
    summary: Object,
    filters: Object,
});

const startDate = ref(props.filters.start_date || '');
const endDate = ref(props.filters.end_date || '');
const expandedId = ref(null);

const formatCurrency = (value) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
const formatNumber = (value) => new Intl.NumberFormat('id-ID').format(value);
const formatDate = (value) => new Intl.DateTimeFormat('id-ID', {
    dateStyle: 'medium', timeStyle: 'short', timeZone: 'Asia/Jakarta',
}).format(new Date(value));

function applyFilter() {
    router.get(route('umkm.sales.report'), {
        start_date: startDate.value,
        end_date: endDate.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
}

function resetFilter() {
    startDate.value = '';
    endDate.value = '';
    router.get(route('umkm.sales.report'), {}, {
        preserveState: true,
        preserveScroll: true,
    });
}

function exportCsv() {
    const url = route('umkm.sales.report.export', {
        start_date: startDate.value || props.filters.start_date,
        end_date: endDate.value || props.filters.end_date,
    });
    window.location.href = url;
}

function toggleExpand(id) {
    expandedId.value = expandedId.value === id ? null : id;
}

const guideSteps = [
    { title: '1. Tentukan rentang tanggal', description: 'Pilih tanggal awal dan akhir penjualan yang ingin direkap.' },
    { title: '2. Analisis performa', description: 'Pantau total omzet, transaksi, dan kuantitas produk terjual.' },
    { title: '3. Ekspor ke CSV', description: 'Unduh rekapitulasi data penjualan untuk pembukuan atau Excel.' },
];
</script>

<template>
    <UmkmLayout title="Rekap & Ekspor Penjualan" subtitle="Analisis riwayat penjualan usaha berdasarkan periode dan unduh laporan CSV." guide-title="Panduan rekap penjualan" guide-description="Langkah mudah menyusun laporan penjualan." :guide-steps="guideSteps">
        <Head title="Rekap & Ekspor Penjualan" />
        <template #header-actions>
            <div class="flex flex-wrap items-center gap-3">
                <button
                    type="button"
                    class="inline-flex min-h-11 items-center gap-2 rounded-xl bg-success-dark px-4 text-sm font-semibold text-white shadow-xs hover:bg-emerald-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-success-dark focus-visible:ring-offset-2"
                    @click="exportCsv"
                >
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                    <span>Ekspor CSV</span>
                </button>
                <Link :href="route('umkm.sales.create')" class="inline-flex min-h-11 items-center rounded-xl bg-primary-600 px-4 text-sm font-semibold text-white shadow-xs hover:bg-primary-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2">+ Catat Penjualan</Link>
            </div>
        </template>

        <div class="flex flex-col gap-6">
            <!-- Filter Tanggal (KSA-57) -->
            <section aria-labelledby="filter-heading" class="rounded-2xl border border-hairline bg-white p-5 shadow-xs">
                <h2 id="filter-heading" class="text-base font-semibold text-ink">Filter Periode Laporan</h2>
                <p class="mt-1 text-sm text-ink-secondary">Pilih rentang tanggal untuk merekap transaksi penjualan.</p>

                <form class="mt-4 flex flex-wrap items-end gap-4" @submit.prevent="applyFilter">
                    <div class="min-w-40 flex-1">
                        <label for="start-date" class="block text-xs font-medium uppercase tracking-wider text-ink-muted">Dari Tanggal</label>
                        <input
                            id="start-date"
                            v-model="startDate"
                            type="date"
                            class="mt-1 h-11 w-full rounded-xl border-hairline bg-canvas px-3 text-sm focus:border-primary-500 focus:bg-white focus:ring-primary-500"
                        />
                    </div>
                    <div class="min-w-40 flex-1">
                        <label for="end-date" class="block text-xs font-medium uppercase tracking-wider text-ink-muted">Sampai Tanggal</label>
                        <input
                            id="end-date"
                            v-model="endDate"
                            type="date"
                            class="mt-1 h-11 w-full rounded-xl border-hairline bg-canvas px-3 text-sm focus:border-primary-500 focus:bg-white focus:ring-primary-500"
                        />
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <PrimaryButton class="min-h-11 bg-primary-600 hover:bg-primary-500 focus:bg-primary-700">Terapkan Filter</PrimaryButton>
                        <SecondaryButton class="min-h-11" type="button" @click="resetFilter">Reset</SecondaryButton>
                    </div>
                </form>
            </section>

            <!-- Ringkasan Periode Terpilih -->
            <section aria-label="Ringkasan periode" class="grid gap-4 sm:grid-cols-3">
                <StatCard label="Omzet Periode Ini" :value="formatCurrency(summary.totalRevenue)" badge="Pendapatan" delta="Akumulasi omzet pada rentang tanggal terpilih." />
                <StatCard label="Jumlah Transaksi" :value="formatNumber(summary.totalSalesCount)" badge="Transaksi" tone="secondary" delta="Total faktur penjualan berhasil tercatat." />
                <StatCard label="Total Item Terjual" :value="formatNumber(summary.totalItemsSold)" badge="Volume" tone="primary" delta="Kuantitas seluruh unit produk yang keluar." />
            </section>

            <!-- Tabel Daftar Rekap Penjualan -->
            <section aria-labelledby="sales-recap-heading" class="rounded-2xl border border-hairline bg-white p-5 shadow-xs">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 id="sales-recap-heading" class="text-base font-semibold text-ink">Daftar Transaksi Periode Ini</h2>
                        <p class="mt-1 text-sm text-ink-secondary">Menampilkan data penjualan dari {{ filters.start_date }} s.d. {{ filters.end_date }}.</p>
                    </div>
                    <StatusBadge tone="primary" size="md">{{ formatNumber(sales.total) }} transaksi</StatusBadge>
                </div>

                <p v-if="!sales.data.length" class="py-10 text-sm text-ink-muted">Tidak ada transaksi penjualan pada rentang tanggal ini.</p>
                <ul v-else class="mt-4 divide-y divide-hairline">
                    <li v-for="sale in sales.data" :key="sale.id" class="py-4">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div class="min-w-0 flex-1 basis-48">
                                <p class="font-semibold text-ink">{{ sale.invoice_number }}</p>
                                <time :datetime="sale.sold_at" class="text-sm text-ink-muted">{{ formatDate(sale.sold_at) }}</time>
                            </div>
                            <StatusBadge :tone="sale.status === 'recorded' ? 'success' : 'neutral'" size="md">{{ sale.status === 'recorded' ? 'Tercatat' : sale.status }}</StatusBadge>
                            <p class="text-base font-bold tabular-nums text-ink">{{ formatCurrency(sale.total_amount) }}</p>
                            <button
                                type="button"
                                class="min-h-11 rounded-xl border border-hairline px-3 text-sm font-semibold text-primary-700 hover:bg-primary-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                                :aria-expanded="expandedId === sale.id"
                                :aria-controls="`report-detail-${sale.id}`"
                                @click="toggleExpand(sale.id)"
                            >
                                {{ expandedId === sale.id ? 'Tutup ▴' : 'Rincian ▾' }}
                            </button>
                        </div>

                        <!-- Rincian Item Penjualan (Progressive Disclosure) -->
                        <div v-if="expandedId === sale.id" :id="`report-detail-${sale.id}`" class="mt-3 rounded-xl bg-canvas p-4">
                            <p v-if="sale.notes" class="mb-3 text-sm text-ink-secondary"><strong>Catatan:</strong> {{ sale.notes }}</p>
                            <table class="w-full text-left text-sm">
                                <thead>
                                    <tr class="border-b border-hairline text-xs uppercase text-ink-muted">
                                        <th class="pb-2 font-medium">Produk</th>
                                        <th class="pb-2 font-medium">Varian</th>
                                        <th class="pb-2 text-right font-medium">Qty</th>
                                        <th class="pb-2 text-right font-medium">Harga Satuan</th>
                                        <th class="pb-2 text-right font-medium">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="item in sale.items" :key="item.id" class="border-b border-hairline last:border-0">
                                        <td class="py-2">{{ item.product_name_snapshot }}</td>
                                        <td class="py-2 text-ink-secondary">{{ item.variant_label_snapshot === 'default' ? 'Standar' : item.variant_label_snapshot }}</td>
                                        <td class="py-2 text-right tabular-nums">{{ formatNumber(item.quantity) }} {{ item.unit_snapshot }}</td>
                                        <td class="py-2 text-right tabular-nums">{{ formatCurrency(item.unit_price) }}</td>
                                        <td class="py-2 text-right font-semibold tabular-nums">{{ formatCurrency(parseFloat(item.unit_price) * item.quantity) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </li>
                </ul>

                <!-- Navigasi Paginasi -->
                <nav v-if="sales.last_page > 1" aria-label="Halaman rekap penjualan" class="mt-4 flex items-center justify-between gap-3 text-sm">
                    <Link v-if="sales.prev_page_url" :href="sales.prev_page_url" class="rounded-lg p-3 text-primary-700 focus-visible:ring-2 focus-visible:ring-primary-500">Sebelumnya</Link>
                    <span>{{ sales.current_page }} / {{ sales.last_page }}</span>
                    <Link v-if="sales.next_page_url" :href="sales.next_page_url" class="rounded-lg p-3 text-primary-700 focus-visible:ring-2 focus-visible:ring-primary-500">Berikutnya</Link>
                </nav>
            </section>
        </div>
    </UmkmLayout>
</template>
