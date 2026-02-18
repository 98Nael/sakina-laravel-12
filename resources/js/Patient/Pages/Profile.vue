<template>
  <div class="space-y-6 pb-6">
    <section class="rounded-3xl border border-white/35 bg-gradient-to-r from-cyan-700 via-cyan-600 to-emerald-500 p-6 text-white shadow-2xl shadow-cyan-900/20">
      <p class="text-xs uppercase tracking-[0.22em] text-cyan-100">Patient Profile</p>
      <h1 class="mt-2 text-2xl font-bold">My Profile</h1>
      <p class="mt-1 text-sm text-cyan-50/90">Update your personal information and profile picture.</p>
    </section>

    <section class="rounded-3xl border border-cyan-100/70 bg-white/90 p-6 shadow-lg shadow-cyan-900/5">
      <form class="grid grid-cols-1 gap-4 md:grid-cols-2" @submit.prevent="submit">
        <div class="md:col-span-2 flex items-center gap-4 rounded-2xl border border-cyan-100 bg-cyan-50/50 p-4">
          <img
            :src="previewImage || profileImage || placeholderAvatar"
            alt="Patient profile photo"
            class="h-20 w-20 rounded-full border border-cyan-200 object-cover"
          />
          <div class="flex flex-col gap-2">
            <label class="inline-flex cursor-pointer items-center justify-center rounded-xl bg-cyan-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-cyan-800">
              Upload photo
              <input type="file" accept="image/*" class="hidden" @change="onPhotoChange" />
            </label>
            <label class="inline-flex items-center gap-2 text-sm text-slate-600">
              <input v-model="form.delete_photo" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-cyan-600 focus:ring-cyan-500" />
              Remove current photo
            </label>
            <p v-if="form.errors.photo" class="text-xs text-red-600">{{ form.errors.photo }}</p>
          </div>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-slate-700">Name</label>
          <input v-model="form.name" type="text" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100" />
          <p v-if="form.errors.name" class="mt-1 text-xs text-red-600">{{ form.errors.name }}</p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-slate-700">Email</label>
          <input v-model="form.email" type="email" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100" />
          <p v-if="form.errors.email" class="mt-1 text-xs text-red-600">{{ form.errors.email }}</p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-slate-700">Phone</label>
          <input v-model="form.phone" type="text" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100" />
          <p v-if="form.errors.phone" class="mt-1 text-xs text-red-600">{{ form.errors.phone }}</p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-slate-700">Age</label>
          <input v-model.number="form.age" type="number" min="0" max="130" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100" />
          <p v-if="form.errors.age" class="mt-1 text-xs text-red-600">{{ form.errors.age }}</p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-slate-700">Gender</label>
          <select v-model="form.gender" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100">
            <option value="">Select gender</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="other">Other</option>
          </select>
          <p v-if="form.errors.gender" class="mt-1 text-xs text-red-600">{{ form.errors.gender }}</p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-slate-700">Blood Type</label>
          <input v-model="form.blood_type" type="text" placeholder="A+" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100" />
          <p v-if="form.errors.blood_type" class="mt-1 text-xs text-red-600">{{ form.errors.blood_type }}</p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-slate-700">City</label>
          <input v-model="form.city" type="text" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100" />
          <p v-if="form.errors.city" class="mt-1 text-xs text-red-600">{{ form.errors.city }}</p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-slate-700">Emergency Contact</label>
          <input v-model="form.emergency_contact" type="text" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100" />
          <p v-if="form.errors.emergency_contact" class="mt-1 text-xs text-red-600">{{ form.errors.emergency_contact }}</p>
        </div>

        <div class="md:col-span-2">
          <label class="mb-1 block text-sm font-medium text-slate-700">Address</label>
          <input v-model="form.address" type="text" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100" />
          <p v-if="form.errors.address" class="mt-1 text-xs text-red-600">{{ form.errors.address }}</p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-slate-700">New Password (optional)</label>
          <input v-model="form.password" type="password" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100" />
          <p v-if="form.errors.password" class="mt-1 text-xs text-red-600">{{ form.errors.password }}</p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-slate-700">Confirm Password</label>
          <input v-model="form.password_confirmation" type="password" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100" />
        </div>

        <div class="md:col-span-2">
          <button
            type="submit"
            :disabled="form.processing"
            class="rounded-xl bg-gradient-to-r from-cyan-600 to-emerald-500 px-5 py-2.5 text-sm font-semibold text-white transition hover:from-cyan-700 hover:to-emerald-600 disabled:opacity-60"
          >
            {{ form.processing ? 'Saving...' : 'Save Profile' }}
          </button>
        </div>
      </form>
    </section>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const placeholderAvatar = '/brand/sakina-favicon.svg';

const props = defineProps({
  profile: {
    type: Object,
    required: true,
  },
});

const previewImage = ref('');

const form = useForm({
  _method: 'patch',
  name: props.profile.name || '',
  email: props.profile.email || '',
  phone: props.profile.phone || '',
  age: props.profile.age ?? null,
  gender: props.profile.gender || '',
  city: props.profile.city || '',
  address: props.profile.address || '',
  blood_type: props.profile.blood_type || '',
  emergency_contact: props.profile.emergency_contact || '',
  photo: null,
  delete_photo: false,
  password: '',
  password_confirmation: '',
});

const profileImage = computed(() => props.profile.photo_url || '');

function onPhotoChange(event) {
  const file = event.target.files?.[0] || null;
  form.photo = file;
  form.delete_photo = false;
  previewImage.value = file ? URL.createObjectURL(file) : '';
}

function submit() {
  form.post('/patient/profile', {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => {
      form.reset('password', 'password_confirmation', 'photo');
      previewImage.value = '';
    },
  });
}
</script>
