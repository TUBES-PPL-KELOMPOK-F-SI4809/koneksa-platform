<script setup>
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import UmkmLayout from '@/Layouts/UmkmLayout.vue';
import Modal from '@/Components/Modal.vue';
import StatCard from '@/Components/StatCard.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import ProductForm from './Partials/ProductForm.vue';

const props = defineProps({ products: Object, counts: Object, filters: Object, hasBusiness: Boolean, status: String });
const search = ref(props.filters.search);
const filter = ref(props.filters.status);
const searchInput = ref(null);
const showForm = ref(false);
const selectedProduct = ref(null);
const archiveProduct = ref(null);
const processing = ref(false);
const feedbackVisible = ref(true);
const addButton = ref(null);
const archiveCancel = ref(null);
const archiveForm = useForm({});
const total = computed(() => props.counts.active + props.counts.archived);
const guideSteps = [
    { title: '1. Tambahkan produk', description: 'Isi nama, satuan, dan harga jual. Tambahkan varian bila diperlukan.' },
    { title: '2. Perbarui informasi', description: 'Buka Rincian untuk melihat varian, lalu pilih Edit produk.' },
    { title: '3. Arsipkan produk', description: 'Arsipkan produk yang sudah tidak dijual. Riwayat lama tetap tersimpan.' },
];
const rupiah = (value) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 2 }).format(value);
function price(product) {
    const prices = product.variants.filter((variant) => !variant.archived_at).map((variant) => Number(variant.selling_price));
    if (!prices.length) return 'Tidak ada varian aktif';
    const min = Math.min(...prices), max = Math.max(...prices);
    return min === max ? rupiah(min) : `${rupiah(min)} – ${rupiah(max)}`;
}
function applyFilters() {
    router.get(route('umkm.products.index'), { search: search.value, status: filter.value }, { preserveState: true, preserveScroll: true });
}
async function openForm(product = null) {
    selectedProduct.value = product;
    showForm.value = true;
    await nextTick();
    document.getElementById('product-name')?.focus();
}
function closeForm() {
    if (processing.value) return;
    showForm.value = false;
}
function restoreFocus() {
    if (document.activeElement === document.body) addButton.value?.$el.focus();
}
async function confirmArchive(product) {
    archiveForm.clearErrors();
    archiveProduct.value = product;
    await nextTick();
    archiveCancel.value?.$el.focus();
}
function archive() {
    archiveForm.post(route('umkm.products.archive', archiveProduct.value.id), {
        preserveScroll: true,
        onSuccess: () => { archiveProduct.value = null; },
    });
}
function shortcut(event) {
    if (event.key === '/' && !event.ctrlKey && !event.metaKey && !event.target.matches('input,textarea,select,[contenteditable="true"]') && !showForm.value && !archiveProduct.value) {
        event.preventDefault(); searchInput.value?.focus();
    }
}
watch(() => props.status, () => { feedbackVisible.value = true; });
watch(() => props.filters, (value) => { search.value = value.search; filter.value = value.status; });
onMounted(() => document.addEventListener('keydown', shortcut));
onUnmounted(() => document.removeEventListener('keydown', shortcut));
</script>

