<template>
  <DoctorLayout>
    <template #header>Patient Records</template>

    <div class="rounded-3xl border border-teal-100/70 bg-white/90 p-6 shadow-lg shadow-teal-900/5">
      <h1 class="text-xl font-bold text-slate-900">Patient Records</h1>
      <p class="mt-2 text-sm text-slate-600">Patient: {{ patient?.name || '-' }}</p>
      <p class="mt-1 text-sm text-slate-500">Records count: {{ records.length }}</p>
    </div>
  </DoctorLayout>
</template>

<script setup>
import DoctorLayout from '../../Layouts/DoctorLayout.vue';

defineProps({
  patient: {
    type: Object,
    default: () => ({}),
  },
  records: {
    type: Array,
    default: () => [],
  },
});
</script>
