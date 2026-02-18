<template>
  <div class="space-y-6">
    <transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="translate-y-2 opacity-0"
      enter-to-class="translate-y-0 opacity-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="translate-y-0 opacity-100"
      leave-to-class="translate-y-2 opacity-0"
    >
      <div
        v-if="toast.show"
        class="fixed right-4 top-4 z-[70] w-full max-w-sm rounded-xl border px-4 py-3 text-sm shadow-xl"
        :class="toast.type === 'success'
          ? 'border-emerald-200 bg-emerald-50 text-emerald-800'
          : toast.type === 'error'
            ? 'border-red-200 bg-red-50 text-red-800'
            : 'border-amber-200 bg-amber-50 text-amber-800'"
      >
        {{ toast.message }}
      </div>
    </transition>

    <section class="rounded-3xl border border-white/10 bg-[linear-gradient(135deg,#0f172a,#111827,#1f2937)] p-6 text-slate-100 shadow-2xl shadow-black/20">
      <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
          <p class="text-xs uppercase tracking-[0.22em] text-amber-300">Admin Patients</p>
          <h1 class="mt-2 text-2xl font-bold text-white">Patients Management</h1>
          <p class="mt-1 text-sm text-slate-300">Create, view, update, delete, and manage patient photos.</p>
        </div>
        <button
          type="button"
          class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 px-4 py-2.5 text-sm font-semibold text-slate-900 transition hover:from-amber-400 hover:to-orange-400"
          @click="showCreateForm = !showCreateForm"
        >
          {{ showCreateForm ? 'Hide Add Form' : 'Add Patient' }}
        </button>
      </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-semibold text-slate-900">Advanced Filters</h2>
      </div>

      <form @submit.prevent="applyFilters" class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-4">
        <input v-model="filterForm.search" type="text" placeholder="Search name/email/phone" class="rounded-lg border border-slate-300 px-3 py-2" />

        <select v-model="filterForm.status" class="rounded-lg border border-slate-300 px-3 py-2">
          <option value="">All Status</option>
          <option value="approved">Approved</option>
          <option value="pending">Pending</option>
          <option value="rejected">Rejected</option>
        </select>

        <select v-model="filterForm.gender" class="rounded-lg border border-slate-300 px-3 py-2">
          <option value="">All Genders</option>
          <option value="male">Male</option>
          <option value="female">Female</option>
          <option value="other">Other</option>
        </select>

        <input v-model="filterForm.city" type="text" placeholder="City" class="rounded-lg border border-slate-300 px-3 py-2" />

        <input v-model="filterForm.min_age" type="number" min="0" placeholder="Min age" class="rounded-lg border border-slate-300 px-3 py-2" />
        <input v-model="filterForm.max_age" type="number" min="0" placeholder="Max age" class="rounded-lg border border-slate-300 px-3 py-2" />

        <select v-model="filterForm.has_photo" class="rounded-lg border border-slate-300 px-3 py-2">
          <option value="">All Photos</option>
          <option value="1">With Photo</option>
          <option value="0">Without Photo</option>
        </select>

        <select v-model="filterForm.sort" class="rounded-lg border border-slate-300 px-3 py-2">
          <option value="latest">Latest</option>
          <option value="oldest">Oldest</option>
          <option value="name_asc">Name A-Z</option>
          <option value="name_desc">Name Z-A</option>
          <option value="age_asc">Age Low-High</option>
          <option value="age_desc">Age High-Low</option>
        </select>

        <div class="xl:col-span-4 flex gap-2">
          <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
            Apply Filters
          </button>
          <button type="button" class="rounded-xl border border-slate-300 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50" @click="resetFilters">
            Reset
          </button>
        </div>
      </form>
    </section>

    <section v-if="showCreateForm" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <h2 class="mb-4 text-lg font-semibold text-slate-900">Create New Patient</h2>

      <form @submit.prevent="submitCreate" class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
          <label class="mb-1 block text-sm font-medium text-slate-700">Name</label>
          <input v-model="createForm.name" type="text" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
          <p v-if="createForm.errors.name" class="mt-1 text-xs text-red-600">{{ createForm.errors.name }}</p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-slate-700">Email</label>
          <input v-model="createForm.email" type="email" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
          <p v-if="createForm.errors.email" class="mt-1 text-xs text-red-600">{{ createForm.errors.email }}</p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-slate-700">Password</label>
          <input v-model="createForm.password" type="password" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
          <p v-if="createForm.errors.password" class="mt-1 text-xs text-red-600">{{ createForm.errors.password }}</p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-slate-700">Confirm Password</label>
          <input v-model="createForm.password_confirmation" type="password" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-slate-700">Phone</label>
          <input v-model="createForm.phone" type="text" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-slate-700">City</label>
          <input v-model="createForm.city" type="text" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-slate-700">Age</label>
          <input v-model="createForm.age" type="number" min="0" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-slate-700">Gender</label>
          <select v-model="createForm.gender" class="w-full rounded-lg border border-slate-300 px-3 py-2">
            <option value="">Select</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="other">Other</option>
          </select>
        </div>

        <div class="md:col-span-2">
          <label class="mb-1 block text-sm font-medium text-slate-700">Patient Photo</label>
          <input type="file" accept="image/*" @change="onCreatePhotoChange" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
          <p v-if="createForm.errors.photo" class="mt-1 text-xs text-red-600">{{ createForm.errors.photo }}</p>

          <div v-if="createPhotoPreview" class="mt-3">
            <img :src="createPhotoPreview" alt="Preview" class="h-24 w-24 rounded-xl border border-slate-200 object-cover" />
          </div>
        </div>

        <div class="md:col-span-2">
          <button
            type="submit"
            :disabled="createForm.processing"
            class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800 disabled:opacity-60"
          >
            {{ createForm.processing ? 'Saving...' : 'Create Patient' }}
          </button>
        </div>
      </form>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
      <div class="border-b border-slate-200 px-6 py-4">
        <h2 class="text-lg font-semibold text-slate-900">Patients List</h2>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-slate-50">
            <tr>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Photo</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Name</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Email</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Phone</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">City</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Status</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="patient in patients.data" :key="patient.id" class="border-t border-slate-100">
              <td class="px-4 py-3">
                <img
                  :src="patient.photo_url || placeholderAvatar"
                  alt="Patient photo"
                  class="h-10 w-10 rounded-full border border-slate-200 object-cover"
                />
              </td>
              <td class="px-4 py-3 text-slate-800">{{ patient.name }}</td>
              <td class="px-4 py-3 text-slate-600">{{ patient.email }}</td>
              <td class="px-4 py-3 text-slate-600">{{ patient.phone || '-' }}</td>
              <td class="px-4 py-3 text-slate-600">{{ patient.city || '-' }}</td>
              <td class="px-4 py-3">
                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">
                  {{ patient.approval_status || 'approved' }}
                </span>
              </td>
              <td class="px-4 py-3">
                <div class="flex flex-wrap gap-2">
                  <button class="rounded-lg border border-slate-300 px-2.5 py-1 text-xs text-slate-700 hover:bg-slate-50" @click="openView(patient)">View</button>
                  <button class="rounded-lg border border-blue-300 px-2.5 py-1 text-xs text-blue-700 hover:bg-blue-50" @click="openEdit(patient)">Edit</button>
                  <a
                    :href="`/admin/patients/${patient.id}/export-pdf`"
                    target="_blank"
                    class="rounded-lg border border-emerald-300 px-2.5 py-1 text-xs text-emerald-700 hover:bg-emerald-50"
                  >
                    PDF
                  </a>
                  <button class="rounded-lg border border-red-300 px-2.5 py-1 text-xs text-red-700 hover:bg-red-50" @click="destroyPatient(patient)">Delete</button>
                </div>
              </td>
            </tr>
            <tr v-if="!patients.data.length">
              <td colspan="7" class="px-4 py-6 text-center text-slate-500">No patients found.</td>
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

    <div v-if="viewPatient" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="viewPatient = null">
      <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl">
        <div class="mb-4 flex items-center justify-between">
          <h3 class="text-lg font-semibold text-slate-900">Patient Details</h3>
          <button class="text-slate-400 hover:text-slate-700" @click="viewPatient = null">X</button>
        </div>

        <div class="space-y-3 text-sm">
          <img :src="viewPatient.photo_url || placeholderAvatar" alt="Patient" class="h-24 w-24 rounded-2xl border border-slate-200 object-cover" />
          <p><span class="font-semibold text-slate-700">Name:</span> {{ viewPatient.name }}</p>
          <p><span class="font-semibold text-slate-700">Email:</span> {{ viewPatient.email }}</p>
          <p><span class="font-semibold text-slate-700">Phone:</span> {{ viewPatient.phone || '-' }}</p>
          <p><span class="font-semibold text-slate-700">Age:</span> {{ viewPatient.age || '-' }}</p>
          <p><span class="font-semibold text-slate-700">Gender:</span> {{ viewPatient.gender || '-' }}</p>
          <p><span class="font-semibold text-slate-700">City:</span> {{ viewPatient.city || '-' }}</p>
          <p><span class="font-semibold text-slate-700">Status:</span> {{ viewPatient.approval_status || 'approved' }}</p>
        </div>
      </div>
    </div>

    <div v-if="isEditOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="closeEdit">
      <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white p-6 shadow-2xl">
        <div class="mb-4 flex items-center justify-between">
          <h3 class="text-lg font-semibold text-slate-900">Edit Patient</h3>
          <button class="text-slate-400 hover:text-slate-700" @click="closeEdit">X</button>
        </div>

        <form @submit.prevent="submitEdit" class="grid grid-cols-1 gap-4 md:grid-cols-2">
          <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Name</label>
            <input v-model="editForm.name" type="text" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
            <p v-if="editForm.errors.name" class="mt-1 text-xs text-red-600">{{ editForm.errors.name }}</p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Email</label>
            <input v-model="editForm.email" type="email" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
            <p v-if="editForm.errors.email" class="mt-1 text-xs text-red-600">{{ editForm.errors.email }}</p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">New Password (optional)</label>
            <input v-model="editForm.password" type="password" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
            <p v-if="editForm.errors.password" class="mt-1 text-xs text-red-600">{{ editForm.errors.password }}</p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Confirm Password</label>
            <input v-model="editForm.password_confirmation" type="password" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Phone</label>
            <input v-model="editForm.phone" type="text" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">City</label>
            <input v-model="editForm.city" type="text" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Age</label>
            <input v-model="editForm.age" type="number" min="0" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Gender</label>
            <select v-model="editForm.gender" class="w-full rounded-lg border border-slate-300 px-3 py-2">
              <option value="">Select</option>
              <option value="male">Male</option>
              <option value="female">Female</option>
              <option value="other">Other</option>
            </select>
          </div>

          <div class="md:col-span-2">
            <label class="mb-1 block text-sm font-medium text-slate-700">Patient Photo</label>
            <input type="file" accept="image/*" @change="onEditPhotoChange" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
            <p v-if="editForm.errors.photo" class="mt-1 text-xs text-red-600">{{ editForm.errors.photo }}</p>

            <div class="mt-3 flex items-center gap-4">
              <img :src="editPhotoPreview || selectedPatient?.photo_url || placeholderAvatar" alt="Preview" class="h-20 w-20 rounded-xl border border-slate-200 object-cover" />
              <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                <input v-model="editForm.delete_photo" type="checkbox" class="h-4 w-4" />
                Remove current photo
              </label>
            </div>
          </div>

          <div class="md:col-span-2 flex gap-2">
            <button
              type="submit"
              :disabled="editForm.processing"
              class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-60"
            >
              {{ editForm.processing ? 'Updating...' : 'Update Patient' }}
            </button>
            <button type="button" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-700" @click="closeEdit">Cancel</button>
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
  patients: {
    type: Object,
    required: true,
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
});

