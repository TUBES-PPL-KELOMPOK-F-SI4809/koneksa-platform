<script setup>
import { computed, ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import KoneksaLogo from '@/Components/KoneksaLogo.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import StatCard from '@/Components/StatCard.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    business: {
        type: Object,
        required: true,
    },
    cooperation_types: {
        type: Array,
        default: () => [],
    },
});

const expandedProductId = ref(null);
const showProposalModal = ref(false);
const proposalSuccessMessage = ref('');
const productSearch = ref('');

const proposalForm = useForm({
    business_id: props.business.id,
    partner_profile_id: null,
    cooperation_type_id: '',
    title: '',
    description: '',
});

function toggleProductVariants(productId) {
    expandedProductId.value = expandedProductId.value === productId ? null : productId;
}

function formatRupiah(amount) {
    if (amount === null || amount === undefined || isNaN(amount)) {
        return '-';
    }
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(amount);
}

const filteredProducts = computed(() => {
    const q = productSearch.value.trim().toLowerCase();
    if (!q) {
        return props.business.products || [];
    }
    return (props.business.products || []).filter((product) =>
        product.name.toLowerCase().includes(q) ||
        (product.description && product.description.toLowerCase().includes(q))
    );
});

const verificationTone = computed(() => {
    switch (props.business.verification?.status) {
        case 'approved':
            return 'success';
        case 'pending':
            return 'warning';
        case 'rejected':
            return 'danger';
        default:
            return 'neutral';
    }
});

function openProposalAction() {
    showProposalModal.value = true;
    proposalSuccessMessage.value = '';
    proposalForm.clearErrors();
    proposalForm.business_id = props.business.id;
    if (!proposalForm.title) {
        proposalForm.title = `Proposal Kerja Sama dengan ${props.business.name}`;
    }
    if (!proposalForm.cooperation_type_id && props.cooperation_types.length > 0) {
        proposalForm.cooperation_type_id = props.cooperation_types[0].id;
    }
}

function submitProposal() {
    proposalForm.post(route('proposals.store'), {
        preserveScroll: true,
        onSuccess: () => {
            proposalSuccessMessage.value = `Proposal kerja sama berhasil diajukan kepada ${props.business.name}. Anda dapat memantau proses review di menu Proposal.`;
            proposalForm.reset('description');
        },
    });
}
</script>

