<template>
  <div class="space-y-6 pb-6">
    <section class="rounded-3xl border border-white/35 bg-gradient-to-r from-cyan-700 via-cyan-600 to-emerald-500 p-6 text-white shadow-2xl shadow-cyan-900/20">
      <p class="text-xs uppercase tracking-[0.22em] text-cyan-100">Book Appointment</p>
      <h1 class="mt-2 text-2xl font-bold">Schedule Your Visit</h1>
      <p class="mt-1 text-sm text-cyan-50/90">Choose a doctor, pick a suitable date and time, and submit your appointment request.</p>
    </section>

    <section class="rounded-3xl border border-cyan-100/70 bg-white/90 p-6 shadow-lg shadow-cyan-900/5">
      <form class="grid grid-cols-1 gap-4 md:grid-cols-2" @submit.prevent="submit">
        <div class="md:col-span-2">
          <label class="mb-1 block text-sm font-medium text-slate-700">Doctor</label>
          <select
            v-model="form.doctor_id"
            class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
            required
          >
            <option value="">Select a doctor</option>
            <option v-for="doctor in doctors" :key="doctor.id" :value="doctor.id">
              {{ doctor.name }} - {{ doctor.specialty }}
            </option>
          </select>
          <p v-if="form.errors.doctor_id" class="mt-1 text-xs text-red-600">{{ form.errors.doctor_id }}</p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-slate-700">Date</label>
          <input
            v-model="form.date"
            type="date"
            :min="minDate"
            class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
            required
          />
          <p v-if="form.errors.date" class="mt-1 text-xs text-red-600">{{ form.errors.date }}</p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-slate-700">Time</label>
          <input
            v-model="form.time"
            type="time"
            class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
            required
          />
          <p v-if="form.errors.time" class="mt-1 text-xs text-red-600">{{ form.errors.time }}</p>
        </div>

        <div class="md:col-span-2">
          <label class="mb-1 block text-sm font-medium text-slate-700">Reason</label>
          <textarea
            v-model="form.reason"
            rows="4"
            placeholder="Describe your symptoms or reason for visit"
            class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
            required
          ></textarea>
          <p v-if="form.errors.reason" class="mt-1 text-xs text-red-600">{{ form.errors.reason }}</p>
        </div>

        <div class="md:col-span-2 flex flex-wrap gap-3">
          <button
            type="submit"
            :disabled="form.processing"
            class="rounded-xl bg-gradient-to-r from-cyan-600 to-emerald-500 px-5 py-2.5 text-sm font-semibold text-white transition hover:from-cyan-700 hover:to-emerald-600 disabled:opacity-60"
          >
            {{ form.processing ? 'Booking...' : 'Book Appointment' }}
          </button>
          <Link href="/patient/appointments" class="rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
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
  doctors: {
    type: Array,
    default: () => [],
  },
});

const form = useForm({
  doctor_id: '',
  date: '',
  time: '',
  reason: '',
});

const minDate = computed(() => new Date().toISOString().split('T')[0]);

function submit() {
  form.post('/patient/appointments', {
    preserveScroll: true,
  });
}
</script>
