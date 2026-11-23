<script setup>
import { Head, Link } from '@inertiajs/vue3';
import UmkmLayout from '@/Layouts/UmkmLayout.vue';
import StatCard from '@/Components/StatCard.vue';
import StockBadge from '@/Components/StockBadge.vue';

defineProps({ counts: Object, lowStockVariants: Array, hasBusiness: Boolean });
const guideSteps = [
    { title: '1. Periksa peringatan', description: 'Stok habis atau mencapai batas minimum ditampilkan di sini.' },
    { title: '2. Tambahkan persediaan', description: 'Buka Persediaan untuk mencatat barang masuk.' },
    { title: '3. Sesuaikan batas stok', description: 'Edit produk untuk mengubah batas peringatan setiap varian.' },
];
</script>

<template>
    <UmkmLayout title="Dashboard Usaha" subtitle="Pantau persediaan yang perlu segera ditambahkan." guide-title="Panduan stok usaha" guide-description="Tiga langkah untuk menjaga persediaan usaha." :guide-steps="guideSteps" :inventory-count="counts.active">
        <Head title="Dashboard Usaha" />
        <template #header-actions><Link :href="route('umkm.inventory.index')" class="inline-flex min-h-11 items-center rounded-xl bg-primary-600 px-4 text-sm font-semibold text-white hover:bg-primary-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2">Buka Persediaan</Link></template>
        <div class="flex flex-col gap-6">
            <section aria-label="Ringkasan stok" class="grid gap-4 sm:grid-cols-3">
                <StatCard label="Varian aktif" :value="String(counts.active)" badge="Persediaan usaha" delta="Produk dan varian arsip tidak dihitung." />
                <StatCard label="Perlu ditambah" :value="String(counts.low)" :tone="counts.low ? 'warning' : 'success'" :badge="counts.low ? 'Periksa stok' : 'Aman'" delta="Termasuk stok habis dan stok pada batas minimum." />
                <StatCard label="Stok habis" :value="String(counts.out)" :tone="counts.out ? 'danger' : 'secondary'" :badge="counts.out ? 'Segera restock' : 'Tidak ada'" delta="Varian dengan saldo stok nol." />
            </section>
            <section aria-labelledby="low-stock-heading" class="rounded-2xl border border-hairline bg-white p-5 shadow-xs sm:p-6">
                <h2 id="low-stock-heading" class="text-base font-semibold">Peringatan persediaan</h2>
                <p class="mt-1 text-sm text-ink-secondary">Stok terendah ditampilkan lebih dahulu. Maksimal lima varian untuk ditinjau.</p>
                <div v-if="!hasBusiness" class="py-8 text-sm text-ink-secondary"><p>Lengkapi profil usaha untuk mulai mengelola produk dan persediaan.</p><Link :href="route('business-profile.edit')" class="mt-3 inline-flex min-h-11 items-center rounded-lg font-semibold text-primary-700 underline focus-visible:ring-2 focus-visible:ring-primary-500">Lengkapi profil usaha</Link></div>
                <p v-else-if="!counts.active" class="py-8 text-sm text-ink-secondary">Belum ada varian produk aktif. Tambahkan produk melalui Katalog Produk.</p>
                <p v-else-if="!lowStockVariants.length" class="py-8 text-sm text-success-dark">Stok aman. Seluruh varian aktif berada di atas batas minimum.</p>
                <ul v-else class="mt-4 divide-y divide-hairline">
                    <li v-for="variant in lowStockVariants" :key="variant.id" class="flex flex-wrap items-center justify-between gap-4 py-4">
                        <div class="min-w-0 flex-1 basis-48"><h3 class="break-words text-sm font-semibold">{{ variant.product.name }}</h3><p class="mt-1 break-words text-sm text-ink-secondary">{{ variant.variant_key === 'default' ? 'Varian standar' : variant.variant_key }}</p></div>
                        <div class="flex flex-col gap-2 text-sm"><StockBadge :status="variant.stock_status" /><p class="text-ink-secondary">Sisa {{ variant.stock_quantity }} {{ variant.product.unit }} · Batas {{ variant.low_stock_threshold }}</p></div>
                    </li>
                </ul>
                <div class="mt-4 flex flex-wrap gap-4 border-t border-hairline pt-4">
                    <Link :href="route('umkm.inventory.index')" class="inline-flex min-h-11 items-center rounded-lg font-semibold text-primary-700 underline focus-visible:ring-2 focus-visible:ring-primary-500">Lihat semua stok dan restock</Link>
                    <Link :href="route('umkm.products.index')" class="inline-flex min-h-11 items-center rounded-lg text-sm text-primary-700 underline focus-visible:ring-2 focus-visible:ring-primary-500">Buka Katalog Produk</Link>
                </div>
            </section>
        </div>
    </UmkmLayout>
</template>
