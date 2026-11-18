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
    verificationQueue: {
        type: Array,
        required: true,
    },
    programs: {
        type: Array,
        required: true,
    },
    sectorSummary: {
        type: Array,
        required: true,
    },
});

const activeSection = ref('verifikasi');
const searchQueue = ref('');
const statusFilter = ref('Semua');
const searchInputRef = ref(null);
const expandedQueueId = ref(null);
const queueList = ref(props.verificationQueue.map((item) => ({ ...item })));
const feedbackMessage = ref('');

const navItems = [
    { key: 'verifikasi', label: 'Verifikasi UMKM', badge: '3' },
    { key: 'program', label: 'Program Pembinaan', badge: '2' },
    { key: 'rekap', label: 'Statistik Sektor' },
];

const filteredQueue = computed(() => {
    const q = searchQueue.value.trim().toLowerCase();

    return queueList.value.filter((row) => {
        const matchesStatus = statusFilter.value === 'Semua' || row.status === statusFilter.value;
        const matchesQuery =
            !q ||
            row.businessName.toLowerCase().includes(q) ||
            row.owner.toLowerCase().includes(q) ||
            row.nib.includes(q);

        return matchesStatus && matchesQuery;
    });
});

function toggleQueueDetail(id) {
    expandedQueueId.value = expandedQueueId.value === id ? null : id;
}

function queueTone(status) {
    if (status === 'Disetujui') {
        return 'success';
    }
    if (status === 'Perlu Revisi') {
        return 'danger';
    }
    return 'warning';
}

