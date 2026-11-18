<script setup>
import { Link } from '@inertiajs/vue3';
import EvaluationHeaderBar from '@/Components/EvaluationHeaderBar.vue';
import KoneksaLogo from '@/Components/KoneksaLogo.vue';

defineProps({
    evaluationPage: {
        type: String,
        default: '',
    },
    cardWidth: {
        type: String,
        default: 'md',
    },
});
</script>

<template>
    <div class="flex min-h-screen flex-col bg-gradient-to-b from-primary-50/60 via-canvas to-canvas text-ink">
        <EvaluationHeaderBar
            v-if="evaluationPage"
            :active-page="evaluationPage"
        />

        <!-- Laravel Breeze Style Centered Guest Container -->
        <main class="flex flex-1 flex-col items-center justify-center px-4 py-10 sm:px-6">
            <div class="mb-5 flex flex-col items-center">
                <Link
                    :href="route('landing')"
                    class="rounded-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                >
                    <KoneksaLogo size="md" />
                </Link>
            </div>

            <div
                class="w-full overflow-hidden rounded-xl border border-hairline bg-white p-6 shadow-xs sm:p-8"
                :class="cardWidth === 'lg' ? 'max-w-lg' : 'max-w-md'"
            >
                <slot />
            </div>

            <div class="mt-5 flex items-center gap-3 text-xs text-ink-muted">
                <Link
                    :href="route('landing')"
                    class="font-medium text-primary-600 transition hover:text-primary-700 hover:underline"
                >
                    ← Kembali ke Beranda
                </Link>
                <span aria-hidden="true">·</span>
                <span>Koneksa Kota Bandung</span>
            </div>
        </main>
    </div>
</template>
