<script setup>
import { computed, ref } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatCard from '@/Components/StatCard.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    incoming: {
        type: Array,
        default: () => [],
    },
    outgoing: {
        type: Array,
        default: () => [],
    },
    cooperation_types: {
        type: Array,
        default: () => [],
    },
    available_partners: {
        type: Array,
        default: () => [],
    },
    available_businesses: {
        type: Array,
        default: () => [],
    },
    user_role: {
        type: String,
        required: true,
    },
});

const page = usePage();
const activeTab = ref('incoming'); // 'incoming' | 'outgoing'
const searchQuery = ref('');
const statusFilter = ref('all'); // 'all' | 'pending' | 'accepted' | 'rejected'

// Modal Review / Respon Proposal
const reviewingProposal = ref(null);
const responseForm = useForm({
    decision: 'accept',
    response_note: '',
});

// Modal Buat Proposal Baru
const showCreateModal = ref(false);
const createForm = useForm({
    title: '',
    description: '',
    cooperation_type_id: props.cooperation_types.length > 0 ? props.cooperation_types[0].id : '',
    business_id: props.available_businesses.length > 0 ? props.available_businesses[0].id : '',
    partner_profile_id: props.available_partners.length > 0 ? props.available_partners[0].id : '',
});

const activeList = computed(() => activeTab.value === 'incoming' ? props.incoming : props.outgoing);

const filteredProposals = computed(() => {
    const q = searchQuery.value.trim().toLowerCase();
    const sf = statusFilter.value;

    return activeList.value.filter((item) => {
        const matchesStatus = sf === 'all' || item.status === sf;
        const targetName = activeTab.value === 'incoming'
            ? (props.user_role === 'umkm' ? item.partner?.organization_name : item.business?.name)
            : (props.user_role === 'umkm' ? item.partner?.organization_name : item.business?.name);

        const matchesQuery = !q
            || item.title.toLowerCase().includes(q)
            || (targetName && targetName.toLowerCase().includes(q))
            || (item.cooperation_type?.name && item.cooperation_type.name.toLowerCase().includes(q));

        return matchesStatus && matchesQuery;
    });
});

// KPI Metrik
const stats = computed(() => {
    const list = activeList.value;
    return {
        total: list.length,
        pending: list.filter((p) => p.status === 'pending').length,
        accepted: list.filter((p) => ['accepted', 'ongoing', 'completed'].includes(p.status)).length,
        rejected: list.filter((p) => p.status === 'rejected').length,
    };
});

function openReviewModal(proposal) {
    reviewingProposal.value = proposal;
    responseForm.reset();
    responseForm.decision = 'accept';
}

function submitResponse(decision) {
    if (!reviewingProposal.value) return;

    responseForm.decision = decision;
    responseForm.post(route('proposals.respond', reviewingProposal.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            reviewingProposal.value = null;
            responseForm.reset();
        },
    });
}

function openCreateModal() {
    createForm.reset();
    if (props.cooperation_types.length > 0) {
        createForm.cooperation_type_id = props.cooperation_types[0].id;
    }
    if (props.available_businesses.length > 0) {
        createForm.business_id = props.available_businesses[0].id;
    }
    if (props.available_partners.length > 0) {
        createForm.partner_profile_id = props.available_partners[0].id;
    }
    showCreateModal.value = true;
}

