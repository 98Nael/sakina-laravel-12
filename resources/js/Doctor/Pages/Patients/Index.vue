<template>
  <DoctorLayout>
    <template #header>Patients</template>

    <div class="space-y-6 pb-6">
    <section class="rounded-3xl border border-white/35 bg-gradient-to-r from-teal-700 via-teal-800 to-emerald-700 p-6 text-white shadow-2xl shadow-teal-900/20">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
          <p class="text-xs uppercase tracking-[0.22em] text-teal-100">Patient Management</p>
          <h1 class="mt-2 text-2xl font-bold">Doctor Patients</h1>
          <p class="mt-1 text-sm text-teal-50/90">Review patient accounts and quickly register a new patient request.</p>
        </div>
      </div>
    </section>

    <section class="grid gap-4 sm:grid-cols-3">
      <article class="rounded-2xl border border-teal-100/70 bg-white/90 p-4 shadow-lg shadow-teal-900/5">
        <p class="text-sm text-slate-500">Total Patients</p>
        <p class="mt-1 text-2xl font-bold text-slate-900">{{ totalPatients }}</p>
      </article>
      <article class="rounded-2xl border border-teal-100/70 bg-white/90 p-4 shadow-lg shadow-teal-900/5">
        <p class="text-sm text-slate-500">Approved</p>
        <p class="mt-1 text-2xl font-bold text-emerald-700">{{ approvedCount }}</p>
      </article>
      <article class="rounded-2xl border border-teal-100/70 bg-white/90 p-4 shadow-lg shadow-teal-900/5">
        <p class="text-sm text-slate-500">Pending</p>
        <p class="mt-1 text-2xl font-bold text-amber-700">{{ pendingCount }}</p>
      </article>
    </section>

    <section class="rounded-3xl border border-teal-100/70 bg-white/90 p-5 shadow-lg shadow-teal-900/5">
      <h2 class="text-lg font-bold text-slate-900">Create Patient</h2>
      <form class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-2" @submit.prevent="submitCreate">
        <div>
          <input v-model="createForm.name" type="text" placeholder="Patient name" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-100" />
          <p v-if="createForm.errors.name" class="mt-1 text-xs text-red-600">{{ createForm.errors.name }}</p>
        </div>
        <div>
          <input v-model="createForm.email" type="email" placeholder="Patient email" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-100" />
          <p v-if="createForm.errors.email" class="mt-1 text-xs text-red-600">{{ createForm.errors.email }}</p>
        </div>
        <div>
          <input v-model="createForm.phone" type="text" placeholder="Phone (optional)" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-100" />
        </div>
        <div>
          <input v-model.number="createForm.age" type="number" min="0" placeholder="Age (optional)" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-100" />
        </div>
        <div>
          <select v-model="createForm.gender" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-100">
            <option value="">Gender (optional)</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="other">Other</option>
          </select>
        </div>
        <div>
          <input v-model="createForm.city" type="text" placeholder="City (optional)" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-100" />
        </div>
        <div class="md:col-span-2">
          <button type="submit" :disabled="createForm.processing" class="rounded-xl bg-gradient-to-r from-teal-600 to-emerald-500 px-5 py-2.5 text-sm font-semibold text-white transition hover:from-teal-700 hover:to-emerald-600 disabled:opacity-60">
            {{ createForm.processing ? 'Creating...' : 'Create Patient' }}
          </button>
        </div>
      </form>
    </section>

    <section class="overflow-hidden rounded-3xl border border-teal-100/70 bg-white/90 shadow-lg shadow-teal-900/5">
      <div class="border-b border-teal-100 bg-teal-50/75 px-5 py-4">
        <h2 class="text-lg font-bold text-slate-900">Patient List</h2>
      </div>

      <div v-if="!patientRows.length" class="px-5 py-10 text-center text-sm text-slate-500">
        No patients found.
      </div>

      <div v-else class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="text-left text-xs uppercase tracking-[0.12em] text-slate-500">
              <th class="px-5 py-3">Name</th>
              <th class="px-5 py-3">Email</th>
              <th class="px-5 py-3">Phone</th>
              <th class="px-5 py-3">City</th>
              <th class="px-5 py-3">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="item in patientRows" :key="item.id" class="hover:bg-teal-50/50">
              <td class="px-5 py-4 font-semibold text-slate-800">{{ item.name }}</td>
              <td class="px-5 py-4 text-slate-600">{{ item.email }}</td>
              <td class="px-5 py-4 text-slate-600">{{ item.phone || '-' }}</td>
              <td class="px-5 py-4 text-slate-600">{{ item.city || '-' }}</td>
              <td class="px-5 py-4">
                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold" :class="statusClass(item.approval_status)">
                  {{ item.approval_status || 'approved' }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="paginationLinks.length" class="flex flex-wrap gap-2 border-t border-teal-100 bg-white px-5 py-4">
        <Link
          v-for="(link, idx) in paginationLinks"
          :key="`${idx}-${link.label}`"
          :href="link.url || ''"
          class="rounded-lg px-3 py-1.5 text-sm font-medium transition"
          :class="[
            link.active
              ? 'bg-teal-700 text-white'
              : 'border border-slate-200 bg-white text-slate-700 hover:border-teal-300 hover:text-teal-700',
            !link.url ? 'cursor-not-allowed opacity-50' : '',
          ]"
          :disabled="!link.url"
          v-html="link.label"
        />
      </div>
    </section>
    </div>
  </DoctorLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import DoctorLayout from '../../Layouts/DoctorLayout.vue';

const props = defineProps({
  patients: {
    type: Object,
    default: () => ({ data: [] }),
  },
});

const patientRows = computed(() => props.patients?.data || []);
const totalPatients = computed(() => props.patients?.total ?? patientRows.value.length);
const paginationLinks = computed(() => props.patients?.links || []);
const approvedCount = computed(() => patientRows.value.filter((item) => (item.approval_status || 'approved') === 'approved').length);
const pendingCount = computed(() => patientRows.value.filter((item) => item.approval_status === 'pending').length);

const createForm = useForm({
  name: '',
  email: '',
  phone: '',
  age: null,
  gender: '',
  city: '',
  password: '',
  password_confirmation: '',
});

function submitCreate() {
  createForm.post('/doctor/patients', {
    preserveScroll: true,
    onSuccess: () => createForm.reset(),
  });
}

function statusClass(status) {
  const value = String(status || 'approved').toLowerCase();
  if (value === 'approved') return 'bg-emerald-100 text-emerald-700';
  if (value === 'pending') return 'bg-amber-100 text-amber-700';
  if (value === 'rejected') return 'bg-rose-100 text-rose-700';
  return 'bg-slate-100 text-slate-700';
}
</script>
