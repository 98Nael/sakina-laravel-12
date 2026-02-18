<template>
  <div class="space-y-6">
    <section class="rounded-3xl border border-white/10 bg-[linear-gradient(135deg,#0f172a,#111827,#1f2937)] p-6 text-slate-100 shadow-2xl shadow-black/20">
      <p class="text-xs uppercase tracking-[0.22em] text-amber-300">Admin Profile</p>
      <h1 class="mt-2 text-2xl font-bold text-white">My Profile</h1>
      <p class="mt-1 text-sm text-slate-300">Update your personal data and password.</p>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <form class="grid grid-cols-1 gap-4 md:grid-cols-2" @submit.prevent="submit">
        <div>
          <label class="mb-1 block text-sm font-medium text-slate-700">Name</label>
          <input v-model="form.name" type="text" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
          <p v-if="form.errors.name" class="mt-1 text-xs text-red-600">{{ form.errors.name }}</p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-slate-700">Email</label>
          <input v-model="form.email" type="email" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
          <p v-if="form.errors.email" class="mt-1 text-xs text-red-600">{{ form.errors.email }}</p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-slate-700">Phone</label>
          <input v-model="form.phone" type="text" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
          <p v-if="form.errors.phone" class="mt-1 text-xs text-red-600">{{ form.errors.phone }}</p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-slate-700">Position</label>
          <input v-model="form.position" type="text" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
          <p v-if="form.errors.position" class="mt-1 text-xs text-red-600">{{ form.errors.position }}</p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-slate-700">New Password (optional)</label>
          <input v-model="form.password" type="password" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
          <p v-if="form.errors.password" class="mt-1 text-xs text-red-600">{{ form.errors.password }}</p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-slate-700">Confirm Password</label>
          <input v-model="form.password_confirmation" type="password" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
        </div>

        <div class="md:col-span-2">
          <button
            type="submit"
            :disabled="form.processing"
            class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800 disabled:opacity-60"
          >
            {{ form.processing ? 'Saving...' : 'Save Profile' }}
          </button>
        </div>
      </form>
    </section>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';

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
  position: props.profile.position || '',
  password: '',
  password_confirmation: '',
});

function submit() {
  form.patch('/admin/profile', {
    preserveScroll: true,
    onSuccess: () => {
      form.reset('password', 'password_confirmation');
    },
  });
}
</script>

