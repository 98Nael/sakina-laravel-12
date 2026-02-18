<template>
  <div class="space-y-6 pb-6">
    <section class="rounded-3xl border border-white/35 bg-gradient-to-r from-cyan-700 via-cyan-600 to-emerald-500 p-6 text-white shadow-2xl shadow-cyan-900/20">
      <p class="text-xs uppercase tracking-[0.22em] text-cyan-100">Reschedule</p>
      <h1 class="mt-2 text-2xl font-bold">Edit Appointment</h1>
      <p class="mt-1 text-sm text-cyan-50/90">{{ appointment.doctor_name }} · {{ appointment.specialization }}</p>
    </section>

    <section class="rounded-3xl border border-cyan-100/70 bg-white/90 p-6 shadow-lg shadow-cyan-900/5">
      <form class="grid grid-cols-1 gap-4 md:grid-cols-2" @submit.prevent="submit">
        <div>
          <label class="mb-1 block text-sm font-medium text-slate-700">Date</label>
          <input v-model="form.date" type="date" :min="minDate" class="w-full rounded-xl border border-slate-300 px-3 py-2.5" required />
          <p v-if="form.errors.date" class="mt-1 text-xs text-red-600">{{ form.errors.date }}</p>
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-slate-700">Time</label>
          <input v-model="form.time" type="time" class="w-full rounded-xl border border-slate-300 px-3 py-2.5" required />
          <p v-if="form.errors.time" class="mt-1 text-xs text-red-600">{{ form.errors.time }}</p>
        </div>
        <div class="md:col-span-2 flex gap-3">
          <button type="submit" :disabled="form.processing" class="rounded-xl bg-gradient-to-r from-cyan-600 to-emerald-500 px-5 py-2.5 text-sm font-semibold text-white disabled:opacity-60">
            {{ form.processing ? 'Saving...' : 'Save Changes' }}
          </button>
          <Link href="/patient/appointments" class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
            Cancel
          </Link>
        </div>
      </form>
    </section>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
  appointment: {
    type: Object,
    default: () => ({}),
  },
});

const form = useForm({
  date: props.appointment.date || '',
  time: props.appointment.time || '',
});

const minDate = computed(() => new Date().toISOString().split('T')[0]);

function submit() {
  form.put(`/patient/appointments/${props.appointment.id}`);
}
</script>
