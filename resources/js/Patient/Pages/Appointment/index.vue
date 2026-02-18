<template>
  <div class="space-y-6 pb-6">
    <section class="rounded-3xl border border-white/35 bg-gradient-to-r from-cyan-700 via-cyan-600 to-emerald-500 p-6 text-white shadow-2xl shadow-cyan-900/20">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
          <p class="text-xs uppercase tracking-[0.22em] text-cyan-100">Patient Appointments</p>
          <h1 class="mt-2 text-2xl font-bold">My Appointments</h1>
          <p class="mt-1 text-sm text-cyan-50/90">Track upcoming visits and manage your schedule quickly.</p>
        </div>
        <Link href="/patient/appointments/create" class="rounded-xl bg-white px-4 py-2 text-sm font-semibold text-cyan-800 transition hover:bg-cyan-50">
          Book Appointment
        </Link>
      </div>
    </section>

    <section class="grid gap-4 sm:grid-cols-3">
      <article class="rounded-2xl border border-cyan-100/70 bg-white/90 p-4 shadow-lg shadow-cyan-900/5">
        <p class="text-sm text-slate-500">Total</p>
        <p class="mt-1 text-2xl font-bold text-slate-900">{{ appointments.length }}</p>
      </article>
      <article class="rounded-2xl border border-cyan-100/70 bg-white/90 p-4 shadow-lg shadow-cyan-900/5">
        <p class="text-sm text-slate-500">Confirmed</p>
        <p class="mt-1 text-2xl font-bold text-emerald-700">{{ confirmedCount }}</p>
      </article>
      <article class="rounded-2xl border border-cyan-100/70 bg-white/90 p-4 shadow-lg shadow-cyan-900/5">
        <p class="text-sm text-slate-500">Pending/Scheduled</p>
        <p class="mt-1 text-2xl font-bold text-amber-700">{{ pendingCount }}</p>
      </article>
    </section>

    <section class="overflow-hidden rounded-3xl border border-cyan-100/70 bg-white/90 shadow-lg shadow-cyan-900/5">
      <div class="border-b border-cyan-100 bg-cyan-50/75 px-5 py-4">
        <h2 class="text-lg font-bold text-slate-900">Appointment List</h2>
      </div>

      <div v-if="appointments.length === 0" class="px-5 py-12 text-center">
        <p class="text-sm font-medium text-slate-500">No appointments yet.</p>
        <Link href="/patient/appointments/create" class="mt-3 inline-flex rounded-xl bg-cyan-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-cyan-800">
          Create your first appointment
        </Link>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="text-left text-xs uppercase tracking-[0.12em] text-slate-500">
              <th class="px-5 py-3">Doctor</th>
              <th class="px-5 py-3">Specialty</th>
              <th class="px-5 py-3">Date</th>
              <th class="px-5 py-3">Time</th>
              <th class="px-5 py-3">Status</th>
              <th class="px-5 py-3">Location</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="appointment in appointments" :key="appointment.id" class="hover:bg-cyan-50/50">
              <td class="px-5 py-4 font-semibold text-slate-800">{{ appointment.doctor_name }}</td>
              <td class="px-5 py-4 text-slate-600">{{ appointment.specialty }}</td>
              <td class="px-5 py-4 text-slate-600">{{ formatDate(appointment.date) }}</td>
              <td class="px-5 py-4 text-slate-600">{{ appointment.time }}</td>
              <td class="px-5 py-4">
                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold" :class="statusClass(appointment.status)">
                  {{ appointment.status }}
                </span>
              </td>
              <td class="px-5 py-4 text-slate-600">{{ appointment.location }}</td>
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

const appointments = computed(() => props.appointments || []);

const confirmedCount = computed(() =>
  appointments.value.filter((item) => String(item.status).toLowerCase() === 'confirmed').length,
);

const pendingCount = computed(() =>
  appointments.value.filter((item) => ['pending', 'scheduled'].includes(String(item.status).toLowerCase())).length,
);

function formatDate(dateString) {
  const date = new Date(dateString);
  if (Number.isNaN(date.getTime())) return dateString;
  return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

function statusClass(status) {
  const value = String(status).toLowerCase();
  if (value === 'confirmed') return 'bg-emerald-100 text-emerald-700';
  if (value === 'scheduled' || value === 'pending') return 'bg-amber-100 text-amber-700';
  if (value === 'cancelled') return 'bg-rose-100 text-rose-700';
  return 'bg-slate-100 text-slate-700';
}
</script>
