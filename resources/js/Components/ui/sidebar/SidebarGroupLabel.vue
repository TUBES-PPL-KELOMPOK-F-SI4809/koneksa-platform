<script setup>
import { useSidebar } from './context';

const { state, isMobile } = useSidebar();
</script>

<template>
    <div
        data-slot="sidebar-group-label"
        class="flex h-7 shrink-0 items-center px-2 text-[11px] font-medium uppercase tracking-wider text-ink-muted transition-opacity duration-150"
        :class="state === 'collapsed' && !isMobile ? 'sr-only' : 'opacity-100'"
    >
        <slot />
    </div>
</template>
