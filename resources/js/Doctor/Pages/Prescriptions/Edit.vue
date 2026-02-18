<template>
  <DoctorLayout>
    <template #header>Edit Prescription</template>

    <div class="space-y-6 pb-6">
      <section class="rounded-3xl border border-white/35 bg-gradient-to-r from-teal-700 via-teal-800 to-emerald-700 p-6 text-white shadow-2xl shadow-teal-900/20">
        <p class="text-xs uppercase tracking-[0.22em] text-teal-100">Update Prescription</p>
        <h1 class="mt-2 text-2xl font-bold">Edit Medication Plan</h1>
        <p class="mt-1 text-sm text-teal-50/90">Modify dosage, frequency, duration, or clinical notes.</p>
      </section>

      <section class="rounded-3xl border border-teal-100/70 bg-white/90 p-6 shadow-lg shadow-teal-900/5">
        <form class="grid grid-cols-1 gap-4 md:grid-cols-2" @submit.prevent="submit">
          <div class="md:col-span-2 rounded-xl border border-teal-100 bg-teal-50/70 px-4 py-3 text-sm text-slate-700">
            <span class="font-semibold">Patient:</span> {{ prescription.patient_name || '-' }}
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Medication</label>
            <input v-model="form.medication" type="text" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-100" required />
            <p v-if="form.errors.medication" class="mt-1 text-xs text-red-600">{{ form.errors.medication }}</p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Dosage</label>
            <input v-model="form.dosage" type="text" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-100" required />
            <p v-if="form.errors.dosage" class="mt-1 text-xs text-red-600">{{ form.errors.dosage }}</p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Frequency</label>
            <input v-model="form.frequency" type="text" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-100" required />
            <p v-if="form.errors.frequency" class="mt-1 text-xs text-red-600">{{ form.errors.frequency }}</p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Duration</label>
            <input v-model="form.duration" type="text" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-100" required />
            <p v-if="form.errors.duration" class="mt-1 text-xs text-red-600">{{ form.errors.duration }}</p>
          </div>

          <div class="md:col-span-2">
            <label class="mb-1 block text-sm font-medium text-slate-700">Notes</label>
            <textarea v-model="form.notes" rows="4" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-100"></textarea>
            <p v-if="form.errors.notes" class="mt-1 text-xs text-red-600">{{ form.errors.notes }}</p>
          </div>

          <div class="md:col-span-2 flex gap-3">
            <button type="submit" :disabled="form.processing" class="rounded-xl bg-gradient-to-r from-teal-600 to-emerald-500 px-5 py-2.5 text-sm font-semibold text-white transition hover:from-teal-700 hover:to-emerald-600 disabled:opacity-60">
              {{ form.processing ? 'Saving...' : 'Update Prescription' }}
            </button>
            <Link href="/doctor/prescriptions" class="rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
              Back
            </Link>
          </div>
        </form>
      </section>
    </div>
  </DoctorLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import DoctorLayout from '../../Layouts/DoctorLayout.vue';

const props = defineProps({
  prescription: {
    type: Object,
    default: () => ({}),
  },
});

const form = useForm({
  medication: props.prescription.medication || '',
  dosage: props.prescription.dosage || '',
  frequency: props.prescription.frequency || '',
  duration: props.prescription.duration || '',
  notes: props.prescription.notes || '',
});

function submit() {
  form.put(`/doctor/prescriptions/${props.prescription.id}`);
}
</script>
