<script setup>
import { Head, Link } from '@inertiajs/vue3';
import UmkmLayout from '@/Layouts/UmkmLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';

const props = defineProps({ sale: Object });

const formatCurrency = (value) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
const formatNumber = (value) => new Intl.NumberFormat('id-ID').format(value);
const formatDate = (value) => new Intl.DateTimeFormat('id-ID', {
    dateStyle: 'full', timeStyle: 'short', timeZone: 'Asia/Jakarta',
}).format(new Date(value));
</script>

<template>
    <UmkmLayout title="Detail Penjualan" :subtitle="`Invoice ${sale.invoice_number}`">
        <Head :title="`Penjualan ${sale.invoice_number}`" />
        <template #header-actions>
            <Link :href="route('umkm.sales.index')" class="inline-flex min-h-11 items-center rounded-xl border border-hairline bg-white px-4 text-sm font-semibold text-ink hover:bg-canvas focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2">← Kembali</Link>
        </template>

        <div class="flex flex-col gap-6">
            <section class="rounded-2xl border border-hairline bg-white p-5 shadow-xs">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-bold text-ink">{{ sale.invoice_number }}</h2>
                        <time :datetime="sale.sold_at" class="text-sm text-ink-secondary">{{ formatDate(sale.sold_at) }}</time>
                    </div>
                    <StatusBadge :tone="sale.status === 'recorded' ? 'success' : 'neutral'" size="md">{{ sale.status === 'recorded' ? 'Tercatat' : sale.status }}</StatusBadge>
                </div>

                <p v-if="sale.notes" class="mt-4 rounded-xl bg-canvas p-4 text-sm text-ink-secondary">{{ sale.notes }}</p>

                <table class="mt-5 w-full text-left text-sm">
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
                            <td class="py-3">{{ item.product_name_snapshot }}</td>
                            <td class="py-3 text-ink-secondary">{{ item.variant_label_snapshot === 'default' ? 'Varian standar' : item.variant_label_snapshot }}</td>
                            <td class="py-3 text-right tabular-nums">{{ formatNumber(item.quantity) }} {{ item.unit_snapshot }}</td>
                            <td class="py-3 text-right tabular-nums">{{ formatCurrency(item.unit_price) }}</td>
                            <td class="py-3 text-right font-semibold tabular-nums">{{ formatCurrency(parseFloat(item.unit_price) * item.quantity) }}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="mt-4 flex items-center justify-between border-t border-hairline pt-4">
                    <span class="text-base font-semibold">Total Penjualan</span>
                    <span class="text-xl font-bold tabular-nums text-primary-700">{{ formatCurrency(sale.total_amount) }}</span>
                </div>
            </section>
        </div>
    </UmkmLayout>
</template>
