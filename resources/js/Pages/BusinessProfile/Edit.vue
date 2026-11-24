<script setup>
import { computed, nextTick, ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import UmkmLayout from '@/Layouts/UmkmLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
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
    business_needs: props.business?.business_needs ?? '',
    is_published: Boolean(props.business?.published_at),
});
const form = useForm(valuesFromBusiness());
const errorSummary = ref(null);
const feedback = ref('');
const showPublicationConfirmation = ref(false);
const publicationCancel = ref(null);
const saveButton = ref(null);
const categoryName = computed(() => props.categories.find((category) =>
    category.id === Number(form.business_category_id))?.name ?? 'Kategori belum dipilih');
const businessInitials = computed(() => (form.name.trim() || 'Usaha Anda').split(/\s+/)
    .slice(0, 2).map((part) => part[0]).join('').toUpperCase());
const locationLabel = computed(() => [form.subdistrict, form.district].filter(Boolean).join(', '));
const guideSteps = [
    { title: '1. Isi identitas usaha', description: 'Masukkan nama usaha dan pilih kategori yang paling sesuai.' },
    { title: '2. Lengkapi lokasi dan kontak', description: 'Tambahkan deskripsi, alamat, dan telepon usaha bila tersedia.' },
    { title: '3. Atur kebutuhan dan publikasi', description: 'Jelaskan kebutuhan usaha, pilih draft atau publik, lalu periksa dan simpan.' },
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

async function submit() {
    if (form.is_published !== Boolean(props.business?.published_at)) {
        showPublicationConfirmation.value = true;
        await nextTick();
        publicationCancel.value?.$el.focus();
        return;
    }
    save();
}

function restoreFocus() {
    (form.hasErrors ? errorSummary.value : saveButton.value)?.focus();
}

function save() {
    feedback.value = '';
    form.put(route('business-profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            form.defaults(valuesFromBusiness());
            form.reset();
            showPublicationConfirmation.value = false;
            feedback.value = `Profil usaha berhasil disimpan. Status: ${form.is_published ? 'Publik' : 'Draft / privat'}.`;
        },
        onError: async () => {
            showPublicationConfirmation.value = false;
            await nextTick();
            errorSummary.value?.focus();
        },
    });
}
</script>

