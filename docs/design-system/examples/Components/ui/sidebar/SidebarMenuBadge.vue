<script setup>
import { useSidebar } from './context';

const { state, isMobile } = useSidebar();
</script>

<template>
    <span
        v-show="state === 'expanded' || isMobile"
        data-slot="sidebar-menu-badge"
        class="ms-auto shrink-0 rounded-full bg-primary-50 px-2 py-0.5 font-mono text-[10px] font-semibold text-primary-700 peer-data-[active=true]/menu-button:bg-white/20 peer-data-[active=true]/menu-button:text-white"
    >
        <slot />
    </span>
</template>
