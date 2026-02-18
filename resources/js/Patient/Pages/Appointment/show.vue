<template>
  <div class="space-y-6 pb-6">
    <section class="rounded-3xl border border-white/35 bg-gradient-to-r from-cyan-700 via-cyan-600 to-emerald-500 p-6 text-white shadow-2xl shadow-cyan-900/20">
      <p class="text-xs uppercase tracking-[0.22em] text-cyan-100">Appointment</p>
      <h1 class="mt-2 text-2xl font-bold">Appointment Details</h1>
      <p class="mt-1 text-sm text-cyan-50/90">Review all details for this booking.</p>
    </section>

    <section class="rounded-3xl border border-cyan-100/70 bg-white/90 p-6 shadow-lg shadow-cyan-900/5">
      <div class="grid gap-4 sm:grid-cols-2">
        <p><span class="font-semibold text-slate-700">Doctor:</span> {{ appointment.doctor_name }}</p>
        <p><span class="font-semibold text-slate-700">Specialization:</span> {{ appointment.specialization }}</p>
        <p><span class="font-semibold text-slate-700">Date:</span> {{ appointment.date }}</p>
        <p><span class="font-semibold text-slate-700">Time:</span> {{ appointment.time }}</p>
        <p><span class="font-semibold text-slate-700">Status:</span> {{ appointment.status }}</p>
        <p><span class="font-semibold text-slate-700">Location:</span> {{ appointment.location }}</p>
        <p class="sm:col-span-2"><span class="font-semibold text-slate-700">Reason:</span> {{ appointment.reason || '-' }}</p>
        <p class="sm:col-span-2"><span class="font-semibold text-slate-700">Notes:</span> {{ appointment.notes || '-' }}</p>
      </div>
      <div class="mt-6 flex gap-3">
        <Link :href="`/patient/appointments/${appointment.id}/edit`" class="rounded-xl border border-cyan-300 px-4 py-2 text-sm font-semibold text-cyan-700 hover:bg-cyan-50">
          Reschedule
        </Link>
        <Link href="/patient/appointments" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
          Back
        </Link>
      </div>
    </section>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
  appointment: {
    type: Object,
    default: () => ({}),
  },
});
</script>