<template>
    <div class="min-h-screen bg-canvas font-sans text-ink antialiased">
        <Head :title="`${business.name} — Profil Publik UMKM`" />

        <!-- Public Navigation Header -->
        <header class="sticky top-0 z-40 border-b border-hairline bg-white/90 backdrop-blur-md">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
                <div class="flex items-center gap-6">
                    <Link href="/" class="flex items-center gap-2 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 rounded-lg">
                        <KoneksaLogo class="h-8 w-auto" />
                    </Link>
                    <nav class="hidden md:flex items-center gap-4 text-xs font-medium text-ink-secondary">
                        <Link href="/" class="hover:text-ink transition">Beranda</Link>
                        <span class="text-hairline">/</span>
                        <span class="text-ink font-semibold">Direktori UMKM</span>
                    </nav>
                </div>

                <div class="flex items-center gap-3 text-xs">
                    <template v-if="$page.props.auth?.user">
                        <Link
                            :href="route('dashboard')"
                            class="inline-flex h-9 items-center rounded-xl bg-canvas px-3.5 font-medium text-ink hover:bg-neutral-200 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                        >
                            Ke Dashboard ({{ $page.props.auth.user.name }})
                        </Link>
                    </template>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="inline-flex h-9 items-center rounded-xl px-3.5 font-medium text-ink-secondary hover:text-ink transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                        >
                            Masuk
                        </Link>
                        <Link
                            :href="route('register')"
                            class="inline-flex h-9 items-center rounded-xl bg-primary-600 px-3.5 font-medium text-white shadow-xs hover:bg-primary-500 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                        >
                            Daftar Mitra / UMKM
                        </Link>
                    </template>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 space-y-8">
            <!-- Hero / Business Header Profile Card -->
            <section class="rounded-2xl border border-hairline bg-white p-6 sm:p-8 shadow-xs">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                    <div class="flex flex-col sm:flex-row items-start gap-5">
                        <!-- Logo / Avatar -->
                        <div class="relative flex h-20 w-20 shrink-0 items-center justify-center rounded-2xl border border-hairline bg-gradient-to-tr from-primary-600 to-secondary-500 text-2xl font-bold text-white shadow-sm overflow-hidden">
                            <img
                                v-if="business.logo_path"
                                :src="business.logo_path"
                                :alt="`Logo ${business.name}`"
                                class="h-full w-full object-cover"
                            />
                            <span v-else>{{ business.name ? business.name.charAt(0).toUpperCase() : 'U' }}</span>
                        </div>

                        <!-- Info Header -->
                        <div class="space-y-2">
                            <div class="flex flex-wrap items-center gap-2.5">
                                <h1 class="text-2xl sm:text-3xl font-bold text-ink tracking-tight">
                                    {{ business.name }}
                                </h1>
                                <!-- Status Verifikasi UMKM (FR-11.2) -->
                                <StatusBadge :tone="verificationTone" dot size="md">
                                    {{ business.verification?.label || 'Belum Terverifikasi' }}
                                </StatusBadge>
                            </div>

                            <p class="text-xs sm:text-sm font-medium text-ink-secondary flex flex-wrap items-center gap-2">
                                <span class="rounded-full bg-primary-50 px-2.5 py-0.5 text-xs font-semibold text-primary-700">
                                    {{ business.category_name || 'UMKM Daerah' }}
                                </span>
                                <span>·</span>
                                <span class="inline-flex items-center gap-1 text-ink-muted">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    Kec. {{ business.district || 'Kota Bandung' }}
                                    <template v-if="business.subdistrict">, Kel. {{ business.subdistrict }}</template>
                                </span>
                            </p>

                            <p class="max-w-2xl text-sm leading-relaxed text-ink-secondary pt-1">
                                {{ business.description || 'Pelaku UMKM lokal terdaftar di platform KONEKSA.' }}
                            </p>
                        </div>
                    </div>

                    <!-- Action Button: Pengajuan Proposal Kerja Sama (FR-11.2) -->
                    <div class="shrink-0 pt-2 lg:pt-0">
                        <template v-if="business.proposal_action?.is_owner">
                            <Link
                                :href="route('business-profile.edit')"
                                class="inline-flex h-11 items-center gap-2 rounded-xl border border-hairline bg-white px-5 text-sm font-semibold text-ink shadow-xs hover:bg-neutral-50 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                            >
                                <svg class="h-4 w-4 text-ink-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                                Kelola Profil Usaha Saya
                            </Link>
                        </template>
                        <template v-else>
                            <button
                                type="button"
                                data-testid="proposal-action-btn"
                                class="inline-flex h-11 items-center gap-2.5 rounded-xl bg-primary-600 px-6 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                                @click="openProposalAction"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                <span>Ajukan Proposal Kerja Sama</span>
                            </button>
                        </template>
                    </div>
                </div>
            </section>

            <!-- Bento Grid: Performa & Kebutuhan Usaha (FR-11.1) -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
                <!-- Kebutuhan Kolaborasi & Usaha (7 cols) -->
                <section class="rounded-2xl border border-hairline bg-white p-6 shadow-xs lg:col-span-7 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-3 border-b border-hairline pb-4">
                            <div>
                                <h2 class="text-base font-semibold text-ink">
                                    Kebutuhan Kerja Sama &amp; Usaha
                                </h2>
                                <p class="text-xs text-ink-muted">
                                    Peluang kolaborasi yang dibuka oleh UMKM untuk mitra industri
                                </p>
                            </div>
                            <span class="rounded-full bg-secondary-50 px-2.5 py-0.5 text-xs font-semibold text-secondary-700">
                                Kemitraan
                            </span>
                        </div>

                        <div class="mt-4 space-y-4">
                            <div v-if="business.business_needs" class="rounded-xl border border-hairline bg-canvas/60 p-4">
                                <p class="text-xs font-medium uppercase tracking-wider text-ink-muted">Rincian Kebutuhan:</p>
                                <p class="mt-1 text-sm leading-relaxed text-ink font-medium whitespace-pre-line">
                                    {{ business.business_needs }}
                                </p>
                            </div>
                            <div v-else class="rounded-xl border border-dashed border-hairline p-6 text-center text-xs text-ink-muted">
                                Pelaku UMKM belum mencantumkan rincian kebutuhan kerja sama secara spesifik.
                            </div>

                            <!-- Tag Kebutuhan Kolaborasi -->
                            <div v-if="business.needs_list && business.needs_list.length > 0">
                                <p class="text-xs font-medium text-ink-muted mb-2">Fokus Kolaborasi:</p>
                                <div class="flex flex-wrap gap-2">
                                    <span
                                        v-for="(need, idx) in business.needs_list"
                                        :key="idx"
                                        class="inline-flex items-center gap-1 rounded-full border border-primary-200 bg-primary-50 px-3 py-1 text-xs font-medium text-primary-700"
                                    >
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        {{ need }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-hairline flex flex-wrap items-center justify-between gap-3 text-xs text-ink-muted">
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="h-4 w-4 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Kemitraan difasilitasi melalui verifikasi administratif platform KONEKSA.
                        </span>
                        <button
                            v-if="business.proposal_action?.can_submit_proposal"
                            type="button"
                            class="text-xs font-semibold text-primary-600 hover:text-primary-700 underline"
                            @click="openProposalAction"
                        >
                            Kirim Proposal Sekarang &rarr;
                        </button>
                    </div>
                </section>

                <!-- Info Performa Bisnis Publik (5 cols) -->
                <section class="rounded-2xl border border-hairline bg-white p-6 shadow-xs lg:col-span-5 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-3 border-b border-hairline pb-4">
                            <div>
                                <h2 class="text-base font-semibold text-ink">
                                    Info Performa Usaha
                                </h2>
                                <p class="text-xs text-ink-muted">
                                    Indikator publik performa operasional UMKM
                                </p>
                            </div>
                            <span class="inline-flex items-center gap-1 text-xs font-medium text-success-dark">
                                <span class="h-2 w-2 rounded-full bg-success"></span>
                                Aktif
                            </span>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-3">
                            <div class="rounded-xl border border-hairline bg-canvas/50 p-4">
                                <p class="text-xs text-ink-muted">Katalog Aktif</p>
                                <p class="mt-1 text-2xl font-bold text-ink">
                                    {{ business.performance?.active_products_count ?? 0 }}
                                </p>
                                <p class="mt-0.5 text-[11px] text-ink-secondary">Produk siap pasok</p>
                            </div>

                            <div class="rounded-xl border border-hairline bg-canvas/50 p-4">
                                <p class="text-xs text-ink-muted">Total Varian</p>
                                <p class="mt-1 text-2xl font-bold text-ink">
                                    {{ business.performance?.total_variants_count ?? 0 }}
                                </p>
                                <p class="mt-0.5 text-[11px] text-ink-secondary">Varian produk</p>
                            </div>

                            <div class="rounded-xl border border-hairline bg-canvas/50 p-4">
                                <p class="text-xs text-ink-muted">Pesanan Sukses</p>
                                <p class="mt-1 text-2xl font-bold text-ink">
                                    {{ business.performance?.completed_sales_count ?? 0 }}
                                </p>
                                <p class="mt-0.5 text-[11px] text-ink-secondary">Transaksi tercatat</p>
                            </div>

                            <div class="rounded-xl border border-hairline bg-canvas/50 p-4">
                                <p class="text-xs text-ink-muted">Bergabung</p>
                                <p class="mt-1 text-base font-bold text-ink">
                                    {{ business.performance?.joined_since || 'Terdaftar' }}
                                </p>
                                <p class="mt-0.5 text-[11px] text-ink-secondary">Mitra KONEKSA</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 rounded-xl bg-canvas p-3 text-[11px] text-ink-muted">
                        Data performa di atas diverifikasi melalui aktivitas operasional kasir &amp; pencatatan berkala di platform.
                    </div>
                </section>
            </div>

            <!-- Katalog Produk & Varian (FR-11.1) -->
            <section class="rounded-2xl border border-hairline bg-white p-6 shadow-xs space-y-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-hairline pb-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-lg font-bold text-ink">
                                Katalog Produk &amp; Varian
                            </h2>
                            <span class="rounded-full bg-canvas px-2.5 py-0.5 text-xs font-semibold text-ink-secondary">
                                {{ filteredProducts.length }} Produk
                            </span>
                        </div>
                        <p class="text-xs text-ink-muted">
                            Katalog produk aktif yang siap dipasok atau dikerjasamakan
                        </p>
                    </div>

                    <div class="w-full sm:w-64">
                        <label for="catalog-search" class="sr-only">Cari produk</label>
                        <input
                            id="catalog-search"
                            v-model="productSearch"
                            type="search"
                            placeholder="Cari produk usaha..."
                            class="h-9 w-full rounded-xl border border-hairline bg-canvas px-3 text-xs text-ink placeholder-ink-muted focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary-500"
                        />
                    </div>
                </div>

                <!-- Product Grid -->
                <div v-if="filteredProducts.length > 0" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <article
                        v-for="product in filteredProducts"
                        :key="product.id"
                        class="rounded-xl border border-hairline bg-canvas/40 p-5 transition hover:bg-white hover:shadow-xs flex flex-col justify-between"
                    >
                        <div class="space-y-3">
                            <!-- Product Image or Placeholder -->
                            <div class="aspect-video w-full rounded-lg bg-neutral-100 overflow-hidden border border-hairline flex items-center justify-center">
                                <img
                                    v-if="product.image_path"
                                    :src="product.image_path"
                                    :alt="product.name"
                                    class="h-full w-full object-cover"
                                />
                                <div v-else class="text-center p-4">
                                    <svg class="h-8 w-8 text-neutral-400 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    <span class="text-[11px] font-medium text-ink-muted">Katalog {{ product.unit || 'Unit' }}</span>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-base font-semibold text-ink">{{ product.name }}</h3>
                                <p class="text-xs text-ink-muted line-clamp-2 mt-1">
                                    {{ product.description || 'Tidak ada deskripsi tambahan.' }}
                                </p>
                            </div>

                            <!-- Price & Unit -->
                            <div class="flex items-baseline justify-between pt-1">
                                <div>
                                    <p class="text-[11px] text-ink-muted">Harga Jual</p>
                                    <p class="text-sm font-bold text-primary-700">
                                        <template v-if="product.min_price !== null">
                                            {{ formatRupiah(product.min_price) }}
                                            <template v-if="product.max_price && product.max_price > product.min_price">
                                                - {{ formatRupiah(product.max_price) }}
                                            </template>
                                        </template>
                                        <template v-else>-</template>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[11px] text-ink-muted">Satuan</p>
                                    <p class="text-xs font-semibold text-ink">{{ product.unit || 'Pcs' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Progressive Disclosure: Varian Produk -->
                        <div class="mt-4 pt-3 border-t border-hairline">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-ink-muted">
                                    {{ product.variants ? product.variants.length : 0 }} varian tersedia
                                </span>
                                <button
                                    type="button"
                                    class="inline-flex h-7 items-center rounded-lg border border-hairline bg-white px-2.5 text-xs font-medium text-ink-secondary hover:text-ink hover:bg-neutral-50 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                                    :aria-expanded="expandedProductId === product.id"
                                    @click="toggleProductVariants(product.id)"
                                >
                                    {{ expandedProductId === product.id ? 'Tutup Varian ▴' : 'Rincian Varian ▾' }}
                                </button>
                            </div>

                            <div v-if="expandedProductId === product.id" class="mt-3 space-y-2 pt-2 border-t border-hairline text-xs">
                                <div
                                    v-for="variant in product.variants"
                                    :key="variant.id"
                                    class="flex items-center justify-between rounded-lg bg-white p-2 border border-hairline"
                                >
                                    <div>
                                        <span class="font-medium text-ink">{{ variant.variant_name }}</span>
                                        <span class="text-[11px] text-ink-muted block font-mono">{{ variant.variant_key }}</span>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-semibold text-primary-700 block">{{ formatRupiah(variant.selling_price) }}</span>
                                        <span class="text-[11px] text-ink-muted">Stok: {{ variant.stock_quantity }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>

                <div v-else class="rounded-xl border border-dashed border-hairline p-10 text-center">
                    <p class="text-sm font-semibold text-ink">Tidak ada produk ditemukan</p>
                    <p class="text-xs text-ink-muted mt-1">UMKM belum mempublikasikan produk aktif atau kata kunci pencarian tidak cocok.</p>
                </div>
            </section>

            <!-- Informasi Kontak & Legalitas -->
            <section class="rounded-2xl border border-hairline bg-white p-6 shadow-xs">
                <h2 class="text-base font-semibold text-ink border-b border-hairline pb-3">
                    Informasi Lokasi &amp; Kontak Resmi
                </h2>
                <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                    <div class="rounded-xl bg-canvas p-4">
                        <p class="text-ink-muted font-medium">Alamat Lengkap</p>
                        <p class="mt-1 text-ink font-semibold leading-relaxed">
                            {{ business.address || 'Bandung, Jawa Barat' }}
                        </p>
                    </div>
                    <div class="rounded-xl bg-canvas p-4">
                        <p class="text-ink-muted font-medium">Wilayah Administratif</p>
                        <p class="mt-1 text-ink font-semibold">
                            Kecamatan {{ business.district || 'Kota Bandung' }}
                            <span v-if="business.subdistrict">, Kelurahan {{ business.subdistrict }}</span>
                        </p>
                    </div>
                    <div class="rounded-xl bg-canvas p-4">
                        <p class="text-ink-muted font-medium">Kontak Narahubung</p>
                        <p class="mt-1 text-ink font-semibold">
                            {{ business.contact_phone || 'Hubungi melalui platform KONEKSA' }}
                        </p>
                    </div>
                </div>
            </section>
        </main>

        <!-- Modal Aksi Pengajuan Proposal Kerja Sama (FR-11.2) -->
        <Modal :show="showProposalModal" max-width="lg" @close="showProposalModal = false">
            <div class="p-6">
                <!-- If Mitra Industri Logged In -->
                <template v-if="business.proposal_action?.can_submit_proposal">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <span class="rounded-full bg-primary-50 px-2.5 py-0.5 text-xs font-semibold text-primary-700">
                                Portal Kemitraan Industri
                            </span>
                            <h3 class="mt-2 text-lg font-bold text-ink">
                                Ajukan Kerja Sama Bisnis
                            </h3>
                            <p class="text-xs text-ink-muted">
                                Kirim usulan proposal kolaborasi kepada <strong>{{ business.name }}</strong>
                            </p>
                        </div>
                        <button
                            type="button"
                            class="text-ink-muted hover:text-ink text-sm p-1"
                            @click="showProposalModal = false"
                        >
                            ✕
                        </button>
                    </div>

                    <div v-if="proposalSuccessMessage" class="mt-4 rounded-xl bg-success-light p-5 text-center">
                        <div class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-full bg-success text-white">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <p class="text-xs font-semibold text-success-dark">
                            {{ proposalSuccessMessage }}
                        </p>
                        <div class="mt-4 flex justify-center gap-2">
                            <Link
                                :href="route('proposals.index')"
                                class="inline-flex h-9 items-center rounded-xl bg-primary-600 px-4 text-xs font-medium text-white hover:bg-primary-500 transition"
                            >
                                Buka Daftar Proposal &rarr;
                            </Link>
                            <SecondaryButton @click="showProposalModal = false">Tutup</SecondaryButton>
                        </div>
                    </div>

                    <form v-else class="mt-4 space-y-4" @submit.prevent="submitProposal">
                        <div>
                            <label for="proposal-title" class="block text-xs font-semibold text-ink mb-1">
                                Judul Proposal Kerja Sama <span class="text-danger-500">*</span>
                            </label>
                            <input
                                id="proposal-title"
                                v-model="proposalForm.title"
                                type="text"
                                placeholder="Contoh: Kerja Sama Distribusi Produk Retail Kopi Braga"
                                class="h-10 w-full rounded-xl border border-hairline bg-canvas px-3 text-xs text-ink focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary-500"
                                required
                            />
                            <p v-if="proposalForm.errors.title" class="mt-1 text-xs text-danger-500">
                                {{ proposalForm.errors.title }}
                            </p>
                        </div>

                        <div>
                            <label for="proposal-cooperation-type" class="block text-xs font-semibold text-ink mb-1">
                                Jenis Kerja Sama <span class="text-danger-500">*</span>
                            </label>
                            <select
                                id="proposal-cooperation-type"
                                v-model="proposalForm.cooperation_type_id"
                                class="h-10 w-full rounded-xl border border-hairline bg-canvas px-3 text-xs text-ink focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary-500"
                                required
                            >
                                <option value="" disabled>Pilih Jenis Kerja Sama...</option>
                                <option
                                    v-for="ctype in cooperation_types"
                                    :key="ctype.id"
                                    :value="ctype.id"
                                >
                                    {{ ctype.name }}
                                </option>
                            </select>
                            <p v-if="proposalForm.errors.cooperation_type_id" class="mt-1 text-xs text-danger-500">
                                {{ proposalForm.errors.cooperation_type_id }}
                            </p>
                        </div>

                        <div>
                            <label for="proposal-description" class="block text-xs font-semibold text-ink mb-1">
                                Rincian Penawaran &amp; Nilai Tambah <span class="text-danger-500">*</span>
                            </label>
                            <textarea
                                id="proposal-description"
                                v-model="proposalForm.description"
                                rows="4"
                                placeholder="Jelaskan secara rinci maksud kerja sama, skema penawaran, proyeksi volume pasokan, atau nilai tambah bagi UMKM..."
                                class="w-full rounded-xl border border-hairline bg-canvas p-3 text-xs text-ink placeholder-ink-muted focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary-500"
                                required
                            ></textarea>
                            <p v-if="proposalForm.errors.description" class="mt-1 text-xs text-danger-500">
                                {{ proposalForm.errors.description }}
                            </p>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2">
                            <SecondaryButton @click="showProposalModal = false">Batal</SecondaryButton>
                            <PrimaryButton type="submit" :disabled="proposalForm.processing">
                                {{ proposalForm.processing ? 'Mengirim...' : 'Kirim Proposal' }}
                            </PrimaryButton>
                        </div>
                    </form>
                </template>

                <!-- If Guest / Unauthenticated -->
                <template v-else-if="business.proposal_action?.requires_auth">
                    <div class="text-center space-y-4">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-primary-50 text-primary-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-ink">
                                Masuk Sebagai Mitra Industri
                            </h3>
                            <p class="mt-1 text-xs text-ink-secondary leading-relaxed">
                                Pengajuan proposal kerja sama dengan UMKM <strong>{{ business.name }}</strong> memerlukan akun terverifikasi sebagai Mitra Industri.
                            </p>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-2 justify-center pt-2">
                            <Link
                                :href="route('login')"
                                class="inline-flex h-10 items-center justify-center rounded-xl bg-primary-600 px-5 text-xs font-semibold text-white shadow-xs hover:bg-primary-500 transition"
                            >
                                Masuk ke Akun
                            </Link>
                            <Link
                                :href="route('register')"
                                class="inline-flex h-10 items-center justify-center rounded-xl border border-hairline bg-white px-5 text-xs font-semibold text-ink hover:bg-neutral-50 transition"
                            >
                                Daftar Mitra Baru
                            </Link>
                        </div>
                    </div>
                </template>

                <!-- If Logged in but other role (e.g. UMKM or Pemda) -->
                <template v-else>
                    <div class="text-center space-y-3">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-warning-light text-warning-dark">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-ink">
                            Akses Khusus Mitra Industri
                        </h3>
                        <p class="text-xs text-ink-secondary leading-relaxed">
                            Fitur pengajuan proposal kerja sama ini diperuntukkan bagi akun berstatus <strong>Mitra Industri</strong>. Akun Anda saat ini memiliki peran <strong>{{ business.proposal_action?.user_role }}</strong>.
                        </p>
                        <div class="pt-2">
                            <SecondaryButton @click="showProposalModal = false">Tutup</SecondaryButton>
                        </div>
                    </div>
                </template>
            </div>
        </Modal>
    </div>
</template>

