<template>
  <DoctorLayout>
    <template #header>Doctor Dashboard</template>

    <div class="space-y-6 pb-6">
      <section class="relative overflow-hidden rounded-3xl border border-white/35 bg-gradient-to-r from-teal-700 via-teal-800 to-emerald-700 p-6 text-white shadow-2xl shadow-teal-900/20">
        <div class="pointer-events-none absolute -right-16 -top-16 h-44 w-44 rounded-full bg-white/10 blur-2xl"></div>
        <div class="pointer-events-none absolute -bottom-20 left-1/3 h-56 w-56 rounded-full bg-emerald-300/20 blur-3xl"></div>

        <div class="relative grid gap-4 lg:grid-cols-[1.7fr_1fr] lg:items-end">
          <div>
            <p class="text-xs uppercase tracking-[0.2em] text-teal-100">Welcome back</p>
            <h1 class="mt-3 text-3xl font-bold leading-tight">{{ greetingTitle }}</h1>
            <p class="mt-3 max-w-xl text-sm text-teal-50/90">
              Keep your daily schedule in control, check patient status quickly, and move from appointments to prescriptions without friction.
            </p>
          </div>

          <div class="rounded-2xl border border-white/20 bg-white/10 p-4 backdrop-blur">
            <p class="text-xs uppercase tracking-[0.18em] text-teal-100">Today</p>
            <p class="mt-2 text-3xl font-bold">{{ stats.todayAppointments }}</p>
            <p class="mt-1 text-sm text-teal-50">appointments on your calendar</p>
            <Link
              href="/doctor/appointments"
              class="mt-4 inline-flex rounded-xl bg-white px-4 py-2 text-sm font-semibold text-teal-800 transition hover:bg-teal-50"
            >
              Open schedule
            </Link>
          </div>
        </div>
      </section>

      <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article
          v-for="item in metricCards"
          :key="item.title"
          class="rounded-2xl border border-teal-100/70 bg-white/90 p-5 shadow-lg shadow-teal-900/5"
        >
          <p class="text-sm font-medium text-slate-500">{{ item.title }}</p>
          <p class="mt-2 text-3xl font-bold text-slate-900">{{ item.value }}</p>
          <p class="mt-2 text-xs uppercase tracking-[0.16em]" :class="item.accentClass">{{ item.trend }}</p>
        </article>
      </section>

      <section class="grid gap-5 xl:grid-cols-[1.6fr_1fr]">
        <div class="rounded-3xl border border-teal-100/70 bg-white/90 p-5 shadow-lg shadow-teal-900/5">
          <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-bold text-slate-900">Patient workflow</h2>
            <Link href="/doctor/patients" class="text-sm font-semibold text-teal-700 hover:text-teal-800">View all</Link>
          </div>

          <div class="mt-4 grid gap-3 sm:grid-cols-2">
            <Link
              href="/doctor/patients"
              class="rounded-2xl border border-teal-200 bg-teal-50/70 p-4 transition hover:border-teal-300 hover:bg-teal-100/70"
            >
              <p class="text-sm font-semibold text-slate-900">Patients list</p>
              <p class="mt-1 text-sm text-slate-600">Open patient profiles and recent activity.</p>
            </Link>

            <Link
              href="/doctor/patients"
              class="rounded-2xl border border-emerald-200 bg-emerald-50/70 p-4 transition hover:border-emerald-300 hover:bg-emerald-100/70"
            >
              <p class="text-sm font-semibold text-slate-900">Medical records</p>
              <p class="mt-1 text-sm text-slate-600">Open patients first, then access each record safely.</p>
            </Link>
          </div>
        </div>

        <div class="rounded-3xl border border-teal-100/70 bg-white/90 p-5 shadow-lg shadow-teal-900/5">
          <h2 class="text-lg font-bold text-slate-900">Quick actions</h2>
          <div class="mt-4 space-y-3">
            <Link href="/doctor/appointments" class="block rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-teal-300 hover:text-teal-800">
              Manage appointments
            </Link>
            <Link href="/doctor/prescriptions/create" class="block rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-teal-300 hover:text-teal-800">
              Create prescription
            </Link>
            <Link href="/doctor/prescriptions" class="block rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-teal-300 hover:text-teal-800">
              Review prescriptions
            </Link>
          </div>
        </div>
      </section>

      <section class="overflow-hidden rounded-3xl border border-teal-100/70 bg-white/90 shadow-lg shadow-teal-900/5">
        <div class="border-b border-teal-100 bg-teal-50/75 px-5 py-4">
          <h2 class="text-lg font-bold text-slate-900">Today's schedule</h2>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead>
              <tr class="text-left text-xs uppercase tracking-[0.12em] text-slate-500">
                <th class="px-5 py-3">Patient</th>
                <th class="px-5 py-3">Time</th>
                <th class="px-5 py-3">Type</th>
                <th class="px-5 py-3">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="appointment in todaySchedule" :key="appointment.patient" class="hover:bg-teal-50/50">
                <td class="px-5 py-4 font-semibold text-slate-800">{{ appointment.patient }}</td>
                <td class="px-5 py-4 text-slate-600">{{ appointment.time }}</td>
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
  </DoctorLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import DoctorLayout from '../Layouts/DoctorLayout.vue';

const props = defineProps({
  user: {
    type: Object,
    default: () => ({}),
  },
});

const stats = {
  totalPatients: 156,
  todayAppointments: 3,
  pendingPrescriptions: 8,
  consultationHours: 24,
};

const metricCards = [
  { title: 'Total patients', value: stats.totalPatients, trend: 'Active panel', accentClass: 'text-teal-700' },
  { title: "Today's appointments", value: stats.todayAppointments, trend: 'On schedule', accentClass: 'text-emerald-700' },
  { title: 'Pending prescriptions', value: stats.pendingPrescriptions, trend: 'Needs follow-up', accentClass: 'text-amber-700' },
  { title: 'Consultation hours', value: `${stats.consultationHours}h`, trend: 'This week', accentClass: 'text-cyan-700' },
];

const todaySchedule = [
  { patient: 'Ahmed Hassan', time: '10:00 AM', type: 'Consultation', status: 'Confirmed' },
  { patient: 'Fatima Ahmed', time: '11:30 AM', type: 'Follow-up', status: 'Pending' },
  { patient: 'Mohammed Ali', time: '2:00 PM', type: 'Check-up', status: 'Confirmed' },
];

const greetingTitle = computed(() => {
  const doctorName = props.user?.name;
  return doctorName ? `Good shift, Dr. ${doctorName}` : 'Good shift, Doctor';
});
</script>
