<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import EvaluationPanelLayout from '@/Layouts/EvaluationPanelLayout.vue';
import StatCard from '@/Components/StatCard.vue';
import StatusBadge from '@/Components/StatusBadge.vue';

const props = defineProps({
    actor: {
        type: Object,
        required: true,
    },
    kpis: {
        type: Array,
        required: true,
    },
    users: {
        type: Array,
        required: true,
    },
    auditLogs: {
        type: Array,
        required: true,
    },
});

const activeSection = ref('pengguna');
const roleFilter = ref('semua');
const searchUser = ref('');
const searchInputRef = ref(null);
const expandedUserId = ref(null);
const showAuditSection = ref(false);
const userList = ref(props.users.map((u) => ({ ...u })));
const pemdaForm = ref({
    name: '',
    email: '',
    unit: 'Dinas Koperasi dan UKM Kota Bandung',
});
const feedbackMessage = ref('');

const navItems = [
    { key: 'pengguna', label: 'Akun Pengguna', badge: '5' },
    { key: 'provisioning', label: 'Buat Akun Pemda' },
    { key: 'audit', label: 'Log Keamanan', badge: '3' },
];

const filteredUsers = computed(() => {
    const q = searchUser.value.trim().toLowerCase();

    return userList.value.filter((u) => {
        const matchesRole = roleFilter.value === 'semua' || u.role === roleFilter.value;
        const matchesQuery =
            !q ||
            u.name.toLowerCase().includes(q) ||
            u.email.toLowerCase().includes(q);

        return matchesRole && matchesQuery;
    });
});

function toggleUserDetail(id) {
    expandedUserId.value = expandedUserId.value === id ? null : id;
}

function handleSelectSection(key) {
    activeSection.value = key;
    if (key === 'audit') {
        showAuditSection.value = true;
    }
}

function toggleAccountStatus(userId) {
    userList.value = userList.value.map((u) => {
        if (u.id !== userId) {
            return u;
        }
        const nextStatus = u.status === 'enabled' ? 'disabled' : 'enabled';
        feedbackMessage.value = `Status akun ${u.name} berhasil diubah menjadi "${nextStatus === 'enabled' ? 'Aktif' : 'Nonaktif'}".`;
        return { ...u, status: nextStatus };
    });
}

function submitPemdaProvisioning() {
    if (!pemdaForm.value.name.trim() || !pemdaForm.value.email.trim()) {
        return;
    }

    userList.value.unshift({
        id: Date.now(),
        name: pemdaForm.value.name,
        email: pemdaForm.value.email,
        role: 'pemda',
        status: 'enabled',
        lastLogin: 'Baru dibuat',
    });

    feedbackMessage.value = `Akun petugas Pemda untuk ${pemdaForm.value.name} berhasil dibuat.`;
    pemdaForm.value.name = '';
    pemdaForm.value.email = '';
}

function handleSlashShortcut(event) {
    const tag = event.target?.tagName;
    const isTyping = tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT' || event.target?.isContentEditable;
    if (!isTyping && event.key === '/') {
        event.preventDefault();
        searchInputRef.value?.focus();
    }
}

onMounted(() => document.addEventListener('keydown', handleSlashShortcut));
onUnmounted(() => document.removeEventListener('keydown', handleSlashShortcut));
</script>

