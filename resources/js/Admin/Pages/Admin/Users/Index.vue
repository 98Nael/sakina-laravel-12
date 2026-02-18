<template>
  <div class="space-y-6">
    <section class="rounded-3xl border border-white/10 bg-[linear-gradient(135deg,#0f172a,#111827,#1f2937)] p-6 text-slate-100 shadow-2xl shadow-black/20">
      <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
          <p class="text-xs uppercase tracking-[0.22em] text-amber-300">Admin Users</p>
          <h1 class="mt-2 text-2xl font-bold text-white">Manage Roles and Account Lifecycle</h1>
          <p class="mt-1 text-sm text-slate-300">Create users, assign roles, and control active/suspended/deactivated states.</p>
        </div>
        <button
          type="button"
          class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 px-4 py-2.5 text-sm font-semibold text-slate-900 transition hover:from-amber-400 hover:to-orange-400"
          @click="showCreate = !showCreate"
        >
          {{ showCreate ? 'Hide Add Form' : 'Add User' }}
        </button>
      </div>

      <div class="mt-5 grid grid-cols-2 gap-3 md:grid-cols-4">
        <div class="rounded-xl border border-white/10 bg-white/5 p-3">
          <p class="text-xs text-slate-400">Total</p>
          <p class="mt-1 text-xl font-bold text-white">{{ stats.total }}</p>
        </div>
        <div class="rounded-xl border border-emerald-400/25 bg-emerald-500/10 p-3">
          <p class="text-xs text-emerald-200">Active</p>
          <p class="mt-1 text-xl font-bold text-emerald-100">{{ stats.active }}</p>
        </div>
        <div class="rounded-xl border border-amber-400/25 bg-amber-500/10 p-3">
          <p class="text-xs text-amber-200">Suspended</p>
          <p class="mt-1 text-xl font-bold text-amber-100">{{ stats.suspended }}</p>
        </div>
        <div class="rounded-xl border border-rose-400/25 bg-rose-500/10 p-3">
          <p class="text-xs text-rose-200">Deactivated</p>
          <p class="mt-1 text-xl font-bold text-rose-100">{{ stats.deactivated }}</p>
        </div>
      </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
      <h2 class="mb-4 text-lg font-semibold text-slate-900">Advanced Filters</h2>
      <form class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-4" @submit.prevent="applyFilters">
        <input v-model="filterForm.search" type="text" placeholder="Search name or email" class="rounded-lg border border-slate-300 px-3 py-2" />

        <select v-model="filterForm.role" class="rounded-lg border border-slate-300 px-3 py-2">
          <option value="">All Roles</option>
          <option value="admin">Admin</option>
          <option value="doctor">Doctor</option>
          <option value="patient">Patient</option>
          <option value="user">User</option>
        </select>

        <select v-model="filterForm.status" class="rounded-lg border border-slate-300 px-3 py-2">
          <option value="">All Status</option>
          <option value="active">Active</option>
          <option value="suspended">Suspended</option>
          <option value="deactivated">Deactivated</option>
        </select>

        <select v-model="filterForm.sort" class="rounded-lg border border-slate-300 px-3 py-2">
          <option value="latest">Latest</option>
          <option value="oldest">Oldest</option>
          <option value="name_asc">Name A-Z</option>
          <option value="name_desc">Name Z-A</option>
          <option value="email_asc">Email A-Z</option>
          <option value="email_desc">Email Z-A</option>
        </select>

        <div class="xl:col-span-4 flex gap-2">
          <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Apply Filters</button>
          <button type="button" class="rounded-xl border border-slate-300 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50" @click="resetFilters">Reset</button>
        </div>
      </form>
    </section>

    <section v-if="showCreate" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <h2 class="mb-4 text-lg font-semibold text-slate-900">Create User</h2>
      <form class="grid grid-cols-1 gap-4 md:grid-cols-2" @submit.prevent="submitCreate">
        <div>
          <input v-model="createForm.name" type="text" placeholder="Full name" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
          <p v-if="createForm.errors.name" class="mt-1 text-xs text-red-600">{{ createForm.errors.name }}</p>
        </div>
        <div>
          <input v-model="createForm.email" type="email" placeholder="Email" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
          <p v-if="createForm.errors.email" class="mt-1 text-xs text-red-600">{{ createForm.errors.email }}</p>
        </div>
        <div>
          <select v-model="createForm.role" class="w-full rounded-lg border border-slate-300 px-3 py-2">
            <option value="user">User</option>
            <option value="patient">Patient</option>
            <option value="doctor">Doctor</option>
            <option value="admin">Admin</option>
          </select>
          <p v-if="createForm.errors.role" class="mt-1 text-xs text-red-600">{{ createForm.errors.role }}</p>
        </div>
        <div>
          <select v-model="createForm.account_status" class="w-full rounded-lg border border-slate-300 px-3 py-2">
            <option value="active">Active</option>
            <option value="suspended">Suspended</option>
            <option value="deactivated">Deactivated</option>
          </select>
          <p v-if="createForm.errors.account_status" class="mt-1 text-xs text-red-600">{{ createForm.errors.account_status }}</p>
        </div>
        <div>
          <input v-model="createForm.password" type="password" placeholder="Password" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
          <p v-if="createForm.errors.password" class="mt-1 text-xs text-red-600">{{ createForm.errors.password }}</p>
        </div>
        <div>
          <input v-model="createForm.password_confirmation" type="password" placeholder="Confirm password" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
        </div>
        <div class="md:col-span-2">
          <button :disabled="createForm.processing" class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
            {{ createForm.processing ? 'Saving...' : 'Create User' }}
          </button>
        </div>
      </form>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
      <div class="border-b border-slate-200 px-6 py-4">
        <h2 class="text-lg font-semibold text-slate-900">Users</h2>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-slate-50">
            <tr>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Name</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Email</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Role</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Status</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Last Login</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in users.data" :key="item.id" class="border-t border-slate-100">
              <td class="px-4 py-3 text-slate-900">{{ item.name }}</td>
              <td class="px-4 py-3 text-slate-700">{{ item.email }}</td>
              <td class="px-4 py-3">
                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium uppercase text-slate-700">
                  {{ item.role }}
                </span>
              </td>
              <td class="px-4 py-3">
                <span
                  class="rounded-full px-2.5 py-1 text-xs font-medium uppercase"
                  :class="statusBadgeClass(item.account_status)"
                >
                  {{ item.account_status }}
                </span>
              </td>
              <td class="px-4 py-3 text-slate-600">{{ formatDate(item.last_login_at) }}</td>
              <td class="px-4 py-3">
                <div class="flex flex-wrap gap-2">
                  <button class="rounded-lg border border-blue-300 px-2.5 py-1 text-xs text-blue-700 hover:bg-blue-50" @click="openEdit(item)">
                    Edit
                  </button>
                  <button class="rounded-lg border border-emerald-300 px-2.5 py-1 text-xs text-emerald-700 hover:bg-emerald-50" @click="changeStatus(item, 'active')">
                    Activate
                  </button>
                  <button class="rounded-lg border border-amber-300 px-2.5 py-1 text-xs text-amber-700 hover:bg-amber-50" @click="changeStatus(item, 'suspended')">
                    Suspend
                  </button>
                  <button class="rounded-lg border border-rose-300 px-2.5 py-1 text-xs text-rose-700 hover:bg-rose-50" @click="changeStatus(item, 'deactivated')">
                    Deactivate
                  </button>
                  <button class="rounded-lg border border-red-300 px-2.5 py-1 text-xs text-red-700 hover:bg-red-50" @click="destroyUser(item)">
                    Delete
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="!users.data.length">
              <td colspan="6" class="px-4 py-6 text-center text-slate-500">No users found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="users.links?.length" class="flex flex-wrap gap-2 border-t border-slate-200 px-6 py-4">
        <Link
          v-for="(link, idx) in users.links"
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

    <div v-if="showEdit" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="closeEdit">
      <div class="w-full max-w-2xl rounded-2xl bg-white p-6 shadow-2xl">
        <div class="mb-4 flex items-center justify-between">
          <h3 class="text-lg font-semibold text-slate-900">Edit User</h3>
          <button class="text-slate-400 hover:text-slate-700" @click="closeEdit">X</button>
        </div>

        <form class="grid grid-cols-1 gap-4 md:grid-cols-2" @submit.prevent="submitEdit">
          <div>
            <input v-model="editForm.name" type="text" placeholder="Full name" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
            <p v-if="editForm.errors.name" class="mt-1 text-xs text-red-600">{{ editForm.errors.name }}</p>
          </div>
          <div>
            <input v-model="editForm.email" type="email" placeholder="Email" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
            <p v-if="editForm.errors.email" class="mt-1 text-xs text-red-600">{{ editForm.errors.email }}</p>
          </div>
          <div>
            <select v-model="editForm.role" class="w-full rounded-lg border border-slate-300 px-3 py-2">
              <option value="user">User</option>
              <option value="patient">Patient</option>
              <option value="doctor">Doctor</option>
              <option value="admin">Admin</option>
            </select>
            <p v-if="editForm.errors.role" class="mt-1 text-xs text-red-600">{{ editForm.errors.role }}</p>
          </div>
          <div>
            <select v-model="editForm.account_status" class="w-full rounded-lg border border-slate-300 px-3 py-2">
              <option value="active">Active</option>
              <option value="suspended">Suspended</option>
              <option value="deactivated">Deactivated</option>
            </select>
            <p v-if="editForm.errors.account_status" class="mt-1 text-xs text-red-600">{{ editForm.errors.account_status }}</p>
          </div>
          <div>
            <input v-model="editForm.password" type="password" placeholder="New password (optional)" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
            <p v-if="editForm.errors.password" class="mt-1 text-xs text-red-600">{{ editForm.errors.password }}</p>
          </div>
          <div>
            <input v-model="editForm.password_confirmation" type="password" placeholder="Confirm new password" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
          </div>
          <div class="md:col-span-2 flex gap-2">
            <button :disabled="editForm.processing" class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
              Update
            </button>
            <button type="button" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-700" @click="closeEdit">
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';

