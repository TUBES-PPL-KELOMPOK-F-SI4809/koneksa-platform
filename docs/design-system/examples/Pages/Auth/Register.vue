<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    role: 'umkm',
    password: '',
    password_confirmation: '',
});

const roleOptions = [
    {
        value: 'umkm',
        title: 'Pelaku UMKM',
        description: 'Kasir, stok, laporan & program',
    },
    {
        value: 'mitra',
        title: 'Mitra Industri',
        description: 'Proposal & solusi digital',
    },
    {
        value: 'pemda',
        title: 'Pemda',
        description: 'Verifikasi & pembinaan',
    },
];

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout evaluation-page="register" card-width="lg">
        <Head title="Daftar Akun — Koneksa" />

        <div class="mb-6">
            <h1 class="text-xl font-semibold tracking-tight text-ink">
                Buat Akun Koneksa
            </h1>
            <p class="mt-1 text-[13px] text-ink-secondary">
                Pilih peran ekosistem Anda dan mulai berkolaborasi di Kota Bandung.
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <!-- Role Selector Cards -->
            <fieldset>
                <legend class="block text-[13px] font-medium text-ink-secondary">
                    Daftar Sebagai
                </legend>
                <div class="mt-1.5 grid grid-cols-1 gap-2 sm:grid-cols-3">
                    <label
                        v-for="opt in roleOptions"
                        :key="opt.value"
                        class="relative flex cursor-pointer flex-col rounded-lg border p-3 transition duration-150 focus-within:ring-2 focus-within:ring-primary-500"
                        :class="
                            form.role === opt.value
                                ? 'border-primary-500 bg-primary-50/70 ring-1 ring-primary-500'
                                : 'border-hairline bg-white hover:border-neutral-300'
                        "
                    >
                        <input
                            type="radio"
                            name="role"
                            :value="opt.value"
                            v-model="form.role"
                            class="sr-only"
                        />
                        <span class="flex items-center justify-between text-[13px] font-semibold text-ink">
                            {{ opt.title }}
                            <span
                                class="h-2 w-2 rounded-full"
                                :class="form.role === opt.value ? 'bg-primary-600' : 'bg-neutral-300'"
                                aria-hidden="true"
                            ></span>
                        </span>
                        <span class="mt-1 text-[11px] leading-snug text-ink-muted">
                            {{ opt.description }}
                        </span>
                    </label>
                </div>
                <InputError class="mt-1.5" :message="form.errors.role" />
            </fieldset>

            <div>
                <InputLabel for="name" value="Nama Lengkap / Penanggung Jawab" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1.5 block w-full"
                    v-model="form.name"
                    placeholder="Ahmad Fauzi"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-1.5" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1.5 block w-full"
                    v-model="form.email"
                    placeholder="usaha@domain.id"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-1.5" :message="form.errors.email" />
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <InputLabel for="password" value="Password" />

                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1.5 block w-full"
                        v-model="form.password"
                        placeholder="Min. 8 karakter"
                        required
                        autocomplete="new-password"
                    />

                    <InputError class="mt-1.5" :message="form.errors.password" />
                </div>

                <div>
                    <InputLabel
                        for="password_confirmation"
                        value="Konfirmasi Password"
                    />

                    <TextInput
                        id="password_confirmation"
                        type="password"
                        class="mt-1.5 block w-full"
                        v-model="form.password_confirmation"
                        placeholder="Ulangi password"
                        required
                        autocomplete="new-password"
                    />

                    <InputError
                        class="mt-1.5"
                        :message="form.errors.password_confirmation"
                    />
                </div>
            </div>

            <div class="pt-2">
                <PrimaryButton
                    class="w-full"
                    :disabled="form.processing"
                >
                    {{ form.processing ? 'Mendaftarkan...' : 'Buat Akun Sekarang' }}
                </PrimaryButton>
            </div>
        </form>

        <div class="mt-6 border-t border-hairline pt-5 text-center text-[13px] text-ink-secondary">
            Sudah memiliki akun?
            <Link
                :href="route('login')"
                class="ms-1 font-medium text-primary-600 hover:text-primary-700 hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
            >
                Masuk
            </Link>
        </div>
    </GuestLayout>
</template>
