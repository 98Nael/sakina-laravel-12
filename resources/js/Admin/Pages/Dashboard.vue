<template>
  <div class="space-y-6">
    <section class="overflow-hidden rounded-3xl border border-white/10 bg-[linear-gradient(135deg,#0f172a,#111827,#1f2937)] p-7 text-slate-100 shadow-2xl shadow-black/20">
      <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
          <p class="text-xs uppercase tracking-[0.22em] text-amber-300">Admin Console</p>
          <h1 class="mt-3 text-3xl font-bold text-white">Operations Dashboard</h1>
          <p class="mt-2 text-sm text-slate-300">Welcome back, {{ user?.name || 'Admin' }}. Monitor users, approvals, and system status.</p>
        </div>
        <Link
          href="/admin/reports"
          class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 px-4 py-2.5 text-sm font-semibold text-slate-900 transition hover:from-amber-400 hover:to-orange-400"
        >
          View Reports
        </Link>
      </div>
    </section>

    <section class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
      <div class="rounded-2xl border border-amber-400/30 bg-[#111827] p-5 text-slate-100 shadow-xl shadow-black/20">
        <p class="text-xs uppercase tracking-wide text-slate-400">Total Users</p>
        <p class="mt-3 text-3xl font-bold text-white">{{ stats.totalUsers }}</p>
        <p class="mt-2 text-xs text-amber-300">Platform-wide accounts</p>
      </div>

      <div class="rounded-2xl border border-blue-400/30 bg-[#111827] p-5 text-slate-100 shadow-xl shadow-black/20">
        <p class="text-xs uppercase tracking-wide text-slate-400">Doctors</p>
        <p class="mt-3 text-3xl font-bold text-white">{{ stats.doctors }}</p>
        <p class="mt-2 text-xs text-blue-300">Active clinical staff</p>
      </div>

      <div class="rounded-2xl border border-emerald-400/30 bg-[#111827] p-5 text-slate-100 shadow-xl shadow-black/20">
        <p class="text-xs uppercase tracking-wide text-slate-400">Patients</p>
        <p class="mt-3 text-3xl font-bold text-white">{{ stats.patients }}</p>
        <p class="mt-2 text-xs text-emerald-300">Registered profiles</p>
      </div>

      <div class="rounded-2xl border border-rose-400/30 bg-[#111827] p-5 text-slate-100 shadow-xl shadow-black/20">
        <p class="text-xs uppercase tracking-wide text-slate-400">Pending Approvals</p>
        <p class="mt-3 text-3xl font-bold text-white">{{ stats.pendingPatients }}</p>
        <p class="mt-2 text-xs text-rose-300">Needs admin action</p>
      </div>
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">
      <div class="xl:col-span-2 rounded-3xl border border-white/10 bg-[#0f172a] p-6 text-slate-100 shadow-2xl shadow-black/20">
        <div class="mb-5 flex items-center justify-between">
          <h2 class="text-lg font-bold text-white">Management Shortcuts</h2>
          <span class="rounded-full border border-amber-400/30 bg-amber-400/10 px-3 py-1 text-xs text-amber-300">Quick Actions</span>
        </div>

        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
          <Link href="/admin/users" class="rounded-xl border border-slate-700 bg-slate-800/60 p-4 transition hover:border-amber-400/40 hover:bg-slate-800">
            <p class="text-sm font-semibold text-white">Users</p>
            <p class="mt-1 text-xs text-slate-400">Manage roles and account lifecycle</p>
          </Link>

          <Link href="/admin/doctors" class="rounded-xl border border-slate-700 bg-slate-800/60 p-4 transition hover:border-amber-400/40 hover:bg-slate-800">
            <p class="text-sm font-semibold text-white">Doctors</p>
            <p class="mt-1 text-xs text-slate-400">Review and manage doctor records</p>
          </Link>

          <Link href="/admin/patients" class="rounded-xl border border-slate-700 bg-slate-800/60 p-4 transition hover:border-amber-400/40 hover:bg-slate-800">
            <p class="text-sm font-semibold text-white">Patients</p>
            <p class="mt-1 text-xs text-slate-400">View all patient profiles</p>
          </Link>

          <Link href="/admin/patients/pending" class="rounded-xl border border-slate-700 bg-slate-800/60 p-4 transition hover:border-amber-400/40 hover:bg-slate-800">
            <div class="flex items-center justify-between gap-3">
              <p class="text-sm font-semibold text-white">Pending Patients</p>
              <span class="inline-flex min-w-8 items-center justify-center rounded-full border border-rose-300/40 bg-rose-500/20 px-2 py-0.5 text-xs font-bold text-rose-200">
                {{ stats.pendingPatients }}
              </span>
            </div>
            <p class="mt-1 text-xs text-slate-400">Approve or reject new registrations</p>
          </Link>

          <Link href="/admin/contact-messages" class="rounded-xl border border-slate-700 bg-slate-800/60 p-4 transition hover:border-amber-400/40 hover:bg-slate-800">
            <p class="text-sm font-semibold text-white">Contact Control</p>
            <p class="mt-1 text-xs text-slate-400">Review and manage Contact Us messages</p>
          </Link>
        </div>
      </div>

      <div class="rounded-3xl border border-white/10 bg-[#111827] p-6 text-slate-100 shadow-2xl shadow-black/20">
        <h2 class="text-lg font-bold text-white">System Status</h2>
        <p class="mt-2 text-sm text-slate-300">
          Today appointments: {{ stats.appointmentsToday }} · Completed: {{ stats.completedToday }}
        </p>

        <div class="mt-5 space-y-3">
          <div class="flex items-center justify-between rounded-lg border border-slate-700 bg-slate-800/60 px-3 py-2">
            <span class="text-sm text-slate-300">Auth Service</span>
            <span class="text-xs font-semibold text-emerald-300">Healthy</span>
          </div>
          <div class="flex items-center justify-between rounded-lg border border-slate-700 bg-slate-800/60 px-3 py-2">
            <span class="text-sm text-slate-300">Database</span>
            <span class="text-xs font-semibold" :class="system.database === 'Connected' ? 'text-emerald-300' : 'text-rose-300'">
              {{ system.database }}
            </span>
          </div>
          <div class="flex items-center justify-between rounded-lg border border-slate-700 bg-slate-800/60 px-3 py-2">
            <span class="text-sm text-slate-300">Queue</span>
            <span class="text-xs font-semibold text-amber-300">{{ system.queue }}</span>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
  user: {
    type: Object,
    default: null,
  },
  stats: {
    type: Object,
    default: () => ({
      totalUsers: 0,
      doctors: 0,
      patients: 0,
      pendingPatients: 0,
      appointmentsToday: 0,
      completedToday: 0,
    }),
  },
  system: {
    type: Object,
    default: () => ({
      database: 'Unavailable',
      api: 'Operational',
      queue: 'Idle',
    }),
  },
});
</script>
