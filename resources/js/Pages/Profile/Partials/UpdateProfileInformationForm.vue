<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
    phone: user.phone || '',
});

const roleLabel = computed(() => {
    switch (user.role) {
        case 'admin':
            return 'Superadmin';
        case 'pemda':
            return 'Pemerintah Daerah (Pemda)';
        case 'mitra':
            return 'Mitra Industri';
        case 'umkm':
        default:
            return 'Pelaku UMKM';
    }
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                Informasi Profil Akun
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Perbarui informasi identitas dasar akun Anda seperti nama, email, dan nomor telepon kontak.
            </p>
        </header>

        <!-- Informasi Peran & Status (Read-Only) -->
        <div class="mt-4 rounded-lg bg-gray-50 p-4 border border-gray-200">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-500">Peran Akun</span>
                    <div class="mt-1 flex items-center gap-2">
                        <span class="inline-flex items-center rounded-full bg-indigo-100 px-3 py-0.5 text-xs font-medium text-indigo-800">
                            {{ roleLabel }}
                        </span>
                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-800">
                            Status: {{ user.account_status === 'enabled' ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                </div>
            </div>
            <p class="mt-2 text-xs text-gray-500">
                Peran dan status akun bersifat tetap (<em>immutable</em>) dan tidak dapat diubah dari formulir profil mandiri.
            </p>
        </div>

        <form
            @submit.prevent="form.patch(route('profile.update'))"
            class="mt-6 space-y-6"
        >
            <div>
                <InputLabel for="name" value="Nama Lengkap" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Alamat Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="phone" value="Nomor Telepon / WhatsApp (Kontak)" />

                <TextInput
                    id="phone"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.phone"
                    placeholder="Contoh: 081234567890"
                    autocomplete="tel"
                />

                <InputError class="mt-2" :message="form.errors.phone" />
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="mt-2 text-sm text-gray-800">
                    Alamat email Anda belum terverifikasi.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Klik di sini untuk mengirim ulang email verifikasi.
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-green-600"
                >
                    Tautan verifikasi baru telah dikirim ke alamat email Anda.
                </div>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Simpan Perubahan</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm font-medium text-emerald-600"
                    >
                        Profil berhasil diperbarui.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
