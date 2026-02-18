<template>
  <div class="space-y-6 pb-6">
    <section class="rounded-3xl border border-white/35 bg-gradient-to-r from-cyan-700 via-cyan-600 to-emerald-500 p-6 text-white shadow-2xl shadow-cyan-900/20">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
          <p class="text-xs uppercase tracking-[0.22em] text-cyan-100">Medical History</p>
          <h1 class="mt-2 text-2xl font-bold">Your Clinical Records</h1>
          <p class="mt-1 text-sm text-cyan-50/90">Review visits, diagnoses, and doctor notes in one timeline.</p>
        </div>
      </div>
    </section>

    <section class="grid gap-4 sm:grid-cols-3">
      <article class="rounded-2xl border border-cyan-100/70 bg-white/90 p-4 shadow-lg shadow-cyan-900/5">
        <p class="text-sm text-slate-500">Total Records</p>
        <p class="mt-1 text-2xl font-bold text-slate-900">{{ records.length }}</p>
      </article>
      <article class="rounded-2xl border border-cyan-100/70 bg-white/90 p-4 shadow-lg shadow-cyan-900/5">
        <p class="text-sm text-slate-500">Completed Visits</p>
        <p class="mt-1 text-2xl font-bold text-emerald-700">{{ completedCount }}</p>
      </article>
      <article class="rounded-2xl border border-cyan-100/70 bg-white/90 p-4 shadow-lg shadow-cyan-900/5">
        <p class="text-sm text-slate-500">Last Visit</p>
        <p class="mt-1 text-2xl font-bold text-cyan-700">{{ lastVisitDate }}</p>
      </article>
    </section>

    <section class="rounded-3xl border border-cyan-100/70 bg-white/90 p-5 shadow-lg shadow-cyan-900/5">
      <div class="mb-4 flex items-center justify-between gap-3">
        <h2 class="text-lg font-bold text-slate-900">History Timeline</h2>
        <span class="text-xs font-semibold uppercase tracking-[0.14em] text-cyan-700">Newest first</span>
      </div>

      <div v-if="records.length === 0" class="rounded-2xl border border-dashed border-cyan-200 px-4 py-10 text-center">
        <p class="text-sm text-slate-500">No medical history records yet.</p>
      </div>

      <div v-else class="space-y-4">
        <article
          v-for="record in records"
          :key="record.id"
          class="rounded-2xl border border-slate-200 bg-white p-4 transition hover:border-cyan-300"
        >
          <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
              <p class="text-sm font-semibold text-slate-900">{{ record.visit_type }}</p>
              <p class="mt-1 text-xs uppercase tracking-[0.12em] text-slate-500">{{ formatDate(record.date) }}</p>
            </div>
            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold" :class="statusClass(record.status)">
              {{ record.status }}
            </span>
          </div>

          <div class="mt-3 grid gap-2 text-sm text-slate-600 sm:grid-cols-2">
            <p><span class="font-semibold text-slate-700">Doctor:</span> {{ record.doctor }}</p>
            <p><span class="font-semibold text-slate-700">Diagnosis:</span> {{ record.diagnosis }}</p>
            <p class="sm:col-span-2"><span class="font-semibold text-slate-700">Notes:</span> {{ record.notes }}</p>
          </div>
        </article>
      </div>
    </section>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  medicalHistory: {
    type: Array,
    default: () => [],
  },
});

const records = computed(() => props.medicalHistory || []);

const completedCount = computed(() =>
  records.value.filter((item) => String(item.status).toLowerCase() === 'completed').length,
);

const lastVisitDate = computed(() => {
  if (!records.value.length) return '-';
  return formatDate(records.value[0].date);
});

function formatDate(dateString) {
  const date = new Date(dateString);
  if (Number.isNaN(date.getTime())) return dateString;
  return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

function statusClass(status) {
  const value = String(status).toLowerCase();
  if (value === 'completed') return 'bg-emerald-100 text-emerald-700';
  if (value === 'pending') return 'bg-amber-100 text-amber-700';
  return 'bg-slate-100 text-slate-700';
}
</script>
