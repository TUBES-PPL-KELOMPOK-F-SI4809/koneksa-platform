<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import UmkmLayout from '@/Layouts/UmkmLayout.vue';
import StatCard from '@/Components/StatCard.vue';
import StatusBadge from '@/Components/StatusBadge.vue';

const props = defineProps({ sales: Object, status: String });
const feedback = ref(props.status);
const expandedId = ref(null);

const formatCurrency = (value) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
const formatNumber = (value) => new Intl.NumberFormat('id-ID').format(value);
const formatDate = (value) => new Intl.DateTimeFormat('id-ID', {
    dateStyle: 'medium', timeStyle: 'short', timeZone: 'Asia/Jakarta',
}).format(new Date(value));

function toggleExpand(id) {
    expandedId.value = expandedId.value === id ? null : id;
}

const guideSteps = [
    { title: '1. Lihat riwayat', description: 'Semua transaksi penjualan ditampilkan dengan nomor invoice dan total.' },
    { title: '2. Periksa detail', description: 'Klik Rincian untuk melihat item yang dijual dalam transaksi.' },
    { title: '3. Catat penjualan baru', description: 'Gunakan tombol Catat Penjualan untuk membuat transaksi baru.' },
];
</script>

<template>
    <UmkmLayout title="Riwayat Penjualan" subtitle="Telusuri seluruh transaksi penjualan yang telah tercatat." guide-title="Panduan riwayat" guide-description="Cara menelusuri riwayat penjualan." :guide-steps="guideSteps">
        <Head title="Riwayat Penjualan" />
        <template #header-actions>
            <Link :href="route('umkm.sales.create')" class="inline-flex min-h-11 items-center rounded-xl bg-primary-600 px-4 text-sm font-semibold text-white hover:bg-primary-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2">+ Catat Penjualan</Link>
        </template>

        <div class="flex flex-col gap-6">
            <div v-if="feedback" role="status" class="flex items-center justify-between gap-3 rounded-2xl border border-primary-200 bg-primary-50 p-4 text-sm text-primary-900">
                <p>{{ feedback }}</p>
                <button type="button" class="min-h-11 shrink-0 rounded-xl px-3 font-semibold text-primary-700 hover:bg-primary-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500" aria-label="Tutup pesan berhasil" @click="feedback = ''">Tutup</button>
            </div>

            <section aria-label="Ringkasan penjualan" class="grid gap-4 sm:grid-cols-2">
                <StatCard label="Total transaksi" :value="formatNumber(sales.total)" :badge="sales.total ? 'Tercatat' : 'Belum ada'" delta="Seluruh transaksi penjualan yang tersimpan." />
            </section>

            <section aria-labelledby="sales-heading" class="rounded-2xl border border-hairline bg-white p-5 shadow-xs">
                <h2 id="sales-heading" class="text-base font-semibold text-ink">Daftar transaksi</h2>
                <p class="mt-1 text-sm text-ink-secondary">Transaksi terbaru ditampilkan lebih dahulu. Waktu dalam WIB.</p>

                <p v-if="!sales.data.length" class="py-10 text-sm text-ink-muted">Belum ada transaksi penjualan. Gunakan tombol Catat Penjualan untuk memulai.</p>
                <ul v-else class="mt-4 divide-y divide-hairline">
                    <li v-for="sale in sales.data" :key="sale.id" class="py-4">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div class="min-w-0 flex-1 basis-48">
                                <p class="font-semibold text-ink">{{ sale.invoice_number }}</p>
                                <time :datetime="sale.sold_at" class="text-sm text-ink-muted">{{ formatDate(sale.sold_at) }}</time>
                            </div>
                            <StatusBadge :tone="sale.status === 'recorded' ? 'success' : 'neutral'" size="md">{{ sale.status === 'recorded' ? 'Tercatat' : sale.status }}</StatusBadge>
                            <p class="text-base font-bold tabular-nums text-ink">{{ formatCurrency(sale.total_amount) }}</p>
                            <button type="button" class="min-h-11 rounded-xl border border-hairline px-3 text-sm font-semibold text-primary-700 hover:bg-primary-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500" :aria-expanded="expandedId === sale.id" :aria-controls="`sale-detail-${sale.id}`" @click="toggleExpand(sale.id)">
                                {{ expandedId === sale.id ? 'Tutup ▴' : 'Rincian ▾' }}
                            </button>
                        </div>

                        <div v-if="expandedId === sale.id" :id="`sale-detail-${sale.id}`" class="mt-3 rounded-xl bg-canvas p-4">
                            <p v-if="sale.notes" class="mb-3 text-sm text-ink-secondary">{{ sale.notes }}</p>
                            <table class="w-full text-left text-sm">
                                <thead>
                                    <tr class="border-b border-hairline text-xs uppercase text-ink-muted">
                                        <th class="pb-2 font-medium">Produk</th>
                                        <th class="pb-2 font-medium">Varian</th>
                                        <th class="pb-2 text-right font-medium">Qty</th>
                                        <th class="pb-2 text-right font-medium">Harga</th>
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
                            <div class="mt-3 flex items-center justify-between border-t border-hairline pt-3">
                                <span class="text-sm font-semibold">Total</span>
                                <span class="text-base font-bold tabular-nums text-primary-700">{{ formatCurrency(sale.total_amount) }}</span>
                            </div>
                        </div>
                    </li>
                </ul>

                <nav v-if="sales.last_page > 1" aria-label="Halaman riwayat penjualan" class="mt-4 flex items-center justify-between gap-3 text-sm">
                    <Link v-if="sales.prev_page_url" :href="sales.prev_page_url" class="rounded-lg p-3 text-primary-700 focus-visible:ring-2 focus-visible:ring-primary-500">Sebelumnya</Link>
                    <span>{{ sales.current_page }} / {{ sales.last_page }}</span>
                    <Link v-if="sales.next_page_url" :href="sales.next_page_url" class="rounded-lg p-3 text-primary-700 focus-visible:ring-2 focus-visible:ring-primary-500">Berikutnya</Link>
                </nav>
            </section>
        </div>
    </UmkmLayout>
</template>
