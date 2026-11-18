<script setup>
import { useSidebar } from './context';

const { toggleSidebar, state, openMobile, isMobile } = useSidebar();
</script>

<template>
    <button
        type="button"
        data-slot="sidebar-trigger"
        class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-hairline bg-white text-ink-secondary transition hover:bg-neutral-100 hover:text-ink focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sidebar-ring"
        :aria-expanded="isMobile ? openMobile : state === 'expanded'"
        aria-label="Buka atau tutup navigasi"
        title="Buka atau tutup navigasi (Ctrl+B)"
        @click="toggleSidebar"
    >
        <svg
            class="h-4 w-4 rtl:rotate-180"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.75"
            aria-hidden="true"
        >
            <rect width="18" height="18" x="3" y="3" rx="2" />
            <path d="M9 3v18" />
        </svg>
    </button>
</template>