function submitCreateProposal() {
    createForm.post(route('proposals.store'), {
        preserveScroll: true,
        onSuccess: () => {
            showCreateModal.value = false;
            createForm.reset();
            activeTab.value = 'outgoing'; // otomatis alihkan ke tab terkirim
        },
    });
}
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Proposal Kolaborasi Bisnis — KONEKSA" />

        <div class="py-8 bg-canvas min-h-screen">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Flash Feedback Banner -->
                <div
                    v-if="$page.props.flash?.success"
                    class="rounded-xl border border-success/30 bg-success-light p-4 text-xs font-semibold text-success-dark flex items-center justify-between"
                >
                    <span>{{ $page.props.flash.success }}</span>
                </div>

                <!-- Header Halaman -->
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-2xl font-bold tracking-tight text-ink">
                                Proposal Kolaborasi Bisnis (FR-12)
                            </h1>
                            <span class="rounded-full bg-primary-50 px-2.5 py-0.5 text-xs font-semibold text-primary-700">
                                KSA-88
                            </span>
                        </div>
                        <p class="text-xs text-ink-muted mt-1">
                            Kelola pengajuan, peninjauan, serta respon persetujuan proposal kerja sama antara UMKM dan Mitra Industri.
                        </p>
                    </div>

                    <div class="flex items-center gap-2.5">
                        <PrimaryButton
                            type="button"
                            class="inline-flex h-10 items-center gap-2 rounded-xl text-xs font-semibold"
                            @click="openCreateModal"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Buat Proposal Baru</span>
                        </PrimaryButton>
                    </div>
                </div>

                <!-- Bento StatCards -->
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 lg:gap-4">
                    <StatCard
                        label="Total Proposal"
                        :value="String(stats.total)"
                        tone="primary"
                        :badge="activeTab === 'incoming' ? 'Masuk' : 'Keluar'"
                    />
                    <StatCard
                        label="Menunggu Respon"
                        :value="String(stats.pending)"
                        tone="warning"
                        badge="Perlu Aksi"
                    />
                    <StatCard
                        label="Diterima / Berjalan"
                        :value="String(stats.accepted)"
                        tone="success"
                        badge="Disetujui"
                    />
                    <StatCard
                        label="Ditolak"
                        :value="String(stats.rejected)"
                        tone="danger"
                        badge="Selesai"
                    />
                </div>

                <!-- Tab Navigasi & Filter Panel -->
                <div class="rounded-2xl border border-hairline bg-white p-5 shadow-xs space-y-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-b border-hairline pb-3">
                        <!-- Tab Switcher -->
                        <div class="inline-flex rounded-xl bg-canvas p-1" role="tablist">
                            <button
                                type="button"
                                role="tab"
                                :aria-selected="activeTab === 'incoming'"
                                class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-xs font-medium transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                                :class="activeTab === 'incoming' ? 'bg-white text-ink shadow-xs font-semibold' : 'text-ink-secondary hover:text-ink'"
                                @click="activeTab = 'incoming'"
                            >
                                <span>Proposal Masuk</span>
                                <span class="rounded-full bg-primary-100 px-2 py-0.2 text-[10px] font-bold text-primary-700">
                                    {{ incoming.length }}
                                </span>
                            </button>
                            <button
                                type="button"
                                role="tab"
                                :aria-selected="activeTab === 'outgoing'"
                                class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-xs font-medium transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                                :class="activeTab === 'outgoing' ? 'bg-white text-ink shadow-xs font-semibold' : 'text-ink-secondary hover:text-ink'"
                                @click="activeTab = 'outgoing'"
                            >
                                <span>Proposal Terkirim</span>
                                <span class="rounded-full bg-neutral-200 px-2 py-0.2 text-[10px] font-bold text-ink-secondary">
                                    {{ outgoing.length }}
                                </span>
                            </button>
                        </div>

                        <!-- Filter Status & Cari -->
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                            <select
                                v-model="statusFilter"
                                class="h-9 rounded-xl border border-hairline bg-canvas px-3 text-xs font-medium text-ink focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary-500"
                            >
                                <option value="all">Semua Status</option>
                                <option value="pending">Menunggu Respon</option>
                                <option value="accepted">Diterima</option>
                                <option value="rejected">Ditolak</option>
                            </select>

                            <input
                                v-model="searchQuery"
                                type="search"
                                placeholder="Cari judul atau pihak lawan..."
                                class="h-9 rounded-xl border border-hairline bg-canvas px-3 text-xs text-ink placeholder-ink-muted focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary-500 sm:w-60"
                            />
                        </div>
                    </div>

                    <!-- Proposal Cards List -->
                    <div v-if="filteredProposals.length > 0" class="space-y-3">
                        <article
                            v-for="item in filteredProposals"
                            :key="item.id"
                            class="rounded-xl border border-hairline bg-canvas/40 p-4 sm:p-5 transition hover:bg-canvas hover:border-neutral-300"
                        >
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div class="space-y-1.5 min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="text-sm sm:text-base font-semibold text-ink">
                                            {{ item.title }}
                                        </h3>
                                        <StatusBadge :tone="item.status_tone" dot size="sm">
                                            {{ item.status_label }}
                                        </StatusBadge>
                                        <span class="rounded-full bg-primary-50 px-2.5 py-0.5 text-[11px] font-medium text-primary-700">
                                            {{ item.cooperation_type?.name || 'Kolaborasi Bisnis' }}
                                        </span>
                                    </div>

                                    <!-- Partner / Business Info -->
                                    <p class="text-xs text-ink-secondary flex flex-wrap items-center gap-2">
                                        <span class="font-medium text-ink">
                                            {{ activeTab === 'incoming' ? 'Dari:' : 'Tujuan:' }}
                                        </span>
                                        <span class="font-semibold text-primary-700">
                                            {{ user_role === 'umkm' ? (item.partner?.organization_name || 'Mitra Industri') : (item.business?.name || 'UMKM Daerah') }}
                                        </span>
                                        <span class="text-ink-muted">·</span>
                                        <span class="text-ink-muted">Pengaju: {{ item.initiator?.name }}</span>
                                        <span class="text-ink-muted">·</span>
                                        <span class="text-ink-muted">{{ item.created_at_human }}</span>
                                    </p>

                                    <!-- Description snippet -->
                                    <p class="text-xs text-ink-secondary line-clamp-2 max-w-3xl pt-1">
                                        {{ item.description }}
                                    </p>

                                    <!-- Response note display if available -->
                                    <div
                                        v-if="item.response_note"
                                        class="mt-2 rounded-lg bg-white p-2.5 border border-hairline text-[11px] text-ink-secondary"
                                    >
                                        <span class="font-semibold text-ink">Catatan Tanggapan:</span> {{ item.response_note }}
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex shrink-0 items-center gap-2 pt-2 sm:pt-0">
                                    <button
                                        type="button"
                                        class="inline-flex h-8 items-center rounded-lg border border-hairline bg-white px-3 text-xs font-medium text-ink-secondary hover:text-ink hover:bg-neutral-50 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                                        @click="openReviewModal(item)"
                                    >
                                        Rincian &amp; Respon
                                    </button>

                                    <!-- Quick Accept / Reject if incoming & pending -->
                                    <template v-if="item.can_respond">
                                        <button
                                            type="button"
                                            class="inline-flex h-8 items-center rounded-lg border border-hairline bg-white px-2.5 text-xs font-semibold text-danger-dark hover:bg-danger-light transition"
                                            @click="openReviewModal(item); responseForm.decision = 'reject'"
                                        >
                                            Tolak
                                        </button>
                                        <button
                                            type="button"
                                            class="inline-flex h-8 items-center rounded-lg bg-primary-600 px-3 text-xs font-semibold text-white shadow-xs hover:bg-primary-500 transition"
                                            @click="openReviewModal(item); responseForm.decision = 'accept'"
                                        >
                                            Terima
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </article>
                    </div>

                    <!-- Empty State -->
                    <div v-else class="rounded-xl border border-dashed border-hairline p-10 text-center">
                        <p class="text-sm font-semibold text-ink">Tidak ada proposal kolaborasi</p>
                        <p class="text-xs text-ink-muted mt-1">
                            {{ activeTab === 'incoming' ? 'Belum ada proposal masuk dari pihak lain.' : 'Anda belum pernah mengajukan proposal kolaborasi.' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Review / Respon Proposal (FR-12.2 / BR-05) -->
        <Modal :show="reviewingProposal !== null" max-width="lg" @close="reviewingProposal = null">
            <div v-if="reviewingProposal" class="p-6 space-y-4">
                <div class="flex items-start justify-between gap-3 border-b border-hairline pb-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <StatusBadge :tone="reviewingProposal.status_tone" dot size="sm">
                                {{ reviewingProposal.status_label }}
                            </StatusBadge>
                            <span class="rounded-full bg-primary-50 px-2 py-0.2 text-[10px] font-semibold text-primary-700">
                                {{ reviewingProposal.cooperation_type?.name }}
                            </span>
                        </div>
                        <h3 class="mt-2 text-base font-bold text-ink">
                            {{ reviewingProposal.title }}
                        </h3>
                    </div>
                    <button
                        type="button"
                        class="text-ink-muted hover:text-ink text-sm p-1"
                        @click="reviewingProposal = null"
                    >
                        ✕
                    </button>
                </div>

                <!-- Informasi Pihak Terlibat -->
                <div class="grid grid-cols-2 gap-3 rounded-xl bg-canvas p-3 text-xs">
                    <div>
                        <p class="text-ink-muted font-medium">UMKM Mitra</p>
                        <p class="font-semibold text-ink mt-0.5">{{ reviewingProposal.business?.name || '-' }}</p>
                        <p class="text-[11px] text-ink-secondary">{{ reviewingProposal.business?.category_name }} · Kec. {{ reviewingProposal.business?.district }}</p>
                    </div>
                    <div>
                        <p class="text-ink-muted font-medium">Mitra Industri</p>
                        <p class="font-semibold text-ink mt-0.5">{{ reviewingProposal.partner?.organization_name || '-' }}</p>
                        <p class="text-[11px] text-ink-secondary">{{ reviewingProposal.partner?.industry }}</p>
                    </div>
                </div>

                <!-- Rincian Penawaran -->
                <div>
                    <h4 class="text-xs font-semibold text-ink uppercase tracking-wider text-ink-muted">Rincian Penawaran:</h4>
                    <p class="mt-1 text-xs leading-relaxed text-ink bg-canvas/50 rounded-xl p-3 border border-hairline whitespace-pre-line">
                        {{ reviewingProposal.description }}
                    </p>
                </div>

                <!-- Jika Sudah Direspon -->
                <div v-if="reviewingProposal.response_note" class="rounded-xl border border-hairline bg-white p-3 text-xs space-y-1">
                    <p class="font-semibold text-ink">Catatan Tanggapan:</p>
                    <p class="text-ink-secondary">{{ reviewingProposal.response_note }}</p>
                </div>

                <!-- Form Respon (Hanya jika penerima & status pending) - BR-05 -->
                <div v-if="reviewingProposal.can_respond" class="border-t border-hairline pt-3 space-y-3">
                    <h4 class="text-xs font-semibold text-ink">Beri Keputusan Proposal Masuk</h4>
                    <div>
                        <label for="response-note" class="block text-xs font-medium text-ink-muted mb-1">
                            Catatan atau Alasan Respon (Opsional):
                        </label>
                        <textarea
                            id="response-note"
                            v-model="responseForm.response_note"
                            rows="2"
                            placeholder="Tuliskan catatan tindak lanjut atau alasan persetujuan/penolakan..."
                            class="w-full rounded-xl border border-hairline bg-canvas p-2.5 text-xs text-ink placeholder-ink-muted focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary-500"
                        ></textarea>
                        <InputError :message="responseForm.errors.response_note" class="mt-1" />
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-1">
                        <DangerButton
                            type="button"
                            :disabled="responseForm.processing"
                            @click="submitResponse('reject')"
                        >
                            Tolak Proposal
                        </DangerButton>
                        <PrimaryButton
                            type="button"
                            :disabled="responseForm.processing"
                            @click="submitResponse('accept')"
                        >
                            Terima Kerja Sama
                        </PrimaryButton>
                    </div>
                </div>
                <div v-else class="flex justify-end pt-2 border-t border-hairline">
                    <SecondaryButton @click="reviewingProposal = null">Tutup</SecondaryButton>
                </div>
            </div>
        </Modal>

        <!-- Modal Buat Proposal Baru (FR-12.1) -->
        <Modal :show="showCreateModal" max-width="lg" @close="showCreateModal = false">
            <div class="p-6 space-y-4">
                <div class="flex items-start justify-between gap-3 border-b border-hairline pb-3">
                    <div>
                        <span class="rounded-full bg-primary-50 px-2.5 py-0.5 text-xs font-semibold text-primary-700">
                            Pengajuan Kolaborasi
                        </span>
                        <h3 class="mt-2 text-base font-bold text-ink">
                            Buat Proposal Kolaborasi Baru
                        </h3>
                    </div>
                    <button
                        type="button"
                        class="text-ink-muted hover:text-ink text-sm p-1"
                        @click="showCreateModal = false"
                    >
                        ✕
                    </button>
                </div>

                <form class="space-y-4" @submit.prevent="submitCreateProposal">
                    <!-- Target Penerima -->
                    <div v-if="user_role === 'mitra'">
                        <label for="target-business" class="block text-xs font-semibold text-ink mb-1">
                            Pilih Target UMKM
                        </label>
                        <select
                            id="target-business"
                            v-model="createForm.business_id"
                            class="h-10 w-full rounded-xl border border-hairline bg-canvas px-3 text-xs text-ink focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary-500"
                            required
                        >
                            <option v-for="biz in available_businesses" :key="biz.id" :value="biz.id">
                                {{ biz.name }} (Kec. {{ biz.district }})
                            </option>
                        </select>
                        <InputError :message="createForm.errors.business_id" class="mt-1" />
                    </div>

                    <div v-else-if="user_role === 'umkm'">
                        <label for="target-partner" class="block text-xs font-semibold text-ink mb-1">
                            Pilih Mitra Industri Tujuan
                        </label>
                        <select
                            id="target-partner"
                            v-model="createForm.partner_profile_id"
                            class="h-10 w-full rounded-xl border border-hairline bg-canvas px-3 text-xs text-ink focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary-500"
                            required
                        >
                            <option v-for="partner in available_partners" :key="partner.id" :value="partner.id">
                                {{ partner.organization_name }} ({{ partner.industry }})
                            </option>
                        </select>
                        <InputError :message="createForm.errors.partner_profile_id" class="mt-1" />
                    </div>

                    <!-- Jenis Kerja Sama -->
                    <div>
                        <label for="coop-type" class="block text-xs font-semibold text-ink mb-1">
                            Jenis Kerja Sama
                        </label>
                        <select
                            id="coop-type"
                            v-model="createForm.cooperation_type_id"
                            class="h-10 w-full rounded-xl border border-hairline bg-canvas px-3 text-xs text-ink focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary-500"
                            required
                        >
                            <option v-for="type in cooperation_types" :key="type.id" :value="type.id">
                                {{ type.name }}
                            </option>
                        </select>
                        <InputError :message="createForm.errors.cooperation_type_id" class="mt-1" />
                    </div>

                    <!-- Judul Proposal -->
                    <div>
                        <label for="proposal-title" class="block text-xs font-semibold text-ink mb-1">
                            Judul Proposal Kolaborasi
                        </label>
                        <input
                            id="proposal-title"
                            v-model="createForm.title"
                            type="text"
                            placeholder="Contoh: Kemitraan Pasokan Biji Kopi untuk Jaringan Kafe..."
                            class="h-10 w-full rounded-xl border border-hairline bg-canvas px-3 text-xs text-ink placeholder-ink-muted focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary-500"
                            required
                        />
                        <InputError :message="createForm.errors.title" class="mt-1" />
                    </div>

                    <!-- Deskripsi Rincian Penawaran -->
                    <div>
                        <label for="proposal-desc" class="block text-xs font-semibold text-ink mb-1">
                            Rincian Penawaran &amp; Skema Kerja Sama
                        </label>
                        <textarea
                            id="proposal-desc"
                            v-model="createForm.description"
                            rows="4"
                            placeholder="Jelaskan secara detail maksud kerja sama, skema pasokan atau pendampingan, serta nilai tambah yang ditawarkan..."
                            class="w-full rounded-xl border border-hairline bg-canvas p-3 text-xs text-ink placeholder-ink-muted focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary-500"
                            required
                        ></textarea>
                        <InputError :message="createForm.errors.description" class="mt-1" />
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-hairline">
                        <SecondaryButton @click="showCreateModal = false">Batal</SecondaryButton>
                        <PrimaryButton type="submit" :disabled="createForm.processing">
                            Kirim Proposal
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

