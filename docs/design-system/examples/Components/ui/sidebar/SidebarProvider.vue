<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import { provideSidebarContext } from './context';

const props = defineProps({
    defaultOpen: {
        type: Boolean,
        default: true,
    },
    open: {
        type: Boolean,
        default: undefined,
    },
});

const emit = defineEmits(['update:open']);

const internalOpen = ref(props.defaultOpen);
const openMobile = ref(false);
const isMobile = ref(false);

const isControlled = computed(() => props.open !== undefined);
const openState = computed(() => (isControlled.value ? props.open : internalOpen.value));

watch(
    () => props.open,
    (next) => {
        if (next !== undefined) {
            internalOpen.value = next;
        }
    },
);

function setOpen(value) {
    if (!isControlled.value) {
        internalOpen.value = value;
    }
    emit('update:open', value);
}

function setOpenMobile(value) {
    openMobile.value = value;
}

function toggleSidebar() {
    if (isMobile.value) {
        setOpenMobile(!openMobile.value);
        return;
    }
    setOpen(!openState.value);
}

const state = computed(() => (openState.value ? 'expanded' : 'collapsed'));

let mediaQuery = null;

function updateIsMobile() {
    if (typeof window === 'undefined') {
        return;
    }
    isMobile.value = window.innerWidth < 1024;
    if (!isMobile.value && openMobile.value) {
        openMobile.value = false;
    }
}

function handleKeyboardShortcut(event) {
    if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'b') {
        event.preventDefault();
        toggleSidebar();
    }
    if (event.key === 'Escape' && openMobile.value) {
        openMobile.value = false;
    }
}

onMounted(() => {
    updateIsMobile();
    mediaQuery = window.matchMedia('(max-width: 1023px)');
    mediaQuery.addEventListener('change', updateIsMobile);
    window.addEventListener('keydown', handleKeyboardShortcut);
});

onUnmounted(() => {
    mediaQuery?.removeEventListener('change', updateIsMobile);
    window.removeEventListener('keydown', handleKeyboardShortcut);
});

provideSidebarContext({
    state,
    open: openState,
    setOpen,
    openMobile,
    setOpenMobile,
    isMobile,
    toggleSidebar,
});
</script>

<template>
    <div
        data-slot="sidebar-wrapper"
        :data-state="state"
        style="--sidebar-width: 15.5rem; --sidebar-width-icon: 3.25rem;"
        class="group/sidebar-wrapper flex min-h-0 w-full flex-1 bg-canvas text-ink"
    >
        <slot
            :state="state"
            :open="openState"
            :is-mobile="isMobile"
            :set-open-mobile="setOpenMobile"
            :toggle-sidebar="toggleSidebar"
        />
    </div>
</template>

