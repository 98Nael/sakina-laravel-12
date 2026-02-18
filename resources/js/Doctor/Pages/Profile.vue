<template>
  <DoctorLayout>
    <template #header>Profile</template>

    <div class="space-y-6 pb-6">
      <section class="rounded-3xl border border-white/35 bg-gradient-to-r from-teal-700 via-teal-800 to-emerald-700 p-6 text-white shadow-2xl shadow-teal-900/20">
        <p class="text-xs uppercase tracking-[0.22em] text-teal-100">Doctor Profile</p>
        <h1 class="mt-2 text-2xl font-bold">My Profile</h1>
        <p class="mt-1 text-sm text-teal-50/90">Update your account details and medical profile information.</p>
      </section>

      <section class="rounded-3xl border border-teal-100/70 bg-white/90 p-6 shadow-lg shadow-teal-900/5">
        <form class="grid grid-cols-1 gap-4 md:grid-cols-2" @submit.prevent="submit">
          <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Name</label>
            <input v-model="form.name" type="text" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-slate-900 outline-none transition focus:border-teal-500 focus:ring-4 focus:ring-teal-100" />
            <p v-if="form.errors.name" class="mt-1 text-xs text-red-600">{{ form.errors.name }}</p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Email</label>
            <input v-model="form.email" type="email" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-slate-900 outline-none transition focus:border-teal-500 focus:ring-4 focus:ring-teal-100" />
            <p v-if="form.errors.email" class="mt-1 text-xs text-red-600">{{ form.errors.email }}</p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Phone</label>
            <input v-model="form.phone" type="text" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-slate-900 outline-none transition focus:border-teal-500 focus:ring-4 focus:ring-teal-100" />
            <p v-if="form.errors.phone" class="mt-1 text-xs text-red-600">{{ form.errors.phone }}</p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Specialization</label>
            <input v-model="form.specialization" type="text" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-slate-900 outline-none transition focus:border-teal-500 focus:ring-4 focus:ring-teal-100" />
            <p v-if="form.errors.specialization" class="mt-1 text-xs text-red-600">{{ form.errors.specialization }}</p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">License Number</label>
            <input v-model="form.license_number" type="text" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-slate-900 outline-none transition focus:border-teal-500 focus:ring-4 focus:ring-teal-100" />
            <p v-if="form.errors.license_number" class="mt-1 text-xs text-red-600">{{ form.errors.license_number }}</p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Hospital Name</label>
            <input v-model="form.hospital_name" type="text" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-slate-900 outline-none transition focus:border-teal-500 focus:ring-4 focus:ring-teal-100" />
            <p v-if="form.errors.hospital_name" class="mt-1 text-xs text-red-600">{{ form.errors.hospital_name }}</p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Years of Experience</label>
            <input v-model.number="form.years_experience" type="number" min="0" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-slate-900 outline-none transition focus:border-teal-500 focus:ring-4 focus:ring-teal-100" />
            <p v-if="form.errors.years_experience" class="mt-1 text-xs text-red-600">{{ form.errors.years_experience }}</p>
          </div>

          <div class="md:col-span-2">
            <label class="mb-1 block text-sm font-medium text-slate-700">Bio</label>
            <textarea v-model="form.bio" rows="4" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-slate-900 outline-none transition focus:border-teal-500 focus:ring-4 focus:ring-teal-100"></textarea>
            <p v-if="form.errors.bio" class="mt-1 text-xs text-red-600">{{ form.errors.bio }}</p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">New Password (optional)</label>
            <input v-model="form.password" type="password" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-slate-900 outline-none transition focus:border-teal-500 focus:ring-4 focus:ring-teal-100" />
            <p v-if="form.errors.password" class="mt-1 text-xs text-red-600">{{ form.errors.password }}</p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Confirm Password</label>
            <input v-model="form.password_confirmation" type="password" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-slate-900 outline-none transition focus:border-teal-500 focus:ring-4 focus:ring-teal-100" />
          </div>

          <div class="md:col-span-2">
            <button
              type="submit"
              :disabled="form.processing"
              class="rounded-xl bg-gradient-to-r from-teal-600 to-emerald-500 px-5 py-2.5 text-sm font-semibold text-white transition hover:from-teal-700 hover:to-emerald-600 disabled:opacity-60"
            >
              {{ form.processing ? 'Saving...' : 'Save Profile' }}
            </button>
          </div>
        </form>
      </section>
    </div>
  </DoctorLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import DoctorLayout from '../Layouts/DoctorLayout.vue';

const props = defineProps({
  profile: {
    type: Object,
    required: true,
  },
});

const form = useForm({
  name: props.profile.name || '',
  email: props.profile.email || '',
  phone: props.profile.phone || '',
  specialization: props.profile.specialization || '',
  license_number: props.profile.license_number || '',
  hospital_name: props.profile.hospital_name || '',
  years_experience: props.profile.years_experience ?? 0,
  bio: props.profile.bio || '',
  password: '',
  password_confirmation: '',
});

function submit() {
  form.patch('/doctor/profile', {
    preserveScroll: true,
    onSuccess: () => {
      form.reset('password', 'password_confirmation');
    },
  });
}
</script>