<template>
    <div>
        <Head title="Panel Admin Platform — Evaluasi UI Koneksa" />

        <EvaluationPanelLayout
            active-role="admin"
            role-label="ADMIN"
            :actor-name="actor.name"
            :actor-subtitle="`${actor.email} · ${actor.roleBadge}`"
            page-title="Kelola Pengguna & Keamanan"
            page-subtitle="Atur akses akun pengguna dan buat akun resmi petugas Pemerintah Daerah."
            :nav-items="navItems"
            :active-section="activeSection"
            @select-section="handleSelectSection"
        >
            <template #header-actions>
                <StatusBadge tone="success" dot size="md">
                    Sistem Normal (99,9%)
                </StatusBadge>
            </template>

            <div
                v-if="feedbackMessage"
                role="status"
                class="mb-5 flex items-center justify-between gap-4 rounded-2xl border border-primary-200 bg-primary-50 px-4 py-3 text-xs font-medium text-primary-900"
            >
                <span class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-primary-600" aria-hidden="true"></span>
                    {{ feedbackMessage }}
                </span>
                <button
                    type="button"
                    class="rounded-lg px-2 py-1 text-xs font-semibold text-primary-700 hover:bg-primary-100"
                    @click="feedbackMessage = ''"
                >
                    Tutup
                </button>
            </div>

            <!-- Row 1: 4 Finexy KPI Cards -->
            <section aria-label="Metrik Admin Platform" class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <StatCard
                    v-for="kpi in kpis"
                    :key="kpi.label"
                    :label="kpi.label"
                    :value="kpi.value"
                    :delta="kpi.delta"
                    :tone="kpi.tone"
                />
            </section>

            <!-- Row 2: 7+5 Bento Split (User Management Table + Pemda Provisioning Card) -->
            <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-12">
                <!-- Left Card: Multi-Role User Table (Minimal Columns + Expandable Detail) -->
                <section class="rounded-2xl border border-hairline bg-white p-5 shadow-xs lg:col-span-7">
                    <div class="flex flex-col gap-3 pb-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-base font-semibold text-ink">
                                Daftar Akun Pengguna
                            </h2>
                            <p class="text-xs text-ink-muted">
                                Klik "Rincian" untuk melihat email &amp; waktu login terakhir
                            </p>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <div class="relative flex-1 sm:flex-initial">
                                <label for="admin-user-search" class="sr-only">Cari akun pengguna</label>
                                <svg
                                    class="pointer-events-none absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-ink-muted"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    aria-hidden="true"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m0 0A7.5 7.5 0 1 0 6.1 6.1a7.5 7.5 0 0 0 10.55 10.55Z" />
                                </svg>
                                <input
                                    id="admin-user-search"
                                    ref="searchInputRef"
                                    v-model="searchUser"
                                    type="search"
                                    placeholder="Cari pengguna..."
                                    class="h-9 w-full rounded-full border border-hairline bg-canvas pl-8 pr-7 text-xs text-ink placeholder-neutral-400 focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary-500 sm:w-44"
                                />
                            </div>

                            <div class="inline-flex rounded-full border border-hairline bg-canvas p-0.5" role="group" aria-label="Filter role pengguna">
                                <button
                                    v-for="r in ['semua', 'umkm', 'mitra', 'pemda']"
                                    :key="r"
                                    type="button"
                                    class="h-8 rounded-full px-2.5 text-xs font-medium uppercase transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                                    :class="roleFilter === r ? 'bg-white text-primary-700 shadow-xs' : 'text-ink-muted hover:text-ink'"
                                    @click="roleFilter = r"
                                >
                                    {{ r }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-hairline text-left text-[13px]">
                            <thead class="bg-canvas/80 text-xs font-medium text-ink-muted">
                                <tr>
                                    <th scope="col" class="rounded-l-xl px-4 py-3">Nama Pengguna</th>
                                    <th scope="col" class="px-4 py-3">Role</th>
                                    <th scope="col" class="px-4 py-3">Status</th>
                                    <th scope="col" class="px-4 py-3">Info</th>
                                    <th scope="col" class="rounded-r-xl px-4 py-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-hairline bg-white">
                                <template v-for="u in filteredUsers" :key="u.id">
                                    <tr class="transition hover:bg-canvas/50">
                                        <td class="px-4 py-3.5 font-medium text-ink">
                                            {{ u.name }}
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3.5">
                                            <StatusBadge :tone="u.role === 'umkm' ? 'primary' : u.role === 'mitra' ? 'secondary' : 'success'">
                                                {{ u.role.toUpperCase() }}
                                            </StatusBadge>
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3.5">
                                            <StatusBadge :tone="u.status === 'enabled' ? 'success' : 'danger'" dot>
                                                {{ u.status === 'enabled' ? 'Aktif' : 'Nonaktif' }}
                                            </StatusBadge>
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3.5">
                                            <button
                                                type="button"
                                                class="inline-flex h-7 items-center rounded-full border border-hairline bg-canvas px-2.5 text-xs font-medium text-ink-secondary hover:text-primary-700"
                                                :aria-expanded="expandedUserId === u.id"
                                                @click="toggleUserDetail(u.id)"
                                            >
                                                {{ expandedUserId === u.id ? 'Tutup ▴' : 'Rincian ▾' }}
                                            </button>
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3.5 text-right">
                                            <button
                                                type="button"
                                                class="h-7 rounded-full border px-3 text-xs font-medium transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                                                :class="
                                                    u.status === 'enabled'
                                                        ? 'border-danger/30 bg-white text-danger-dark hover:bg-danger-light'
                                                        : 'border-success/30 bg-white text-success-dark hover:bg-success-light'
                                                "
                                                @click="toggleAccountStatus(u.id)"
                                            >
                                                {{ u.status === 'enabled' ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </td>
                                    </tr>

                                    <tr v-if="expandedUserId === u.id" class="bg-canvas/70">
                                        <td colspan="5" class="px-4 py-2.5 text-xs text-ink-secondary">
                                            <div class="flex flex-wrap items-center gap-4">
                                                <span><strong class="text-ink">Email:</strong> <code class="font-mono text-primary-700">{{ u.email }}</code></span>
                                                <span><strong class="text-ink">Login Terakhir:</strong> {{ u.lastLogin }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                </template>

                                <tr v-if="!filteredUsers.length">
                                    <td colspan="5" class="px-4 py-8 text-center text-xs text-ink-muted">
                                        Tidak ada akun cocok dengan filter.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Right Card: Pemda Provisioning + Collapsible Audit Logs -->
                <section class="flex flex-col justify-between rounded-2xl border border-hairline bg-white p-5 shadow-xs lg:col-span-5">
                    <div>
                        <h2 class="text-base font-semibold text-ink">
                            Tambah Petugas Pemda
                        </h2>
                        <p class="mt-0.5 text-xs text-ink-muted">
                            Buat akun resmi baru untuk petugas Dinas Kota Bandung
                        </p>

                        <form class="mt-4 space-y-3" @submit.prevent="submitPemdaProvisioning">
                            <div>
                                <label for="pemda-officer-name" class="block text-xs font-medium text-ink-secondary">
                                    Nama Petugas
                                </label>
                                <input
                                    id="pemda-officer-name"
                                    v-model="pemdaForm.name"
                                    type="text"
                                    required
                                    placeholder="Contoh: Raka Pratama"
                                    class="mt-1 block h-9 w-full rounded-xl border-hairline text-[13px] placeholder-neutral-400 focus:border-primary-500 focus:ring-primary-500"
                                />
                            </div>

                            <div>
                                <label for="pemda-officer-email" class="block text-xs font-medium text-ink-secondary">
                                    Email Dinas Resmi
                                </label>
                                <input
                                    id="pemda-officer-email"
                                    v-model="pemdaForm.email"
                                    type="email"
                                    required
                                    placeholder="petugas@bandung.go.id"
                                    class="mt-1 block h-9 w-full rounded-xl border-hairline text-[13px] placeholder-neutral-400 focus:border-primary-500 focus:ring-primary-500"
                                />
                            </div>

                            <div>
                                <label for="pemda-unit" class="block text-xs font-medium text-ink-secondary">
                                    Instansi
                                </label>
                                <input
                                    id="pemda-unit"
                                    v-model="pemdaForm.unit"
                                    type="text"
                                    readonly
                                    class="mt-1 block h-9 w-full rounded-xl border-hairline bg-canvas text-xs text-ink-muted"
                                />
                            </div>

                            <button
                                type="submit"
                                class="inline-flex h-9 w-full items-center justify-center rounded-full bg-primary-600 px-4 text-xs font-medium text-white shadow-xs transition hover:bg-primary-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                            >
                                Buat Akun Pemda
                            </button>
                        </form>
                    </div>

                    <!-- Progressive Disclosure: Audit Security Logs -->
                    <div class="mt-5 border-t border-hairline pt-3">
                        <button
                            type="button"
                            class="flex w-full items-center justify-between rounded-xl bg-canvas px-3 py-2 text-xs font-medium text-ink-secondary transition hover:bg-neutral-100 hover:text-ink"
                            :aria-expanded="showAuditSection"
                            @click="showAuditSection = !showAuditSection"
                        >
                            <span>Riwayat Log Keamanan ({{ auditLogs.length }})</span>
                            <span class="font-mono text-[11px] text-primary-600">
                                {{ showAuditSection ? 'Sembunyikan ▴' : 'Lihat Log ▾' }}
                            </span>
                        </button>

                        <ul v-if="showAuditSection" class="mt-2.5 space-y-2">
                            <li
                                v-for="log in auditLogs"
                                :key="log.event"
                                class="rounded-xl border border-hairline bg-canvas/50 p-3 text-xs"
                            >
                                <div class="flex items-center justify-between gap-2">
                                    <span class="font-mono text-[11px] text-ink-muted">{{ log.time }}</span>
                                    <StatusBadge :tone="log.severity.toLowerCase()" dot>
                                        {{ log.severity }}
                                    </StatusBadge>
                                </div>
                                <p class="mt-1 font-medium text-ink">{{ log.event }}</p>
                                <p class="mt-0.5 font-mono text-[11px] text-ink-muted">{{ log.actor }}</p>
                            </li>
                        </ul>
                    </div>
                </section>
            </div>
        </EvaluationPanelLayout>
    </div>
</template>
