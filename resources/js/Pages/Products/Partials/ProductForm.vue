<script setup>
import { computed, nextTick, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({ product: Object });
const emit = defineEmits(['saved', 'cancel', 'processing']);
const variantDefaults = () => ({ id: null, variant_key: 'default', selling_price: '', sku: '', size: '', color: '', low_stock_threshold: 0 });
const form = useForm({
    name: props.product?.name ?? '', description: props.product?.description ?? '', unit: props.product?.unit ?? 'pcs',
    variants: props.product ? props.product.variants.map((variant) => ({
        ...variantDefaults(), ...Object.fromEntries(['id', 'variant_key', 'selling_price', 'sku', 'size', 'color', 'low_stock_threshold'].map((key) => [key, variant[key] ?? ''])),
    })) : [variantDefaults()],
});
const errorSummary = ref(null);
const errors = computed(() => Object.entries(form.errors));
const optionalFields = [{ key: 'sku', label: 'SKU', max: 100 }, { key: 'size', label: 'Ukuran', max: 50 }, { key: 'color', label: 'Warna', max: 50 }];
const idFor = (key) => `product-${key.replaceAll('.', '-')}`;
function focusError(key) {
    const target = document.getElementById(idFor(key));
    target?.closest('details')?.setAttribute('open', '');
    (target ?? errorSummary.value)?.focus();
}
function addVariant() {
    form.variants.push({ ...variantDefaults(), variant_key: '' });
    nextTick(() => document.getElementById(idFor(`variants.${form.variants.length - 1}.variant_key`))?.focus());
}
function submit() {
    const options = {
        preserveScroll: true,
        onStart: () => emit('processing', true),
        onFinish: () => emit('processing', false),
        onSuccess: () => { emit('processing', false); emit('saved'); },
        onError: async () => { await nextTick(); errorSummary.value?.focus(); },
    };
    props.product ? form.put(route('umkm.products.update', props.product.id), options) : form.post(route('umkm.products.store'), options);
}
</script>

<template>
    <form class="flex flex-col gap-5" @submit.prevent="submit" @invalid.capture="$event.target.closest('details')?.setAttribute('open', '')">
        <div>
            <h2 id="product-form-title" class="text-xl font-bold">{{ product ? 'Edit produk' : 'Tambah produk' }}</h2>
            <p id="product-form-description" class="mt-2 text-sm text-ink-secondary">Lengkapi nama, satuan, dan harga. Stok baru dimulai dari nol; catat barang masuk melalui Persediaan.</p>
        </div>
        <div v-if="errors.length" ref="errorSummary" tabindex="-1" role="alert" class="rounded-xl border border-danger/30 bg-danger-light p-4 text-sm text-danger-dark focus:outline-none focus:ring-2 focus:ring-danger">
            <p class="font-semibold">Periksa isian sebelum menyimpan</p>
            <ul class="mt-2 list-inside list-disc">
                <li v-for="[key, message] in errors" :key="key"><button type="button" class="min-h-11 text-left underline" @click="focusError(key)">{{ message }}</button></li>
            </ul>
        </div>
        <div class="grid gap-4 sm:grid-cols-3">
            <div class="sm:col-span-2">
                <InputLabel for="product-name" value="Nama produk (wajib)" />
                <TextInput id="product-name" v-model="form.name" autofocus required maxlength="150" class="mt-2 block w-full" :aria-invalid="!!form.errors.name" aria-describedby="product-name-error" :disabled="form.processing" />
                <InputError id="product-name-error" :message="form.errors.name" class="mt-1" />
            </div>
            <div>
                <InputLabel for="product-unit" value="Satuan (wajib)" />
                <TextInput id="product-unit" v-model="form.unit" required maxlength="50" placeholder="Contoh: bungkus" class="mt-2 block w-full" :aria-invalid="!!form.errors.unit" aria-describedby="product-unit-error" :disabled="form.processing" />
                <InputError id="product-unit-error" :message="form.errors.unit" class="mt-1" />
            </div>
        </div>
        <div>
            <InputLabel for="product-description" value="Deskripsi produk" />
            <textarea id="product-description" v-model="form.description" rows="3" maxlength="10000" class="mt-2 block w-full rounded-lg border-hairline text-sm focus:border-primary-500 focus:ring-primary-500" :aria-invalid="!!form.errors.description" aria-describedby="product-description-error" :disabled="form.processing" />
            <InputError id="product-description-error" :message="form.errors.description" class="mt-1" />
        </div>
        <fieldset id="product-variants" tabindex="-1" class="flex min-w-0 flex-col gap-4 rounded-xl border border-hairline p-4">
            <legend class="px-1 text-sm font-semibold">Varian dan harga</legend>
            <InputError :message="form.errors.variants" />
            <p class="text-sm text-ink-secondary">Tanpa ukuran atau warna? Gunakan varian standar. Varian tersimpan dipertahankan agar riwayat tetap utuh.</p>
            <section v-for="(variant, index) in form.variants" :key="variant.id ?? `new-${index}`" class="flex min-w-0 flex-col gap-3 rounded-xl border border-hairline bg-canvas p-4" :aria-label="`Varian ${index + 1}`">
                <div class="flex items-center justify-between gap-2">
                    <h3 class="text-sm font-semibold">Varian {{ index + 1 }}</h3>
                    <button v-if="!variant.id && form.variants.length > 1" type="button" class="min-h-11 rounded-lg px-3 text-sm text-danger-dark focus-visible:ring-2 focus-visible:ring-primary-500" :disabled="form.processing" :aria-label="`Hapus varian baru ${index + 1}`" @click="form.variants.splice(index, 1); form.clearErrors()">Hapus</button>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <InputLabel :for="idFor(`variants.${index}.variant_key`)" value="Nama varian (wajib)" />
                        <TextInput :id="idFor(`variants.${index}.variant_key`)" v-model="variant.variant_key" required maxlength="100" class="mt-2 block w-full" :readonly="!!variant.id" :disabled="form.processing" :aria-invalid="!!form.errors[`variants.${index}.variant_key`]" :aria-describedby="`${idFor(`variants.${index}.variant_key`)}-error`" />
                        <InputError :id="`${idFor(`variants.${index}.variant_key`)}-error`" :message="form.errors[`variants.${index}.variant_key`]" />
                    </div>
                    <div>
                        <InputLabel :for="idFor(`variants.${index}.selling_price`)" value="Harga jual / satuan (Rp, wajib)" />
                        <TextInput :id="idFor(`variants.${index}.selling_price`)" v-model="variant.selling_price" type="number" inputmode="decimal" min="0" max="9999999999999.99" step="0.01" required class="mt-2 block w-full" :disabled="form.processing" :aria-invalid="!!form.errors[`variants.${index}.selling_price`]" :aria-describedby="`${idFor(`variants.${index}.selling_price`)}-error`" />
                        <InputError :id="`${idFor(`variants.${index}.selling_price`)}-error`" :message="form.errors[`variants.${index}.selling_price`]" />
                    </div>
                </div>
                <details class="text-sm">
                    <summary class="min-h-11 cursor-pointer py-3 text-primary-700 focus-visible:ring-2 focus-visible:ring-primary-500">Rincian varian (opsional)</summary>
                    <div class="grid gap-4 pt-2 sm:grid-cols-2">
                        <div v-for="field in optionalFields" :key="field.key">
                            <InputLabel :for="idFor(`variants.${index}.${field.key}`)" :value="field.label" />
                            <TextInput :id="idFor(`variants.${index}.${field.key}`)" v-model="variant[field.key]" :maxlength="field.max" class="mt-2 block w-full" :disabled="form.processing" :aria-invalid="!!form.errors[`variants.${index}.${field.key}`]" :aria-describedby="`${idFor(`variants.${index}.${field.key}`)}-error`" />
                            <InputError :id="`${idFor(`variants.${index}.${field.key}`)}-error`" :message="form.errors[`variants.${index}.${field.key}`]" />
                        </div>
                        <div>
                            <InputLabel :for="idFor(`variants.${index}.low_stock_threshold`)" value="Batas stok rendah" />
                            <TextInput :id="idFor(`variants.${index}.low_stock_threshold`)" v-model="variant.low_stock_threshold" type="number" min="0" max="2147483647" step="1" required class="mt-2 block w-full" :disabled="form.processing" :aria-invalid="!!form.errors[`variants.${index}.low_stock_threshold`]" :aria-describedby="`${idFor(`variants.${index}.low_stock_threshold`)}-error`" />
                            <InputError :id="`${idFor(`variants.${index}.low_stock_threshold`)}-error`" :message="form.errors[`variants.${index}.low_stock_threshold`]" />
                        </div>
                    </div>
                </details>
            </section>
            <SecondaryButton v-if="form.variants.length < 50" class="min-h-11" :disabled="form.processing" @click="addVariant">+ Tambah varian</SecondaryButton>
        </fieldset>
        <div class="flex flex-wrap justify-end gap-3 border-t border-hairline pt-4">
            <SecondaryButton class="min-h-11" :disabled="form.processing" @click="emit('cancel')">Batal</SecondaryButton>
            <PrimaryButton class="min-h-11 rounded-xl bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 active:bg-primary-700 disabled:opacity-50" :disabled="form.processing">{{ form.processing ? 'Menyimpan…' : 'Simpan produk' }}</PrimaryButton>
        </div>
    </form>
</template>
