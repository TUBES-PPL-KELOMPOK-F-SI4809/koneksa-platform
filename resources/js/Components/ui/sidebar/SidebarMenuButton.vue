<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useSidebar } from './context';

const props = defineProps({
    isActive: {
        type: Boolean,
        default: false,
    },
    href: {
        type: String,
        default: '',
    },
    tooltip: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['click']);

const { state, isMobile } = useSidebar();
const isCollapsedDesktop = computed(() => state.value === 'collapsed' && !isMobile.value);
</script>

<template>
    <Link
        v-if="href"
        :href="href"
        data-slot="sidebar-menu-button"
        :data-active="isActive ? 'true' : 'false'"
        :title="isCollapsedDesktop && tooltip ? tooltip : undefined"
        :aria-current="isActive ? 'page' : undefined"
        :aria-label="tooltip || undefined"
        class="peer/menu-button flex min-h-11 w-full items-center gap-2.5 overflow-hidden rounded-xl px-3 text-left text-sm transition duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sidebar-ring"
        :class="[
            isCollapsedDesktop ? 'justify-center px-0' : '',
            isActive
                ? 'bg-primary-600 font-medium text-white shadow-xs'
                : 'text-ink-secondary hover:bg-neutral-100 hover:text-ink',
        ]"
        @click="emit('click', $event)"
    >
        <slot />
    </Link>
    <button
        v-else
        type="button"
        data-slot="sidebar-menu-button"
        :data-active="isActive ? 'true' : 'false'"
        :title="isCollapsedDesktop && tooltip ? tooltip : undefined"
        :aria-current="isActive ? 'page' : undefined"
        :aria-label="tooltip || undefined"
        class="peer/menu-button flex min-h-11 w-full items-center gap-2.5 overflow-hidden rounded-xl px-3 text-left text-sm transition duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sidebar-ring"
        :class="[
            isCollapsedDesktop ? 'justify-center px-0' : '',
            isActive
                ? 'bg-primary-600 font-medium text-white shadow-xs'
                : 'text-ink-secondary hover:bg-neutral-100 hover:text-ink',
        ]"
        @click="emit('click', $event)"
    >
        <slot />
    </button>
</template>
