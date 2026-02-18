<template>
    <div :class="[color, 'rounded-lg p-6 text-white shadow-lg']">
        <div class="flex justify-between items-start">
            <div>
                <p class="opacity-90 text-sm font-medium">{{ title }}</p>
                <p class="text-3xl font-bold mt-2">{{ value }}</p>
            </div>
            <div class="text-4xl opacity-60">{{ icon }}</div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    title: String,
    value: [String, Number],
    icon: {
        type: String,
        default: '📊'
    },
    color: {
        type: String,
        default: 'bg-gradient-to-br from-blue-500 to-blue-600'
    }
});
</script>
