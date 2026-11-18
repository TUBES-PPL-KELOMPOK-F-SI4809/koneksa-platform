<script setup>
import { computed, nextTick, ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import UmkmLayout from '@/Layouts/UmkmLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({ business: Object, categories: Array, identityLocked: Boolean });
const valuesFromBusiness = () => ({
    name: props.business?.name ?? '',
    business_category_id: props.business?.business_category_id ?? '',
    description: props.business?.description ?? '',
    address: props.business?.address ?? '',
    district: props.business?.district ?? '',
    subdistrict: props.business?.subdistrict ?? '',
    contact_phone: props.business?.contact_phone ?? '',
});
const form = useForm(valuesFromBusiness());
const errorSummary = ref(null);
const feedback = ref('');
const categoryName = computed(() => props.categories.find((category) =>
    category.id === Number(form.business_category_id))?.name ?? 'Kategori belum dipilih');
const businessInitials = computed(() => (form.name.trim() || 'Usaha Anda').split(/\s+/)
    .slice(0, 2).map((part) => part[0]).join('').toUpperCase());
const locationLabel = computed(() => [form.subdistrict, form.district].filter(Boolean).join(', '));
const guideSteps = [
    { title: '1. Isi identitas usaha', description: 'Masukkan nama usaha dan pilih kategori yang paling sesuai.' },
    { title: '2. Lengkapi lokasi dan kontak', description: 'Tambahkan deskripsi, alamat, dan telepon usaha bila tersedia.' },
    { title: '3. Periksa lalu simpan', description: 'Cek ringkasan isian, lalu simpan profil milik akun Anda.' },
];

function focusField(field) {
    const input = document.getElementById(`business-${field}`);
    (input?.disabled ? document.getElementById('identity-lock-help') : input)?.focus();
}

function resetChanges() {
    form.defaults(valuesFromBusiness());
    form.reset();
    form.clearErrors();
    feedback.value = '';
    focusField('name');
}

function submit() {
    feedback.value = '';
    form.put(route('business-profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            form.defaults(valuesFromBusiness());
            form.reset();
            feedback.value = 'Profil usaha berhasil disimpan.';
        },
        onError: async () => {
            await nextTick();
            errorSummary.value?.focus();
        },
    });
}
</script>

