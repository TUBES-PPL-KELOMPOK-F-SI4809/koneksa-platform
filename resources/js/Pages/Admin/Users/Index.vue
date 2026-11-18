<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { reactive, ref } from 'vue';

const props = defineProps({
    users: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    summary: {
        type: Object,
        default: () => ({ total: 0, enabled: 0, disabled: 0, pemda: 0 }),
    },
    status: {
        type: String,
        default: '',
    },
});

const currentUser = usePage().props.auth.user;
const showPemdaForm = ref(false);

const filterForm = reactive({
    search: props.filters.search || '',
    role: props.filters.role || '',
    account_status: props.filters.account_status || '',
});

const pemdaForm = useForm({
    name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
});

const applyFilters = () => {
    router.get(
        route('admin.users.index'),
        {
            search: filterForm.search || undefined,
            role: filterForm.role || undefined,
            account_status: filterForm.account_status || undefined,
        },
        {
            preserveState: true,
            replace: true,
        },
    );
};

const resetFilters = () => {
    filterForm.search = '';
    filterForm.role = '';
    filterForm.account_status = '';
    router.get(route('admin.users.index'));
};

const submitPemdaForm = () => {
    pemdaForm.post(route('admin.users.pemda.store'), {
        onSuccess: () => {
            pemdaForm.reset();
            showPemdaForm.value = false;
        },
    });
};

const toggleUserStatus = (user) => {
    const nextStatus = user.account_status === 'enabled' ? 'disabled' : 'enabled';
    router.patch(
        route('admin.users.status', user.id),
        { account_status: nextStatus },
        { preserveScroll: true },
    );
};

const deleteUser = (user) => {
    if (!window.confirm(`Apakah Anda yakin ingin menghapus akun ${user.name}?`)) {
        return;
    }
    router.delete(route('admin.users.destroy', user.id), {
        preserveScroll: true,
    });
};

const formatRoleLabel = (role) => {
    switch (role) {
        case 'admin':
            return 'Superadmin';
        case 'pemda':
            return 'Pemda';
        case 'mitra':
            return 'Mitra Industri';
        case 'umkm':
        default:
            return 'Pelaku UMKM';
    }
};
</script>

