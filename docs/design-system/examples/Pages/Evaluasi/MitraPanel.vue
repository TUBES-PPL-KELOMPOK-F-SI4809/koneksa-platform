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
    verifiedBusinesses: {
        type: Array,
        required: true,
    },
    partnerships: {
        type: Array,
        required: true,
    },
    digitalOfferings: {
        type: Array,
        required: true,
    },
});

const activeSection = ref('direktori');
const searchUmkm = ref('');
const selectedCategory = ref('Semua');
const searchInputRef = ref(null);
const expandedBizId = ref(null);
const showFocusAreas = ref(false);
const proposalModalTarget = ref(null);
const proposalTitle = ref('');
const proposalScope = ref('Adopsi Teknologi Digital');
const feedbackMessage = ref('');

const navItems = [
    { key: 'direktori', label: 'Cari UMKM', badge: '3' },
    { key: 'monitoring', label: 'Progres Mitra', badge: '3' },
    { key: 'solusi', label: 'Solusi Digital', badge: '2' },
    { key: 'profil', label: 'Profil Mitra' },
];

const filteredBusinesses = computed(() => {
    const q = searchUmkm.value.trim().toLowerCase();

    return props.verifiedBusinesses.filter((biz) => {
        const matchesCat = selectedCategory.value === 'Semua' || biz.category === selectedCategory.value;
        const matchesQuery =
            !q ||
            biz.name.toLowerCase().includes(q) ||
            biz.district.toLowerCase().includes(q);

        return matchesCat && matchesQuery;
    });
});

function toggleBizDetail(id) {
    expandedBizId.value = expandedBizId.value === id ? null : id;
}

function stageTone(stage) {
    if (stage === 'Selesai') {
        return 'success';
    }
    if (stage === 'Sedang Berjalan') {
        return 'info';
    }
    return 'neutral';
}

function openProposalModal(business) {
    proposalModalTarget.value = business;
    proposalTitle.value = `Kolaborasi ${proposalScope.value} — ${business.name}`;
}

function submitProposal() {
    if (!proposalTitle.value.trim()) {
        return;
    }

    feedbackMessage.value = `Proposal "${proposalTitle.value}" berhasil dikirim ke ${proposalModalTarget.value.name}.`;
    proposalModalTarget.value = null;
    proposalTitle.value = '';
}

function handleSlashShortcut(event) {
    const tag = event.target?.tagName;
    const isTyping = tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT' || event.target?.isContentEditable;
    if (!isTyping && event.key === '/') {
        event.preventDefault();
        searchInputRef.value?.focus();
    }
    if (event.key === 'Escape' && proposalModalTarget.value) {
        proposalModalTarget.value = null;
    }
}

onMounted(() => document.addEventListener('keydown', handleSlashShortcut));
onUnmounted(() => document.removeEventListener('keydown', handleSlashShortcut));
</script>

