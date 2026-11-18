<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive, ref } from 'vue';

const props = defineProps({
    logs: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    modules: {
        type: Array,
        default: () => [],
    },
});

const filterForm = reactive({
    search: props.filters.search || '',
    module: props.filters.module || '',
    action: props.filters.action || '',
    actor_role: props.filters.actor_role || '',
    date_from: props.filters.date_from || '',
    date_to: props.filters.date_to || '',
});

const expandedRowId = ref(null);

const toggleMetadata = (id) => {
    expandedRowId.value = expandedRowId.value === id ? null : id;
};

const applyFilters = () => {
    router.get(
        route('admin.audit_logs.index'),
        {
            search: filterForm.search || undefined,
            module: filterForm.module || undefined,
            action: filterForm.action || undefined,
            actor_role: filterForm.actor_role || undefined,
            date_from: filterForm.date_from || undefined,
            date_to: filterForm.date_to || undefined,
        },
        {
            preserveState: true,
            replace: true,
        },
    );
};

const resetFilters = () => {
    filterForm.search = '';
    filterForm.module = '';
    filterForm.action = '';
    filterForm.actor_role = '';
    filterForm.date_from = '';
    filterForm.date_to = '';
    router.get(route('admin.audit_logs.index'));
};

const formatDate = (dateStr) => {
    if (!dateStr) return '-';
    return new Date(dateStr).toLocaleString('id-ID', {
        year: 'numeric',
        month: 'short',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
    });
};
</script>