const placeholderAvatar = 'https://ui-avatars.com/api/?name=Patient&background=0f172a&color=ffffff';

const showCreateForm = ref(false);
const viewPatient = ref(null);
const selectedPatient = ref(null);
const isEditOpen = ref(false);

const createPhotoPreview = ref('');
const editPhotoPreview = ref('');
const toast = ref({
  show: false,
  type: 'success',
  message: '',
});
let toastTimer = null;

const createForm = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  phone: '',
  age: '',
  gender: '',
  city: '',
  photo: null,
});

const filterForm = useForm({
  search: props.filters.search || '',
  status: props.filters.status || '',
  gender: props.filters.gender || '',
  city: props.filters.city || '',
  min_age: props.filters.min_age || '',
  max_age: props.filters.max_age || '',
  has_photo: props.filters.has_photo || '',
  sort: props.filters.sort || 'latest',
});

const editForm = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  phone: '',
  age: '',
  gender: '',
  city: '',
  photo: null,
  delete_photo: false,
});

function onCreatePhotoChange(event) {
  const file = event.target.files?.[0] || null;
  createForm.photo = file;
  createPhotoPreview.value = file ? URL.createObjectURL(file) : '';
}

function onEditPhotoChange(event) {
  const file = event.target.files?.[0] || null;
  editForm.photo = file;
  editPhotoPreview.value = file ? URL.createObjectURL(file) : '';
}

