<script setup>
import StatusBadge from '@/Components/StatusBadge.vue';

defineProps({ status: { type: String, required: true } });
const states = {
    out: { tone: 'danger', label: 'Stok habis' },
    low: { tone: 'warning', label: 'Stok rendah' },
    healthy: { tone: 'success', label: 'Stok aman' },
    archived: { tone: 'neutral', label: 'Diarsipkan' },
};
</script>

<template>
    <StatusBadge :tone="states[status].tone" class="w-fit" size="md" dot>{{ states[status].label }}</StatusBadge>
</template>