<template>
    <div>
        <Head title="Katalog Produk" />
        <UmkmLayout title="Katalog Produk" subtitle="Kelola produk usaha, perbarui harga, dan simpan produk yang tidak lagi dijual ke arsip." guide-title="Panduan katalog produk" guide-description="Tiga langkah untuk merapikan katalog usaha Anda." :guide-steps="guideSteps">
            <template #search><span class="text-sm text-ink-secondary">Produk usaha Anda</span></template>
            <template #header-actions><PrimaryButton ref="addButton" class="min-h-11 rounded-xl bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 active:bg-primary-700 disabled:opacity-50" :disabled="!hasBusiness" @click="openForm()">+ Tambah produk</PrimaryButton></template>
            <div v-if="status && feedbackVisible" role="status" class="mb-6 flex items-start justify-between gap-3 rounded-xl border border-success/25 bg-success-light p-4 text-sm text-success-dark">
                <p class="py-2">{{ status }}</p><button type="button" aria-label="Tutup pemberitahuan" class="min-h-11 rounded-lg px-3 font-semibold focus-visible:ring-2 focus-visible:ring-primary-500" @click="feedbackVisible = false">Tutup</button>
            </div>
            <div class="mb-6 grid gap-4 sm:grid-cols-3">
                <StatCard label="Seluruh produk" :value="String(total)" badge="Katalog usaha" delta="Hanya produk milik usaha Anda" tone="secondary" />
                <StatCard label="Produk aktif" :value="String(counts.active)" badge="Aktif" delta="Tersedia untuk operasional usaha" />
                <StatCard label="Produk diarsipkan" :value="String(counts.archived)" badge="Tersimpan" delta="Riwayat lama tetap tersedia" tone="info" />
            </div>
            <section aria-labelledby="catalog-title" class="overflow-hidden rounded-2xl border border-hairline bg-white shadow-xs">
                <div class="flex flex-wrap items-start justify-between gap-4 border-b border-hairline p-5">
                    <div><h2 id="catalog-title" class="font-semibold">Daftar produk</h2><p class="mt-1 text-sm text-ink-secondary">Buka rincian untuk melihat deskripsi dan varian.</p></div>
                    <form class="flex w-full flex-wrap gap-3 sm:w-auto" role="search" @submit.prevent="applyFilters">
                        <label class="w-full min-w-0 sm:w-auto sm:flex-1"><span class="sr-only">Cari nama produk</span><input ref="searchInput" v-model="search" type="search" maxlength="150" placeholder="Cari nama produk… /" class="h-11 w-full rounded-full border-hairline text-sm focus:border-primary-500 focus:ring-primary-500 sm:w-52" /></label>
                        <label><span class="sr-only">Status produk</span><select v-model="filter" class="h-11 rounded-xl border-hairline text-sm focus:border-primary-500 focus:ring-primary-500" @change="applyFilters"><option value="active">Aktif</option><option value="archived">Diarsipkan</option><option value="all">Semua</option></select></label>
                        <SecondaryButton class="min-h-11" type="submit">Cari</SecondaryButton>
                    </form>
                </div>
                <div v-if="!hasBusiness" class="p-10 text-center"><h3 class="font-semibold">Lengkapi profil usaha terlebih dahulu</h3><p class="mx-auto mt-2 max-w-md text-sm text-ink-secondary">Produk perlu terhubung dengan profil usaha Anda. Setelah profil tersedia, Anda dapat menambahkan produk di sini.</p></div>
                <div v-else-if="!products.data.length" class="p-10 text-center"><h3 class="font-semibold">{{ total ? 'Produk tidak ditemukan' : 'Mulai katalog usaha Anda' }}</h3><p class="mt-2 text-sm text-ink-secondary">{{ total ? 'Coba nama atau status produk yang lain.' : 'Tambahkan produk pertama beserta harga jualnya.' }}</p><SecondaryButton v-if="!total" class="mt-5 min-h-11" @click="openForm()">+ Tambah produk pertama</SecondaryButton></div>
                <div v-else class="divide-y divide-hairline px-5">
                    <article v-for="product in products.data" :key="product.id" class="py-5">
                        <div class="grid items-center gap-3 lg:grid-cols-12">
                            <div class="min-w-0 lg:col-span-5"><h3 class="break-words text-sm font-semibold">{{ product.name }}</h3><p class="mt-1 text-sm text-ink-muted">{{ product.variants.length }} varian · per {{ product.unit }}</p></div>
                            <p class="break-words text-sm font-medium tabular-nums lg:col-span-4">{{ price(product) }}</p>
                            <div class="lg:col-span-3 lg:text-right"><StatusBadge :tone="product.archived_at ? 'neutral' : 'success'" dot size="md">{{ product.archived_at ? 'Diarsipkan' : 'Aktif' }}</StatusBadge></div>
                        </div>
                        <details class="mt-2 text-sm">
                            <summary class="w-fit min-h-11 cursor-pointer rounded-lg py-3 text-primary-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500">Rincian <span class="sr-only">{{ product.name }}</span></summary>
                            <div class="flex flex-col gap-4 rounded-xl border border-hairline bg-canvas p-4">
                                <p class="whitespace-pre-wrap break-words leading-relaxed text-ink-secondary">{{ product.description || 'Belum ada deskripsi produk.' }}</p>
                                <ul class="flex flex-col gap-3">
                                    <li v-for="variant in product.variants" :key="variant.id" class="rounded-lg border border-hairline bg-white p-3">
                                        <div class="flex flex-wrap justify-between gap-2"><span class="break-words font-medium">{{ variant.variant_key === 'default' ? 'Varian standar' : variant.variant_key }}</span><span class="tabular-nums">{{ rupiah(variant.selling_price) }}</span></div>
                                        <p class="mt-2 break-words text-ink-secondary">Stok {{ variant.stock_quantity }} {{ product.unit }} · Batas stok rendah {{ variant.low_stock_threshold }}</p>
                                        <p v-if="variant.sku || variant.size || variant.color" class="mt-1 break-words text-ink-muted">{{ [variant.sku && `SKU: ${variant.sku}`, variant.size, variant.color].filter(Boolean).join(' · ') }}</p>
                                        <p v-if="variant.archived_at" class="mt-1 text-warning-dark">Varian diarsipkan</p>
                                    </li>
                                </ul>
                                <p v-if="product.archived_at" class="text-ink-secondary">Diarsipkan pada {{ new Date(product.archived_at).toLocaleString('id-ID', { timeZone: 'Asia/Jakarta' }) }} WIB. Produk tidak tersedia untuk transaksi baru.</p>
                                <div v-else class="flex flex-wrap gap-3"><SecondaryButton class="min-h-11" @click="openForm(product)">Edit produk <span class="sr-only">{{ product.name }}</span></SecondaryButton><button type="button" class="min-h-11 rounded-xl border border-danger/25 bg-white px-4 text-sm font-semibold text-danger-dark hover:bg-danger-light focus-visible:ring-2 focus-visible:ring-primary-500" @click="confirmArchive(product)">Arsipkan <span class="sr-only">{{ product.name }}</span></button></div>
                            </div>
                        </details>
                    </article>
                </div>
                <nav v-if="products.last_page > 1" aria-label="Halaman katalog" class="flex flex-wrap justify-between gap-3 border-t border-hairline p-5 text-sm"><Link v-if="products.prev_page_url" :href="products.prev_page_url" class="min-h-11 rounded-lg p-3 text-primary-700 focus-visible:ring-2 focus-visible:ring-primary-500">← Sebelumnya</Link><p class="py-3">Halaman {{ products.current_page }} dari {{ products.last_page }}</p><Link v-if="products.next_page_url" :href="products.next_page_url" class="min-h-11 rounded-lg p-3 text-primary-700 focus-visible:ring-2 focus-visible:ring-primary-500">Berikutnya →</Link></nav>
            </section>
            <p class="mt-4 text-sm text-ink-secondary">Perlu menambah stok? <Link :href="route('umkm.inventory.index')" class="inline-block min-h-11 rounded py-3 font-medium text-primary-700 underline focus-visible:ring-2 focus-visible:ring-primary-500">Buka Persediaan</Link></p>
        </UmkmLayout>
        <Modal :show="showForm" :closeable="!processing" aria-labelledby="product-form-title" aria-describedby="product-form-description" @close="closeForm" @closed="restoreFocus">
            <div class="p-5 sm:p-6"><ProductForm v-if="showForm" :product="selectedProduct" @cancel="closeForm" @saved="closeForm" @processing="processing = $event" /></div>
        </Modal>
        <Modal :show="!!archiveProduct" :closeable="!archiveForm.processing" max-width="md" aria-labelledby="archive-title" aria-describedby="archive-description" @close="archiveProduct = null" @closed="restoreFocus">
            <div class="p-6"><h2 id="archive-title" class="text-xl font-bold">Arsipkan produk?</h2><p id="archive-description" class="mt-3 break-words text-sm leading-relaxed text-ink-secondary">{{ archiveProduct?.name }} tidak lagi tersedia untuk transaksi baru atau restock. Stok dan riwayat lama tetap tersimpan.</p><p v-if="archiveForm.errors.product" role="alert" class="mt-3 text-sm text-danger-dark">{{ archiveForm.errors.product }}</p><div class="mt-6 flex flex-wrap justify-end gap-3"><SecondaryButton class="min-h-11" ref="archiveCancel" :disabled="archiveForm.processing" @click="archiveProduct = null">Batal</SecondaryButton><button type="button" class="min-h-11 rounded-xl bg-danger px-4 text-sm font-semibold text-white hover:bg-danger-dark focus-visible:ring-2 focus-visible:ring-primary-500 disabled:opacity-50" :disabled="archiveForm.processing" @click="archive">{{ archiveForm.processing ? 'Mengarsipkan…' : 'Ya, arsipkan' }}</button></div></div>
        </Modal>
    </div>
</template>