function submitCreate() {
  createForm.post('/admin/patients', {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => {
      createForm.reset();
      createPhotoPreview.value = '';
      showCreateForm.value = false;
      showToast('Patient created successfully.', 'success');
    },
    onError: () => {
      showToast('Failed to create patient. Please check the form.', 'error');
    },
  });
}

function openView(patient) {
  viewPatient.value = patient;
}

function openEdit(patient) {
  selectedPatient.value = patient;
  editPhotoPreview.value = '';

  editForm.reset();
  editForm.clearErrors();
  editForm.name = patient.name || '';
  editForm.email = patient.email || '';
  editForm.phone = patient.phone || '';
  editForm.age = patient.age || '';
  editForm.gender = patient.gender || '';
  editForm.city = patient.city || '';
  editForm.password = '';
  editForm.password_confirmation = '';
  editForm.photo = null;
  editForm.delete_photo = false;

  isEditOpen.value = true;
}

function closeEdit() {
  isEditOpen.value = false;
  selectedPatient.value = null;
}

function submitEdit() {
  if (!selectedPatient.value) return;

  editForm
    .transform((data) => ({ ...data, _method: 'patch' }))
    .post(`/admin/patients/${selectedPatient.value.id}`, {
      forceFormData: true,
      preserveScroll: true,
      onSuccess: () => {
        closeEdit();
        showToast('Patient updated successfully.', 'success');
      },
      onError: () => {
        showToast('Failed to update patient.', 'error');
      },
    });
}

