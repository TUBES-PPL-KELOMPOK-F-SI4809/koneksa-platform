<script setup>
defineProps({
    showWordmark: {
        type: Boolean,
        default: true,
    },
    size: {
        type: String,
        default: 'md',
    },
    inverted: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <div class="inline-flex items-center gap-2.5">
        <svg
            :class="size === 'sm' ? 'h-7 w-7' : 'h-8 w-8'"
            viewBox="0 0 32 32"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
            aria-hidden="true"
        >
            <rect
                width="32"
                height="32"
                rx="7"
                :fill="inverted ? '#FFFFFF' : '#4F46E5'"
            />
            <path
                d="M10.5 9.5V22.5M10.5 16L19.5 9.5M13.5 13.8L21.5 22.5"
                :stroke="inverted ? '#4F46E5' : '#FFFFFF'"
                stroke-width="2.2"
                stroke-linecap="round"
                stroke-linejoin="round"
            />
            <circle cx="21.5" cy="9.5" r="2.2" fill="#06B6D4" />
        </svg>

        <div v-if="showWordmark" class="flex items-center gap-2 leading-none">
            <span
                class="font-semibold tracking-tight"
                :class="[
                    size === 'sm' ? 'text-sm' : 'text-[15px]',
                    inverted ? 'text-white' : 'text-ink',
                ]"
            >
                Koneksa
            </span>
            <span
                class="hidden rounded-full border px-1.5 py-0.5 font-mono text-[10px] font-medium sm:inline-block"
                :class="
                    inverted
                        ? 'border-neutral-700 bg-neutral-800 text-secondary-200'
                        : 'border-primary-200 bg-primary-50 text-primary-700'
                "
            >
                Bandung
            </span>
        </div>
    </div>
</template>