<template>
    <UmkmLayout
        title="Profil Usaha"
        subtitle="Lengkapi identitas dan informasi usaha agar data bisnis Anda selalu tepat dan mudah diperbarui."
        guide-title="Panduan profil usaha"
        guide-description="Tiga langkah untuk melengkapi profil usaha Anda."
        :guide-steps="guideSteps"
    >
        <Head title="Profil Usaha" />
        <template #header-actions>
            <StatusBadge :tone="business ? 'primary' : 'neutral'" size="md" dot>
                {{ business ? 'Profil tersimpan' : 'Profil baru' }}
            </StatusBadge>
        </template>

        <div class="flex flex-col gap-6">
            <div v-if="feedback" role="status" class="flex items-center justify-between gap-3 rounded-2xl border border-primary-200 bg-primary-50 p-4 text-sm text-primary-900">
                <p>{{ feedback }}</p>
                <button type="button" aria-label="Tutup pesan berhasil" class="min-h-11 shrink-0 rounded-xl px-3 font-semibold text-primary-700 hover:bg-primary-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500" @click="feedback = ''">Tutup</button>
            </div>

            <div class="grid items-start gap-6 xl:grid-cols-12">
                <form id="business-profile-form" class="min-w-0 rounded-2xl border border-hairline bg-white p-5 shadow-xs sm:p-6 xl:col-span-8" :aria-busy="form.processing" @submit.prevent="submit">
                    <div class="mb-6 flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h2 class="text-base font-semibold">{{ business ? 'Perbarui informasi usaha' : 'Buat profil usaha Anda' }}</h2>
                            <p class="mt-1 text-sm text-ink-secondary">Nama dan kategori wajib diisi. Informasi lainnya bisa dilengkapi bertahap.</p>
                        </div>
                        <StatusBadge v-if="form.isDirty" tone="warning" size="md">Belum disimpan</StatusBadge>
                    </div>

                    <div v-if="form.hasErrors" ref="errorSummary" tabindex="-1" role="alert" aria-labelledby="profile-errors-title" class="mb-6 rounded-xl border border-danger/30 bg-danger-light p-4 text-sm text-danger-dark focus:outline-none focus:ring-2 focus:ring-danger">
                        <h3 id="profile-errors-title" class="font-semibold">Periksa kembali isian berikut</h3>
                        <ul class="mt-2 list-inside list-disc space-y-1">
                            <li v-for="(message, field) in form.errors" :key="field">
                                <a :href="`#business-${field}`" class="rounded underline underline-offset-2 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-danger" @click.prevent="focusField(field)">{{ message }}</a>
                            </li>
                        </ul>
                    </div>

                    <div v-if="identityLocked" id="identity-lock-help" tabindex="-1" class="mb-6 rounded-xl border border-primary-200 bg-primary-50 p-4 text-sm text-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <p class="font-semibold">Identitas usaha dikunci setelah persetujuan verifikasi</p>
                        <p class="mt-1 leading-relaxed">Nama, kategori, dan alamat tidak dapat diubah langsung. Deskripsi, kecamatan, kelurahan, serta telepon tetap dapat diperbarui.</p>
                    </div>

                    <fieldset :disabled="form.processing" class="min-w-0">
                        <legend class="mb-4 text-sm font-semibold text-ink">Identitas usaha</legend>
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <InputLabel for="business-name" value="Nama usaha (wajib)" />
                                <TextInput id="business-name" v-model="form.name" name="name" required maxlength="150" autocomplete="organization" class="mt-2 min-h-11 w-full rounded-lg border-hairline text-sm read-only:bg-neutral-100 focus:border-primary-500 focus:ring-primary-500 disabled:opacity-60" :readonly="identityLocked" :aria-invalid="Boolean(form.errors.name)" aria-describedby="business-name-error" />
                                <InputError id="business-name-error" :message="form.errors.name" class="mt-2" />
                            </div>
                            <div class="sm:col-span-2">
                                <InputLabel for="business-business_category_id" value="Kategori usaha (wajib)" />
                                <select id="business-business_category_id" v-model="form.business_category_id" name="business_category_id" required :disabled="identityLocked || categories.length === 0" class="mt-2 min-h-11 w-full rounded-lg border-hairline text-sm focus:border-primary-500 focus:ring-primary-500 disabled:bg-neutral-100 disabled:text-ink-secondary" :aria-invalid="Boolean(form.errors.business_category_id)" aria-describedby="category-help business-business_category_id-error">
                                    <option value="" disabled>Pilih kategori usaha</option>
                                    <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                                </select>
                                <p id="category-help" class="mt-2 text-xs leading-relaxed" :class="categories.length ? 'text-ink-muted' : 'text-warning-dark'">{{ categories.length ? 'Pilih kategori yang paling menggambarkan kegiatan usaha Anda.' : 'Kategori usaha belum tersedia. Hubungi pengelola agar profil dapat disimpan.' }}</p>
                                <InputError id="business-business_category_id-error" :message="form.errors.business_category_id" class="mt-2" />
                            </div>
                            <div class="sm:col-span-2">
                                <InputLabel for="business-description" value="Deskripsi usaha" />
                                <textarea id="business-description" v-model="form.description" name="description" rows="4" maxlength="10000" placeholder="Ceritakan produk, layanan, atau keunggulan usaha Anda." class="mt-2 w-full rounded-lg border-hairline text-sm placeholder:text-ink-muted focus:border-primary-500 focus:ring-primary-500 disabled:opacity-60" :aria-invalid="Boolean(form.errors.description)" aria-describedby="business-description-error"></textarea>
                                <InputError id="business-description-error" :message="form.errors.description" class="mt-2" />
                            </div>
                        </div>
                    </fieldset>

                    <fieldset :disabled="form.processing" class="mt-6 min-w-0 border-t border-hairline pt-5">
                        <legend class="px-1 text-sm font-semibold text-ink">Lokasi dan kontak</legend>
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <InputLabel for="business-address" value="Alamat usaha" />
                                <textarea id="business-address" v-model="form.address" name="address" rows="3" maxlength="10000" autocomplete="street-address" placeholder="Nama jalan, nomor, dan keterangan lokasi usaha." class="mt-2 w-full rounded-lg border-hairline text-sm read-only:bg-neutral-100 placeholder:text-ink-muted focus:border-primary-500 focus:ring-primary-500 disabled:opacity-60" :readonly="identityLocked" :aria-invalid="Boolean(form.errors.address)" aria-describedby="business-address-error"></textarea>
                                <InputError id="business-address-error" :message="form.errors.address" class="mt-2" />
                            </div>
                            <div>
                                <InputLabel for="business-district" value="Kecamatan" />
                                <TextInput id="business-district" v-model="form.district" name="district" maxlength="100" autocomplete="address-level2" class="mt-2 min-h-11 w-full rounded-lg border-hairline text-sm focus:border-primary-500 focus:ring-primary-500 disabled:opacity-60" :aria-invalid="Boolean(form.errors.district)" aria-describedby="business-district-error" />
                                <InputError id="business-district-error" :message="form.errors.district" class="mt-2" />
                            </div>
                            <div>
                                <InputLabel for="business-subdistrict" value="Kelurahan" />
                                <TextInput id="business-subdistrict" v-model="form.subdistrict" name="subdistrict" maxlength="100" autocomplete="address-level3" class="mt-2 min-h-11 w-full rounded-lg border-hairline text-sm focus:border-primary-500 focus:ring-primary-500 disabled:opacity-60" :aria-invalid="Boolean(form.errors.subdistrict)" aria-describedby="business-subdistrict-error" />
                                <InputError id="business-subdistrict-error" :message="form.errors.subdistrict" class="mt-2" />
                            </div>
                            <div class="sm:col-span-2">
                                <InputLabel for="business-contact_phone" value="Telepon usaha" />
                                <TextInput id="business-contact_phone" v-model="form.contact_phone" name="contact_phone" type="tel" inputmode="tel" maxlength="30" autocomplete="tel" placeholder="Contoh: 081234567890" class="mt-2 min-h-11 w-full rounded-lg border-hairline text-sm focus:border-primary-500 focus:ring-primary-500 disabled:opacity-60" :aria-invalid="Boolean(form.errors.contact_phone)" aria-describedby="business-contact_phone-error" />
                                <InputError id="business-contact_phone-error" :message="form.errors.contact_phone" class="mt-2" />
                            </div>
                        </div>
                    </fieldset>

                    <div class="mt-6 flex flex-wrap items-center gap-3 border-t border-hairline pt-5">
                        <button type="submit" :disabled="form.processing || categories.length === 0" class="min-h-11 rounded-xl bg-primary-600 px-5 text-sm font-semibold text-white shadow-xs hover:bg-primary-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">{{ form.processing ? 'Menyimpan…' : business ? 'Simpan perubahan' : 'Simpan profil usaha' }}</button>
                        <SecondaryButton :disabled="form.processing || !form.isDirty" class="min-h-11 rounded-xl" @click="resetChanges">Reset perubahan</SecondaryButton>
                    </div>
                </form>

                <aside aria-label="Ringkasan profil usaha" class="flex min-w-0 flex-col gap-5 xl:col-span-4">
                    <section class="overflow-hidden rounded-2xl border border-hairline bg-white shadow-xs" aria-labelledby="preview-heading">
                        <div class="border-b border-hairline bg-secondary-50 p-5">
                            <p class="text-xs font-semibold uppercase tracking-wider text-secondary-700">Ringkasan isian</p>
                            <div class="mt-4 flex items-center gap-3">
                                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-primary-600 text-base font-semibold text-white" aria-hidden="true">{{ businessInitials }}</span>
                                <h2 id="preview-heading" class="min-w-0 break-words text-lg font-semibold">{{ form.name.trim() || 'Nama usaha Anda' }}</h2>
                            </div>
                            <div class="mt-4"><StatusBadge tone="secondary" size="md" class="max-w-full"><span class="min-w-0 break-words">{{ categoryName }}</span></StatusBadge></div>
                        </div>
                        <div class="p-5 text-sm leading-relaxed">
                            <p class="line-clamp-4 whitespace-pre-wrap break-words text-ink-secondary">{{ form.description.trim() || 'Deskripsi usaha akan tampil di sini setelah diisi.' }}</p>
                            <p v-if="locationLabel" class="mt-4 break-words font-medium text-ink-secondary">{{ locationLabel }}</p>
                            <details class="mt-4 text-ink-secondary">
                                <summary class="w-fit cursor-pointer rounded py-1 font-medium text-primary-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500">Rincian lokasi dan kontak</summary>
                                <dl class="mt-3 flex flex-col gap-3 rounded-xl bg-canvas p-4">
                                    <div><dt class="text-xs text-ink-muted">Alamat</dt><dd class="whitespace-pre-wrap break-words">{{ form.address.trim() || 'Belum diisi' }}</dd></div>
                                    <div><dt class="text-xs text-ink-muted">Telepon usaha</dt><dd class="break-words">{{ form.contact_phone.trim() || 'Belum diisi' }}</dd></div>
                                </dl>
                            </details>
                            <p class="mt-4 border-t border-hairline pt-4 text-xs text-ink-muted">Ringkasan mengikuti isian form. Klik Simpan agar perubahan tersimpan.</p>
                        </div>
                    </section>
                    <div class="rounded-2xl border border-primary-200 bg-primary-50 p-5 text-sm text-primary-900">
                        <h2 class="font-semibold">Satu akun, satu profil usaha</h2>
                        <p class="mt-2 leading-relaxed">Profil ini terhubung dengan akun Anda. Menyimpan kembali akan memperbarui usaha yang sama.</p>
                    </div>
                </aside>
            </div>
        </div>
    </UmkmLayout>
</template>
