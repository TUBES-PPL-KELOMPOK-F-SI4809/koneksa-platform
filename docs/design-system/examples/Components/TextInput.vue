<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="h-10 rounded-md border border-hairline bg-white px-3 text-sm text-ink placeholder-neutral-400 shadow-xs transition duration-150 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
        v-model="model"
        ref="input"
    />
</template>
