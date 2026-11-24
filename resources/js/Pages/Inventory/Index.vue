<script setup>
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import UmkmLayout from '@/Layouts/UmkmLayout.vue';
import StatCard from '@/Components/StatCard.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import StockBadge from '@/Components/StockBadge.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({ variants: Object, movements: Object, status: String });
const selected = ref(null);
const quantityInput = ref(null);
const searchInput = ref(null);
const mobileSearchInput = ref(null);
const search = ref('');
const feedback = ref(props.status);
const filteredVariants = computed(() => {
    const query = search.value.trim().toLocaleLowerCase('id-ID');
    return props.variants.data.filter((variant) => [
        variant.product.name, variant.variant_key, variant.sku, variant.size, variant.color,
    ].some((value) => value?.toLocaleLowerCase('id-ID').includes(query)));
});
watch(() => props.variants.current_page, () => { search.value = ''; });
const formatNumber = (value) => new Intl.NumberFormat('id-ID').format(value);
const form = useForm({ quantity: '', notes: '' });
const movementLabels = {
    opening: 'Stok awal', restock: 'Restock', sale: 'Penjualan',
    sale_reversal: 'Pembatalan penjualan', adjustment: 'Penyesuaian',
};
const variantLabel = (variant) => variant.variant_key === 'default'
    ? 'Varian standar' : variant.variant_key.replace(/[-_]/g, ' ').replace(/^./, (letter) => letter.toUpperCase());
const formatDate = (value) => new Intl.DateTimeFormat('id-ID', {
    dateStyle: 'medium', timeStyle: 'short', timeZone: 'Asia/Jakarta',
}).format(new Date(value));

async function selectVariant(variant) {
    selected.value = variant;
    form.reset();
    form.clearErrors();
    await nextTick();
    quantityInput.value?.focus();
}

function cancel() {
    const id = selected.value?.id;
    selected.value = null;
    form.reset();
    form.clearErrors();
    nextTick(() => {
        const trigger = document.getElementById(`restock-${id}`);
        (trigger || (searchInput.value?.offsetParent ? searchInput.value : mobileSearchInput.value))?.focus();
    });
}

function submit() {
    form.post(route('umkm.inventory.restocks.store', {
        variant: selected.value.id,
        stock_page: props.variants.current_page,
        history_page: 1,
    }), {
        preserveScroll: true,
        onSuccess: () => {
            feedback.value = props.status;
            cancel();
        },
    });
}

function focusSearch(event) {
    if (event.key === '/' && !event.ctrlKey && !event.metaKey
        && !event.target?.matches('input, textarea, select, [contenteditable="true"]')) {
        event.preventDefault();
        (searchInput.value?.offsetParent ? searchInput.value : mobileSearchInput.value)?.focus();
    }
}
onMounted(() => document.addEventListener('keydown', focusSearch));
onUnmounted(() => document.removeEventListener('keydown', focusSearch));
</script>

