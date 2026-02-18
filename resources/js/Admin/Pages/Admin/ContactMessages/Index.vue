<template>
  <div class="space-y-6">
    <section class="rounded-3xl border border-white/10 bg-[linear-gradient(135deg,#0f172a,#111827,#1f2937)] p-6 text-slate-100 shadow-2xl shadow-black/20">
      <p class="text-xs uppercase tracking-[0.22em] text-amber-300">Contact Control</p>
      <h1 class="mt-2 text-2xl font-bold text-white">Contact Messages</h1>
      <p class="mt-1 text-sm text-slate-300">Review inbound messages and manage response status.</p>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
      <form class="grid grid-cols-1 gap-3 md:grid-cols-3" @submit.prevent="applyFilters">
        <input v-model="filterForm.search" type="text" placeholder="Search name/email/subject/message" class="rounded-lg border border-slate-300 px-3 py-2" />
        <select v-model="filterForm.status" class="rounded-lg border border-slate-300 px-3 py-2">
          <option value="">All Status</option>
          <option value="new">New</option>
          <option value="read">Read</option>
          <option value="resolved">Resolved</option>
        </select>
        <select v-model="filterForm.sort" class="rounded-lg border border-slate-300 px-3 py-2">
          <option value="latest">Latest</option>
          <option value="oldest">Oldest</option>
        </select>
        <div class="md:col-span-3 flex gap-2">
          <button class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Apply Filters</button>
          <button type="button" class="rounded-xl border border-slate-300 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50" @click="resetFilters">Reset</button>
        </div>
      </form>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
      <div class="border-b border-slate-200 px-6 py-4">
        <h2 class="text-lg font-semibold text-slate-900">Messages</h2>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-slate-50">
            <tr>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Sender</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Subject</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Message</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Status</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Date</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in messages.data" :key="item.id" class="border-t border-slate-100">
              <td class="px-4 py-3">
                <p class="font-medium text-slate-800">{{ item.name }}</p>
                <p class="text-xs text-slate-500">{{ item.email }}</p>
              </td>
              <td class="px-4 py-3 text-slate-700">{{ item.subject }}</td>
              <td class="px-4 py-3 text-slate-600">
                <p class="line-clamp-2 max-w-xs">{{ item.message }}</p>
              </td>
              <td class="px-4 py-3">
                <span
                  class="rounded-full px-2.5 py-1 text-xs font-semibold"
                  :class="item.status === 'new'
                    ? 'bg-amber-100 text-amber-700'
                    : item.status === 'read'
                      ? 'bg-blue-100 text-blue-700'
                      : 'bg-emerald-100 text-emerald-700'"
                >
                  {{ item.status }}
                </span>
              </td>
              <td class="px-4 py-3 text-slate-600">{{ formatDate(item.created_at) }}</td>
              <td class="px-4 py-3">
                <div class="flex flex-wrap gap-2">
                  <button class="rounded-lg border border-amber-300 px-2.5 py-1 text-xs text-amber-700 hover:bg-amber-50" @click="setStatus(item.id, 'new')">New</button>
                  <button class="rounded-lg border border-blue-300 px-2.5 py-1 text-xs text-blue-700 hover:bg-blue-50" @click="setStatus(item.id, 'read')">Read</button>
                  <button class="rounded-lg border border-emerald-300 px-2.5 py-1 text-xs text-emerald-700 hover:bg-emerald-50" @click="setStatus(item.id, 'resolved')">Resolved</button>
                  <button class="rounded-lg border border-red-300 px-2.5 py-1 text-xs text-red-700 hover:bg-red-50" @click="destroyMessage(item)">Delete</button>
                </div>
              </td>
            </tr>
            <tr v-if="!messages.data.length">
              <td colspan="6" class="px-4 py-6 text-center text-slate-500">No messages found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="messages.links?.length" class="flex flex-wrap gap-2 border-t border-slate-200 px-6 py-4">
        <Link
          v-for="(link, idx) in messages.links"
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
  </div>
</template>

<script setup>
import { Link, router, useForm } from '@inertiajs/vue3';

const props = defineProps({
  messages: {
    type: Object,
    required: true,
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
});

const filterForm = useForm({
  search: props.filters.search || '',
  status: props.filters.status || '',
  sort: props.filters.sort || 'latest',
});

function applyFilters() {
  router.get('/admin/contact-messages', buildFilterParams(), {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  });
}

function resetFilters() {
  filterForm.search = '';
  filterForm.status = '';
  filterForm.sort = 'latest';

  router.get('/admin/contact-messages', {}, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  });
}

function buildFilterParams() {
  const params = {
    search: filterForm.search,
    status: filterForm.status,
    sort: filterForm.sort,
  };

  return Object.fromEntries(
    Object.entries(params).filter(([, value]) => value !== '' && value !== null && value !== undefined),
  );
}

function setStatus(id, status) {
  router.patch(`/admin/contact-messages/${id}/status`, { status }, {
    preserveScroll: true,
  });
}

function destroyMessage(item) {
  if (!confirm(`Delete message from ${item.name}?`)) return;
  router.delete(`/admin/contact-messages/${item.id}`, {
    preserveScroll: true,
  });
}

function formatDate(value) {
  if (!value) return '-';
  return new Date(value).toLocaleString();
}
</script>

