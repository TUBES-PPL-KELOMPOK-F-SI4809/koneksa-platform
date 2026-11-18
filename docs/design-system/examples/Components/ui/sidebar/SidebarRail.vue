<script setup>
import { useSidebar } from './context';

const { toggleSidebar } = useSidebar();
</script>

<template>
    <button
        type="button"
        data-slot="sidebar-rail"
        tabindex="-1"
        aria-label="Toggle Sidebar"
        title="Toggle Sidebar"
        class="absolute inset-y-0 -right-1.5 z-20 hidden w-3 cursor-ew-resize after:absolute after:inset-y-0 after:left-1/2 after:w-[2px] after:transition-colors hover:after:bg-neutral-300 lg:block"
        @click="toggleSidebar"
    ></button>
</template>

