<template>
  <div class="min-h-screen bg-[#eefcf9]">
    <div class="mx-auto grid min-h-screen w-full max-w-7xl grid-cols-1 lg:grid-cols-2">
      <aside class="relative hidden overflow-hidden lg:block">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_25%,rgba(20,184,166,0.22),transparent_45%),radial-gradient(circle_at_80%_80%,rgba(16,185,129,0.25),transparent_50%),linear-gradient(140deg,#0f766e,#115e59)]"></div>
        <div class="relative flex h-full flex-col justify-between p-12 text-white">
          <div>
            <p class="text-xs uppercase tracking-[0.28em] text-teal-100">Doctor Workspace</p>
            <h1 class="mt-7 max-w-lg text-5xl font-bold leading-tight">Focused tools for better patient care</h1>
            <p class="mt-5 max-w-md text-teal-50/90">
              Review records, manage appointments, and coordinate treatment plans with confidence.
            </p>
          </div>

          <div class="rounded-2xl border border-white/20 bg-white/10 p-5 backdrop-blur">
            <p class="text-sm text-teal-100">Clinical Access</p>
            <p class="mt-2 text-sm font-semibold">Authorized medical staff only</p>
          </div>
        </div>
      </aside>

      <main class="flex items-center justify-center p-5 md:p-10">
        <div class="w-full max-w-xl rounded-3xl bg-white p-8 shadow-2xl ring-1 ring-teal-900/10">
          <div class="mb-8 flex items-center gap-3">
            <div class="rounded-xl bg-teal-100 p-2 text-teal-700">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-3-3v6m8 4V5a2 2 0 00-2-2H6a2 2 0 00-2 2v14l4-3h10a2 2 0 002-2z" />
              </svg>
            </div>
            <div>
              <h2 class="text-3xl font-bold text-slate-900">Doctor Login</h2>
              <p class="text-sm text-slate-500">Sign in to continue your clinical workflow.</p>
            </div>
          </div>

          <div v-if="errors.email || errors.password || errors.general" class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4">
            <p v-if="errors.general" class="text-sm text-red-700">{{ errors.general }}</p>
            <p v-if="errors.email" class="text-sm text-red-700">{{ errors.email }}</p>
            <p v-if="errors.password" class="text-sm text-red-700">{{ errors.password }}</p>
          </div>

          <form @submit.prevent="handleLogin" class="space-y-5">
            <div>
              <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">Email</label>
              <input
                id="email"
                v-model="form.email"
                type="email"
                required
                placeholder="doctor@example.com"
                class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 outline-none transition focus:border-teal-500 focus:ring-4 focus:ring-teal-100"
              />
            </div>

            <div>
              <label for="password" class="mb-2 block text-sm font-semibold text-slate-700">Password</label>
              <div class="relative">
                <input
                  id="password"
                  v-model="form.password"
                  :type="showPassword ? 'text' : 'password'"
                  required
                  class="w-full rounded-xl border border-slate-300 px-4 py-2.5 pr-12 text-slate-900 outline-none transition focus:border-teal-500 focus:ring-4 focus:ring-teal-100"
                />
                <button
                  type="button"
                  class="absolute inset-y-0 right-0 flex w-11 items-center justify-center text-slate-500 hover:text-teal-700"
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

            <label class="inline-flex items-center gap-2 text-sm text-slate-600">
              <input
                id="remember"
                v-model="form.remember"
                type="checkbox"
                class="h-4 w-4 rounded border-slate-300 text-teal-600 focus:ring-teal-500"
              />
              Remember me
            </label>

            <button
              type="submit"
              :disabled="form.processing"
              class="inline-flex w-full items-center justify-center rounded-xl bg-gradient-to-r from-teal-600 to-emerald-500 px-4 py-3 text-sm font-semibold text-white transition hover:from-teal-700 hover:to-emerald-600 disabled:cursor-not-allowed disabled:opacity-60"
            >
              <span v-if="form.processing">Signing in...</span>
              <span v-else>Enter Doctor Portal</span>
            </button>
          </form>

          <p class="mt-6 text-center text-sm text-slate-500">
            Need access? <a href="/" class="font-semibold text-teal-700 hover:text-teal-800">Contact admin</a>
          </p>
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

  router.post('/doctor/login', {
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