<template>
    <div>
        <Head title="Panel Mitra Industri — Evaluasi UI Koneksa" />

        <EvaluationPanelLayout
            active-role="mitra"
            role-label="MITRA"
            :actor-name="actor.organization"
            :actor-subtitle="`PIC: ${actor.name}`"
            page-title="Portal Kemitraan Industri"
            page-subtitle="Temukan UMKM terverifikasi dan pantau kerja sama secara praktis."
            :nav-items="navItems"
            :active-section="activeSection"
            @select-section="activeSection = $event"
        >
            <template #header-actions>
                <span class="inline-flex h-9 items-center rounded-full border border-hairline bg-white px-3.5 text-xs font-medium text-ink-secondary shadow-xs">
                    Fokus: {{ actor.focusAreas[0] }}
                </span>
                <button
                    type="button"
                    class="inline-flex h-9 items-center rounded-full bg-primary-600 px-4 text-xs font-medium text-white shadow-xs transition hover:bg-primary-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                    @click="feedbackMessage = 'Form publikasi solusi teknologi siap digunakan.'"
                >
                    + Tambah Solusi
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
            <section aria-label="Metrik Mitra Industri" class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <StatCard
                    v-for="kpi in kpis"
                    :key="kpi.label"
                    :label="kpi.label"
                    :value="kpi.value"
                    :delta="kpi.delta"
                    :tone="kpi.tone"
                />
            </section>

            <!-- Row 2: 7+5 Bento Split (Curated UMKM Directory + Digital Offerings) -->
            <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-12">
                <!-- Left Card: UMKM Siap Bermitra (Minimal Info + Expandable Details) -->
                <section class="rounded-2xl border border-hairline bg-white p-5 shadow-xs lg:col-span-7">
                    <div class="flex flex-col gap-3 pb-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-base font-semibold text-ink">
                                UMKM Siap Bermitra
                            </h2>
                            <p class="text-xs text-ink-muted">
                                Klik "Rincian" untuk melihat legalitas &amp; kebutuhan usaha
                            </p>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <div class="relative flex-1 sm:flex-initial">
                                <label for="mitra-umkm-search" class="sr-only">Cari UMKM atau Kecamatan</label>
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
                                    id="mitra-umkm-search"
                                    ref="searchInputRef"
                                    v-model="searchUmkm"
                                    type="search"
                                    placeholder="Cari UMKM..."
                                    class="h-9 w-full rounded-full border border-hairline bg-canvas pl-8 pr-7 text-xs text-ink placeholder-neutral-400 focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary-500 sm:w-44"
                                />
                            </div>

                            <div>
                                <label for="mitra-umkm-cat" class="sr-only">Kategori Usaha</label>
                                <select
                                    id="mitra-umkm-cat"
                                    v-model="selectedCategory"
                                    class="h-9 rounded-full border border-hairline bg-canvas px-3 text-xs font-medium text-ink focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary-500"
                                >
                                    <option value="Semua">Semua Sektor</option>
                                    <option value="Kuliner">Kuliner</option>
                                    <option value="Fesyen & Kriya">Fesyen &amp; Kriya</option>
                                    <option value="Kerajinan">Kerajinan</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <article
                            v-for="biz in filteredBusinesses"
                            :key="biz.id"
                            class="rounded-xl border border-hairline bg-canvas/50 p-4 transition hover:bg-canvas"
                        >
                            <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="text-sm font-semibold text-ink">{{ biz.name }}</h3>
                                        <StatusBadge tone="success" dot>Terverifikasi</StatusBadge>
                                    </div>
                                    <p class="mt-0.5 text-xs text-ink-muted">
                                        {{ biz.category }} · Kec. {{ biz.district }}
                                    </p>
                                </div>

                                <div class="flex shrink-0 items-center gap-2">
                                    <button
                                        type="button"
                                        class="inline-flex h-8 items-center rounded-full border border-hairline bg-white px-3 text-xs font-medium text-ink-secondary transition hover:text-ink"
                                        :aria-expanded="expandedBizId === biz.id"
                                        @click="toggleBizDetail(biz.id)"
                                    >
                                        {{ expandedBizId === biz.id ? 'Tutup ▴' : 'Rincian ▾' }}
                                    </button>
                                    <button
                                        type="button"
                                        class="inline-flex h-8 items-center rounded-full bg-primary-600 px-3.5 text-xs font-medium text-white shadow-xs transition hover:bg-primary-500"
                                        @click="openProposalModal(biz)"
                                    >
                                        Ajukan Kerjasama
                                    </button>
                                </div>
                            </div>

                            <!-- Progressive Disclosure: Legalitas & Needs -->
                            <div
                                v-if="expandedBizId === biz.id"
                                class="mt-3 flex flex-wrap items-center justify-between gap-2 border-t border-hairline pt-3 text-xs text-ink-secondary"
                            >
                                <span><strong class="text-ink">Legalitas:</strong> {{ biz.legalStatus }}</span>
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <span class="text-ink-muted">Kebutuhan:</span>
                                    <span
                                        v-for="need in biz.needs"
                                        :key="need"
                                        class="rounded-full border border-hairline bg-white px-2.5 py-0.5 text-[11px] font-medium text-primary-700"
                                    >
                                        {{ need }}
                                    </span>
                                </div>
                            </div>
                        </article>

                        <div v-if="!filteredBusinesses.length" class="p-8 text-center text-xs text-ink-muted">
                            Tidak ada UMKM cocok dengan filter saat ini.
                        </div>
                    </div>
                </section>

                <!-- Right Card: Solusi Digital & Preferensi -->
                <section class="flex flex-col justify-between rounded-2xl border border-hairline bg-white p-5 shadow-xs lg:col-span-5">
                    <div>
                        <div class="flex items-center justify-between gap-2">
                            <div>
                                <h2 class="text-base font-semibold text-ink">
                                    Katalog Solusi Digital
                                </h2>
                                <p class="text-xs text-ink-muted">
                                    Layanan teknologi yang ditawarkan ke UMKM
                                </p>
                            </div>
                            <StatusBadge tone="secondary" dot>Aktif</StatusBadge>
                        </div>

                        <div class="mt-4 space-y-2.5">
                            <article
                                v-for="off in digitalOfferings"
                                :key="off.title"
                                class="rounded-xl border border-hairline bg-canvas/60 p-3.5"
                            >
                                <div class="flex items-center justify-between gap-2">
                                    <span class="rounded-full bg-secondary-50 px-2.5 py-0.5 text-[11px] font-medium text-secondary-700">
                                        {{ off.category }}
                                    </span>
                                    <span class="font-mono text-xs font-semibold text-primary-700">
                                        {{ off.interestedCount }} minat
                                    </span>
                                </div>
                                <h3 class="mt-2 text-xs font-semibold text-ink">
                                    {{ off.title }}
                                </h3>
                            </article>
                        </div>
                    </div>

                    <div class="mt-4 border-t border-hairline pt-3">
                        <button
                            type="button"
                            class="flex w-full items-center justify-between rounded-xl bg-canvas px-3 py-2 text-xs font-medium text-ink-secondary transition hover:bg-neutral-100 hover:text-ink"
                            :aria-expanded="showFocusAreas"
                            @click="showFocusAreas = !showFocusAreas"
                        >
                            <span>Fokus Kolaborasi Korporasi ({{ actor.focusAreas.length }})</span>
                            <span class="font-mono text-[11px] text-primary-600">
                                {{ showFocusAreas ? 'Tutup ▴' : 'Lihat ▾' }}
                            </span>
                        </button>

                        <div v-if="showFocusAreas" class="mt-2.5 flex flex-wrap gap-1.5">
                            <span
                                v-for="area in actor.focusAreas"
                                :key="area"
                                class="rounded-full border border-hairline bg-white px-3 py-1 text-xs font-medium text-ink-secondary"
                            >
                                {{ area }}
                            </span>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Row 3: Active Partnerships Table -->
            <section class="mt-6 rounded-2xl border border-hairline bg-white p-5 shadow-xs">
                <div class="pb-4">
                    <h2 class="text-base font-semibold text-ink">
                        Monitoring Kemitraan Berjalan
                    </h2>
                    <p class="text-xs text-ink-muted">
                        Perkembangan kolaborasi dengan UMKM binaan
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-hairline text-left text-[13px]">
                        <thead class="bg-canvas/80 text-xs font-medium text-ink-muted">
                            <tr>
                                <th scope="col" class="rounded-l-xl px-4 py-3">UMKM</th>
                                <th scope="col" class="px-4 py-3">Program Kolaborasi</th>
                                <th scope="col" class="px-4 py-3">Progres</th>
                                <th scope="col" class="rounded-r-xl px-4 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-hairline bg-white">
                            <tr v-for="item in partnerships" :key="item.title" class="hover:bg-canvas/50">
                                <td class="whitespace-nowrap px-4 py-3.5 font-medium text-ink">
                                    {{ item.business }}
                                </td>
                                <td class="px-4 py-3.5">
                                    <p class="text-ink">{{ item.title }}</p>
                                    <p class="text-[11px] text-ink-muted">Diperbarui {{ item.updatedAt }}</p>
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="h-2 w-24 overflow-hidden rounded-full bg-canvas ring-1 ring-inset ring-hairline">
                                            <div
                                                class="h-full rounded-full bg-gradient-to-r from-secondary-500 to-primary-600"
                                                :style="{ width: `${item.progress}%` }"
                                            ></div>
                                        </div>
                                        <span class="font-mono text-xs font-semibold text-ink">{{ item.progress }}%</span>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3.5">
                                    <StatusBadge :tone="stageTone(item.stage)" dot>
                                        {{ item.stage }}
                                    </StatusBadge>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Proposal Modal -->
            <div
                v-if="proposalModalTarget"
                class="fixed inset-0 z-50 flex items-center justify-center bg-ink-deep/50 p-4 backdrop-blur-[1px]"
                role="dialog"
                aria-modal="true"
                aria-labelledby="mitra-proposal-modal"
                @click.self="proposalModalTarget = null"
            >
                <form
                    class="w-full max-w-lg rounded-2xl border border-hairline bg-white p-6 shadow-lg"
                    @submit.prevent="submitProposal"
                >
                    <h3 id="mitra-proposal-modal" class="text-base font-semibold text-ink">
                        Ajukan Kerjasama ke {{ proposalModalTarget.name }}
                    </h3>
                    <p class="mt-1 text-xs text-ink-muted">
                        Isi topik kolaborasi yang ingin ditawarkan kepada pelaku UMKM.
                    </p>

                    <div class="mt-4 space-y-4">
                        <div>
                            <label for="proposal-scope" class="block text-xs font-medium text-ink-secondary">
                                Bidang Kolaborasi
                            </label>
                            <select
                                id="proposal-scope"
                                v-model="proposalScope"
                                class="mt-1 block h-10 w-full rounded-xl border-hairline text-sm focus:border-primary-500 focus:ring-primary-500"
                            >
                                <option>Adopsi Teknologi Digital</option>
                                <option>Rantai Pasok &amp; Distribusi</option>
                                <option>Pembiayaan &amp; Pendampingan</option>
                            </select>
                        </div>

                        <div>
                            <label for="proposal-title" class="block text-xs font-medium text-ink-secondary">
                                Judul Proposal Kerja Sama
                            </label>
                            <input
                                id="proposal-title"
                                v-model="proposalTitle"
                                type="text"
                                required
                                class="mt-1 block h-10 w-full rounded-xl border-hairline text-sm focus:border-primary-500 focus:ring-primary-500"
                            />
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-2">
                        <button
                            type="button"
                            class="h-9 rounded-full border border-hairline bg-white px-4 text-xs font-medium text-ink hover:bg-canvas"
                            @click="proposalModalTarget = null"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="h-9 rounded-full bg-primary-600 px-4 text-xs font-medium text-white shadow-xs hover:bg-primary-500"
                        >
                            Kirim Proposal
                        </button>
                    </div>
                </form>
            </div>
        </EvaluationPanelLayout>
    </div>
</template>
