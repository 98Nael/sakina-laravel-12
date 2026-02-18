<template>
  <div class="space-y-6">
    <section class="rounded-3xl border border-white/10 bg-[linear-gradient(135deg,#0f172a,#111827,#1f2937)] p-6 text-slate-100 shadow-2xl shadow-black/20">
      <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
          <p class="text-xs uppercase tracking-[0.22em] text-amber-300">Admin Doctors</p>
          <h1 class="mt-2 text-2xl font-bold text-white">Doctors Management</h1>
          <p class="mt-1 text-sm text-slate-300">Create, update, delete, view, and export doctor data to PDF.</p>
        </div>
        <button
          type="button"
          class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 px-4 py-2.5 text-sm font-semibold text-slate-900 transition hover:from-amber-400 hover:to-orange-400"
          @click="showCreate = !showCreate"
        >
          {{ showCreate ? 'Hide Add Form' : 'Add Doctor' }}
        </button>
      </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-semibold text-slate-900">Advanced Filters</h2>
      </div>

      <form class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-4" @submit.prevent="applyFilters">
        <input v-model="filterForm.search" type="text" placeholder="Search name/email/phone/license" class="rounded-lg border border-slate-300 px-3 py-2" />

        <select v-model="filterForm.verified" class="rounded-lg border border-slate-300 px-3 py-2">
          <option value="">All Status</option>
          <option value="1">Verified</option>
          <option value="0">Not Verified</option>
        </select>

        <input v-model="filterForm.specialization" type="text" placeholder="Specialization" class="rounded-lg border border-slate-300 px-3 py-2" />
        <input v-model="filterForm.hospital_name" type="text" placeholder="Hospital Name" class="rounded-lg border border-slate-300 px-3 py-2" />

        <input v-model="filterForm.min_experience" type="number" min="0" placeholder="Min experience" class="rounded-lg border border-slate-300 px-3 py-2" />
        <input v-model="filterForm.max_experience" type="number" min="0" placeholder="Max experience" class="rounded-lg border border-slate-300 px-3 py-2" />

        <select v-model="filterForm.sort" class="rounded-lg border border-slate-300 px-3 py-2">
          <option value="latest">Latest</option>
          <option value="oldest">Oldest</option>
          <option value="name_asc">Name A-Z</option>
          <option value="name_desc">Name Z-A</option>
          <option value="exp_asc">Experience Low-High</option>
          <option value="exp_desc">Experience High-Low</option>
        </select>

        <div class="xl:col-span-4 flex gap-2">
          <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Apply Filters</button>
          <button type="button" class="rounded-xl border border-slate-300 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50" @click="resetFilters">Reset</button>
        </div>
      </form>
    </section>

    <section v-if="showCreate" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <h2 class="mb-4 text-lg font-semibold text-slate-900">Create Doctor</h2>

      <form class="grid grid-cols-1 gap-4 md:grid-cols-2" @submit.prevent="submitCreate">
        <div><input v-model="createForm.name" type="text" placeholder="Name" class="w-full rounded-lg border border-slate-300 px-3 py-2" /></div>
        <div><input v-model="createForm.email" type="email" placeholder="Email" class="w-full rounded-lg border border-slate-300 px-3 py-2" /></div>
        <div><input v-model="createForm.phone" type="text" placeholder="Phone" class="w-full rounded-lg border border-slate-300 px-3 py-2" /></div>
        <div><input v-model="createForm.license_number" type="text" placeholder="License Number" class="w-full rounded-lg border border-slate-300 px-3 py-2" /></div>
        <div><input v-model="createForm.specialization" type="text" placeholder="Specialization" class="w-full rounded-lg border border-slate-300 px-3 py-2" /></div>
        <div><input v-model="createForm.hospital_name" type="text" placeholder="Hospital Name" class="w-full rounded-lg border border-slate-300 px-3 py-2" /></div>
        <div><input v-model="createForm.years_experience" type="number" min="0" placeholder="Years Experience" class="w-full rounded-lg border border-slate-300 px-3 py-2" /></div>
        <div>
          <select v-model="createForm.verified" class="w-full rounded-lg border border-slate-300 px-3 py-2">
            <option :value="false">Not Verified</option>
            <option :value="true">Verified</option>
          </select>
        </div>
        <div><input v-model="createForm.password" type="password" placeholder="Password (optional)" class="w-full rounded-lg border border-slate-300 px-3 py-2" /></div>
        <div><input v-model="createForm.password_confirmation" type="password" placeholder="Confirm Password" class="w-full rounded-lg border border-slate-300 px-3 py-2" /></div>

        <div class="md:col-span-2">
          <label class="mb-1 block text-sm font-medium text-slate-700">Doctor Photo</label>
          <input type="file" accept="image/*" @change="onCreatePhotoChange" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
          <p v-if="createForm.errors.photo" class="mt-1 text-xs text-red-600">{{ createForm.errors.photo }}</p>
          <div v-if="createPhotoPreview" class="mt-3">
            <img :src="createPhotoPreview" alt="Preview" class="h-20 w-20 rounded-xl border border-slate-200 object-cover" />
          </div>
        </div>

        <div class="md:col-span-2">
          <textarea v-model="createForm.bio" rows="3" placeholder="Bio" class="w-full rounded-lg border border-slate-300 px-3 py-2"></textarea>
        </div>
        <div class="md:col-span-2">
          <button :disabled="createForm.processing" class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
            {{ createForm.processing ? 'Saving...' : 'Create Doctor' }}
          </button>
        </div>
      </form>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
      <div class="border-b border-slate-200 px-6 py-4">
        <h2 class="text-lg font-semibold text-slate-900">Doctors List</h2>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-slate-50">
            <tr>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Photo</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Name</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Email</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Specialization</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Verified</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="doctor in doctors.data" :key="doctor.id" class="border-t border-slate-100">
              <td class="px-4 py-3">
                <img
                  :src="doctor.photo_url || placeholderAvatar"
                  alt="Doctor photo"
                  class="h-10 w-10 rounded-full border border-slate-200 object-cover"
                />
              </td>
              <td class="px-4 py-3 text-slate-800">{{ doctor.name }}</td>
              <td class="px-4 py-3 text-slate-600">{{ doctor.email }}</td>
              <td class="px-4 py-3 text-slate-600">{{ doctor.specialization }}</td>
              <td class="px-4 py-3">
                <span class="rounded-full px-2.5 py-1 text-xs font-medium" :class="doctor.verified ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700'">
                  {{ doctor.verified ? 'Verified' : 'Not Verified' }}
                </span>
              </td>
              <td class="px-4 py-3">
                <div class="flex flex-wrap gap-2">
                  <button
                    class="rounded-lg px-2.5 py-1 text-xs font-medium"
                    :class="doctor.verified
                      ? 'border border-amber-300 text-amber-700 hover:bg-amber-50'
                      : 'border border-emerald-300 text-emerald-700 hover:bg-emerald-50'"
                    @click="toggleDoctorStatus(doctor)"
                  >
                    {{ doctor.verified ? 'Set Unverified' : 'Set Verified' }}
                  </button>
                  <Link :href="`/admin/doctors/${doctor.id}`" class="rounded-lg border border-slate-300 px-2.5 py-1 text-xs text-slate-700 hover:bg-slate-50">View</Link>
                  <button class="rounded-lg border border-blue-300 px-2.5 py-1 text-xs text-blue-700 hover:bg-blue-50" @click="openEdit(doctor)">Edit</button>
                  <a :href="`/admin/doctors/${doctor.id}/export-pdf`" target="_blank" class="rounded-lg border border-emerald-300 px-2.5 py-1 text-xs text-emerald-700 hover:bg-emerald-50">PDF</a>
                  <button class="rounded-lg border border-red-300 px-2.5 py-1 text-xs text-red-700 hover:bg-red-50" @click="destroyDoctor(doctor)">Delete</button>
                </div>
              </td>
            </tr>
            <tr v-if="!doctors.data.length">
              <td colspan="6" class="px-4 py-6 text-center text-slate-500">No doctors found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="doctors.links?.length" class="flex flex-wrap gap-2 border-t border-slate-200 px-6 py-4">
        <Link
          v-for="(link, idx) in doctors.links"
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
      <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white p-6 shadow-2xl">
        <div class="mb-4 flex items-center justify-between">
          <h3 class="text-lg font-semibold text-slate-900">Edit Doctor</h3>
          <button class="text-slate-400 hover:text-slate-700" @click="closeEdit">X</button>
        </div>

        <form class="grid grid-cols-1 gap-4 md:grid-cols-2" @submit.prevent="submitEdit">
          <div><input v-model="editForm.name" type="text" placeholder="Name" class="w-full rounded-lg border border-slate-300 px-3 py-2" /></div>
          <div><input v-model="editForm.email" type="email" placeholder="Email" class="w-full rounded-lg border border-slate-300 px-3 py-2" /></div>
          <div><input v-model="editForm.phone" type="text" placeholder="Phone" class="w-full rounded-lg border border-slate-300 px-3 py-2" /></div>
          <div><input v-model="editForm.license_number" type="text" placeholder="License Number" class="w-full rounded-lg border border-slate-300 px-3 py-2" /></div>
          <div><input v-model="editForm.specialization" type="text" placeholder="Specialization" class="w-full rounded-lg border border-slate-300 px-3 py-2" /></div>
          <div><input v-model="editForm.hospital_name" type="text" placeholder="Hospital Name" class="w-full rounded-lg border border-slate-300 px-3 py-2" /></div>
          <div><input v-model="editForm.years_experience" type="number" min="0" placeholder="Years Experience" class="w-full rounded-lg border border-slate-300 px-3 py-2" /></div>
          <div>
            <select v-model="editForm.verified" class="w-full rounded-lg border border-slate-300 px-3 py-2">
              <option :value="true">Verified</option>
              <option :value="false">Not Verified</option>
            </select>
          </div>

          <div class="md:col-span-2">
            <label class="mb-1 block text-sm font-medium text-slate-700">Doctor Photo</label>
            <input type="file" accept="image/*" @change="onEditPhotoChange" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
            <p v-if="editForm.errors.photo" class="mt-1 text-xs text-red-600">{{ editForm.errors.photo }}</p>
            <div class="mt-3 flex items-center gap-4">
              <img :src="editPhotoPreview || selectedDoctor?.photo_url || placeholderAvatar" alt="Preview" class="h-20 w-20 rounded-xl border border-slate-200 object-cover" />
              <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                <input v-model="editForm.delete_photo" type="checkbox" class="h-4 w-4" />
                Remove current photo
              </label>
            </div>
          </div>

          <div class="md:col-span-2">
            <textarea v-model="editForm.bio" rows="3" placeholder="Bio" class="w-full rounded-lg border border-slate-300 px-3 py-2"></textarea>
          </div>
          <div class="md:col-span-2 flex gap-2">
            <button :disabled="editForm.processing" class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">Update</button>
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

