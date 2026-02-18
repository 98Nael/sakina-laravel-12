<template>
  <div class="space-y-6">
    <section class="overflow-hidden rounded-3xl border border-white/10 bg-[linear-gradient(135deg,#0b1220,#111827,#1f2937)] p-7 text-slate-100 shadow-2xl shadow-black/20">
      <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
          <p class="text-xs uppercase tracking-[0.22em] text-amber-300">Analytics Center</p>
          <h1 class="mt-3 text-3xl font-bold text-white">Admin Reports</h1>
          <p class="mt-2 text-sm text-slate-300">Track user growth, role distribution, and core platform health.</p>
        </div>
        <div class="flex flex-wrap gap-2">
          <Link href="/admin/reports/users" class="rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/15">
            User Report
          </Link>
          <Link href="/admin/reports/appointments" class="rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 px-4 py-2 text-sm font-semibold text-slate-900 transition hover:from-amber-400 hover:to-orange-400">
            Appointment Report
          </Link>
        </div>
      </div>
    </section>

    <section class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">
      <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <p class="text-xs uppercase tracking-wide text-slate-500">Total Users</p>
        <p class="mt-2 text-3xl font-bold text-slate-900">{{ growth.total_users }}</p>
      </article>
      <article class="rounded-2xl border border-blue-200 bg-blue-50/70 p-4 shadow-sm">
        <p class="text-xs uppercase tracking-wide text-blue-700">New This Month</p>
        <p class="mt-2 text-3xl font-bold text-blue-900">{{ growth.new_users_this_month }}</p>
      </article>
      <article class="rounded-2xl border border-violet-200 bg-violet-50/70 p-4 shadow-sm">
        <p class="text-xs uppercase tracking-wide text-violet-700">Admins</p>
        <p class="mt-2 text-3xl font-bold text-violet-900">{{ growth.admins }}</p>
      </article>
      <article class="rounded-2xl border border-emerald-200 bg-emerald-50/70 p-4 shadow-sm">
        <p class="text-xs uppercase tracking-wide text-emerald-700">Doctors</p>
        <p class="mt-2 text-3xl font-bold text-emerald-900">{{ growth.doctors }}</p>
      </article>
      <article class="rounded-2xl border border-amber-200 bg-amber-50/70 p-4 shadow-sm">
        <p class="text-xs uppercase tracking-wide text-amber-700">Patients</p>
        <p class="mt-2 text-3xl font-bold text-amber-900">{{ growth.patients }}</p>
      </article>
    </section>

    <section class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
      <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <p class="text-xs uppercase tracking-wide text-slate-500">Appointments (All)</p>
        <p class="mt-2 text-3xl font-bold text-slate-900">{{ activity.appointments_total || 0 }}</p>
      </article>
      <article class="rounded-2xl border border-blue-200 bg-blue-50/70 p-4 shadow-sm">
        <p class="text-xs uppercase tracking-wide text-blue-700">Appointments This Month</p>
        <p class="mt-2 text-3xl font-bold text-blue-900">{{ activity.appointments_this_month || 0 }}</p>
      </article>
      <article class="rounded-2xl border border-emerald-200 bg-emerald-50/70 p-4 shadow-sm">
        <p class="text-xs uppercase tracking-wide text-emerald-700">Completed Appointments</p>
        <p class="mt-2 text-3xl font-bold text-emerald-900">{{ activity.completed || 0 }}</p>
      </article>
      <article class="rounded-2xl border border-rose-200 bg-rose-50/70 p-4 shadow-sm">
        <p class="text-xs uppercase tracking-wide text-rose-700">Pending Patient Approvals</p>
        <p class="mt-2 text-3xl font-bold text-rose-900">{{ activity.pending_patients || 0 }}</p>
      </article>
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">
      <article class="xl:col-span-2 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-900">Role Distribution</h2>
        <p class="mt-1 text-sm text-slate-500">Share of accounts by role.</p>

        <div class="mt-5 space-y-4">
          <div v-for="item in distribution" :key="item.key">
            <div class="mb-1 flex items-center justify-between text-xs">
              <span class="font-semibold uppercase tracking-wide text-slate-600">{{ item.label }}</span>
              <span class="font-semibold text-slate-700">{{ item.value }} ({{ item.percent }}%)</span>
            </div>
            <div class="h-2 rounded-full bg-slate-100">
              <div class="h-2 rounded-full" :class="item.barClass" :style="{ width: `${item.percent}%` }"></div>
            </div>
          </div>
        </div>
      </article>

      <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-900">System Health</h2>
        <div class="mt-4 space-y-3">
          <div class="flex items-center justify-between rounded-lg border border-slate-100 bg-slate-50/80 px-3 py-2">
            <span class="text-sm text-slate-600">Database</span>
            <span class="text-xs font-semibold text-emerald-700">{{ health.database_status || '-' }}</span>
          </div>
          <div class="flex items-center justify-between rounded-lg border border-slate-100 bg-slate-50/80 px-3 py-2">
            <span class="text-sm text-slate-600">API</span>
            <span class="text-xs font-semibold text-emerald-700">{{ health.api_status || '-' }}</span>
          </div>
          <div class="flex items-center justify-between rounded-lg border border-slate-100 bg-slate-50/80 px-3 py-2">
            <span class="text-sm text-slate-600">Disk Usage</span>
            <span class="text-xs font-semibold text-slate-800">{{ health.disk_usage || '-' }}</span>
          </div>
          <div class="flex items-center justify-between rounded-lg border border-slate-100 bg-slate-50/80 px-3 py-2">
            <span class="text-sm text-slate-600">Memory Usage</span>
            <span class="text-xs font-semibold text-slate-800">{{ health.memory_usage || '-' }}</span>
          </div>
        </div>
      </article>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-semibold text-slate-900">Monthly User Registrations</h2>
        <p class="text-xs text-slate-500">
          Last update: {{ freshness.generated_at || '-' }} ({{ freshness.timezone || '-' }})
        </p>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-slate-50">
            <tr>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Month</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">New Users</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in monthlyRegistrations" :key="item.month" class="border-t border-slate-100">
              <td class="px-4 py-3 text-slate-800">{{ item.month }}</td>
              <td class="px-4 py-3 font-semibold text-slate-900">{{ item.users }}</td>
            </tr>
            <tr v-if="!monthlyRegistrations.length">
              <td colspan="2" class="px-4 py-6 text-center text-slate-500">No registration data.</td>
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
  reports: {
    type: Object,
    default: () => ({}),
  },
});

const growth = computed(() => props.reports?.user_growth || {});
const health = computed(() => props.reports?.system_health || {});
const activity = computed(() => props.reports?.platform_activity || {});
const monthlyRegistrations = computed(() => props.reports?.monthly_registrations || []);
const freshness = computed(() => props.reports?.data_freshness || {});

const totalUsers = computed(() => Number(growth.value.total_users || 0));

const distribution = computed(() => {
  const safePercent = (value) => {
    if (!totalUsers.value) return 0;
    return Math.round((Number(value || 0) / totalUsers.value) * 100);
  };

  return [
    { key: 'admins', label: 'Admins', value: Number(growth.value.admins || 0), percent: safePercent(growth.value.admins), barClass: 'bg-violet-500' },
    { key: 'doctors', label: 'Doctors', value: Number(growth.value.doctors || 0), percent: safePercent(growth.value.doctors), barClass: 'bg-emerald-500' },
    { key: 'patients', label: 'Patients', value: Number(growth.value.patients || 0), percent: safePercent(growth.value.patients), barClass: 'bg-amber-500' },
  ];
});
</script>
