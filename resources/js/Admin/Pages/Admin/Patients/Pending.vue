<template>
  <div class="space-y-6">
    <section class="rounded-3xl border border-white/10 bg-[linear-gradient(135deg,#0f172a,#111827,#1f2937)] p-6 text-slate-100 shadow-2xl shadow-black/20">
      <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
          <p class="text-xs uppercase tracking-[0.22em] text-amber-300">Approval Center</p>
          <h1 class="mt-2 text-2xl font-bold text-white">Pending Patients</h1>
          <p class="mt-1 text-sm text-slate-300">Review pending registrations and approve or reject quickly.</p>
        </div>
        <Link href="/admin/patients" class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-white/15">
          All Patients
        </Link>
      </div>
    </section>

    <section class="grid grid-cols-1 gap-4 md:grid-cols-3">
      <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <p class="text-xs uppercase tracking-wide text-slate-500">Total Pending</p>
        <p class="mt-2 text-3xl font-bold text-slate-900">{{ stats.total_pending || 0 }}</p>
      </article>
      <article class="rounded-2xl border border-blue-200 bg-blue-50/70 p-4 shadow-sm">
        <p class="text-xs uppercase tracking-wide text-blue-700">Pending Today</p>
        <p class="mt-2 text-3xl font-bold text-blue-900">{{ stats.pending_today || 0 }}</p>
      </article>
      <article class="rounded-2xl border border-rose-200 bg-rose-50/70 p-4 shadow-sm">
        <p class="text-xs uppercase tracking-wide text-rose-700">Waiting Over 3 Days</p>
        <p class="mt-2 text-3xl font-bold text-rose-900">{{ stats.waiting_over_3_days || 0 }}</p>
      </article>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
      <form class="grid grid-cols-1 gap-3 md:grid-cols-3 xl:grid-cols-5" @submit.prevent="applyFilters">
        <input v-model="filterForm.search" type="text" placeholder="Search name, email, or phone" class="rounded-lg border border-slate-300 px-3 py-2" />
        <select v-model="filterForm.sort" class="rounded-lg border border-slate-300 px-3 py-2">
          <option value="latest">Latest</option>
          <option value="oldest">Oldest</option>
          <option value="name_asc">Name A-Z</option>
          <option value="name_desc">Name Z-A</option>
        </select>
        <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Apply</button>
        <button type="button" class="rounded-xl border border-slate-300 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50" @click="resetFilters">Reset</button>
      </form>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
      <div class="border-b border-slate-200 px-6 py-4">
        <h2 class="text-lg font-semibold text-slate-900">Pending Requests</h2>
      </div>

      <div v-if="!rows.length" class="px-6 py-10 text-center text-sm text-slate-500">
        No pending patients.
      </div>

      <div v-else class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-slate-50">
            <tr>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Patient</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Contact</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Created By</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Waiting</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Submitted</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="patient in rows" :key="patient.id" class="border-t border-slate-100">
              <td class="px-4 py-3">
                <p class="font-semibold text-slate-900">{{ patient.name }}</p>
                <p class="text-xs text-slate-500">ID #{{ patient.id }}</p>
              </td>
              <td class="px-4 py-3">
                <p class="text-slate-700">{{ patient.email }}</p>
                <p class="text-xs text-slate-500">{{ patient.phone || '-' }}</p>
              </td>
              <td class="px-4 py-3 text-slate-700">
                {{ creatorLabel(patient) }}
              </td>
              <td class="px-4 py-3">
                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold" :class="patient.waiting_days > 3 ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700'">
                  {{ patient.waiting_days }} day(s)
                </span>
              </td>
              <td class="px-4 py-3 text-slate-600">{{ formatDateTime(patient.created_at) }}</td>
              <td class="px-4 py-3">
                <div class="flex flex-wrap gap-2">
                  <button type="button" class="rounded-lg border border-emerald-300 px-2.5 py-1 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-50" @click="approve(patient.id)">
                    Approve
                  </button>
                  <button type="button" class="rounded-lg border border-rose-300 px-2.5 py-1 text-xs font-semibold text-rose-700 transition hover:bg-rose-50" @click="openReject(patient)">
                    Reject
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="patients.links?.length" class="flex flex-wrap gap-2 border-t border-slate-200 px-6 py-4">
        <Link
          v-for="(link, idx) in patients.links"
          :key="idx"
          :href="link.url || '#'"
          :class="[
            'rounded-lg px-3 py-1.5 text-sm',
            link.active ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200',
            !link.url ? 'pointer-events-none opacity-50' : '',
          ]"
          v-html="link.label"
        />
      </div>
    </section>

    <div v-if="isRejectOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="closeReject">
      <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl">
        <div class="mb-4 flex items-center justify-between">
          <h3 class="text-lg font-semibold text-slate-900">Reject Patient</h3>
          <button class="text-slate-400 hover:text-slate-700" @click="closeReject">X</button>
        </div>
        <p class="text-sm text-slate-600">
          Patient: <span class="font-semibold text-slate-800">{{ rejectTarget?.name || '-' }}</span>
        </p>
        <textarea
          v-model="rejectForm.approval_notes"
          rows="4"
          placeholder="Optional rejection note..."
          class="mt-3 w-full rounded-lg border border-slate-300 px-3 py-2"
        />
        <p v-if="rejectForm.errors.approval_notes" class="mt-1 text-xs text-red-600">{{ rejectForm.errors.approval_notes }}</p>
        <div class="mt-4 flex gap-2">
          <button type="button" :disabled="rejectForm.processing" class="rounded-xl bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700 disabled:opacity-60" @click="submitReject">
            {{ rejectForm.processing ? 'Rejecting...' : 'Confirm Reject' }}
          </button>
          <button type="button" class="rounded-xl border border-slate-300 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50" @click="closeReject">
            Cancel
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';