const props = defineProps({
  users: {
    type: Object,
    required: true,
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
  stats: {
    type: Object,
    default: () => ({ total: 0, active: 0, suspended: 0, deactivated: 0 }),
  },
});

const showCreate = ref(false);
const showEdit = ref(false);
const selectedUser = ref(null);

const filterForm = useForm({
  search: props.filters.search || '',
  role: props.filters.role || '',
  status: props.filters.status || '',
  sort: props.filters.sort || 'latest',
});

const createForm = useForm({
  name: '',
  email: '',
  role: 'user',
  account_status: 'active',
  password: '',
  password_confirmation: '',
});

const editForm = useForm({
  name: '',
  email: '',
  role: 'user',
  account_status: 'active',
  password: '',
  password_confirmation: '',
});

function submitCreate() {
  createForm.post('/admin/users', {
    preserveScroll: true,
    onSuccess: () => {
      createForm.reset();
      showCreate.value = false;
    },
  });
}

function openEdit(item) {
  selectedUser.value = item;
  editForm.reset();
  editForm.clearErrors();
  editForm.name = item.name || '';
  editForm.email = item.email || '';
  editForm.role = item.role || 'user';
  editForm.account_status = item.account_status || 'active';
  showEdit.value = true;
}

function closeEdit() {
  showEdit.value = false;
  selectedUser.value = null;
}

function submitEdit() {
  if (!selectedUser.value) return;

  editForm
    .transform((data) => ({ ...data, _method: 'put' }))
    .post(`/admin/users/${selectedUser.value.id}`, {
      preserveScroll: true,
      onSuccess: () => {
        closeEdit();
      },
    });
}

function changeStatus(item, status) {
  router.patch(`/admin/users/${item.id}/status`, {
    account_status: status,
  }, {
    preserveScroll: true,
  });
}

function destroyUser(item) {
  if (!confirm(`Delete user ${item.name}?`)) return;

  router.delete(`/admin/users/${item.id}`, {
    preserveScroll: true,
  });
}

function applyFilters() {
  router.get('/admin/users', buildFilterParams(), {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  });
}

function resetFilters() {
  filterForm.search = '';
  filterForm.role = '';
  filterForm.status = '';
  filterForm.sort = 'latest';

  router.get('/admin/users', {}, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  });
}

function buildFilterParams() {
  const params = {
    search: filterForm.search,
    role: filterForm.role,
    status: filterForm.status,
    sort: filterForm.sort,
  };

  return Object.fromEntries(
    Object.entries(params).filter(([, value]) => value !== '' && value !== null && value !== undefined),
  );
}

function formatDate(value) {
  if (!value) return '-';
  return new Date(value).toLocaleString();
}

function statusBadgeClass(status) {
  if (status === 'active') return 'bg-emerald-100 text-emerald-700';
  if (status === 'suspended') return 'bg-amber-100 text-amber-700';
  if (status === 'deactivated') return 'bg-rose-100 text-rose-700';
  return 'bg-slate-100 text-slate-700';
}
</script>
