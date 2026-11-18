<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({ business: Object, categories: Array });
const form = useForm({
    name: props.business?.name ?? '',
    business_category_id: props.business?.business_category_id ?? '',
    description: props.business?.description ?? '',
    address: props.business?.address ?? '',
    district: props.business?.district ?? '',
    subdistrict: props.business?.subdistrict ?? '',
    contact_phone: props.business?.contact_phone ?? '',
});
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Profil Usaha" />
        <template #header><h1 class="text-xl font-semibold text-gray-800">Profil Usaha</h1></template>
        <div class="py-12">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <form @submit.prevent="form.put(route('business-profile.update'))" class="space-y-6 rounded-lg bg-white p-6 shadow-sm">
                    <p class="text-sm text-gray-600">Isi informasi usaha Anda. Nama dan kategori wajib diisi.</p>
                    <div>
                        <InputLabel for="business-name" value="Nama usaha" />
                        <TextInput id="business-name" v-model="form.name" class="mt-1 block w-full" required maxlength="150" />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel for="business-category" value="Kategori usaha" />
                        <select id="business-category" v-model="form.business_category_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="" disabled>Pilih kategori</option>
                            <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                        </select>
                        <p v-if="categories.length === 0" class="mt-2 text-sm text-gray-600">Kategori usaha belum tersedia. Hubungi pengelola.</p>
                        <InputError :message="form.errors.business_category_id" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel for="business-description" value="Deskripsi usaha" />
                        <textarea id="business-description" v-model="form.description" rows="4" maxlength="10000" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        <InputError :message="form.errors.description" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel for="business-address" value="Alamat" />
                        <textarea id="business-address" v-model="form.address" rows="3" maxlength="10000" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        <InputError :message="form.errors.address" class="mt-2" />
                    </div>
                    <div v-for="field in [{ key: 'district', label: 'Kecamatan', max: 100 }, { key: 'subdistrict', label: 'Kelurahan', max: 100 }, { key: 'contact_phone', label: 'Telepon usaha', max: 30 }]" :key="field.key">
                        <InputLabel :for="field.key" :value="field.label" />
                        <TextInput :id="field.key" v-model="form[field.key]" :maxlength="field.max" class="mt-1 block w-full" />
                        <InputError :message="form.errors[field.key]" class="mt-2" />
                    </div>
                    <div class="flex items-center gap-4">
                        <PrimaryButton :disabled="form.processing">{{ form.processing ? 'Menyimpan...' : 'Simpan' }}</PrimaryButton>
                        <p v-if="form.recentlySuccessful" role="status" class="text-sm text-green-700">Profil berhasil disimpan.</p>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
