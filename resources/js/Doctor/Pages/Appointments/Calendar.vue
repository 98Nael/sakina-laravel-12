<template>
  <DoctorLayout>
    <template #header>Appointments Calendar</template>

    <div class="space-y-6 pb-6">
      <section class="rounded-3xl border border-white/35 bg-gradient-to-r from-teal-700 via-teal-800 to-emerald-700 p-6 text-white shadow-2xl shadow-teal-900/20">
        <p class="text-xs uppercase tracking-[0.22em] text-teal-100">Calendar View</p>
        <h1 class="mt-2 text-2xl font-bold">Schedule Timeline</h1>
        <p class="mt-1 text-sm text-teal-50/90">A quick chronological view of your appointment records.</p>
      </section>

      <section class="rounded-3xl border border-teal-100/70 bg-white/90 p-5 shadow-lg shadow-teal-900/5">
        <div v-if="!items.length" class="py-10 text-center text-sm text-slate-500">
          No appointments to display.
        </div>
        <ul v-else class="space-y-3">
          <li v-for="item in items" :key="item.id" class="rounded-2xl border border-slate-200 p-4">
            <p class="text-sm font-semibold text-slate-800">Appointment #{{ item.id }}</p>
            <p class="mt-1 text-xs text-slate-600">Status: {{ item.status || 'scheduled' }}</p>
          </li>
        </ul>
      </section>
    </div>
  </DoctorLayout>
</template>

<script setup>
import { computed } from 'vue';
import DoctorLayout from '../../Layouts/DoctorLayout.vue';

const props = defineProps({
  appointments: {
    type: Array,
    default: () => [],
  },
});

const items = computed(() => props.appointments || []);
</script>
