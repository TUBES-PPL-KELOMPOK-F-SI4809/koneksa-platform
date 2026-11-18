<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const activeTab = ref('profile');
</script>

<template>
    <Head title="Pengaturan Profil" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Pengaturan Profil & Keamanan
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <!-- Navigasi Tab Pengaturan (KSA-36) -->
                <div class="border-b border-gray-200 bg-white px-4 pt-3 rounded-t-lg shadow-sm">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <button
                            type="button"
                            @click="activeTab = 'profile'"
                            :class="[
                                activeTab === 'profile'
                                    ? 'border-indigo-600 text-indigo-600 font-semibold'
                                    : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
                                'whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium transition-colors duration-150',
                            ]"
                        >
                            Informasi Profil Dasar
                        </button>

                        <button
                            type="button"
                            @click="activeTab = 'security'"
                            :class="[
                                activeTab === 'security'
                                    ? 'border-indigo-600 text-indigo-600 font-semibold'
                                    : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
                                'whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium transition-colors duration-150',
                            ]"
                        >
                            Ganti Kata Sandi
                        </button>

                        <button
                            type="button"
                            @click="activeTab = 'danger'"
                            :class="[
                                activeTab === 'danger'
                                    ? 'border-rose-600 text-rose-600 font-semibold'
                                    : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
                                'whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium transition-colors duration-150',
                            ]"
                        >
                            Hapus Akun
                        </button>
                    </nav>
                </div>

                <!-- Konten Tab 1: Informasi Profil -->
                <div
                    v-show="activeTab === 'profile'"
                    class="bg-white p-4 shadow sm:rounded-lg sm:p-8"
                >
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>

                <!-- Konten Tab 2: Keamanan / Ganti Password -->
                <div
                    v-show="activeTab === 'security'"
                    class="bg-white p-4 shadow sm:rounded-lg sm:p-8"
                >
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <!-- Konten Tab 3: Hapus Akun -->
                <div
                    v-show="activeTab === 'danger'"
                    class="bg-white p-4 shadow sm:rounded-lg sm:p-8"
                >
                    <DeleteUserForm class="max-w-xl" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