function destroyPatient(patient) {
  if (!confirm(`Delete patient ${patient.name}?`)) {
    showToast('Delete operation cancelled.', 'info');
    return;
  }

  router.delete(`/admin/patients/${patient.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      showToast(`Patient ${patient.name} deleted successfully.`, 'success');
    },
    onError: () => {
      showToast('Failed to delete patient.', 'error');
    },
  });
}

function applyFilters() {
  router.get('/admin/patients', buildFilterParams(), {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  });
}

function resetFilters() {
  filterForm.search = '';
  filterForm.status = '';
  filterForm.gender = '';
  filterForm.city = '';
  filterForm.min_age = '';
  filterForm.max_age = '';
  filterForm.has_photo = '';
  filterForm.sort = 'latest';

  router.get('/admin/patients', {}, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  });
}

function buildFilterParams() {
  const params = {
    search: filterForm.search,
    status: filterForm.status,
    gender: filterForm.gender,
    city: filterForm.city,
    min_age: filterForm.min_age,
    max_age: filterForm.max_age,
    has_photo: filterForm.has_photo,
    sort: filterForm.sort,
  };

  return Object.fromEntries(
    Object.entries(params).filter(([, value]) => value !== '' && value !== null && value !== undefined),
  );
}

function showToast(message, type = 'success') {
  if (toastTimer) {
    clearTimeout(toastTimer);
  }

  toast.value = {
    show: true,
    type,
    message,
  };

  toastTimer = setTimeout(() => {
    toast.value.show = false;
  }, 2600);
}
</script>
