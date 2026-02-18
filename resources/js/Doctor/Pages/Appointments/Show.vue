<template>
  <DoctorLayout>
    <template #header>Appointment Details</template>

    <div class="rounded-3xl border border-teal-100/70 bg-white/90 p-6 shadow-lg shadow-teal-900/5">
      <h1 class="text-xl font-bold text-slate-900">Appointment Details</h1>
      <p class="mt-2 text-sm text-slate-600">Detailed appointment page is ready for full data binding.</p>
    </div>
  </DoctorLayout>
</template>

<script setup>
import DoctorLayout from '../../Layouts/DoctorLayout.vue';

defineProps({
  appointment: {
    type: Object,
    default: () => ({}),
  },
});
</script>
