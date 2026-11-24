<script setup>
import { computed, nextTick, ref, watch } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import UmkmLayout from '@/Layouts/UmkmLayout.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import StatusBadge from '@/Components/StatusBadge.vue';

const props = defineProps({ variants: Array, hasBusiness: Boolean });
const search = ref('');
const cart = ref([]);
const notes = ref('');

const filteredVariants = computed(() => {
    const query = search.value.trim().toLocaleLowerCase('id-ID');
    return props.variants.filter((variant) => [
        variant.product.name, variant.variant_key, variant.sku,
    ].some((value) => value?.toLocaleLowerCase('id-ID').includes(query)));
});

const cartTotal = computed(() =>
    cart.value.reduce((sum, item) => sum + (parseFloat(item.variant.selling_price) * item.quantity), 0)
);

const cartItemCount = computed(() => cart.value.reduce((sum, item) => sum + item.quantity, 0));

const formatCurrency = (value) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
const formatNumber = (value) => new Intl.NumberFormat('id-ID').format(value);
const variantLabel = (variant) => variant.variant_key === 'default'
    ? 'Varian standar' : variant.variant_key.replace(/[-_]/g, ' ').replace(/^./, (l) => l.toUpperCase());

function addToCart(variant) {
    const existing = cart.value.find((item) => item.variant.id === variant.id);
    if (existing) {
        if (existing.quantity < variant.stock_quantity) {
            existing.quantity++;
        }
    } else {
        cart.value.push({ variant, quantity: 1 });
    }
}

function updateQuantity(item, quantity) {
    const qty = parseInt(quantity, 10);
    if (isNaN(qty) || qty < 1) {
        item.quantity = 1;
    } else if (qty > item.variant.stock_quantity) {
        item.quantity = item.variant.stock_quantity;
    } else {
        item.quantity = qty;
    }
}

function removeFromCart(variantId) {
    cart.value = cart.value.filter((item) => item.variant.id !== variantId);
}

function clearCart() {
    cart.value = [];
    notes.value = '';
}

const isInCart = (variantId) => cart.value.some((item) => item.variant.id === variantId);
const cartQuantity = (variantId) => cart.value.find((item) => item.variant.id === variantId)?.quantity || 0;

const form = useForm({});

function submit() {
    if (cart.value.length === 0) return;
    form.transform(() => ({
        items: cart.value.map((item) => ({
            product_variant_id: item.variant.id,
            quantity: item.quantity,
        })),
        notes: notes.value || null,
    })).post(route('umkm.sales.store'), {
        preserveScroll: true,
        onSuccess: () => { clearCart(); },
    });
}

const guideSteps = [
    { title: '1. Pilih produk', description: 'Cari dan klik tombol Tambah pada produk yang akan dijual.' },
    { title: '2. Atur jumlah', description: 'Sesuaikan jumlah penjualan pada keranjang di sebelah kanan.' },
    { title: '3. Simpan transaksi', description: 'Klik Simpan Penjualan. Stok akan berkurang secara otomatis.' },
];
</script>

