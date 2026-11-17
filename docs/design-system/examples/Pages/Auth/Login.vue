<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout evaluation-page="login">
        <Head title="Masuk — Koneksa" />

        <div
            v-if="status"
            role="status"
            class="mb-4 rounded-md border border-success/25 bg-success-light px-3 py-2 text-xs font-medium text-success-dark"
        >
            {{ status }}
        </div>

        <div class="mb-6">
            <h1 class="text-xl font-semibold tracking-tight text-ink">
                Masuk ke Koneksa
            </h1>
            <p class="mt-1 text-[13px] text-ink-secondary">
                Akses panel UMKM, Mitra Industri, Pemerintah Daerah, atau Admin.
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1.5 block w-full"
                    v-model="form.email"
                    placeholder="nama@instansi.id"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-1.5" :message="form.errors.email" />
            </div>

            <div>
                <div class="flex items-center justify-between">
                    <InputLabel for="password" value="Password" />
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-xs font-medium text-primary-600 transition hover:text-primary-700 hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                    >
                        Lupa password?
                    </Link>
                </div>

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1.5 block w-full"
                    v-model="form.password"
                    placeholder="••••••••"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-1.5" :message="form.errors.password" />
            </div>

            <div class="flex items-center justify-between pt-1">
                <label class="flex cursor-pointer items-center gap-2">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="text-[13px] text-ink-secondary">Ingat sesi saya</span>
                </label>
            </div>

            <div class="pt-2">
                <PrimaryButton
                    class="w-full"
                    :disabled="form.processing"
                >
                    {{ form.processing ? 'Memproses...' : 'Masuk ke Workspace' }}
                </PrimaryButton>
            </div>
        </form>

        <div class="mt-6 border-t border-hairline pt-5 text-center text-[13px] text-ink-secondary">
            Belum memiliki akun?
            <Link
                :href="route('register')"
                class="ms-1 font-medium text-primary-600 hover:text-primary-700 hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
            >
                Daftar Akun Baru
            </Link>
        </div>
    </GuestLayout>
</template>