<template>
    <UmkmLayout
        title="Profil Usaha"
        subtitle="Kelola identitas, kebutuhan pengembangan, dan publikasi profil usaha Anda."
        guide-title="Panduan profil usaha"
        guide-description="Tiga langkah untuk melengkapi profil usaha Anda."
        :guide-steps="guideSteps"
    >
        <Head title="Profil Usaha" />
        <template #header-actions>
            <StatusBadge :tone="business?.published_at ? 'success' : 'neutral'" size="md" dot>
                {{ business?.published_at ? 'Profil publik' : 'Draft / privat' }}
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

                    <fieldset :disabled="form.processing" class="mt-6 min-w-0 border-t border-hairline pt-5">
                        <legend class="px-1 text-sm font-semibold text-ink">Kebutuhan dan publikasi</legend>
                        <div class="mt-3">
                            <InputLabel for="business-business_needs" value="Kebutuhan pengembangan usaha" />
                            <textarea id="business-business_needs" v-model="form.business_needs" name="business_needs" rows="4" maxlength="10000" placeholder="Contoh: mencari mitra distribusi, pendanaan mesin, atau pendampingan pemasaran." class="mt-2 w-full rounded-lg border-hairline text-sm placeholder:text-ink-muted focus:border-primary-500 focus:ring-primary-500 disabled:opacity-60" :aria-invalid="Boolean(form.errors.business_needs)" aria-describedby="business-needs-help business-business_needs-error"></textarea>
                            <p id="business-needs-help" class="mt-2 text-xs leading-relaxed text-ink-muted">Jelaskan dukungan yang Anda butuhkan. Kebutuhan ini ikut terlihat saat profil dipublikasikan.</p>
                            <InputError id="business-business_needs-error" :message="form.errors.business_needs" class="mt-2" />
                        </div>
                        <label for="business-is_published" class="mt-5 flex min-h-11 cursor-pointer items-start gap-3 rounded-xl border border-hairline bg-canvas p-4">
                            <input id="business-is_published" v-model="form.is_published" name="is_published" type="checkbox" class="mt-1 rounded border-neutral-300 text-primary-600 focus:ring-primary-500" :aria-invalid="Boolean(form.errors.is_published)" aria-describedby="publication-help business-is_published-error" />
                            <span class="text-sm"><span class="font-semibold">Publikasikan profil usaha</span><span id="publication-help" class="mt-1 block leading-relaxed text-ink-secondary">Aktifkan agar profil dan kebutuhan usaha dapat dilihat pengguna lain. Nonaktifkan untuk menyimpannya sebagai draft / privat.</span></span>
                        </label>
                        <InputError id="business-is_published-error" :message="form.errors.is_published" class="mt-2" />
                        <details class="mt-3 text-sm text-ink-secondary">
                            <summary class="min-h-11 w-fit cursor-pointer rounded py-3 font-medium text-primary-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500">Informasi yang dipublikasikan</summary>
                            <p class="leading-relaxed">Nama, kategori, deskripsi, logo, alamat, kecamatan, kelurahan, telepon usaha, dan kebutuhan pengembangan. Pastikan lokasi dan kontak yang Anda isi memang boleh dibagikan.</p>
                            <p class="mt-2 leading-relaxed">Data akun, transaksi, keuangan, dan proposal tidak ikut dipublikasikan. Izin indikator performa dikelola terpisah.</p>
                        </details>
                    </fieldset>

                    <div class="mt-6 flex flex-wrap items-center gap-3 border-t border-hairline pt-5">
                        <button ref="saveButton" type="submit" :disabled="form.processing || categories.length === 0" class="min-h-11 rounded-xl bg-primary-600 px-5 text-sm font-semibold text-white shadow-xs hover:bg-primary-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">{{ form.processing ? 'Menyimpan…' : business ? 'Simpan perubahan' : 'Simpan profil usaha' }}</button>
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
                            <StatusBadge :tone="form.is_published ? 'success' : 'neutral'" size="md" dot>{{ form.is_published ? 'Akan ditampilkan publik' : 'Draft / privat' }}</StatusBadge>
                            <p class="line-clamp-4 whitespace-pre-wrap break-words text-ink-secondary">{{ form.description.trim() || 'Deskripsi usaha akan tampil di sini setelah diisi.' }}</p>
                            <p v-if="locationLabel" class="mt-4 break-words font-medium text-ink-secondary">{{ locationLabel }}</p>
                            <div class="mt-4 rounded-xl bg-canvas p-4">
                                <h3 class="text-xs font-semibold text-ink-muted">Kebutuhan pengembangan</h3>
                                <p class="mt-2 line-clamp-4 whitespace-pre-wrap break-words text-ink-secondary">{{ form.business_needs.trim() || 'Belum ada kebutuhan yang ditambahkan.' }}</p>
                            </div>
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
        <Modal :show="showPublicationConfirmation" :closeable="!form.processing" max-width="lg" aria-labelledby="publication-confirm-title" aria-describedby="publication-confirm-description" @close="showPublicationConfirmation = false" @closed="restoreFocus">
            <div class="p-6">
                <h2 id="publication-confirm-title" class="text-lg font-semibold">{{ form.is_published ? 'Publikasikan profil usaha?' : 'Simpan profil sebagai draft?' }}</h2>
                <p id="publication-confirm-description" class="mt-3 text-sm leading-relaxed text-ink-secondary">{{ form.is_published ? 'Profil, lokasi, telepon usaha, dan kebutuhan pengembangan akan dapat dilihat pengguna lain. Data transaksi, keuangan, akun, dan proposal tetap privat.' : 'Profil dan kebutuhan pengembangan tidak lagi dapat dilihat pengguna lain. Data usaha tetap tersimpan untuk Anda dan petugas yang berwenang.' }}</p>
                <p class="mt-3 break-words rounded-xl bg-canvas p-4 text-sm font-semibold">{{ form.name }}</p>
                <p class="mt-3 text-xs text-ink-muted">Seluruh perubahan form akan ikut disimpan.</p>
                <div class="mt-6 flex flex-wrap justify-end gap-3">
                    <SecondaryButton ref="publicationCancel" class="min-h-11" :disabled="form.processing" @click="showPublicationConfirmation = false">Batal</SecondaryButton>
                    <button type="button" :disabled="form.processing" class="min-h-11 rounded-xl bg-primary-600 px-4 text-sm font-semibold text-white hover:bg-primary-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 disabled:opacity-50" @click="save">{{ form.processing ? 'Menyimpan…' : form.is_published ? 'Ya, publikasikan' : 'Ya, simpan sebagai draft' }}</button>
                </div>
            </div>
        </Modal>
    </UmkmLayout>
</template>
