<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex h-10 items-center justify-center rounded-md border border-hairline bg-white px-4 py-2 text-sm font-medium text-ink shadow-xs transition duration-150 ease-in-out hover:border-neutral-300 hover:bg-canvas focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 disabled:opacity-40"
    >
        <slot />
    </button>
</template>