const props = defineProps({
  patients: {
    type: Object,
    required: true,
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
  stats: {
    type: Object,
    default: () => ({
      total_pending: 0,
      pending_today: 0,
      waiting_over_3_days: 0,
    }),
  },
});

const rows = computed(() => props.patients?.data || []);

const filterForm = useForm({
  search: props.filters.search || '',
  sort: props.filters.sort || 'latest',
});

const isRejectOpen = ref(false);
const rejectTarget = ref(null);
const rejectForm = useForm({
  approval_notes: '',
});

function applyFilters() {
  router.get('/admin/patients/pending', buildFilterParams(), {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  });
}

function resetFilters() {
  filterForm.search = '';
  filterForm.sort = 'latest';

  router.get('/admin/patients/pending', {}, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  });
}

function buildFilterParams() {
  return Object.fromEntries(
    Object.entries({
      search: filterForm.search,
      sort: filterForm.sort,
    }).filter(([, value]) => value !== '' && value !== null && value !== undefined),
  );
}

function approve(id) {
  router.patch(`/admin/patients/${id}/approve`, {}, { preserveScroll: true });
}

function openReject(patient) {
  rejectTarget.value = patient;
  rejectForm.reset();
  rejectForm.clearErrors();
  isRejectOpen.value = true;
}

function closeReject() {
  isRejectOpen.value = false;
  rejectTarget.value = null;
}

function submitReject() {
  if (!rejectTarget.value) return;

  rejectForm.patch(`/admin/patients/${rejectTarget.value.id}/reject`, {
    preserveScroll: true,
    onSuccess: () => closeReject(),
  });
}

function creatorLabel(patient) {
  if (patient.created_by_name) return `${patient.created_by_name} (${patient.created_by_role || 'user'})`;
  if (patient.created_by_role) return patient.created_by_role;
  return '-';
}

function formatDateTime(value) {
  if (!value) return '-';
  const date = new Date(value);
  if (Number.isNaN(date.getTime())) return value;
  return date.toLocaleString();
}
</script>
