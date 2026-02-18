<template>
  <DoctorLayout>
    <template #header>Prescription Details</template>

    <div class="space-y-6 pb-6">
      <section class="rounded-3xl border border-white/35 bg-gradient-to-r from-teal-700 via-teal-800 to-emerald-700 p-6 text-white shadow-2xl shadow-teal-900/20">
        <p class="text-xs uppercase tracking-[0.22em] text-teal-100">Prescription Record</p>
        <h1 class="mt-2 text-2xl font-bold">{{ details.medication || 'Prescription' }}</h1>
        <p class="mt-1 text-sm text-teal-50/90">
          Patient: {{ details.patient_name || '-' }} | Issued: {{ formatDate(details.issued_date) }}
        </p>
      </section>

      <section class="grid gap-4 sm:grid-cols-3">
        <article class="rounded-2xl border border-teal-100/70 bg-white/90 p-4 shadow-lg shadow-teal-900/5">
          <p class="text-sm text-slate-500">Dosage</p>
          <p class="mt-1 text-xl font-bold text-slate-900">{{ details.dosage || '-' }}</p>
        </article>
        <article class="rounded-2xl border border-teal-100/70 bg-white/90 p-4 shadow-lg shadow-teal-900/5">
          <p class="text-sm text-slate-500">Frequency</p>
          <p class="mt-1 text-xl font-bold text-slate-900">{{ details.frequency || '-' }}</p>
        </article>
        <article class="rounded-2xl border border-teal-100/70 bg-white/90 p-4 shadow-lg shadow-teal-900/5">
          <p class="text-sm text-slate-500">Status</p>
          <span class="mt-2 inline-flex rounded-full px-2.5 py-1 text-xs font-semibold" :class="statusClass(details.status)">
            {{ details.status || 'unknown' }}
          </span>
        </article>
      </section>

      <section class="rounded-3xl border border-teal-100/70 bg-white/90 p-6 shadow-lg shadow-teal-900/5">
        <h2 class="text-lg font-bold text-slate-900">Prescription Information</h2>

        <div class="mt-4 grid gap-4 md:grid-cols-2">
          <div class="rounded-xl border border-slate-100 bg-slate-50/70 p-4">
            <p class="text-xs uppercase tracking-[0.12em] text-slate-500">Prescription ID</p>
            <p class="mt-1 text-sm font-semibold text-slate-800">#{{ details.id || '-' }}</p>
          </div>
          <div class="rounded-xl border border-slate-100 bg-slate-50/70 p-4">
            <p class="text-xs uppercase tracking-[0.12em] text-slate-500">Patient ID</p>
            <p class="mt-1 text-sm font-semibold text-slate-800">#{{ details.patient_id || '-' }}</p>
          </div>
          <div class="rounded-xl border border-slate-100 bg-slate-50/70 p-4">
            <p class="text-xs uppercase tracking-[0.12em] text-slate-500">Patient Name</p>
            <p class="mt-1 text-sm font-semibold text-slate-800">{{ details.patient_name || '-' }}</p>
          </div>
          <div class="rounded-xl border border-slate-100 bg-slate-50/70 p-4">
            <p class="text-xs uppercase tracking-[0.12em] text-slate-500">Duration</p>
            <p class="mt-1 text-sm font-semibold text-slate-800">{{ details.duration || '-' }}</p>
          </div>
          <div class="rounded-xl border border-slate-100 bg-slate-50/70 p-4 md:col-span-2">
            <p class="text-xs uppercase tracking-[0.12em] text-slate-500">Notes</p>
            <p class="mt-1 whitespace-pre-line text-sm text-slate-700">{{ details.notes || 'No clinical notes.' }}</p>
          </div>
        </div>

        <div class="mt-6 flex flex-wrap gap-3">
          <Link :href="`/doctor/prescriptions/${details.id}/edit`" class="rounded-xl bg-gradient-to-r from-teal-600 to-emerald-500 px-5 py-2.5 text-sm font-semibold text-white transition hover:from-teal-700 hover:to-emerald-600">
            Edit Prescription
          </Link>
          <Link href="/doctor/prescriptions" class="rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
            Back to List
          </Link>
        </div>
      </section>
    </div>
  </DoctorLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import DoctorLayout from '../../Layouts/DoctorLayout.vue';

const props = defineProps({
  prescription: {
    type: Object,
    default: () => ({}),
  },
});

const details = computed(() => props.prescription || {});

function formatDate(dateString) {
  const date = new Date(dateString);
  if (Number.isNaN(date.getTime())) return dateString || '-';
  return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

function statusClass(status) {
  const value = String(status || '').toLowerCase();
  if (value === 'active') return 'bg-emerald-100 text-emerald-700';
  if (value === 'cancelled') return 'bg-rose-100 text-rose-700';
  return 'bg-slate-100 text-slate-700';
}
</script>
