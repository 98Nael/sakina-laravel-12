<template>
  <DoctorLayout>
    <template #header>Patient Details</template>

    <div class="rounded-3xl border border-teal-100/70 bg-white/90 p-6 shadow-lg shadow-teal-900/5">
      <h1 class="text-xl font-bold text-slate-900">Patient Details</h1>
      <p class="mt-2 text-sm text-slate-600">Name: {{ patient?.name || '-' }}</p>
      <p class="mt-1 text-sm text-slate-600">Email: {{ patient?.email || '-' }}</p>
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
  medical_history: {
    type: Array,
    default: () => [],
  },
  appointments: {
    type: Array,
    default: () => [],
  },
});
</script>
