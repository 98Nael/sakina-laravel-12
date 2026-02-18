<template>
  <div class="min-h-screen bg-[#0b1020] text-slate-100">
    <div class="grid min-h-screen grid-cols-1 lg:grid-cols-2">
      <aside class="relative hidden overflow-hidden lg:block">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_20%,rgba(251,191,36,0.18),transparent_45%),radial-gradient(circle_at_80%_80%,rgba(59,130,246,0.2),transparent_45%),linear-gradient(160deg,#0f172a,#111827)]"></div>
        <div class="relative flex h-full flex-col justify-between p-12">
          <div>
            <p class="text-xs uppercase tracking-[0.28em] text-amber-300">Admin Console</p>
            <h1 class="mt-7 max-w-lg text-5xl font-bold leading-tight text-white">Secure access to system operations</h1>
            <p class="mt-5 max-w-md text-slate-300">
              Monitor users, approvals, medical workflows, and platform activity from one trusted control panel.
            </p>
          </div>

          <div class="rounded-2xl border border-white/15 bg-white/5 p-5 backdrop-blur">
            <p class="text-sm text-slate-300">Environment</p>
            <p class="mt-2 text-sm font-semibold text-amber-300">Administrative Access Only</p>
          </div>
        </div>
      </aside>

      <main class="flex items-center justify-center px-5 py-10">
        <div class="w-full max-w-md rounded-3xl border border-white/10 bg-[#111827] p-8 shadow-2xl shadow-black/40">
          <div class="mb-8 flex items-center gap-3">
            <div class="rounded-xl bg-amber-400/20 p-2 text-amber-300">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2l7 4v6c0 5-3.5 9-7 10-3.5-1-7-5-7-10V6l7-4z" />
              </svg>
            </div>
            <div>
              <h2 class="text-2xl font-bold text-white">Admin Login</h2>
              <p class="text-sm text-slate-400">Sign in to manage the platform</p>
            </div>
          </div>

          <div v-if="errors.email || errors.password || errors.general" class="mb-5 rounded-xl border border-red-500/30 bg-red-500/10 p-4">
            <p v-if="errors.general" class="text-sm text-red-300">{{ errors.general }}</p>
            <p v-if="errors.email" class="text-sm text-red-300">{{ errors.email }}</p>
            <p v-if="errors.password" class="text-sm text-red-300">{{ errors.password }}</p>
          </div>

          <form @submit.prevent="handleLogin" class="space-y-5">
            <div>
              <label for="email" class="mb-2 block text-sm font-semibold text-slate-200">Email</label>
              <input
                id="email"
                v-model="form.email"
                type="email"
                required
                placeholder="admin@example.com"
                class="w-full rounded-xl border border-slate-700 bg-slate-900/80 px-4 py-2.5 text-slate-100 outline-none transition focus:border-amber-400 focus:ring-4 focus:ring-amber-400/15"
              />
            </div>

            <div>
              <label for="password" class="mb-2 block text-sm font-semibold text-slate-200">Password</label>
              <div class="relative">
                <input
                  id="password"
                  v-model="form.password"
                  :type="showPassword ? 'text' : 'password'"
                  required
                  class="w-full rounded-xl border border-slate-700 bg-slate-900/80 px-4 py-2.5 pr-12 text-slate-100 outline-none transition focus:border-amber-400 focus:ring-4 focus:ring-amber-400/15"
                />
                <button
                  type="button"
                  class="absolute inset-y-0 right-0 flex w-11 items-center justify-center text-slate-400 hover:text-amber-300"
                  @click="showPassword = !showPassword"
                >
                  <svg v-if="showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18M10.6 10.6a2 2 0 102.8 2.8M9.88 4.24A10.94 10.94 0 0112 4c7 0 10 8 10 8a17.12 17.12 0 01-4.05 5.8M6.1 6.1A16.98 16.98 0 002 12s3 8 10 8a9.76 9.76 0 005.22-1.56" />
                  </svg>
                  <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.46 12C3.73 7.94 7.52 5 12 5c4.48 0 8.27 2.94 9.54 7-1.27 4.06-5.06 7-9.54 7-4.48 0-8.27-2.94-9.54-7z" />
                  </svg>
                </button>
              </div>
            </div>

            <label class="inline-flex items-center gap-2 text-sm text-slate-300">
              <input
                id="remember"
                v-model="form.remember"
                type="checkbox"
                class="h-4 w-4 rounded border-slate-600 bg-slate-900 text-amber-400 focus:ring-amber-500"
              />
              Keep me signed in
            </label>

            <button
              type="submit"
              :disabled="form.processing"
              class="inline-flex w-full items-center justify-center rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 px-4 py-3 text-sm font-semibold text-slate-900 transition hover:from-amber-400 hover:to-orange-400 disabled:cursor-not-allowed disabled:opacity-60"
            >
              <span v-if="form.processing">Signing in...</span>
              <span v-else>Access Admin Panel</span>
            </button>
          </form>

          <p class="mt-6 text-center text-xs text-slate-500">Authorized administrators only.</p>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { router } from '@inertiajs/vue3';

defineProps({
  errors: {
    type: Object,
    default: () => ({}),
  },
});

const showPassword = ref(false);

const form = reactive({
  email: '',
  password: '',
  remember: false,
  processing: false,
});

const handleLogin = () => {
  form.processing = true;

  router.post('/admin/login', {
    email: form.email,
    password: form.password,
    remember: form.remember,
  }, {
    onFinish: () => {
      form.processing = false;
    },
  });
};
</script>