<template>
    <UmkmLayout title="Catat Penjualan" subtitle="Pilih produk dari katalog, tentukan jumlah, dan simpan transaksi penjualan." guide-title="Panduan kasir" guide-description="Tiga langkah untuk mencatat penjualan." :guide-steps="guideSteps">
        <Head title="Catat Penjualan" />
        <template #header-actions>
            <Link :href="route('umkm.sales.index')" class="inline-flex min-h-11 items-center rounded-xl border border-hairline bg-white px-4 text-sm font-semibold text-ink hover:bg-canvas focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2">Riwayat Penjualan</Link>
        </template>

        <div v-if="!hasBusiness" class="rounded-2xl border border-hairline bg-white p-8 text-center shadow-xs">
            <p class="text-sm text-ink-secondary">Lengkapi profil usaha untuk mulai mencatat penjualan.</p>
            <Link :href="route('business-profile.edit')" class="mt-3 inline-flex min-h-11 items-center rounded-lg font-semibold text-primary-700 underline focus-visible:ring-2 focus-visible:ring-primary-500">Lengkapi profil usaha</Link>
        </div>

        <div v-else class="grid items-start gap-6 xl:grid-cols-12">
            <!-- Katalog Produk -->
            <section aria-labelledby="catalog-heading" class="min-w-0 rounded-2xl border border-hairline bg-white p-5 shadow-xs xl:col-span-7">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 id="catalog-heading" class="text-base font-semibold text-ink">Pilih produk</h2>
                        <p class="mt-1 text-sm text-ink-secondary">Klik Tambah pada produk yang ingin dicatat penjualannya.</p>
                    </div>
                    <StatusBadge tone="primary" size="md">{{ formatNumber(variants.length) }} tersedia</StatusBadge>
                </div>

                <div class="mt-4">
                    <label for="product-search" class="sr-only">Cari produk</label>
                    <input id="product-search" v-model="search" type="search" placeholder="Cari nama produk, varian, atau SKU…" class="h-11 w-full rounded-full border-hairline bg-canvas px-4 text-sm placeholder:text-ink-muted focus:border-primary-500 focus:ring-primary-500" />
                </div>

                <p v-if="!variants.length" class="py-10 text-sm text-ink-muted">Belum ada produk dengan stok tersedia. Tambahkan produk dan restock terlebih dahulu.</p>
                <div v-else-if="!filteredVariants.length" role="status" class="py-8 text-sm text-ink-secondary">
                    <p>Tidak ada produk yang cocok.</p>
                    <button type="button" class="mt-3 min-h-11 rounded-xl px-3 font-semibold text-primary-700 hover:bg-primary-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500" @click="search = ''">Hapus pencarian</button>
                </div>
                <ul v-else class="mt-4 divide-y divide-hairline">
                    <li v-for="variant in filteredVariants" :key="variant.id" class="flex flex-wrap items-center justify-between gap-4 py-4">
                        <div class="min-w-0 flex-1 basis-40 break-words">
                            <h3 class="font-semibold text-ink">{{ variant.product.name }}</h3>
                            <p class="text-sm text-ink-secondary">{{ variantLabel(variant) }}</p>
                            <p class="mt-1 text-sm text-ink-muted">Stok: {{ formatNumber(variant.stock_quantity) }} {{ variant.product.unit }}</p>
                        </div>
                        <div class="flex flex-col items-end gap-1">
                            <p class="text-sm font-semibold tabular-nums text-ink">{{ formatCurrency(variant.selling_price) }}</p>
                            <p class="text-xs text-ink-muted">per {{ variant.product.unit }}</p>
                        </div>
                        <button v-if="!isInCart(variant.id)" type="button" class="min-h-11 rounded-xl bg-primary-600 px-4 text-sm font-semibold text-white shadow-xs hover:bg-primary-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2" :aria-label="`Tambah ${variant.product.name} ke keranjang`" @click="addToCart(variant)">+ Tambah</button>
                        <span v-else class="inline-flex min-h-11 items-center rounded-xl bg-primary-50 px-4 text-sm font-semibold text-primary-700">
                            ✓ {{ cartQuantity(variant.id) }} di keranjang
                        </span>
                    </li>
                </ul>
            </section>

            <!-- Keranjang / Ringkasan Penjualan -->
            <section aria-labelledby="cart-heading" class="min-w-0 rounded-2xl border border-hairline bg-white p-5 shadow-xs xl:col-span-5 xl:sticky xl:top-4">
                <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-xl border border-secondary-100 bg-secondary-50 text-secondary-700" aria-hidden="true">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" /></svg>
                </div>
                <h2 id="cart-heading" class="text-base font-semibold text-ink">Keranjang penjualan</h2>

                <div v-if="cart.length === 0" class="mt-3 text-sm leading-relaxed text-ink-secondary">
                    <p>Belum ada produk di keranjang. Pilih <strong>Tambah</strong> pada katalog produk untuk mulai mencatat penjualan.</p>
                </div>

                <form v-else class="mt-4 flex flex-col gap-4" @submit.prevent="submit">
                    <ul class="divide-y divide-hairline">
                        <li v-for="item in cart" :key="item.variant.id" class="py-3">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0 flex-1 break-words">
                                    <p class="text-sm font-semibold">{{ item.variant.product.name }}</p>
                                    <p class="text-xs text-ink-secondary">{{ variantLabel(item.variant) }} · {{ formatCurrency(item.variant.selling_price) }}/{{ item.variant.product.unit }}</p>
                                </div>
                                <button type="button" class="shrink-0 rounded-lg p-2 text-danger-dark hover:bg-danger-light focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500" :aria-label="`Hapus ${item.variant.product.name} dari keranjang`" @click="removeFromCart(item.variant.id)">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                                </button>
                            </div>
                            <div class="mt-2 flex items-center gap-3">
                                <label :for="`qty-${item.variant.id}`" class="sr-only">Jumlah {{ item.variant.product.name }}</label>
                                <input :id="`qty-${item.variant.id}`" type="number" :value="item.quantity" min="1" :max="item.variant.stock_quantity" step="1" class="h-9 w-20 rounded-lg border-hairline text-center text-sm tabular-nums focus:border-primary-500 focus:ring-primary-500" @change="updateQuantity(item, $event.target.value)" />
                                <span class="text-xs text-ink-muted">maks. {{ formatNumber(item.variant.stock_quantity) }}</span>
                                <span class="ml-auto text-sm font-semibold tabular-nums">{{ formatCurrency(parseFloat(item.variant.selling_price) * item.quantity) }}</span>
                            </div>
                        </li>
                    </ul>

                    <div class="rounded-xl bg-canvas p-4">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-ink-secondary">Total item</span>
                            <span class="font-semibold tabular-nums">{{ cartItemCount }} {{ cart.length > 0 ? cart[0].variant.product.unit : '' }}</span>
                        </div>
                        <div class="mt-2 flex items-center justify-between border-t border-hairline pt-2">
                            <span class="text-base font-semibold text-ink">Total</span>
                            <span class="text-lg font-bold tabular-nums text-primary-700">{{ formatCurrency(cartTotal) }}</span>
                        </div>
                    </div>

                    <div>
                        <label for="sale-notes" class="text-sm font-medium text-ink">Catatan (opsional)</label>
                        <textarea id="sale-notes" v-model="notes" rows="2" maxlength="2000" class="mt-1 w-full rounded-lg border-hairline text-sm focus:border-primary-500 focus:ring-primary-500" placeholder="Contoh: penjualan harian siang"></textarea>
                    </div>

                    <InputError :message="form.errors.sale" role="alert" />
                    <InputError :message="form.errors.items" role="alert" />

                    <div class="flex flex-wrap gap-3">
                        <PrimaryButton :disabled="form.processing || cart.length === 0" class="min-h-11 flex-1 justify-center bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 active:bg-primary-700">
                            {{ form.processing ? 'Menyimpan…' : 'Simpan Penjualan' }}
                        </PrimaryButton>
                        <SecondaryButton :disabled="form.processing" class="min-h-11" @click="clearCart">Batal</SecondaryButton>
                    </div>
                </form>
            </section>
        </div>
    </UmkmLayout>
</template>
