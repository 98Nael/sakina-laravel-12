<template>
  <DoctorLayout>
    <template #header>Appointments</template>

    <div class="space-y-6 pb-6">
      <section class="rounded-3xl border border-white/35 bg-gradient-to-r from-teal-700 via-teal-800 to-emerald-700 p-6 text-white shadow-2xl shadow-teal-900/20">
        <div class="flex flex-wrap items-center justify-between gap-3">
          <div>
            <p class="text-xs uppercase tracking-[0.22em] text-teal-100">Doctor Schedule</p>
            <h1 class="mt-2 text-2xl font-bold">Today Appointments</h1>
            <p class="mt-1 text-sm text-teal-50/90">Monitor your day, update statuses, and keep patient flow organized.</p>
          </div>
          <Link href="/doctor/appointments/calendar" class="rounded-xl bg-white px-4 py-2 text-sm font-semibold text-teal-800 transition hover:bg-teal-50">
            Open Calendar
          </Link>
        </div>
      </section>

      <section class="grid gap-4 sm:grid-cols-3">
        <article class="rounded-2xl border border-teal-100/70 bg-white/90 p-4 shadow-lg shadow-teal-900/5">
          <p class="text-sm text-slate-500">Total</p>
          <p class="mt-1 text-2xl font-bold text-slate-900">{{ appointments.total ?? rows.length }}</p>
        </article>
        <article class="rounded-2xl border border-teal-100/70 bg-white/90 p-4 shadow-lg shadow-teal-900/5">
          <p class="text-sm text-slate-500">Scheduled</p>
          <p class="mt-1 text-2xl font-bold text-amber-700">{{ scheduledCount }}</p>
        </article>
        <article class="rounded-2xl border border-teal-100/70 bg-white/90 p-4 shadow-lg shadow-teal-900/5">
          <p class="text-sm text-slate-500">Completed</p>
          <p class="mt-1 text-2xl font-bold text-emerald-700">{{ completedCount }}</p>
        </article>
      </section>

      <section class="overflow-hidden rounded-3xl border border-teal-100/70 bg-white/90 shadow-lg shadow-teal-900/5">
        <div class="border-b border-teal-100 bg-teal-50/75 px-5 py-4">
          <h2 class="text-lg font-bold text-slate-900">Appointment Queue</h2>
        </div>

        <div v-if="!rows.length" class="px-5 py-10 text-center text-sm text-slate-500">
          No appointments found.
        </div>

        <div v-else class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead>
              <tr class="text-left text-xs uppercase tracking-[0.12em] text-slate-500">
                <th class="px-5 py-3">Patient</th>
                <th class="px-5 py-3">Time</th>
                <th class="px-5 py-3">Type</th>
                <th class="px-5 py-3">Status</th>
                <th class="px-5 py-3">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="item in rows" :key="item.id" class="hover:bg-teal-50/50">
                <td class="px-5 py-4 font-semibold text-slate-800">{{ item.patient_name }}</td>
                <td class="px-5 py-4 text-slate-600">{{ item.time }}</td>
                <td class="px-5 py-4 text-slate-600">{{ item.type }}</td>
                <td class="px-5 py-4">
                  <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold" :class="statusClass(item.status)">
                    {{ item.status }}
                  </span>
                </td>
                <td class="px-5 py-4">
                  <div class="flex flex-wrap gap-2">
                    <button
                      type="button"
                      class="rounded-lg border border-emerald-200 px-2.5 py-1 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-50"
                      @click="updateStatus(item.id, 'completed')"
                    >
                      Complete
                    </button>
                    <button
                      type="button"
                      class="rounded-lg border border-rose-200 px-2.5 py-1 text-xs font-semibold text-rose-700 transition hover:bg-rose-50"
                      @click="updateStatus(item.id, 'cancelled')"
                    >
                      Cancel
                    </button>
                    <Link :href="`/doctor/appointments/${item.id}`" class="rounded-lg border border-slate-300 px-2.5 py-1 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">
                      View
                    </Link>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="(appointments.links || []).length > 3" class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 px-5 py-4">
          <p class="text-xs text-slate-500">
            Showing {{ appointments.from || 0 }} to {{ appointments.to || 0 }} of {{ appointments.total || 0 }}
          </p>
          <div class="flex flex-wrap gap-2">
            <Link
              v-for="link in appointments.links"
              :key="`${link.url}-${link.label}`"
              :href="link.url || ''"
              class="rounded-lg border px-3 py-1.5 text-xs font-semibold transition"
              :class="[
                link.active ? 'border-teal-600 bg-teal-600 text-white' : 'border-slate-300 text-slate-700 hover:bg-slate-50',
                !link.url ? 'pointer-events-none opacity-50' : '',
              ]"
              v-html="link.label"
            />
          </div>
        </div>
      </section>
    </div>
  </DoctorLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import DoctorLayout from '../../Layouts/DoctorLayout.vue';

const props = defineProps({
  appointments: {
    type: Object,
    default: () => ({ data: [], links: [] }),
  },
});

const rows = computed(() => props.appointments?.data || []);
const scheduledCount = computed(() => rows.value.filter((item) => String(item.status).toLowerCase() === 'scheduled').length);
const completedCount = computed(() => rows.value.filter((item) => String(item.status).toLowerCase() === 'completed').length);

function updateStatus(appointmentId, status) {
  router.put(`/doctor/appointments/${appointmentId}/status`, { status });
}

function statusClass(status) {
  const value = String(status).toLowerCase();
  if (value === 'completed') return 'bg-emerald-100 text-emerald-700';
  if (value === 'scheduled') return 'bg-amber-100 text-amber-700';
  if (value === 'cancelled') return 'bg-rose-100 text-rose-700';
  if (value === 'no-show') return 'bg-slate-200 text-slate-700';
  return 'bg-slate-100 text-slate-700';
}
</script>