<template>
    <Head title="Manajemen Pengguna &amp; Pemda" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Manajemen Pengguna &amp; Akun Pemda
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Kelola status aktif/nonaktif akun pengguna dan sediakan akun resmi Pemerintah Daerah (KSA-38).
                    </p>
                </div>
                <div>
                    <PrimaryButton type="button" @click="showPemdaForm = !showPemdaForm">
                        {{ showPemdaForm ? 'Tutup Form Pemda' : '+ Buat Akun Petugas Pemda' }}
                    </PrimaryButton>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <!-- Flash Alert -->
                <div
                    v-if="status"
                    class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800"
                >
                    <span v-if="status === 'pemda-user-created'">
                        Akun Petugas Pemerintah Daerah berhasil dibuat dan wajib mengganti kata sandi saat login pertama.
                    </span>
                    <span v-else-if="status === 'user-status-updated'">
                        Status akun pengguna berhasil diperbarui dan dicatat pada Audit Trail.
                    </span>
                    <span v-else-if="status === 'user-deleted'">
                        Akun pengguna berhasil dinonaktifkan/dihapus (soft delete).
                    </span>
                    <span v-else>{{ status }}</span>
                </div>

                <!-- Ringkasan Statistik Pengguna -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Pengguna</div>
                        <div class="mt-2 text-2xl font-bold text-gray-900">{{ summary.total }}</div>
                    </div>
                    <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="text-xs font-semibold uppercase tracking-wider text-emerald-600">Akun Aktif (Enabled)</div>
                        <div class="mt-2 text-2xl font-bold text-emerald-700">{{ summary.enabled }}</div>
                    </div>
                    <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="text-xs font-semibold uppercase tracking-wider text-rose-600">Akun Nonaktif (Disabled)</div>
                        <div class="mt-2 text-2xl font-bold text-rose-700">{{ summary.disabled }}</div>
                    </div>
                    <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="text-xs font-semibold uppercase tracking-wider text-indigo-600">Petugas Pemda</div>
                        <div class="mt-2 text-2xl font-bold text-indigo-700">{{ summary.pemda }}</div>
                    </div>
                </div>

                <!-- Form Pembuatan Akun Petugas Pemda (KSA-40) -->
                <div
                    v-if="showPemdaForm"
                    class="rounded-lg border border-indigo-200 bg-white p-6 shadow-sm"
                >
                    <div class="border-b border-gray-200 pb-4 mb-5">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Penyediaan Akun Resmi Pemerintah Daerah (Pemda)
                        </h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Akun Pemda tidak dapat mendaftar secara publik. Akun yang dibuat di sini otomatis memiliki peran
                            <span class="font-semibold text-indigo-700">pemda</span> dan berstatus
                            <span class="font-semibold text-amber-700">Wajib Ganti Password</span> saat pertama kali masuk.
                        </p>
                    </div>

                    <form @submit.prevent="submitPemdaForm" class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <InputLabel for="pemda_name" value="Nama Instansi / Petugas Pemda" />
                            <TextInput
                                id="pemda_name"
                                v-model="pemdaForm.name"
                                type="text"
                                class="mt-1 block w-full"
                                placeholder="Dinas Koperasi & UKM Kota Bandung"
                                required
                            />
                            <InputError class="mt-1" :message="pemdaForm.errors.name" />
                        </div>

                        <div>
                            <InputLabel for="pemda_email" value="Email Kedinasan" />
                            <TextInput
                                id="pemda_email"
                                v-model="pemdaForm.email"
                                type="email"
                                class="mt-1 block w-full"
                                placeholder="petugas@pemda.go.id"
                                required
                            />
                            <InputError class="mt-1" :message="pemdaForm.errors.email" />
                        </div>

                        <div>
                            <InputLabel for="pemda_phone" value="Nomor Telepon / Kontak Resmi" />
                            <TextInput
                                id="pemda_phone"
                                v-model="pemdaForm.phone"
                                type="text"
                                class="mt-1 block w-full"
                                placeholder="081234567890"
                            />
                            <InputError class="mt-1" :message="pemdaForm.errors.phone" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="pemda_password" value="Password Awal Sementara" />
                                <TextInput
                                    id="pemda_password"
                                    v-model="pemdaForm.password"
                                    type="password"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError class="mt-1" :message="pemdaForm.errors.password" />
                            </div>
                            <div>
                                <InputLabel for="pemda_password_confirmation" value="Konfirmasi Password" />
                                <TextInput
                                    id="pemda_password_confirmation"
                                    v-model="pemdaForm.password_confirmation"
                                    type="password"
                                    class="mt-1 block w-full"
                                    required
                                />
                            </div>
                        </div>

                        <div class="sm:col-span-2 flex justify-end gap-3 pt-2">
                            <SecondaryButton type="button" @click="showPemdaForm = false">
                                Batal
                            </SecondaryButton>
                            <PrimaryButton :disabled="pemdaForm.processing">
                                Simpan &amp; Buat Akun Pemda
                            </PrimaryButton>
                        </div>
                    </form>
                </div>

                <!-- Filter Bar (KSA-39) -->
                <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                    <form @submit.prevent="applyFilters" class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                        <div class="sm:col-span-2">
                            <InputLabel for="search" value="Cari Nama / Email / Telepon" />
                            <TextInput
                                id="search"
                                v-model="filterForm.search"
                                type="text"
                                class="mt-1 block w-full text-sm"
                                placeholder="Ketik nama atau alamat email..."
                            />
                        </div>

                        <div>
                            <InputLabel for="role" value="Filter Peran" />
                            <select
                                id="role"
                                v-model="filterForm.role"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                            >
                                <option value="">Semua Peran</option>
                                <option value="umkm">Pelaku UMKM</option>
                                <option value="mitra">Mitra Industri</option>
                                <option value="pemda">Pemerintah Daerah (Pemda)</option>
                                <option value="admin">Superadmin</option>
                            </select>
                        </div>

                        <div>
                            <InputLabel for="account_status" value="Filter Status Akun" />
                            <select
                                id="account_status"
                                v-model="filterForm.account_status"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                            >
                                <option value="">Semua Status</option>
                                <option value="enabled">Aktif (Enabled)</option>
                                <option value="disabled">Nonaktif (Disabled)</option>
                            </select>
                        </div>

                        <div class="sm:col-span-4 flex justify-end gap-3">
                            <SecondaryButton type="button" @click="resetFilters">
                                Reset
                            </SecondaryButton>
                            <PrimaryButton type="submit">
                                Terapkan Filter
                            </PrimaryButton>
                        </div>
                    </form>
                </div>

                <!-- Tabel Daftar Pengguna (KSA-39) -->
                <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Pengguna
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Peran (Role)
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Status Akun
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Kontak Telepon
                                    </th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Tindakan Administratif
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <template v-if="users.data && users.data.length > 0">
                                    <tr v-for="u in users.data" :key="u.id" class="hover:bg-gray-50">
                                        <td class="px-4 py-3">
                                            <div class="font-medium text-gray-900">{{ u.name }}</div>
                                            <div class="text-xs text-gray-500">{{ u.email }}</div>
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-xs">
                                            <span
                                                class="inline-flex items-center rounded-full px-2.5 py-0.5 font-semibold"
                                                :class="{
                                                    'bg-purple-100 text-purple-800': u.role === 'admin',
                                                    'bg-indigo-100 text-indigo-800': u.role === 'pemda',
                                                    'bg-blue-100 text-blue-800': u.role === 'mitra',
                                                    'bg-teal-100 text-teal-800': u.role === 'umkm',
                                                }"
                                            >
                                                {{ formatRoleLabel(u.role) }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-xs">
                                            <span
                                                class="inline-flex items-center rounded-full px-2.5 py-0.5 font-semibold"
                                                :class="
                                                    u.account_status === 'enabled'
                                                        ? 'bg-emerald-100 text-emerald-800'
                                                        : 'bg-rose-100 text-rose-800'
                                                "
                                            >
                                                {{ u.account_status === 'enabled' ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                            <span
                                                v-if="u.must_change_password"
                                                class="ms-1.5 inline-flex items-center rounded bg-amber-100 px-2 py-0.5 text-[10px] font-medium text-amber-800"
                                            >
                                                Wajib Ganti Sandi
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-xs text-gray-600">
                                            {{ u.phone || '-' }}
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-xs">
                                            <template v-if="u.id !== currentUser?.id">
                                                <button
                                                    type="button"
                                                    @click="toggleUserStatus(u)"
                                                    class="me-3 font-semibold underline"
                                                    :class="
                                                        u.account_status === 'enabled'
                                                            ? 'text-amber-600 hover:text-amber-900'
                                                            : 'text-emerald-600 hover:text-emerald-900'
                                                    "
                                                >
                                                    {{ u.account_status === 'enabled' ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                                <button
                                                    type="button"
                                                    @click="deleteUser(u)"
                                                    class="font-semibold text-rose-600 hover:text-rose-900 underline"
                                                >
                                                    Hapus
                                                </button>
                                            </template>
                                            <span v-else class="italic text-gray-400">
                                                Akun Anda (Superadmin)
                                            </span>
                                        </td>
                                    </tr>
                                </template>
                                <tr v-else>
                                    <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">
                                        Tidak ada pengguna yang ditemukan sesuai kriteria pencarian.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div
                        v-if="users.links && users.links.length > 3"
                        class="flex items-center justify-between border-t border-gray-200 bg-gray-50 px-4 py-3 sm:px-6"
                    >
                        <div class="text-xs text-gray-600">
                            Menampilkan {{ users.from ?? 0 }} sampai {{ users.to ?? 0 }} dari {{ users.total ?? 0 }} pengguna
                        </div>
                        <div class="flex flex-wrap gap-1">
                            <template v-for="(link, idx) in users.links" :key="idx">
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    v-html="link.label"
                                    class="rounded border px-2.5 py-1 text-xs font-medium"
                                    :class="
                                        link.active
                                            ? 'border-indigo-600 bg-indigo-600 text-white'
                                            : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-100'
                                    "
                                />
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
