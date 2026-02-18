<template>
  <div class="space-y-6">
    <section class="overflow-hidden rounded-3xl border border-white/10 bg-[linear-gradient(135deg,#0b1220,#111827,#1f2937)] p-7 text-slate-100 shadow-2xl shadow-black/20">
      <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
          <p class="text-xs uppercase tracking-[0.22em] text-amber-300">Report Module</p>
          <h1 class="mt-3 text-3xl font-bold text-white">Appointment Report</h1>
          <p class="mt-2 text-sm text-slate-300">Daily appointment volume and status performance overview.</p>
        </div>
        <div class="flex flex-wrap gap-2">
          <Link href="/admin/reports" class="rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/15">
            Back to Reports
          </Link>
          <a
            href="/admin/reports/appointments/Export"
            class="rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 px-4 py-2 text-sm font-semibold text-slate-900 transition hover:from-amber-400 hover:to-orange-400"
          >
            Export
          </a>
        </div>
      </div>
    </section>

    <section class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">
      <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <p class="text-xs uppercase tracking-wide text-slate-500">Total Appointments</p>
        <p class="mt-2 text-3xl font-bold text-slate-900">{{ totals.total }}</p>
      </article>
      <article class="rounded-2xl border border-amber-200 bg-amber-50/70 p-4 shadow-sm">
        <p class="text-xs uppercase tracking-wide text-amber-700">Scheduled</p>
        <p class="mt-2 text-3xl font-bold text-amber-900">{{ totals.scheduled }}</p>
      </article>
      <article class="rounded-2xl border border-emerald-200 bg-emerald-50/70 p-4 shadow-sm">
        <p class="text-xs uppercase tracking-wide text-emerald-700">Completed</p>
        <p class="mt-2 text-3xl font-bold text-emerald-900">{{ totals.completed }}</p>
      </article>
      <article class="rounded-2xl border border-rose-200 bg-rose-50/70 p-4 shadow-sm">
        <p class="text-xs uppercase tracking-wide text-rose-700">Cancelled</p>
        <p class="mt-2 text-3xl font-bold text-rose-900">{{ totals.cancelled }}</p>
      </article>
      <article class="rounded-2xl border border-slate-300 bg-slate-100/80 p-4 shadow-sm">
        <p class="text-xs uppercase tracking-wide text-slate-600">No-show</p>
        <p class="mt-2 text-3xl font-bold text-slate-800">{{ totals.noShow }}</p>
      </article>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
      <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-6 py-4">
        <h2 class="text-lg font-semibold text-slate-900">Daily Breakdown</h2>
        <p class="text-xs text-slate-500">Last {{ groupedRows.length }} active day(s)</p>
      </div>

      <div v-if="!groupedRows.length" class="px-6 py-10 text-center text-sm text-slate-500">
        No appointment data available.
      </div>

      <div v-else class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-slate-50">
            <tr>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Date</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Total</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Scheduled</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Completed</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Cancelled</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">No-show</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="row in groupedRows" :key="row.date" class="border-t border-slate-100">
              <td class="px-4 py-3 font-medium text-slate-900">{{ formatDate(row.date) }}</td>
              <td class="px-4 py-3 text-slate-700">{{ row.total }}</td>
              <td class="px-4 py-3 text-amber-700">{{ row.scheduled }}</td>
              <td class="px-4 py-3 text-emerald-700">{{ row.completed }}</td>
              <td class="px-4 py-3 text-rose-700">{{ row.cancelled }}</td>
              <td class="px-4 py-3 text-slate-700">{{ row.noShow }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  appointments: {
    type: Array,
    default: () => [],
  },
});

const groupedRows = computed(() => {
  const grouped = {};

  for (const item of props.appointments || []) {
    const date = item.appointment_date;
    const status = String(item.status || '').toLowerCase();
    const count = Number(item.count || 0);

    if (!grouped[date]) {
      grouped[date] = {
        date,
        total: 0,
        scheduled: 0,
        completed: 0,
        cancelled: 0,
        noShow: 0,
      };
    }

    grouped[date].total += count;
    if (status === 'scheduled') grouped[date].scheduled += count;
    else if (status === 'completed') grouped[date].completed += count;
    else if (status === 'cancelled') grouped[date].cancelled += count;
    else if (status === 'no-show') grouped[date].noShow += count;
  }

  return Object.values(grouped).sort((a, b) => String(b.date).localeCompare(String(a.date)));
});

const totals = computed(() => {
  return groupedRows.value.reduce(
    (acc, item) => {
      acc.total += item.total;
      acc.scheduled += item.scheduled;
      acc.completed += item.completed;
      acc.cancelled += item.cancelled;
      acc.noShow += item.noShow;
      return acc;
    },
    { total: 0, scheduled: 0, completed: 0, cancelled: 0, noShow: 0 },
  );
});

function formatDate(value) {
  if (!value) return '-';
  return new Date(value).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}
</script>