const showCreate = ref(false);
const showEdit = ref(false);
const selectedDoctor = ref(null);
const createPhotoPreview = ref('');
const editPhotoPreview = ref('');
const placeholderAvatar = 'https://ui-avatars.com/api/?name=Doctor&background=0f172a&color=ffffff';

const props = defineProps({
  doctors: {
    type: Object,
    required: true,
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
});

const createForm = useForm({
  name: '',
  email: '',
  phone: '',
  license_number: '',
  specialization: '',
  hospital_name: '',
  bio: '',
  years_experience: 0,
  verified: false,
  photo: null,
  password: '',
  password_confirmation: '',
});

const filterForm = useForm({
  search: props.filters.search || '',
  verified: props.filters.verified || '',
  specialization: props.filters.specialization || '',
  hospital_name: props.filters.hospital_name || '',
  min_experience: props.filters.min_experience || '',
  max_experience: props.filters.max_experience || '',
  sort: props.filters.sort || 'latest',
});

const editForm = useForm({
  name: '',
  email: '',
  phone: '',
  license_number: '',
  specialization: '',
  hospital_name: '',
  bio: '',
  years_experience: 0,
  verified: false,
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
  createForm.post('/admin/doctors', {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => {
      createForm.reset();
      createPhotoPreview.value = '';
      showCreate.value = false;
    },
  });
}

function openEdit(doctor) {
  selectedDoctor.value = doctor;
  editPhotoPreview.value = '';
  editForm.reset();
  editForm.clearErrors();
  editForm.name = doctor.name || '';
  editForm.email = doctor.email || '';
  editForm.phone = doctor.phone || '';
  editForm.license_number = doctor.license_number || '';
  editForm.specialization = doctor.specialization || '';
  editForm.hospital_name = doctor.hospital_name || '';
  editForm.bio = doctor.bio || '';
  editForm.years_experience = doctor.years_experience ?? 0;
  editForm.verified = !!doctor.verified;
  editForm.photo = null;
  editForm.delete_photo = false;
  showEdit.value = true;
}

function closeEdit() {
  showEdit.value = false;
  selectedDoctor.value = null;
}

function submitEdit() {
  if (!selectedDoctor.value) return;

  editForm
    .transform((data) => ({ ...data, _method: 'put' }))
    .post(`/admin/doctors/${selectedDoctor.value.id}`, {
      forceFormData: true,
      preserveScroll: true,
      onSuccess: () => {
        closeEdit();
      },
    });
}

function destroyDoctor(doctor) {
  if (!confirm(`Delete doctor ${doctor.name}?`)) return;

  router.delete(`/admin/doctors/${doctor.id}`, {
    preserveScroll: true,
  });
}

function toggleDoctorStatus(doctor) {
  router.patch(`/admin/doctors/${doctor.id}/status`, {
    verified: doctor.verified ? 0 : 1,
  }, {
    preserveScroll: true,
  });
}

function applyFilters() {
  router.get('/admin/doctors', buildFilterParams(), {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  });
}

function resetFilters() {
  filterForm.search = '';
  filterForm.verified = '';
  filterForm.specialization = '';
  filterForm.hospital_name = '';
  filterForm.min_experience = '';
  filterForm.max_experience = '';
  filterForm.sort = 'latest';

  router.get('/admin/doctors', {}, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  });
}

function buildFilterParams() {
  const params = {
    search: filterForm.search,
    verified: filterForm.verified,
    specialization: filterForm.specialization,
    hospital_name: filterForm.hospital_name,
    min_experience: filterForm.min_experience,
    max_experience: filterForm.max_experience,
    sort: filterForm.sort,
  };

  return Object.fromEntries(
    Object.entries(params).filter(([, value]) => value !== '' && value !== null && value !== undefined),
  );
}
</script>
