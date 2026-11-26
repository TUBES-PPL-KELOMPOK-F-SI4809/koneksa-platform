<script setup>
import { computed, nextTick, ref } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import MitraLayout from '@/Layouts/MitraLayout.vue';
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    partnerProfile: {
        type: Object,
        default: null,
    },
    cooperationTypes: {
        type: Array,
        default: () => [],
    },
    entityCategories: {
        type: Array,
        default: () => [],
    },
    status: {
        type: String,
        default: '',
    },
});

const valuesFromProfile = () => ({
    organization_name: props.partnerProfile?.organization_name ?? '',
    industry: props.partnerProfile?.industry ?? '',
    description: props.partnerProfile?.description ?? '',
    address: props.partnerProfile?.address ?? '',
    contact_phone: props.partnerProfile?.contact_phone ?? '',
    website_url: props.partnerProfile?.website_url ?? '',
    cooperation_type_ids: Array.isArray(props.partnerProfile?.cooperation_type_ids)
        ? props.partnerProfile.cooperation_type_ids.map((id) => Number(id))
        : [],
});

const form = useForm(valuesFromProfile());
const errorSummary = ref(null);
const feedback = ref(props.status || '');
const showDeleteModal = ref(false);
const deleteProcessing = ref(false);
const deleteCancelButton = ref(null);

const categoryOptions = computed(() => {
    const base = [...props.entityCategories];
    const current = props.partnerProfile?.industry?.trim();

    if (current && !base.includes(current)) {
        base.unshift(current);
    }

    return base;
});

const organizationInitials = computed(() =>
    (form.organization_name.trim() || 'Mitra Industri')
        .split(/\s+/)
        .slice(0, 2)
        .map((part) => part[0])
        .join('')
        .toUpperCase(),
);

const selectedCooperationNames = computed(() =>
    props.cooperationTypes
        .filter((type) => form.cooperation_type_ids.includes(Number(type.id)))
        .map((type) => type.name),
);

const cooperationTypeError = computed(() => {
    if (form.errors.cooperation_type_ids) {
        return form.errors.cooperation_type_ids;
    }

    const nestedKey = Object.keys(form.errors).find((key) => key.startsWith('cooperation_type_ids.'));

    return nestedKey ? form.errors[nestedKey] : '';
});

function focusField(field) {
    const normalized = field.startsWith('cooperation_type_ids') ? 'cooperation_type_ids' : field;
    const target = document.getElementById(`partner-${normalized}`);
    target?.focus();
}

function selectAllCooperationTypes() {
    form.cooperation_type_ids = props.cooperationTypes.map((type) => Number(type.id));
}

function clearCooperationTypes() {
    form.cooperation_type_ids = [];
}

function resetChanges() {
    form.defaults(valuesFromProfile());
    form.reset();
    form.clearErrors();
    feedback.value = '';
    focusField('organization_name');
}

function submit() {
    feedback.value = '';

    const options = {
        preserveScroll: true,
        onSuccess: () => {
            form.defaults(valuesFromProfile());
            form.reset();
            feedback.value = props.partnerProfile
                ? 'Profil organisasi mitra dan dukungan bisnis berhasil disimpan.'
                : 'Profil organisasi mitra berhasil dibuat.';
        },
        onError: async () => {
            await nextTick();
            errorSummary.value?.focus();
        },
    };

    if (props.partnerProfile?.id) {
        form.put(route('mitra.profile.update', props.partnerProfile.id), options);
        return;
    }

    form.post(route('mitra.profile.store'), options);
}

async function openDeleteConfirmation() {
    showDeleteModal.value = true;
    await nextTick();
    deleteCancelButton.value?.$el?.focus();
}

function confirmDelete() {
    if (!props.partnerProfile?.id) {
        return;
    }

    deleteProcessing.value = true;
    router.delete(route('mitra.profile.destroy', props.partnerProfile.id), {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false;
            form.defaults(valuesFromProfile());
            form.reset();
            feedback.value = 'Profil organisasi mitra berhasil dihapus.';
        },
        onError: async () => {
            showDeleteModal.value = false;
            await nextTick();
            errorSummary.value?.focus();
        },
        onFinish: () => {
            deleteProcessing.value = false;
        },
    });
}
</script>

