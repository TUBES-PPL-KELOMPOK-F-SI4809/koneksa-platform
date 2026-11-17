<script setup>
import { computed } from 'vue';

const props = defineProps({
    tone: {
        type: String,
        default: 'neutral',
        validator: (value) =>
            ['primary', 'secondary', 'success', 'warning', 'danger', 'info', 'neutral'].includes(value),
    },
    size: {
        type: String,
        default: 'sm',
    },
    dot: {
        type: Boolean,
        default: false,
    },
});

const toneClasses = computed(() => {
    const map = {
        primary: 'bg-primary-50 text-primary-700 border-primary-200',
        secondary: 'bg-secondary-50 text-secondary-700 border-secondary-200',
        success: 'bg-success-light text-success-dark border-success/25',
        warning: 'bg-warning-light text-warning-dark border-warning/30',
        danger: 'bg-danger-light text-danger-dark border-danger/25',
        info: 'bg-info-light text-info-dark border-info/25',
        neutral: 'bg-neutral-100 text-ink-secondary border-hairline',
    };

    return map[props.tone] || map.neutral;
});

const dotColor = computed(() => {
    const map = {
        primary: 'bg-primary-600',
        secondary: 'bg-secondary-500',
        success: 'bg-success',
        warning: 'bg-warning',
        danger: 'bg-danger',
        info: 'bg-accent-500',
        neutral: 'bg-ink-muted',
    };

    return map[props.tone] || map.neutral;
});

const sizeClasses = computed(() =>
    props.size === 'md' ? 'px-2.5 py-0.5 text-xs' : 'px-2 py-0.5 text-[11px]',
);
</script>

<template>
    <span
        class="inline-flex items-center gap-1.5 rounded-full border font-medium leading-tight"
        :class="[toneClasses, sizeClasses]"
    >
        <span
            v-if="dot"
            class="h-1.5 w-1.5 rounded-full"
            :class="dotColor"
            aria-hidden="true"
        ></span>
        <slot />
    </span>
</template>