<template>
    <Head title="Audit Trail Sistem" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Audit Trail &amp; Log Aktivitas Sistem
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Penelusuran riwayat tindakan kritis dan perubahan administratif KONEKSA (Hanya Superadmin).
                    </p>
                </div>
                <span class="inline-flex items-center rounded-md bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700 ring-1 ring-inset ring-indigo-700/10">
                    Total Log: {{ logs.total ?? 0 }}
                </span>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <!-- Filter Panel -->
                <div class="bg-white p-6 shadow-sm sm:rounded-lg border border-gray-200">
                    <form @submit.prevent="applyFilters" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-6">
                        <div class="lg:col-span-2">
                            <InputLabel for="search" value="Cari Deskripsi / Aktor / IP" />
                            <TextInput
                                id="search"
                                v-model="filterForm.search"
                                type="text"
                                class="mt-1 block w-full text-sm"
                                placeholder="Nama, email, IP, atau deskripsi..."
                            />
                        </div>

                        <div>
                            <InputLabel for="module" value="Modul" />
                            <select
                                id="module"
                                v-model="filterForm.module"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                            >
                                <option value="">Semua Modul</option>
                                <option v-for="mod in modules" :key="mod" :value="mod">
                                    {{ mod.toUpperCase() }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <InputLabel for="actor_role" value="Peran Aktor" />
                            <select
                                id="actor_role"
                                v-model="filterForm.actor_role"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                            >
                                <option value="">Semua Peran</option>
                                <option value="admin">Superadmin</option>
                                <option value="pemda">Pemda</option>
                                <option value="mitra">Mitra Industri</option>
                                <option value="umkm">UMKM</option>
                            </select>
                        </div>

                        <div>
                            <InputLabel for="date_from" value="Dari Tanggal" />
                            <TextInput
                                id="date_from"
                                v-model="filterForm.date_from"
                                type="date"
                                class="mt-1 block w-full text-sm"
                            />
                        </div>

                        <div>
                            <InputLabel for="date_to" value="Sampai Tanggal" />
                            <TextInput
                                id="date_to"
                                v-model="filterForm.date_to"
                                type="date"
                                class="mt-1 block w-full text-sm"
                            />
                        </div>

                        <div class="sm:col-span-2 lg:col-span-6 flex items-center justify-end gap-3 pt-2">
                            <SecondaryButton type="button" @click="resetFilters">
                                Reset Filter
                            </SecondaryButton>
                            <PrimaryButton type="submit">
                                Terapkan Filter
                            </PrimaryButton>
                        </div>
                    </form>
                </div>

                <!-- Tabel Audit Logs -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg border border-gray-200">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Waktu
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Aktor Pelaksana
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Modul &amp; Aksi
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Deskripsi Aktivitas
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        IP Address
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Detail
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <template v-if="logs.data && logs.data.length > 0">
                                    <template v-for="log in logs.data" :key="log.id">
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="whitespace-nowrap px-4 py-3 text-xs text-gray-600">
                                                {{ formatDate(log.created_at) }}
                                            </td>
                                            <td class="px-4 py-3 text-sm">
                                                <div v-if="log.actor" class="font-medium text-gray-900">
                                                    {{ log.actor.name }}
                                                </div>
                                                <div v-else class="italic text-gray-400">
                                                    Sistem / Terhapus
                                                </div>
                                                <div class="mt-0.5 flex items-center gap-1.5">
                                                    <span
                                                        v-if="log.actor_role"
                                                        class="inline-flex items-center rounded bg-gray-100 px-1.5 py-0.5 text-[10px] font-semibold uppercase text-gray-700"
                                                    >
                                                        {{ log.actor_role }}
                                                    </span>
                                                    <span v-if="log.actor?.email" class="text-xs text-gray-500">
                                                        {{ log.actor.email }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3 text-xs">
                                                <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-0.5 font-semibold text-indigo-700 uppercase">
                                                    {{ log.module }}
                                                </span>
                                                <div class="mt-1 font-mono text-gray-600">
                                                    {{ log.action }}
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-800">
                                                {{ log.description }}
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3 font-mono text-xs text-gray-500">
                                                {{ log.ip_address || '-' }}
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3 text-right text-xs">
                                                <button
                                                    v-if="log.metadata"
                                                    type="button"
                                                    @click="toggleMetadata(log.id)"
                                                    class="font-medium text-indigo-600 hover:text-indigo-900 underline"
                                                >
                                                    {{ expandedRowId === log.id ? 'Tutup' : 'Lihat Metadata' }}
                                                </button>
                                                <span v-else class="text-gray-400">-</span>
                                            </td>
                                        </tr>
                                        <tr v-if="expandedRowId === log.id && log.metadata" class="bg-gray-50">
                                            <td colspan="6" class="px-6 py-3">
                                                <div class="text-xs font-semibold text-gray-600 mb-1">
                                                    Konteks Metadata (Target: {{ log.auditable_type || '-' }} #{{ log.auditable_id || '-' }}):
                                                </div>
                                                <pre class="overflow-x-auto rounded bg-gray-900 p-3 text-xs text-gray-100 font-mono">{{ JSON.stringify(log.metadata, null, 2) }}</pre>
                                            </td>
                                        </tr>
                                    </template>
                                </template>
                                <tr v-else>
                                    <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">
                                        Tidak ada catatan audit trail yang sesuai dengan filter pencarian.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div
                        v-if="logs.links && logs.links.length > 3"
                        class="flex items-center justify-between border-t border-gray-200 bg-gray-50 px-4 py-3 sm:px-6"
                    >
                        <div class="text-xs text-gray-600">
                            Menampilkan {{ logs.from ?? 0 }} sampai {{ logs.to ?? 0 }} dari {{ logs.total ?? 0 }} entri
                        </div>
                        <div class="flex flex-wrap gap-1">
                            <template v-for="(link, idx) in logs.links" :key="idx">
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    v-html="link.label"
                                    class="rounded border px-2.5 py-1 text-xs font-medium transition-colors"
                                    :class="
                                        link.active
                                            ? 'border-indigo-600 bg-indigo-600 text-white'
                                            : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-100'
                                    "
                                />
                                <span
                                    v-else
                                    v-html="link.label"
                                    class="rounded border border-gray-200 bg-gray-100 px-2.5 py-1 text-xs text-gray-400"
                                />
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
