<template>
  <div class="space-y-6 pb-6">
    <section class="relative overflow-hidden rounded-3xl border border-white/35 bg-gradient-to-r from-cyan-700 via-cyan-600 to-emerald-500 p-6 text-white shadow-2xl shadow-cyan-900/20">
      <div class="pointer-events-none absolute -right-16 -top-16 h-44 w-44 rounded-full bg-white/10 blur-2xl"></div>
      <div class="pointer-events-none absolute -bottom-20 left-1/3 h-56 w-56 rounded-full bg-emerald-300/20 blur-3xl"></div>

      <div class="relative grid gap-4 lg:grid-cols-[1.7fr_1fr] lg:items-end">
        <div>
          <p class="text-xs uppercase tracking-[0.2em] text-cyan-100">Welcome back</p>
          <h1 class="mt-3 text-3xl font-bold leading-tight">{{ greetingTitle }}</h1>
          <p class="mt-3 max-w-xl text-sm text-cyan-50/90">
            Track appointments, prescriptions, and medical records from one secure place designed for your daily care journey.
          </p>
        </div>

        <div class="rounded-2xl border border-white/20 bg-white/10 p-4 backdrop-blur">
          <p class="text-xs uppercase tracking-[0.18em] text-cyan-100">Next step</p>
          <p class="mt-2 text-3xl font-bold">{{ stats.upcomingAppointments }}</p>
          <p class="mt-1 text-sm text-cyan-50">upcoming appointments</p>
          <Link
            href="/patient/appointments"
            class="mt-4 inline-flex rounded-xl bg-white px-4 py-2 text-sm font-semibold text-cyan-800 transition hover:bg-cyan-50"
          >
            View appointments
          </Link>
        </div>
      </div>
    </section>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
      <article
        v-for="item in metricCards"
        :key="item.title"
        class="rounded-2xl border border-cyan-100/70 bg-white/90 p-5 shadow-lg shadow-cyan-900/5"
      >
        <p class="text-sm font-medium text-slate-500">{{ item.title }}</p>
        <p class="mt-2 text-3xl font-bold text-slate-900">{{ item.value }}</p>
        <p class="mt-2 text-xs uppercase tracking-[0.16em]" :class="item.accentClass">{{ item.note }}</p>
      </article>
    </section>

    <section class="grid gap-5 xl:grid-cols-[1.6fr_1fr]">
      <div class="rounded-3xl border border-cyan-100/70 bg-white/90 p-5 shadow-lg shadow-cyan-900/5">
        <div class="flex items-center justify-between gap-3">
          <h2 class="text-lg font-bold text-slate-900">Health management</h2>
          <Link href="/patient/appointments" class="text-sm font-semibold text-cyan-700 hover:text-cyan-800">Open</Link>
        </div>

        <div class="mt-4 grid gap-3 sm:grid-cols-2">
          <Link
            href="/patient/appointments/create"
            class="rounded-2xl border border-cyan-200 bg-cyan-50/70 p-4 transition hover:border-cyan-300 hover:bg-cyan-100/70"
          >
            <p class="text-sm font-semibold text-slate-900">Book appointment</p>
            <p class="mt-1 text-sm text-slate-600">Schedule your next visit with available doctors.</p>
          </Link>

          <Link
            href="/patient/medical-history"
            class="rounded-2xl border border-emerald-200 bg-emerald-50/70 p-4 transition hover:border-emerald-300 hover:bg-emerald-100/70"
          >
            <p class="text-sm font-semibold text-slate-900">Medical history</p>
            <p class="mt-1 text-sm text-slate-600">Review your records, diagnosis, and care timeline.</p>
          </Link>
        </div>
      </div>

      <div class="rounded-3xl border border-cyan-100/70 bg-white/90 p-5 shadow-lg shadow-cyan-900/5">
        <h2 class="text-lg font-bold text-slate-900">Quick actions</h2>
        <div class="mt-4 space-y-3">
          <Link href="/patient/appointments" class="block rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-cyan-300 hover:text-cyan-800">
            Manage appointments
          </Link>
          <Link href="/patient/prescriptions" class="block rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-cyan-300 hover:text-cyan-800">
            Check prescriptions
          </Link>
          <Link href="/patient/doctors" class="block rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-cyan-300 hover:text-cyan-800">
            Explore doctors
          </Link>
        </div>
      </div>
    </section>

    <section class="overflow-hidden rounded-3xl border border-cyan-100/70 bg-white/90 shadow-lg shadow-cyan-900/5">
      <div class="border-b border-cyan-100 bg-cyan-50/75 px-5 py-4">
        <h2 class="text-lg font-bold text-slate-900">Upcoming appointments</h2>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="text-left text-xs uppercase tracking-[0.12em] text-slate-500">
              <th class="px-5 py-3">Doctor</th>
              <th class="px-5 py-3">Date & Time</th>
              <th class="px-5 py-3">Type</th>
              <th class="px-5 py-3">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="appointment in upcomingAppointments" :key="`${appointment.doctor}-${appointment.datetime}`" class="hover:bg-cyan-50/50">
              <td class="px-5 py-4 font-semibold text-slate-800">{{ appointment.doctor }}</td>
              <td class="px-5 py-4 text-slate-600">{{ appointment.datetime }}</td>
              <td class="px-5 py-4 text-slate-600">{{ appointment.type }}</td>
              <td class="px-5 py-4">
                <span
                  class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold"
                  :class="appointment.status === 'Confirmed' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'"
                >
                  {{ appointment.status }}
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
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  user: {
    type: Object,
    default: () => ({}),
  },
});

const stats = {
  upcomingAppointments: 2,
  activePrescriptions: 4,
  medicalRecords: 12,
  assignedDoctors: 3,
};

const metricCards = [
  { title: 'Upcoming appointments', value: stats.upcomingAppointments, note: 'Next 7 days', accentClass: 'text-cyan-700' },
  { title: 'Active prescriptions', value: stats.activePrescriptions, note: 'Current medicines', accentClass: 'text-emerald-700' },
  { title: 'Medical records', value: stats.medicalRecords, note: 'History entries', accentClass: 'text-indigo-700' },
  { title: 'Assigned doctors', value: stats.assignedDoctors, note: 'Care team', accentClass: 'text-amber-700' },
];

const upcomingAppointments = [
  { doctor: 'Dr. Ahmed Hassan', datetime: 'March 15, 2026 - 10:00 AM', type: 'Consultation', status: 'Confirmed' },
  { doctor: 'Dr. Fatima Mahmoud', datetime: 'March 18, 2026 - 2:30 PM', type: 'Follow-up', status: 'Pending' },
  { doctor: 'Dr. Mohammed Ali', datetime: 'March 22, 2026 - 9:00 AM', type: 'Check-up', status: 'Confirmed' },
];

const greetingTitle = computed(() => {
  const patientName = props.user?.name;
  return patientName ? `Hello, ${patientName}` : 'Hello, Patient';
});
</script>