function reviewVerification(id, nextStatus) {
    queueList.value = queueList.value.map((row) =>
        row.id === id ? { ...row, status: nextStatus } : row,
    );
    feedbackMessage.value = `Status verifikasi usaha #${id} berhasil diperbarui menjadi "${nextStatus}".`;
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
        <Head title="Panel Pemerintah Daerah — Evaluasi UI Koneksa" />

        <EvaluationPanelLayout
            active-role="pemda"
            role-label="PEMDA"
            :actor-name="actor.name"
            :actor-subtitle="`${actor.officer} · ${actor.jurisdiction}`"
            page-title="Pembinaan & Verifikasi Daerah"
            page-subtitle="Verifikasi pendaftaran UMKM baru dan pantau kuota program pelatihan Kota Bandung."
            :nav-items="navItems"
            :active-section="activeSection"
            @select-section="activeSection = $event"
        >
            <template #header-actions>
                <button
                    type="button"
                    class="inline-flex h-9 items-center rounded-full border border-hairline bg-white px-3.5 text-xs font-medium text-ink shadow-xs transition hover:bg-neutral-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                    @click="feedbackMessage = 'Rekapitulasi UMKM Kota Bandung sedang diunduh dalam format CSV.'"
                >
                    Unduh Rekap CSV
                </button>
                <button
                    type="button"
                    class="inline-flex h-9 items-center rounded-full bg-primary-600 px-4 text-xs font-medium text-white shadow-xs transition hover:bg-primary-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                    @click="feedbackMessage = 'Form pembuatan program pembinaan baru siap dibuka.'"
                >
                    + Buat Program
                </button>
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
            <section aria-label="Metrik Pemerintah Daerah" class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <StatCard
                    v-for="kpi in kpis"
                    :key="kpi.label"
                    :label="kpi.label"
                    :value="kpi.value"
                    :delta="kpi.delta"
                    :tone="kpi.tone"
                />
            </section>

            <!-- Row 2: 7+5 Bento Split (Sector Distribution + Active Programs) -->
            <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-12">
                <!-- Left Card: Sector Distribution Capsule Bars -->
                <section class="rounded-2xl border border-hairline bg-white p-5 shadow-xs lg:col-span-7">
                    <div class="flex items-center justify-between gap-2">
                        <div>
                            <h2 class="text-base font-semibold text-ink">
                                Sebaran Sektor UMKM
                            </h2>
                            <p class="text-xs text-ink-muted">
                                Proporsi usaha terverifikasi per sektor di Kota Bandung
                            </p>
                        </div>
                        <span class="rounded-full border border-hairline bg-canvas px-3 py-1 text-xs font-medium text-ink-secondary">
                            Q4 2026
                        </span>
                    </div>

                    <div class="mt-5 space-y-4">
                        <div v-for="sec in sectorSummary" :key="sec.sector">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-medium text-ink">{{ sec.sector }}</span>
                                <span class="font-mono text-xs text-ink-secondary">
                                    <strong class="text-ink">{{ sec.verified }}</strong> / {{ sec.count }} UMKM ({{ sec.share }}%)
                                </span>
                            </div>
                            <div class="mt-2 h-2.5 w-full overflow-hidden rounded-full bg-canvas ring-1 ring-inset ring-hairline">
                                <div
                                    class="h-full rounded-full bg-gradient-to-r from-primary-600 to-secondary-500"
                                    :style="{ width: `${sec.share}%` }"
                                ></div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Right Card: Kuota Program Pembinaan -->
                <section class="rounded-2xl border border-hairline bg-white p-5 shadow-xs lg:col-span-5">
                    <div class="flex items-center justify-between gap-2">
                        <div>
                            <h2 class="text-base font-semibold text-ink">
                                Kuota Program Pembinaan
                            </h2>
                            <p class="text-xs text-ink-muted">
                                Keterisian peserta pelatihan aktif
                            </p>
                        </div>
                        <StatusBadge tone="success" dot>{{ programs.length }} Program</StatusBadge>
                    </div>

                    <div class="mt-4 space-y-3">
                        <article
                            v-for="prog in programs"
                            :key="prog.id"
                            class="rounded-xl border border-hairline bg-canvas/60 p-4"
                        >
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="text-xs font-semibold text-ink">{{ prog.title }}</h3>
                                <StatusBadge :tone="prog.status === 'Published' ? 'success' : 'neutral'" dot>
                                    {{ prog.status === 'Published' ? 'Dibuka' : 'Penuh' }}
                                </StatusBadge>
                            </div>
                            <div class="mt-3 flex items-center justify-between text-xs text-ink-secondary">
                                <span>Peserta terdaftar</span>
                                <span class="font-mono font-semibold text-ink">{{ prog.quotaFilled }} / {{ prog.quotaMax }}</span>
                            </div>
                            <div class="mt-1.5 h-2 w-full overflow-hidden rounded-full bg-white ring-1 ring-inset ring-hairline">
                                <div
                                    class="h-full rounded-full bg-gradient-to-r from-success to-secondary-500"
                                    :style="{ width: `${Math.round((prog.quotaFilled / prog.quotaMax) * 100)}%` }"
                                ></div>
                            </div>
                        </article>
                    </div>
                </section>
            </div>

            <!-- Row 3: Verification Queue Table with Progressive Disclosure -->
            <section class="mt-6 rounded-2xl border border-hairline bg-white p-5 shadow-xs">
                <div class="flex flex-col gap-3 pb-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-ink">
                            Antrean Verifikasi UMKM
                        </h2>
                        <p class="text-xs text-ink-muted">
                            Klik "Rincian" untuk melihat nomor NIB &amp; nama pemilik sebelum menyetujui.
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <div class="relative flex-1 sm:flex-initial">
                            <label for="pemda-queue-search" class="sr-only">Cari UMKM atau NIB</label>
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
                                id="pemda-queue-search"
                                ref="searchInputRef"
                                v-model="searchQueue"
                                type="search"
                                placeholder="Cari usaha atau NIB..."
                                class="h-9 w-full rounded-full border border-hairline bg-canvas pl-8 pr-8 text-xs text-ink placeholder-neutral-400 focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary-500 sm:w-52"
                            />
                            <kbd
                                class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 rounded-full border border-hairline bg-white px-1.5 py-0.5 font-mono text-[10px] text-ink-muted"
                                aria-hidden="true"
                            >
                                /
                            </kbd>
                        </div>

                        <div class="inline-flex flex-wrap rounded-full border border-hairline bg-canvas p-0.5" role="group" aria-label="Filter status verifikasi">
                            <button
                                v-for="st in ['Semua', 'Menunggu Review', 'Disetujui', 'Perlu Revisi']"
                                :key="st"
                                type="button"
                                class="h-8 rounded-full px-3 text-xs font-medium transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                                :class="statusFilter === st ? 'bg-white text-primary-700 shadow-xs' : 'text-ink-muted hover:text-ink'"
                                @click="statusFilter = st"
                            >
                                {{ st }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-hairline text-left text-[13px]">
                        <thead class="bg-canvas/80 text-xs font-medium text-ink-muted">
                            <tr>
                                <th scope="col" class="rounded-l-xl px-4 py-3">Nama Usaha</th>
                                <th scope="col" class="px-4 py-3">Kecamatan</th>
                                <th scope="col" class="px-4 py-3">Status</th>
                                <th scope="col" class="px-4 py-3">Rincian Berkas</th>
                                <th scope="col" class="rounded-r-xl px-4 py-3 text-right">Keputusan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-hairline bg-white">
                            <template v-for="row in filteredQueue" :key="row.id">
                                <tr class="transition hover:bg-canvas/50">
                                    <td class="px-4 py-3.5 font-medium text-ink">
                                        {{ row.businessName }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3.5 text-xs text-ink-secondary">
                                        {{ row.district }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3.5">
                                        <StatusBadge :tone="queueTone(row.status)" dot>
                                            {{ row.status }}
                                        </StatusBadge>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3.5">
                                        <button
                                            type="button"
                                            class="inline-flex h-7 items-center rounded-full border border-hairline bg-canvas px-3 text-xs font-medium text-ink-secondary hover:text-primary-700"
                                            :aria-expanded="expandedQueueId === row.id"
                                            @click="toggleQueueDetail(row.id)"
                                        >
                                            {{ expandedQueueId === row.id ? 'Tutup ▴' : 'Lihat NIB ▾' }}
                                        </button>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3.5 text-right">
                                        <div class="inline-flex items-center gap-1.5">
                                            <button
                                                type="button"
                                                class="h-7 rounded-full border border-hairline bg-white px-3 text-xs font-medium text-ink-secondary hover:bg-canvas hover:text-ink focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                                                @click="reviewVerification(row.id, 'Perlu Revisi')"
                                            >
                                                Revisi
                                            </button>
                                            <button
                                                type="button"
                                                class="h-7 rounded-full bg-primary-600 px-3 text-xs font-medium text-white shadow-xs hover:bg-primary-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                                                @click="reviewVerification(row.id, 'Disetujui')"
                                            >
                                                Setujui
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Expandable Administrative Details -->
                                <tr v-if="expandedQueueId === row.id" class="bg-canvas/70">
                                    <td colspan="5" class="px-4 py-3 text-xs text-ink-secondary">
                                        <div class="flex flex-wrap items-center gap-4">
                                            <span><strong class="text-ink">Pemilik:</strong> {{ row.owner }}</span>
                                            <span><strong class="text-ink">Sektor:</strong> {{ row.category }}</span>
                                            <span><strong class="text-ink">Nomor NIB:</strong> <code class="font-mono text-primary-700">{{ row.nib }}</code></span>
                                            <span><strong class="text-ink">Diajukan:</strong> {{ row.submittedAt }}</span>
                                        </div>
                                    </td>
                                </tr>
                            </template>

                            <tr v-if="!filteredQueue.length">
                                <td colspan="5" class="px-4 py-8 text-center text-xs text-ink-muted">
                                    Tidak ada berkas verifikasi cocok dengan filter.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </EvaluationPanelLayout>
    </div>
</template>