<template>
    <UmkmLayout title="Persediaan & Restock" subtitle="Catat barang masuk, perbarui stok, dan telusuri setiap perubahan persediaan usaha Anda." :inventory-count="variants.total">
        <Head title="Persediaan & Restock" />
        <template #search>
            <div class="relative">
                <label for="inventory-search" class="sr-only">Cari produk di halaman ini</label>
                <input id="inventory-search" ref="searchInput" v-model="search" type="search" placeholder="Cari produk di halaman ini…" class="h-11 w-64 rounded-full border-hairline bg-canvas pl-4 pr-10 text-sm placeholder:text-ink-muted focus:border-primary-500 focus:bg-white focus:ring-primary-500" />
                <kbd class="pointer-events-none absolute right-4 top-3 font-mono text-sm text-ink-muted" aria-hidden="true">/</kbd>
            </div>
        </template>

        <div class="flex flex-col gap-6">
            <div v-if="feedback" role="status" class="flex items-center justify-between gap-3 rounded-2xl border border-primary-200 bg-primary-50 p-4 text-sm text-primary-900">
                <p>{{ feedback }}</p>
                <button type="button" class="min-h-11 shrink-0 rounded-xl px-3 font-semibold text-primary-700 hover:bg-primary-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500" aria-label="Tutup pesan berhasil" @click="feedback = ''">Tutup</button>
            </div>
            <section aria-label="Ringkasan persediaan" class="grid gap-4 sm:grid-cols-2">
                <StatCard label="Varian produk aktif" :value="formatNumber(variants.total)" :badge="variants.total ? 'Siap direstock' : 'Belum ada'" delta="Seluruh varian aktif milik usaha Anda." />
                <StatCard label="Perubahan stok tercatat" :value="formatNumber(movements.total)" :badge="movements.total ? 'Tersimpan' : 'Belum ada'" tone="secondary" delta="Termasuk stok awal, restock, dan perubahan lainnya." />
            </section>
            <div class="grid items-start gap-6 xl:grid-cols-12">

                <section aria-labelledby="stock-heading" class="min-w-0 rounded-2xl border border-hairline bg-white p-5 shadow-xs xl:col-span-8">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div><h2 id="stock-heading" class="text-base font-semibold text-ink">Stok produk aktif</h2>
                        <p class="mt-1 text-sm text-ink-secondary">Pilih varian produk yang menerima tambahan stok.</p></div>
                        <StatusBadge tone="primary" size="md">{{ formatNumber(variants.total) }} varian</StatusBadge>
                    </div>
                    <div class="mt-4 sm:hidden">
                        <label for="inventory-mobile-search" class="sr-only">Cari produk di halaman ini</label>
                        <input id="inventory-mobile-search" ref="mobileSearchInput" v-model="search" type="search" placeholder="Cari produk di halaman ini…" class="h-11 w-full rounded-full border-hairline bg-canvas px-4 text-sm placeholder:text-ink-muted focus:border-primary-500 focus:ring-primary-500" />
                    </div>
                    <p v-if="!variants.data.length" class="py-10 text-sm leading-relaxed text-ink-muted">Belum ada produk aktif. Produk dan variannya perlu tersedia sebelum mencatat restock.</p>
                    <div v-else-if="!filteredVariants.length" role="status" class="py-8 text-sm text-ink-secondary">
                        <p>Tidak ada produk yang cocok di halaman ini.</p>
                        <button type="button" class="mt-3 min-h-11 rounded-xl px-3 font-semibold text-primary-700 hover:bg-primary-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500" @click="search = ''">Hapus pencarian</button>
                    </div>
                    <ul v-else class="mt-5 divide-y divide-hairline">
                        <li v-for="variant in filteredVariants" :key="variant.id" class="flex flex-wrap items-center justify-between gap-4 py-5">
                            <div class="min-w-0 flex-1 basis-40 break-words">
                                <h3 class="font-semibold text-ink">{{ variant.product.name }}</h3>
                                <p class="text-sm text-ink-secondary">{{ variantLabel(variant) }}</p>
                                <details class="mt-1 text-sm text-ink-secondary">
                                    <summary class="w-fit cursor-pointer rounded py-1 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500">Rincian varian</summary>
                                    <p class="mt-1">SKU: {{ variant.sku || 'Belum diisi' }}</p>
                                    <p>Kode varian: {{ variant.variant_key }}</p>
                                    <p v-if="variant.size || variant.color">{{ [variant.size, variant.color].filter(Boolean).join(' · ') }}</p>
                                </details>
                            </div>
                            <div class="flex flex-col items-start gap-2"><p class="text-sm font-semibold tabular-nums text-ink">{{ formatNumber(variant.stock_quantity) }} <span class="font-normal text-ink-muted">{{ variant.product.unit }}</span></p><StockBadge :status="variant.stock_status" /><p class="text-xs text-ink-muted">Batas: {{ formatNumber(variant.low_stock_threshold) }} {{ variant.product.unit }}</p></div>
                            <button :id="`restock-${variant.id}`" type="button" class="min-h-11 rounded-xl bg-primary-600 px-4 text-sm font-semibold text-white shadow-xs hover:bg-primary-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 disabled:opacity-50" :aria-label="`Tambah stok ${variant.product.name}, ${variantLabel(variant)}`" :disabled="form.processing" @click="selectVariant(variant)">+ Tambah stok</button>
                        </li>
                    </ul>
                    <nav v-if="variants.last_page > 1" aria-label="Halaman stok produk" class="mt-4 flex items-center justify-between gap-3 text-sm">
                        <Link v-if="variants.prev_page_url" :href="variants.prev_page_url" class="rounded-lg p-3 text-primary-700 focus-visible:ring-2 focus-visible:ring-primary-500">Sebelumnya</Link>
                        <span>{{ variants.current_page }} / {{ variants.last_page }}</span>
                        <Link v-if="variants.next_page_url" :href="variants.next_page_url" class="rounded-lg p-3 text-primary-700 focus-visible:ring-2 focus-visible:ring-primary-500">Berikutnya</Link>
                    </nav>
                </section>

                <section aria-labelledby="restock-heading" class="min-w-0 rounded-2xl border border-hairline bg-white p-5 shadow-xs xl:col-span-4">
                    <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-xl border border-secondary-100 bg-secondary-50 text-secondary-700" aria-hidden="true"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="m3 7 9-4 9 4-9 4-9-4Zm0 0v10l9 4 9-4V7M12 11v10m3-7h4m-2-2v4" /></svg></div>
                    <h2 id="restock-heading" class="text-base font-semibold text-ink">Catat restock</h2>
                    <form v-if="selected" class="mt-4 flex flex-col gap-5" @submit.prevent="submit">
                        <div class="break-words rounded-xl bg-secondary-50 p-4 text-sm text-secondary-700">
                            <p class="font-semibold">{{ selected.product.name }}</p>
                            <p>{{ variantLabel(selected) }}</p>
                            <p class="mt-2">Stok saat dipilih: {{ selected.stock_quantity }} {{ selected.product.unit }}</p>
                        </div>
                        <div>
                            <InputLabel for="quantity" value="Jumlah stok masuk" />
                            <TextInput id="quantity" ref="quantityInput" v-model="form.quantity" type="number" min="1" max="2147483647" step="1" required class="mt-1 w-full" :disabled="form.processing" :aria-invalid="Boolean(form.errors.quantity)" aria-describedby="quantity-error" />
                            <InputError id="quantity-error" :message="form.errors.quantity" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel for="notes" value="Catatan (opsional)" />
                            <textarea id="notes" v-model="form.notes" rows="3" maxlength="2000" class="mt-1 w-full rounded-lg border-hairline text-sm focus:border-primary-500 focus:ring-primary-500" placeholder="Contoh: barang masuk dari produksi hari ini" :disabled="form.processing" :aria-invalid="Boolean(form.errors.notes)" aria-describedby="notes-error"></textarea>
                            <InputError id="notes-error" :message="form.errors.notes" class="mt-2" />
                        </div>
                        <InputError :message="form.errors.restock" role="alert" />
                        <div class="flex flex-wrap gap-3">
                            <PrimaryButton :disabled="form.processing" class="min-h-11 bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 active:bg-primary-700">{{ form.processing ? 'Menyimpan…' : 'Simpan restock' }}</PrimaryButton>
                            <SecondaryButton :disabled="form.processing" class="min-h-11" @click="cancel">Batal</SecondaryButton>
                        </div>
                    </form>
                    <div v-else class="mt-3 text-sm leading-relaxed text-ink-secondary">
                        <p>Pilih <strong>Tambah stok</strong> pada varian yang menerima barang untuk mulai mencatat.</p>
                        <div class="mt-5 rounded-xl border border-secondary-100 bg-secondary-50 p-4 text-secondary-700">
                            <p class="font-semibold">Setiap barang masuk bisa ditelusuri</p>
                            <p class="mt-1">Jumlah dan catatan restock akan tersimpan di riwayat usaha Anda.</p>
                        </div>
                    </div>
                </section>

                <section aria-labelledby="history-heading" class="min-w-0 rounded-2xl border border-hairline bg-white p-5 shadow-xs xl:col-span-12">
                    <h2 id="history-heading" class="text-base font-semibold text-ink">Riwayat perubahan stok</h2>
                    <p class="mt-1 text-sm text-ink-secondary">Perubahan terbaru ditampilkan lebih dahulu. Waktu ditampilkan dalam WIB.</p>
                    <p v-if="!movements.data.length" class="py-8 text-sm text-ink-muted">Belum ada perubahan stok yang tercatat.</p>
                    <ul v-else class="mt-4 divide-y divide-hairline">
                        <li v-for="movement in movements.data" :key="movement.id" class="flex flex-wrap items-start justify-between gap-3 py-4">
                            <div class="min-w-0 flex-1 basis-48 break-words">
                                <p class="font-semibold text-ink">{{ movement.product_variant.product.name }}</p>
                                <p class="text-sm text-ink-secondary">{{ variantLabel(movement.product_variant) }}</p>
                                <details class="mt-2 text-sm text-ink-secondary">
                                    <summary class="w-fit cursor-pointer rounded py-1 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500">Rincian pencatatan</summary>
                                    <p class="mt-2">Dicatat oleh: {{ movement.performer?.name || 'Pengguna tidak tersedia' }}</p>
                                    <p class="whitespace-pre-wrap">{{ movement.notes || 'Tanpa catatan.' }}</p>
                                </details>
                            </div>
                            <StatusBadge :tone="movement.type === 'restock' ? 'primary' : 'neutral'" size="md">{{ movementLabels[movement.type] || movement.type }}</StatusBadge>
                            <p class="font-semibold tabular-nums" :class="movement.quantity_delta > 0 ? 'text-success-dark' : 'text-danger-dark'">{{ movement.quantity_delta > 0 ? '+' : '' }}{{ formatNumber(movement.quantity_delta) }} {{ movement.product_variant.product.unit }}</p>
                            <time :datetime="movement.created_at" class="text-sm text-ink-muted">{{ formatDate(movement.created_at) }}</time>
                        </li>
                    </ul>
                    <nav v-if="movements.last_page > 1" aria-label="Halaman riwayat stok" class="mt-4 flex items-center justify-between gap-3 text-sm">
                        <Link v-if="movements.prev_page_url" :href="movements.prev_page_url" class="rounded-lg p-3 text-primary-700 focus-visible:ring-2 focus-visible:ring-primary-500">Sebelumnya</Link>
                        <span>{{ movements.current_page }} / {{ movements.last_page }}</span>
                        <Link v-if="movements.next_page_url" :href="movements.next_page_url" class="rounded-lg p-3 text-primary-700 focus-visible:ring-2 focus-visible:ring-primary-500">Berikutnya</Link>
                    </nav>
                </section>
            </div>
        </div>
    </UmkmLayout>
</template>
