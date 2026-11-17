<script setup>
import { computed } from 'vue';
import { useSidebar } from './context';

const props = defineProps({
    side: {
        type: String,
        default: 'left',
        validator: (v) => ['left', 'right'].includes(v),
    },
    variant: {
        type: String,
        default: 'sidebar',
        validator: (v) => ['sidebar', 'floating', 'inset'].includes(v),
    },
    collapsible: {
        type: String,
        default: 'icon',
        validator: (v) => ['offcanvas', 'icon', 'none'].includes(v),
    },
});

const { state, isMobile, openMobile, setOpenMobile } = useSidebar();

const collapsibleAttr = computed(() =>
    state.value === 'collapsed' ? props.collapsible : '',
);

const desktopWidthClass = computed(() => {
    if (props.collapsible === 'none') {
        return 'w-[var(--sidebar-width)]';
    }
    if (state.value === 'collapsed') {
        return props.collapsible === 'icon'
            ? 'w-[var(--sidebar-width-icon)]'
            : 'w-0 overflow-hidden border-r-0';
    }
    return 'w-[var(--sidebar-width)]';
});
</script>

<template>
    <!-- Non-collapsible static mode -->
    <aside
        v-if="collapsible === 'none'"
        data-slot="sidebar"
        data-state="expanded"
        :data-variant="variant"
        :data-side="side"
        class="hidden shrink-0 flex-col border-r border-sidebar-border bg-sidebar text-sidebar-foreground lg:flex"
        :class="desktopWidthClass"
    >
        <slot />
    </aside>

    <!-- Desktop Collapsible Sidebar -->
    <aside
        v-else
        data-slot="sidebar"
        :data-state="state"
        :data-collapsible="collapsibleAttr"
        :data-variant="variant"
        :data-side="side"
        class="group relative hidden shrink-0 flex-col bg-sidebar text-sidebar-foreground transition-[width] duration-200 ease-linear lg:flex"
        :class="[
            desktopWidthClass,
            variant === 'floating' ? 'm-2 rounded-lg border border-sidebar-border shadow-xs' : 'border-r border-sidebar-border',
        ]"
    >
        <slot />
    </aside>

    <!-- Mobile Overlay Drawer (< lg) -->
    <div v-if="isMobile && openMobile" class="fixed inset-0 z-50 flex lg:hidden" role="dialog" aria-modal="true" aria-label="Navigasi Sidebar">
        <div
            class="fixed inset-0 bg-ink-deep/40 backdrop-blur-[1px]"
            aria-hidden="true"
            @click="setOpenMobile(false)"
        ></div>

        <aside
            data-slot="sidebar"
            data-mobile="true"
            data-state="expanded"
            :data-side="side"
            class="relative z-10 flex h-full w-72 max-w-[85vw] flex-col border-r border-sidebar-border bg-sidebar text-sidebar-foreground shadow-lg"
        >
            <slot />
        </aside>
    </div>
</template>

