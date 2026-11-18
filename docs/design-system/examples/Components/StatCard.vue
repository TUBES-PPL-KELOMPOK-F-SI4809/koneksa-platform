<script setup>
import { computed } from 'vue';

const props = defineProps({
    label: {
        type: String,
        required: true,
    },
    value: {
        type: String,
        required: true,
    },
    delta: {
        type: String,
        default: '',
    },
    badge: {
        type: String,
        default: '',
    },
    tone: {
        type: String,
        default: 'primary',
    },
});

const toneConfig = computed(() => {
    const map = {
        primary: {
            iconBg: 'bg-primary-50 text-primary-600 border-primary-100',
            pill: 'bg-primary-50 text-primary-700',
            defaultBadge: 'Aktif',
            iconPath: 'M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z',
        },
        secondary: {
            iconBg: 'bg-secondary-50 text-secondary-600 border-secondary-100',
            pill: 'bg-secondary-50 text-secondary-700',
            defaultBadge: 'Stabil',
            iconPath: 'M13.5 16.875h3.375m0 0h3.375m-3.375 0V13.5m0 3.375v3.375M6 10.5h2.25a2.25 2.25 0 0 0 2.25-2.25V6a2.25 2.25 0 0 0-2.25-2.25H6A2.25 2.25 0 0 0 3.75 6v2.25A2.25 2.25 0 0 0 6 10.5Zm0 9.75h2.25A2.25 2.25 0 0 0 10.5 18v-2.25a2.25 2.25 0 0 0-2.25-2.25H6a2.25 2.25 0 0 0-2.25 2.25V18A2.25 2.25 0 0 0 6 20.25Zm9.75-9.75H18a2.25 2.25 0 0 0 2.25-2.25V6A2.25 2.25 0 0 0 18 3.75h-2.25A2.25 2.25 0 0 0 13.5 6v2.25a2.25 2.25 0 0 0 2.25 2.25Z',
        },
        success: {
            iconBg: 'bg-success-light text-success-dark border-success/20',
            pill: 'bg-success-light text-success-dark',
            defaultBadge: '↑ Positif',
            iconPath: 'M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941',
        },
        warning: {
            iconBg: 'bg-warning-light text-warning-dark border-warning/25',
            pill: 'bg-warning-light text-warning-dark',
            defaultBadge: 'Perhatian',
            iconPath: 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z',
        },
        danger: {
            iconBg: 'bg-danger-light text-danger-dark border-danger/20',
            pill: 'bg-danger-light text-danger-dark',
            defaultBadge: 'Prioritas',
            iconPath: 'M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z',
        },
        info: {
            iconBg: 'bg-info-light text-info-dark border-info/20',
            pill: 'bg-info-light text-info-dark',
            defaultBadge: 'Baru',
            iconPath: 'M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0',
        },
    };

    return map[props.tone] || map.primary;
});
</script>

<template>
    <article class="rounded-2xl border border-hairline bg-white p-5 shadow-xs transition duration-150 hover:shadow-paas">
        <div class="flex items-center justify-between gap-2">
            <p class="text-xs font-medium text-ink-secondary">
                {{ label }}
            </p>
            <span
                class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full border"
                :class="toneConfig.iconBg"
                aria-hidden="true"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" :d="toneConfig.iconPath" />
                </svg>
            </span>
        </div>

        <div class="mt-3 flex flex-wrap items-baseline gap-2">
            <p class="font-sans text-2xl font-bold tracking-tight text-ink tabular-nums">
                {{ value }}
            </p>
            <span
                class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-semibold"
                :class="toneConfig.pill"
            >
                {{ badge || toneConfig.defaultBadge }}
            </span>
        </div>

        <p v-if="delta" class="mt-2 text-xs text-ink-muted">
            {{ delta }}
        </p>
    </article>
</template>
