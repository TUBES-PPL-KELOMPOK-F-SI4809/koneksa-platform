<script setup>
import { nextTick, ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({ variants: Object, movements: Object, status: String });
const selected = ref(null);
const quantityInput = ref(null);
const form = useForm({ quantity: '', notes: '' });
const movementLabels = {
    opening: 'Stok awal', restock: 'Restock', sale: 'Penjualan',
    sale_reversal: 'Pembatalan penjualan', adjustment: 'Penyesuaian',
};
const variantLabel = (variant) => variant.variant_key === 'default'
    ? 'Varian standar' : variant.variant_key;
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
    nextTick(() => document.getElementById(`restock-${id}`)?.focus());
}

function submit() {
    form.post(route('umkm.inventory.restocks.store', {
        variant: selected.value.id,
        stock_page: props.variants.current_page,
        history_page: 1,
    }), {
        preserveScroll: true,
        onSuccess: () => cancel(),
    });
}
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Persediaan & Restock" />
        <template #header>
            <h1 class="text-xl font-semibold text-ink">Persediaan & Restock</h1>
            <p class="mt-1 text-sm text-ink-secondary">Catat barang masuk dan telusuri perubahan stok usaha Anda.</p>
        </template>

        <div class="bg-canvas px-4 py-8 sm:px-6">
            <div class="mx-auto grid max-w-7xl gap-6 lg:grid-cols-3">
                <p v-if="status" role="status" class="rounded-2xl border border-primary-200 bg-primary-50 p-4 text-sm text-primary-900 lg:col-span-3">{{ status }}</p>

                <section aria-labelledby="stock-heading" class="min-w-0 rounded-2xl border border-hairline bg-white p-5 shadow-xs lg:col-span-2">
                    <h2 id="stock-heading" class="text-lg font-semibold text-ink">Stok produk aktif</h2>
                    <p class="mt-1 text-sm text-ink-secondary">Pilih varian produk yang menerima tambahan stok.</p>
                    <p v-if="!variants.data.length" class="py-10 text-sm text-ink-muted">Belum ada produk aktif. Produk dan variannya perlu tersedia sebelum mencatat restock.</p>
                    <ul v-else class="mt-5 divide-y divide-hairline">
                        <li v-for="variant in variants.data" :key="variant.id" class="flex flex-wrap items-center justify-between gap-4 py-4">
                            <div class="min-w-0 flex-1 basis-40 break-words">
                                <h3 class="font-semibold text-ink">{{ variant.product.name }}</h3>
                                <p class="text-sm text-ink-secondary">{{ variantLabel(variant) }}</p>
                                <details class="mt-1 text-sm text-ink-muted">
                                    <summary class="w-fit cursor-pointer rounded focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500">Rincian varian</summary>
                                    <p class="mt-1">SKU: {{ variant.sku || 'Belum diisi' }}</p>
                                    <p v-if="variant.size || variant.color">{{ [variant.size, variant.color].filter(Boolean).join(' · ') }}</p>
                                </details>
                            </div>
                            <p class="text-sm font-semibold tabular-nums text-ink">{{ variant.stock_quantity }} {{ variant.product.unit }}</p>
                            <button :id="`restock-${variant.id}`" type="button" class="min-h-11 rounded-xl bg-primary-50 px-4 text-sm font-semibold text-primary-700 hover:bg-primary-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 disabled:opacity-50" :aria-label="`Tambah stok ${variant.product.name}, ${variantLabel(variant)}`" :disabled="form.processing" @click="selectVariant(variant)">Tambah stok</button>
                        </li>
                    </ul>
                    <nav v-if="variants.last_page > 1" aria-label="Halaman stok produk" class="mt-4 flex items-center justify-between gap-3 text-sm">
                        <Link v-if="variants.prev_page_url" :href="variants.prev_page_url" class="rounded-lg p-3 text-primary-700 focus-visible:ring-2 focus-visible:ring-primary-500">Sebelumnya</Link>
                        <span>{{ variants.current_page }} / {{ variants.last_page }}</span>
                        <Link v-if="variants.next_page_url" :href="variants.next_page_url" class="rounded-lg p-3 text-primary-700 focus-visible:ring-2 focus-visible:ring-primary-500">Berikutnya</Link>
                    </nav>
                </section>

                <section aria-labelledby="restock-heading" class="min-w-0 self-start rounded-2xl border border-hairline bg-white p-5 shadow-xs">
                    <h2 id="restock-heading" class="text-lg font-semibold text-ink">Catat restock</h2>
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
                            <PrimaryButton :disabled="form.processing" class="min-h-11">{{ form.processing ? 'Menyimpan…' : 'Simpan restock' }}</PrimaryButton>
                            <SecondaryButton :disabled="form.processing" class="min-h-11" @click="cancel">Batal</SecondaryButton>
                        </div>
                    </form>
                    <div v-else class="mt-4 text-sm leading-relaxed text-ink-secondary">
                        <p>Pilih <strong>Tambah stok</strong> pada produk, isi jumlah barang masuk, lalu simpan.</p>
                        <p class="mt-3">Stok dan riwayat akan diperbarui bersama setelah penyimpanan berhasil.</p>
                    </div>
                </section>

                <section aria-labelledby="history-heading" class="min-w-0 rounded-2xl border border-hairline bg-white p-5 shadow-xs lg:col-span-3">
                    <h2 id="history-heading" class="text-lg font-semibold text-ink">Riwayat perubahan stok</h2>
                    <p class="mt-1 text-sm text-ink-secondary">Perubahan terbaru ditampilkan lebih dahulu. Waktu ditampilkan dalam WIB.</p>
                    <p v-if="!movements.data.length" class="py-8 text-sm text-ink-muted">Belum ada perubahan stok yang tercatat.</p>
                    <ul v-else class="mt-4 divide-y divide-hairline">
                        <li v-for="movement in movements.data" :key="movement.id" class="flex flex-wrap items-start justify-between gap-3 py-4">
                            <div class="min-w-0 flex-1 basis-48 break-words">
                                <p class="font-semibold text-ink">{{ movement.product_variant.product.name }}</p>
                                <p class="text-sm text-ink-secondary">{{ variantLabel(movement.product_variant) }}</p>
                                <details class="mt-2 text-sm text-ink-secondary">
                                    <summary class="w-fit cursor-pointer rounded focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500">Rincian pencatatan</summary>
                                    <p class="mt-2">Dicatat oleh: {{ movement.performer?.name || 'Pengguna tidak tersedia' }}</p>
                                    <p class="whitespace-pre-wrap">{{ movement.notes || 'Tanpa catatan.' }}</p>
                                </details>
                            </div>
                            <span class="rounded-full bg-neutral-100 px-3 py-1 text-xs font-medium text-ink-secondary">{{ movementLabels[movement.type] || movement.type }}</span>
                            <p class="font-semibold tabular-nums" :class="movement.quantity_delta > 0 ? 'text-success-dark' : 'text-danger-dark'">{{ movement.quantity_delta > 0 ? '+' : '' }}{{ movement.quantity_delta }} {{ movement.product_variant.product.unit }}</p>
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
    </AuthenticatedLayout>
</template>
