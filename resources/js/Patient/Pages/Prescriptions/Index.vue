<template>
  <div class="space-y-6 pb-6">
    <section class="rounded-3xl border border-white/35 bg-gradient-to-r from-cyan-700 via-cyan-600 to-emerald-500 p-6 text-white shadow-2xl shadow-cyan-900/20">
      <p class="text-xs uppercase tracking-[0.22em] text-cyan-100">Prescriptions</p>
      <h1 class="mt-2 text-2xl font-bold">Medication Overview</h1>
      <p class="mt-1 text-sm text-cyan-50/90">Track your active medications, expiry dates, and refill availability.</p>
    </section>

    <section class="grid gap-4 sm:grid-cols-3">
      <article class="rounded-2xl border border-cyan-100/70 bg-white/90 p-4 shadow-lg shadow-cyan-900/5">
        <p class="text-sm text-slate-500">Total Prescriptions</p>
        <p class="mt-1 text-2xl font-bold text-slate-900">{{ items.length }}</p>
      </article>
      <article class="rounded-2xl border border-cyan-100/70 bg-white/90 p-4 shadow-lg shadow-cyan-900/5">
        <p class="text-sm text-slate-500">Active</p>
        <p class="mt-1 text-2xl font-bold text-emerald-700">{{ activeCount }}</p>
      </article>
      <article class="rounded-2xl border border-cyan-100/70 bg-white/90 p-4 shadow-lg shadow-cyan-900/5">
        <p class="text-sm text-slate-500">Expired</p>
        <p class="mt-1 text-2xl font-bold text-rose-700">{{ expiredCount }}</p>
      </article>
    </section>

    <section class="overflow-hidden rounded-3xl border border-cyan-100/70 bg-white/90 shadow-lg shadow-cyan-900/5">
      <div class="border-b border-cyan-100 bg-cyan-50/75 px-5 py-4">
        <h2 class="text-lg font-bold text-slate-900">Prescription List</h2>
      </div>

      <div v-if="items.length === 0" class="px-5 py-12 text-center">
        <p class="text-sm text-slate-500">No prescriptions available.</p>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="text-left text-xs uppercase tracking-[0.12em] text-slate-500">
              <th class="px-5 py-3">Medication</th>
              <th class="px-5 py-3">Dosage</th>
              <th class="px-5 py-3">Frequency</th>
              <th class="px-5 py-3">Prescribed By</th>
              <th class="px-5 py-3">Issued</th>
              <th class="px-5 py-3">Expiry</th>
              <th class="px-5 py-3">Refills</th>
              <th class="px-5 py-3">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="item in items" :key="item.id" class="hover:bg-cyan-50/50">
              <td class="px-5 py-4 font-semibold text-slate-800">{{ item.medication }}</td>
              <td class="px-5 py-4 text-slate-600">{{ item.dosage }}</td>
              <td class="px-5 py-4 text-slate-600">{{ item.frequency }}</td>
              <td class="px-5 py-4 text-slate-600">{{ item.prescribed_by }}</td>
              <td class="px-5 py-4 text-slate-600">{{ formatDate(item.issued_date) }}</td>
              <td class="px-5 py-4 text-slate-600">{{ formatDate(item.expiry_date) }}</td>
              <td class="px-5 py-4 text-slate-600">{{ item.refills }}</td>
              <td class="px-5 py-4">
                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold" :class="statusClass(item.status)">
                  {{ item.status }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  prescriptions: {
    type: Array,
    default: () => [],
  },
});

const items = computed(() => props.prescriptions || []);
const activeCount = computed(() => items.value.filter((item) => String(item.status).toLowerCase() === 'active').length);
const expiredCount = computed(() => items.value.filter((item) => String(item.status).toLowerCase() === 'expired').length);

function formatDate(dateString) {
  const date = new Date(dateString);
  if (Number.isNaN(date.getTime())) return dateString;
  return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

function statusClass(status) {
  const value = String(status).toLowerCase();
  if (value === 'active') return 'bg-emerald-100 text-emerald-700';
  if (value === 'expired') return 'bg-rose-100 text-rose-700';
  return 'bg-slate-100 text-slate-700';
}
</script>
