<template>
  <div class="space-y-6">
    <section class="overflow-hidden rounded-3xl border border-white/10 bg-[linear-gradient(135deg,#0b1220,#111827,#1f2937)] p-7 text-slate-100 shadow-2xl shadow-black/20">
      <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
          <p class="text-xs uppercase tracking-[0.22em] text-amber-300">Report Module</p>
          <h1 class="mt-3 text-3xl font-bold text-white">User Report</h1>
          <p class="mt-2 text-sm text-slate-300">Comprehensive view of account distribution and registration activity.</p>
        </div>
        <div class="flex flex-wrap gap-2">
          <Link href="/admin/reports" class="rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/15">
            Back to Reports
          </Link>
          <a
            href="/admin/reports/users/Export"
            class="rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 px-4 py-2 text-sm font-semibold text-slate-900 transition hover:from-amber-400 hover:to-orange-400"
          >
            Export
          </a>
        </div>
      </div>
    </section>

    <section class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">
      <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <p class="text-xs uppercase tracking-wide text-slate-500">Total Users</p>
        <p class="mt-2 text-3xl font-bold text-slate-900">{{ totals.total }}</p>
      </article>
      <article class="rounded-2xl border border-violet-200 bg-violet-50/70 p-4 shadow-sm">
        <p class="text-xs uppercase tracking-wide text-violet-700">Admins</p>
        <p class="mt-2 text-3xl font-bold text-violet-900">{{ totals.admin }}</p>
      </article>
      <article class="rounded-2xl border border-emerald-200 bg-emerald-50/70 p-4 shadow-sm">
        <p class="text-xs uppercase tracking-wide text-emerald-700">Doctors</p>
        <p class="mt-2 text-3xl font-bold text-emerald-900">{{ totals.doctor }}</p>
      </article>
      <article class="rounded-2xl border border-amber-200 bg-amber-50/70 p-4 shadow-sm">
        <p class="text-xs uppercase tracking-wide text-amber-700">Patients</p>
        <p class="mt-2 text-3xl font-bold text-amber-900">{{ totals.patient }}</p>
      </article>
      <article class="rounded-2xl border border-slate-300 bg-slate-100/80 p-4 shadow-sm">
        <p class="text-xs uppercase tracking-wide text-slate-600">Other Roles</p>
        <p class="mt-2 text-3xl font-bold text-slate-800">{{ totals.other }}</p>
      </article>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
      <div class="border-b border-slate-200 px-6 py-4">
        <h2 class="text-lg font-semibold text-slate-900">Users Table</h2>
      </div>

      <div v-if="!rows.length" class="px-6 py-10 text-center text-sm text-slate-500">
        No users found.
      </div>

      <div v-else class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-slate-50">
            <tr>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">ID</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Name</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Email</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Role</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Created</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in rows" :key="item.id" class="border-t border-slate-100">
              <td class="px-4 py-3 text-slate-700">#{{ item.id }}</td>
              <td class="px-4 py-3 font-medium text-slate-900">{{ item.name }}</td>
              <td class="px-4 py-3 text-slate-700">{{ item.email }}</td>
              <td class="px-4 py-3">
                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold uppercase" :class="roleClass(item.role)">
                  {{ item.role }}
                </span>
              </td>
              <td class="px-4 py-3 text-slate-600">{{ formatDate(item.created_at) }}</td>
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
  users: {
    type: Array,
    default: () => [],
  },
});

const rows = computed(() => props.users || []);

const totals = computed(() => {
  const counter = {
    total: rows.value.length,
    admin: 0,
    doctor: 0,
    patient: 0,
    other: 0,
  };

  for (const item of rows.value) {
    const role = String(item.role || '').toLowerCase();
    if (role === 'admin') counter.admin += 1;
    else if (role === 'doctor') counter.doctor += 1;
    else if (role === 'patient') counter.patient += 1;
    else counter.other += 1;
  }

  return counter;
});

function roleClass(role) {
  const value = String(role || '').toLowerCase();
  if (value === 'admin') return 'bg-violet-100 text-violet-700';
  if (value === 'doctor') return 'bg-emerald-100 text-emerald-700';
  if (value === 'patient') return 'bg-amber-100 text-amber-700';
  return 'bg-slate-100 text-slate-700';
}

function formatDate(value) {
  if (!value) return '-';
  const date = new Date(value);
  if (Number.isNaN(date.getTime())) return value;
  return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}
</script>
