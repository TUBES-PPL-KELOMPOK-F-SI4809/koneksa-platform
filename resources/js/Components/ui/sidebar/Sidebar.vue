<script setup>
import { computed, nextTick, onBeforeUnmount, ref, watch } from 'vue';
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
const mobileDialog = ref(null);

watch(() => isMobile.value && openMobile.value, async (show) => {
    await nextTick();
    if (show) {
        mobileDialog.value?.showModal();
        document.body.style.overflow = 'hidden';
    } else {
        mobileDialog.value?.close();
        document.body.style.overflow = '';
    }
});

onBeforeUnmount(() => {
    if (mobileDialog.value?.open) {
        mobileDialog.value.close();
        document.body.style.overflow = '';
    }
});

function keepFocusInDrawer(event) {
    if (event.key !== 'Tab') {
        return;
    }
    const controls = [...mobileDialog.value.querySelectorAll('a[href], button:not(:disabled)')]
        .filter((element) => element.getClientRects().length);
    const first = controls[0];
    const last = controls[controls.length - 1];
    if (event.shiftKey && document.activeElement === first) {
        event.preventDefault();
        last?.focus();
    } else if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault();
        first?.focus();
    }
}

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
        class="group sticky top-0 hidden h-dvh shrink-0 self-start flex-col bg-sidebar text-sidebar-foreground transition-[width] duration-200 ease-linear lg:flex"
        :class="[
            desktopWidthClass,
            variant === 'floating' ? 'm-2 rounded-lg border border-sidebar-border shadow-xs' : 'border-r border-sidebar-border',
        ]"
    >
        <slot />
    </aside>

    <!-- Mobile Overlay Drawer (< lg) -->
    <dialog
        ref="mobileDialog"
        aria-modal="true"
        aria-label="Navigasi UMKM"
        class="fixed inset-y-0 left-0 m-0 h-dvh max-h-none w-72 max-w-[85vw] border-0 bg-sidebar p-0 text-sidebar-foreground shadow-lg backdrop:bg-ink-deep/40 backdrop:backdrop-blur-[1px]"
        @cancel.prevent="setOpenMobile(false)"
        @close="setOpenMobile(false)"
        @click.self="setOpenMobile(false)"
        @keydown="keepFocusInDrawer"
    >
        <aside
            data-slot="sidebar"
            data-mobile="true"
            data-state="expanded"
            :data-side="side"
            class="relative flex h-full flex-col border-r border-sidebar-border"
        >
            <button type="button" aria-label="Tutup navigasi" class="absolute right-1 top-2 z-10 flex h-11 w-11 items-center justify-center rounded-xl bg-white text-ink-secondary hover:bg-neutral-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500" @click="setOpenMobile(false)">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" d="m6 6 12 12M6 18 18 6" /></svg>
            </button>
            <slot />
        </aside>
    </dialog>
</template>
