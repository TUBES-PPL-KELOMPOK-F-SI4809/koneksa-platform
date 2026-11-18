<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';

defineProps({
    metrics: {
        type: Object,
        default: () => ({}),
    },
    businesses: {
        type: Array,
        default: () => [],
    },
    partners: {
        type: Array,
        default: () => [],
    },
    categories: {
        type: Array,
        default: () => [],
    },
    recentAudits: {
        type: Array,
        default: () => [],
    },
    status: {
        type: String,
        default: '',
    },
});

const categoryForm = useForm({
    name: '',
});

const submitCategory = () => {
    categoryForm.post(route('admin.categories.store'), {
        onSuccess: () => categoryForm.reset(),
    });
};

const toggleBusinessPublication = (business) => {
    const publish = !business.published_at;
    router.patch(
        route('admin.businesses.publication', business.id),
        {
            publish,
            reason: publish
                ? 'Publikasi profil usaha disetujui via supervisi Superadmin'
                : 'Publikasi profil usaha ditangguhkan via supervisi Superadmin',
        },
        { preserveScroll: true },
    );
};
</script>

<template>
    <Head title="Panel Supervisi Sistem" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Panel Supervisi Administratif Lintas Modul
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Pemantauan entitas usaha UMKM, profil organisasi Mitra, referensi master, dan tindakan administratif lintas modul (KSA-148 / FR-31).
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <Link
                        :href="route('admin.users.index')"
                        class="rounded-md border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50"
                    >
                        Manajemen Pengguna &rarr;
                    </Link>
                    <Link
                        :href="route('admin.audit_logs.index')"
                        class="rounded-md bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-indigo-700"
                    >
                        Audit Trail Lengkap &rarr;
                    </Link>
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
                    <span v-if="status === 'business-publication-updated'">
                        Status publikasi usaha UMKM berhasil diperbarui oleh Superadmin dan tercatat di Audit Trail.
                    </span>
                    <span v-else-if="status === 'category-created'">
                        Kategori usaha master baru berhasil ditambahkan.
                    </span>
                    <span v-else>{{ status }}</span>
                </div>

                <!-- Metrik Lintas Entitas Sistem -->
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
                    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                        <div class="text-[11px] font-semibold uppercase tracking-wider text-gray-500">Profil UMKM</div>
                        <div class="mt-1 text-2xl font-bold text-gray-900">{{ metrics.businesses_total ?? 0 }}</div>
                        <div class="mt-0.5 text-xs text-emerald-600">
                            {{ metrics.businesses_published ?? 0 }} Terpublikasi
                        </div>
                    </div>
                    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                        <div class="text-[11px] font-semibold uppercase tracking-wider text-gray-500">Organisasi Mitra</div>
                        <div class="mt-1 text-2xl font-bold text-blue-700">{{ metrics.partners_total ?? 0 }}</div>
                    </div>
                    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                        <div class="text-[11px] font-semibold uppercase tracking-wider text-gray-500">Kategori Master</div>
                        <div class="mt-1 text-2xl font-bold text-indigo-700">{{ metrics.categories_total ?? 0 }}</div>
                    </div>
                    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                        <div class="text-[11px] font-semibold uppercase tracking-wider text-gray-500">Pengajuan Verifikasi</div>
                        <div class="mt-1 text-2xl font-bold text-amber-700">{{ metrics.verifications_total ?? 0 }}</div>
                    </div>
                    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                        <div class="text-[11px] font-semibold uppercase tracking-wider text-gray-500">Program Pemda</div>
                        <div class="mt-1 text-2xl font-bold text-teal-700">{{ metrics.programs_total ?? 0 }}</div>
                    </div>
                    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                        <div class="text-[11px] font-semibold uppercase tracking-wider text-gray-500">Proposal Kolaborasi</div>
                        <div class="mt-1 text-2xl font-bold text-purple-700">{{ metrics.collaborations_total ?? 0 }}</div>
                    </div>
                </div>

                <!-- Grid Supervisi: Usaha UMKM & Kategori Master -->
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <!-- Supervisi Entitas Usaha UMKM (2 Kolom) -->
                    <div class="lg:col-span-2 rounded-lg border border-gray-200 bg-white shadow-sm">
                        <div class="border-b border-gray-200 px-5 py-4">
                            <h3 class="text-base font-semibold text-gray-900">
                                Supervisi Profil Usaha UMKM
                            </h3>
                            <p class="text-xs text-gray-500">
                                Kendali publikasi direktori lintas modul dengan pencatatan Actor ID Superadmin.
                            </p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase text-gray-500">Nama Usaha</th>
                                        <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase text-gray-500">Pemilik &amp; Kategori</th>
                                        <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase text-gray-500">Wilayah</th>
                                        <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase text-gray-500">Publikasi</th>
                                        <th class="px-4 py-2.5 text-right text-xs font-semibold uppercase text-gray-500">Tindakan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white text-xs">
                                    <template v-if="businesses.length > 0">
                                        <tr v-for="biz in businesses" :key="biz.id">
                                            <td class="px-4 py-3 font-medium text-gray-900">{{ biz.name }}</td>
                                            <td class="px-4 py-3">
                                                <div>{{ biz.user?.name || '-' }}</div>
                                                <div class="text-gray-500">{{ biz.category?.name || '-' }}</div>
                                            </td>
                                            <td class="px-4 py-3 text-gray-600">
                                                {{ biz.district || '-' }}
                                            </td>
                                            <td class="px-4 py-3">
                                                <span
                                                    class="inline-flex rounded-full px-2 py-0.5 font-semibold"
                                                    :class="
                                                        biz.published_at
                                                            ? 'bg-emerald-100 text-emerald-800'
                                                            : 'bg-gray-100 text-gray-600'
                                                    "
                                                >
                                                    {{ biz.published_at ? 'Terpublikasi' : 'Draft / Ditangguhkan' }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-right">
                                                <button
                                                    type="button"
                                                    @click="toggleBusinessPublication(biz)"
                                                    class="font-semibold underline"
                                                    :class="
                                                        biz.published_at
                                                            ? 'text-amber-600 hover:text-amber-900'
                                                            : 'text-indigo-600 hover:text-indigo-900'
                                                    "
                                                >
                                                    {{ biz.published_at ? 'Tangguhkan' : 'Publikasikan' }}
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr v-else>
                                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                                            Belum ada entitas usaha UMKM yang terdaftar.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Master Data Kategori Usaha -->
                    <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm space-y-4">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">
                                Master Kategori Usaha
                            </h3>
                            <p class="text-xs text-gray-500">
                                Kelola referensi kategori yang digunakan oleh profil UMKM.
                            </p>
                        </div>

                        <form @submit.prevent="submitCategory" class="space-y-3">
                            <div>
                                <InputLabel for="cat_name" value="Nama Kategori Baru" />
                                <div class="mt-1 flex gap-2">
                                    <TextInput
                                        id="cat_name"
                                        v-model="categoryForm.name"
                                        type="text"
                                        class="block w-full text-sm"
                                        placeholder="Contoh: Kuliner & Pangan"
                                        required
                                    />
                                    <PrimaryButton :disabled="categoryForm.processing">
                                        Tambah
                                    </PrimaryButton>
                                </div>
                                <InputError class="mt-1" :message="categoryForm.errors.name" />
                            </div>
                        </form>

                        <div class="divide-y divide-gray-100 border-t border-gray-200 pt-2 max-h-60 overflow-y-auto">
                            <div
                                v-for="cat in categories"
                                :key="cat.id"
                                class="flex items-center justify-between py-2 text-xs"
                            >
                                <span class="font-medium text-gray-800">{{ cat.name }}</span>
                                <span class="rounded bg-gray-100 px-2 py-0.5 text-gray-600">
                                    {{ cat.businesses_count ?? 0 }} UMKM
                                </span>
                            </div>
                            <div v-if="categories.length === 0" class="py-4 text-center text-xs text-gray-400">
                                Belum ada kategori usaha.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grid Bawah: Organisasi Mitra & Log Aktivitas Terbaru -->
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <!-- Daftar Organisasi Mitra Industri -->
                    <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
                        <div class="border-b border-gray-200 px-5 py-4">
                            <h3 class="text-base font-semibold text-gray-900">
                                Direktori Organisasi Mitra Industri
                            </h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-xs">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2.5 text-left font-semibold uppercase text-gray-500">Organisasi</th>
                                        <th class="px-4 py-2.5 text-left font-semibold uppercase text-gray-500">Akun PIC</th>
                                        <th class="px-4 py-2.5 text-left font-semibold uppercase text-gray-500">Kontak</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    <template v-if="partners.length > 0">
                                        <tr v-for="partner in partners" :key="partner.id">
                                            <td class="px-4 py-2.5 font-medium text-gray-900">
                                                {{ partner.organization_name }}
                                            </td>
                                            <td class="px-4 py-2.5 text-gray-600">
                                                {{ partner.user?.name || '-' }} ({{ partner.user?.email || '-' }})
                                            </td>
                                            <td class="px-4 py-2.5 text-gray-500">
                                                {{ partner.contact_phone || '-' }}
                                            </td>
                                        </tr>
                                    </template>
                                    <tr v-else>
                                        <td colspan="3" class="px-4 py-6 text-center text-gray-500">
                                            Belum ada profil organisasi Mitra yang terdaftar.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Aktivitas Audit Trail Terbaru -->
                    <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
                        <div class="border-b border-gray-200 px-5 py-4 flex items-center justify-between">
                            <h3 class="text-base font-semibold text-gray-900">
                                Jejak Audit Terbaru
                            </h3>
                            <Link :href="route('admin.audit_logs.index')" class="text-xs font-semibold text-indigo-600 hover:underline">
                                Lihat Semua ({{ metrics.audit_logs_total ?? 0 }})
                            </Link>
                        </div>
                        <div class="divide-y divide-gray-100 px-5 py-2 text-xs">
                            <template v-if="recentAudits.length > 0">
                                <div v-for="audit in recentAudits" :key="audit.id" class="py-2.5">
                                    <div class="flex items-center justify-between">
                                        <span class="font-semibold text-indigo-700 uppercase">{{ audit.module }} &bull; {{ audit.action }}</span>
                                        <span class="text-gray-400">{{ audit.actor?.name || 'Sistem' }} ({{ audit.actor_role || '-' }})</span>
                                    </div>
                                    <div class="mt-0.5 text-gray-700">{{ audit.description }}</div>
                                </div>
                            </template>
                            <div v-else class="py-6 text-center text-gray-500">
                                Belum ada catatan aktivitas audit.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