<template>
    <MitraLayout
        title="Profil Organisasi Mitra"
        subtitle="Kelola identitas organisasi, kategori entitas bisnis, dan dukungan yang ditawarkan kepada UMKM."
    >
        <Head title="Profil Organisasi Mitra" />

        <template #header-actions>
            <StatusBadge :tone="partnerProfile ? 'secondary' : 'warning'" size="md" dot>
                {{ partnerProfile ? 'Profil organisasi aktif' : 'Belum dilengkapi' }}
            </StatusBadge>
        </template>

        <div class="flex flex-col gap-6">
            <div
                v-if="feedback"
                role="status"
                class="flex items-center justify-between gap-3 rounded-2xl border border-secondary-200 bg-secondary-50 p-4 text-sm text-ink"
            >
                <p class="font-medium text-secondary-700">{{ feedback }}</p>
                <button
                    type="button"
                    aria-label="Tutup pesan berhasil"
                    class="min-h-11 shrink-0 rounded-xl px-3 font-semibold text-secondary-700 hover:bg-secondary-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                    @click="feedback = ''"
                >
                    Tutup
                </button>
            </div>

            <div class="grid items-start gap-6 xl:grid-cols-12">
                <form
                    id="partner-profile-form"
                    class="min-w-0 rounded-2xl border border-hairline bg-white p-5 shadow-xs sm:p-6 xl:col-span-8"
                    :aria-busy="form.processing"
                    @submit.prevent="submit"
                >
                    <div class="mb-6 flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h2 class="text-base font-semibold text-ink">
                                {{ partnerProfile ? 'Perbarui profil organisasi' : 'Buat profil organisasi mitra' }}
                            </h2>
                            <p class="mt-1 text-sm text-ink-secondary">
                                Lengkapi identitas entitas bisnis dan pilih jenis dukungan yang ditawarkan kepada UMKM.
                            </p>
                        </div>
                        <StatusBadge v-if="form.isDirty" tone="warning" size="md">Belum disimpan</StatusBadge>
                    </div>

                    <div
                        v-if="form.hasErrors || $page.props.errors?.partner_profile"
                        ref="errorSummary"
                        tabindex="-1"
                        role="alert"
                        aria-labelledby="partner-errors-title"
                        class="mb-6 rounded-xl border border-danger/30 bg-danger-light p-4 text-sm text-danger-dark focus:outline-none focus:ring-2 focus:ring-danger"
                    >
                        <h3 id="partner-errors-title" class="font-semibold">Periksa kembali isian berikut</h3>
                        <ul class="mt-2 list-inside list-disc space-y-1">
                            <li v-if="$page.props.errors?.partner_profile">{{ $page.props.errors.partner_profile }}</li>
                            <li v-for="(message, field) in form.errors" :key="field">
                                <a
                                    :href="`#partner-${field}`"
                                    class="rounded underline underline-offset-2 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-danger"
                                    @click.prevent="focusField(field)"
                                >
                                    {{ message }}
                                </a>
                            </li>
                        </ul>
                    </div>

                    <fieldset :disabled="form.processing" class="min-w-0">
                        <legend class="mb-4 text-sm font-semibold text-ink">Identitas organisasi &amp; entitas bisnis</legend>
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <InputLabel for="partner-organization_name" value="Nama organisasi / perusahaan (wajib)" />
                                <TextInput
                                    id="partner-organization_name"
                                    v-model="form.organization_name"
                                    name="organization_name"
                                    required
                                    maxlength="150"
                                    autocomplete="organization"
                                    placeholder="Contoh: PT Sinergi Digital Nusantara"
                                    class="mt-2 min-h-11 w-full rounded-lg border-hairline text-sm focus:border-primary-500 focus:ring-primary-500 disabled:opacity-60"
                                    :aria-invalid="Boolean(form.errors.organization_name)"
                                    aria-describedby="partner-organization_name-error"
                                />
                                <InputError id="partner-organization_name-error" :message="form.errors.organization_name" class="mt-2" />
                            </div>

                            <div class="sm:col-span-2">
                                <InputLabel for="partner-industry" value="Kategori entitas bisnis (wajib)" />
                                <select
                                    id="partner-industry"
                                    v-model="form.industry"
                                    name="industry"
                                    required
                                    class="mt-2 min-h-11 w-full rounded-lg border-hairline text-sm focus:border-primary-500 focus:ring-primary-500 disabled:bg-neutral-100 disabled:text-ink-secondary"
                                    :aria-invalid="Boolean(form.errors.industry)"
                                    aria-describedby="partner-industry-help partner-industry-error"
                                >
                                    <option value="" disabled>Pilih kategori entitas bisnis</option>
                                    <option v-for="category in categoryOptions" :key="category" :value="category">
                                        {{ category }}
                                    </option>
                                </select>
                                <p id="partner-industry-help" class="mt-2 text-xs leading-relaxed text-ink-muted">
                                    Pilih kategori organisasi atau sektor industri yang paling sesuai dengan profil mitra Anda.
                                </p>
                                <InputError id="partner-industry-error" :message="form.errors.industry" class="mt-2" />
                            </div>

                            <div class="sm:col-span-2">
                                <InputLabel for="partner-description" value="Deskripsi organisasi &amp; dukungan bisnis" />
                                <textarea
                                    id="partner-description"
                                    v-model="form.description"
                                    name="description"
                                    rows="4"
                                    maxlength="10000"
                                    placeholder="Jelaskan profil singkat organisasi dan bentuk dukungan bisnis atau program pembinaan yang ditawarkan kepada UMKM."
                                    class="mt-2 w-full rounded-lg border-hairline text-sm placeholder:text-ink-muted focus:border-primary-500 focus:ring-primary-500 disabled:opacity-60"
                                    :aria-invalid="Boolean(form.errors.description)"
                                    aria-describedby="partner-description-error"
                                ></textarea>
                                <InputError id="partner-description-error" :message="form.errors.description" class="mt-2" />
                            </div>
                        </div>
                    </fieldset>

                    <fieldset :disabled="form.processing" class="mt-6 min-w-0 border-t border-hairline pt-5">
                        <legend class="px-1 text-sm font-semibold text-ink">Lokasi &amp; kontak organisasi</legend>
                        <div class="mt-3 grid gap-5 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <InputLabel for="partner-address" value="Alamat organisasi" />
                                <textarea
                                    id="partner-address"
                                    v-model="form.address"
                                    name="address"
                                    rows="3"
                                    maxlength="10000"
                                    autocomplete="street-address"
                                    placeholder="Alamat kantor atau sekretariat organisasi."
                                    class="mt-2 w-full rounded-lg border-hairline text-sm placeholder:text-ink-muted focus:border-primary-500 focus:ring-primary-500 disabled:opacity-60"
                                    :aria-invalid="Boolean(form.errors.address)"
                                    aria-describedby="partner-address-error"
                                ></textarea>
                                <InputError id="partner-address-error" :message="form.errors.address" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="partner-contact_phone" value="Telepon organisasi" />
                                <TextInput
                                    id="partner-contact_phone"
                                    v-model="form.contact_phone"
                                    name="contact_phone"
                                    type="tel"
                                    inputmode="tel"
                                    maxlength="30"
                                    autocomplete="tel"
                                    placeholder="Contoh: 081198765001"
                                    class="mt-2 min-h-11 w-full rounded-lg border-hairline text-sm focus:border-primary-500 focus:ring-primary-500 disabled:opacity-60"
                                    :aria-invalid="Boolean(form.errors.contact_phone)"
                                    aria-describedby="partner-contact_phone-error"
                                />
                                <InputError id="partner-contact_phone-error" :message="form.errors.contact_phone" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="partner-website_url" value="Situs web resmi" />
                                <TextInput
                                    id="partner-website_url"
                                    v-model="form.website_url"
                                    name="website_url"
                                    type="url"
                                    maxlength="255"
                                    autocomplete="url"
                                    placeholder="https://perusahaan.co.id"
                                    class="mt-2 min-h-11 w-full rounded-lg border-hairline text-sm focus:border-primary-500 focus:ring-primary-500 disabled:opacity-60"
                                    :aria-invalid="Boolean(form.errors.website_url)"
                                    aria-describedby="partner-website_url-error"
                                />
                                <InputError id="partner-website_url-error" :message="form.errors.website_url" class="mt-2" />
                            </div>
                        </div>
                    </fieldset>

                    <fieldset
                        id="partner-cooperation_type_ids"
                        tabindex="-1"
                        :disabled="form.processing"
                        class="mt-6 min-w-0 border-t border-hairline pt-5 focus:outline-none"
                    >
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <legend class="px-1 text-sm font-semibold text-ink">
                                Jenis kerja sama &amp; dukungan bisnis untuk UMKM (wajib minimal 1)
                            </legend>
                            <div class="flex items-center gap-2 text-xs">
                                <StatusBadge tone="secondary" size="md">
                                    {{ form.cooperation_type_ids.length }} dipilih
                                </StatusBadge>
                                <button
                                    v-if="cooperationTypes.length"
                                    type="button"
                                    class="rounded-lg px-2 py-1 font-medium text-secondary-700 hover:bg-secondary-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                                    @click="selectAllCooperationTypes"
                                >
                                    Pilih semua
                                </button>
                                <button
                                    v-if="form.cooperation_type_ids.length"
                                    type="button"
                                    class="rounded-lg px-2 py-1 font-medium text-ink-secondary hover:bg-neutral-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                                    @click="clearCooperationTypes"
                                >
                                    Kosongkan
                                </button>
                            </div>
                        </div>

                        <p id="partner-cooperation-help" class="mt-2 text-xs leading-relaxed text-ink-muted">
                            Preferensi ini membantu UMKM memahami bentuk dukungan bisnis yang dapat diajukan melalui proposal kolaborasi.
                        </p>

                        <div
                            v-if="cooperationTypes.length"
                            class="mt-4 grid gap-3 sm:grid-cols-2"
                            role="group"
                            aria-describedby="partner-cooperation-help partner-cooperation-error"
                        >
                            <label
                                v-for="type in cooperationTypes"
                                :key="type.id"
                                :for="`cooperation-type-${type.id}`"
                                class="flex min-h-11 cursor-pointer items-start gap-3 rounded-xl border p-3.5 text-sm transition"
                                :class="
                                    form.cooperation_type_ids.includes(Number(type.id))
                                        ? 'border-secondary-500 bg-secondary-50 text-ink shadow-xs'
                                        : 'border-hairline bg-canvas text-ink-secondary hover:border-neutral-300 hover:bg-white'
                                "
                            >
                                <input
                                    :id="`cooperation-type-${type.id}`"
                                    v-model="form.cooperation_type_ids"
                                    type="checkbox"
                                    name="cooperation_type_ids[]"
                                    :value="Number(type.id)"
                                    class="mt-0.5 rounded border-neutral-300 text-secondary-600 focus:ring-secondary-500"
                                />
                                <span class="font-medium leading-snug">{{ type.name }}</span>
                            </label>
                        </div>

                        <p v-else class="mt-4 rounded-xl border border-warning/30 bg-warning-light p-4 text-sm text-warning-dark">
                            Daftar jenis kerja sama belum tersedia. Hubungi pengelola platform sebelum menyimpan profil.
                        </p>

                        <InputError id="partner-cooperation-error" :message="cooperationTypeError" class="mt-2" />
                    </fieldset>

                    <div class="mt-6 flex flex-wrap items-center gap-3 border-t border-hairline pt-5">
                        <button
                            type="submit"
                            :disabled="form.processing || cooperationTypes.length === 0"
                            class="min-h-11 rounded-xl bg-primary-600 px-5 text-sm font-semibold text-white shadow-xs hover:bg-primary-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            {{
                                form.processing
                                    ? 'Menyimpan…'
                                    : partnerProfile
                                      ? 'Simpan perubahan'
                                      : 'Simpan profil organisasi'
                            }}
                        </button>

                        <SecondaryButton :disabled="form.processing || !form.isDirty" class="min-h-11 rounded-xl" @click="resetChanges">
                            Reset perubahan
                        </SecondaryButton>
                    </div>

                    <details v-if="partnerProfile" class="mt-6 border-t border-hairline pt-4 text-sm text-ink-secondary">
                        <summary class="min-h-11 w-fit cursor-pointer rounded py-2 font-medium text-danger-dark focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-danger">
                            Pengaturan lanjutan profil
                        </summary>
                        <div class="mt-3 flex flex-wrap items-center justify-between gap-4 rounded-xl border border-danger/20 bg-danger-light p-4">
                            <div class="min-w-0 flex-1">
                                <p class="font-semibold text-danger-dark">Hapus profil organisasi</p>
                                <p class="mt-1 text-xs leading-relaxed text-ink-secondary">
                                    Profil hanya dapat dihapus jika belum terikat pada penawaran solusi digital atau proposal kolaborasi.
                                </p>
                            </div>
                            <DangerButton type="button" class="min-h-11 shrink-0 rounded-xl" @click="openDeleteConfirmation">
                                Hapus profil
                            </DangerButton>
                        </div>
                    </details>
                </form>

                <aside aria-label="Ringkasan profil organisasi mitra" class="flex min-w-0 flex-col gap-5 xl:col-span-4">
                    <section class="overflow-hidden rounded-2xl border border-hairline bg-white shadow-xs" aria-labelledby="preview-heading">
                        <div class="border-b border-hairline bg-secondary-50 p-5">
                            <p class="text-xs font-semibold uppercase tracking-wider text-secondary-700">Ringkasan isian</p>
                            <div class="mt-4 flex items-center gap-3">
                                <span
                                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-secondary-600 text-base font-semibold text-white"
                                    aria-hidden="true"
                                >
                                    {{ organizationInitials }}
                                </span>
                                <h2 id="preview-heading" class="min-w-0 break-words text-lg font-semibold text-ink">
                                    {{ form.organization_name.trim() || 'Nama organisasi Anda' }}
                                </h2>
                            </div>
                            <div class="mt-4">
                                <StatusBadge tone="secondary" size="md" class="max-w-full">
                                    <span class="min-w-0 break-words">{{ form.industry || 'Kategori belum dipilih' }}</span>
                                </StatusBadge>
                            </div>
                        </div>

                        <div class="p-5 text-sm leading-relaxed">
                            <p class="line-clamp-4 whitespace-pre-wrap break-words text-ink-secondary">
                                {{ form.description.trim() || 'Deskripsi organisasi dan dukungan bisnis akan tampil di sini.' }}
                            </p>

                            <div class="mt-4 rounded-xl bg-canvas p-4">
                                <h3 class="text-xs font-semibold uppercase tracking-wider text-ink-muted">
                                    Dukungan bisnis ditawarkan ({{ selectedCooperationNames.length }})
                                </h3>
                                <div v-if="selectedCooperationNames.length" class="mt-2.5 flex flex-wrap gap-1.5">
                                    <StatusBadge v-for="name in selectedCooperationNames" :key="name" tone="primary" size="md">
                                        {{ name }}
                                    </StatusBadge>
                                </div>
                                <p v-else class="mt-2 text-xs text-ink-muted">
                                    Belum ada jenis kerja sama yang dipilih.
                                </p>
                            </div>

                            <details class="mt-4 text-ink-secondary">
                                <summary class="w-fit cursor-pointer rounded py-1 font-medium text-secondary-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500">
                                    Rincian lokasi dan kontak
                                </summary>
                                <dl class="mt-3 flex flex-col gap-3 rounded-xl bg-canvas p-4">
                                    <div>
                                        <dt class="text-xs text-ink-muted">Alamat</dt>
                                        <dd class="whitespace-pre-wrap break-words">{{ form.address.trim() || 'Belum diisi' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs text-ink-muted">Telepon organisasi</dt>
                                        <dd class="break-words">{{ form.contact_phone.trim() || 'Belum diisi' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs text-ink-muted">Situs web</dt>
                                        <dd class="break-words">{{ form.website_url.trim() || 'Belum diisi' }}</dd>
                                    </div>
                                </dl>
                            </details>

                            <p class="mt-4 border-t border-hairline pt-4 text-xs text-ink-muted">
                                Ringkasan mengikuti isian form. Klik Simpan agar perubahan tersimpan.
                            </p>
                        </div>
                    </section>

                    <div class="rounded-2xl border border-secondary-200 bg-secondary-50 p-5 text-sm text-ink">
                        <h2 class="font-semibold text-secondary-700">Satu akun, satu profil organisasi</h2>
                        <p class="mt-2 leading-relaxed text-ink-secondary">
                            Profil organisasi terhubung langsung dengan akun Mitra Anda. Hanya pemilik akun ini yang dapat memperbarui profil dan preferensi kerja sama.
                        </p>
                    </div>
                </aside>
            </div>
        </div>

        <Modal
            :show="showDeleteModal"
            :closeable="!deleteProcessing"
            max-width="lg"
            aria-labelledby="delete-partner-title"
            @close="showDeleteModal = false"
        >
            <div class="p-6">
                <h2 id="delete-partner-title" class="text-lg font-semibold text-ink">Hapus profil organisasi mitra?</h2>
                <p class="mt-3 text-sm leading-relaxed text-ink-secondary">
                    Tindakan ini akan menghapus profil organisasi beserta pilihan dukungan bisnis yang tersimpan. Anda dapat membuat profil baru kembali nanti selama belum ada proposal atau penawaran terkait.
                </p>
                <div class="mt-6 flex flex-wrap justify-end gap-3">
                    <SecondaryButton
                        ref="deleteCancelButton"
                        class="min-h-11"
                        :disabled="deleteProcessing"
                        @click="showDeleteModal = false"
                    >
                        Batal
                    </SecondaryButton>
                    <DangerButton :disabled="deleteProcessing" class="min-h-11 rounded-xl" @click="confirmDelete">
                        {{ deleteProcessing ? 'Menghapus…' : 'Ya, hapus profil' }}
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </MitraLayout>
</template>

