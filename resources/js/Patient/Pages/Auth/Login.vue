<template>
  <div class="min-h-screen bg-slate-100">
    <div class="mx-auto grid min-h-screen w-full max-w-7xl grid-cols-1 lg:grid-cols-2">
      <aside class="hidden lg:flex flex-col justify-between bg-gradient-to-br from-cyan-700 via-cyan-600 to-emerald-500 p-10 text-white">
        <div>
          <img src="/brand/sakina-logo-light.svg" alt="Sakina Health Logo" class="h-11 w-auto" />
          <h1 class="mt-6 text-4xl font-bold leading-tight">Welcome back to your patient portal</h1>
          <p class="mt-4 max-w-md text-cyan-50/90">
            Access appointments, prescriptions, and your medical history in one secure place.
          </p>
        </div>
        <div class="rounded-2xl border border-white/20 bg-white/10 p-5 backdrop-blur">
          <p class="text-sm text-cyan-50">New patient?</p>
          <Link href="/patient/register" class="mt-2 inline-flex text-sm font-semibold text-white underline-offset-4 hover:underline">
            Create an account
          </Link>
        </div>
      </aside>

      <main class="flex items-center justify-center p-5 md:p-10">
        <div class="w-full max-w-xl rounded-3xl bg-white p-6 shadow-2xl ring-1 ring-slate-900/5 md:p-10">
          <div class="mb-8">
            <h2 class="text-3xl font-bold text-slate-900">Patient Login</h2>
            <p class="mt-2 text-sm text-slate-500">Sign in to continue to your dashboard.</p>
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
                placeholder="you@example.com"
                class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
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
                  class="w-full rounded-xl border border-slate-300 px-4 py-2.5 pr-12 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
                />
                <button
                  type="button"
                  class="absolute inset-y-0 right-0 flex w-11 items-center justify-center text-slate-500 hover:text-slate-700"
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

            <div class="flex items-center justify-between">
              <label class="inline-flex items-center gap-2 text-sm text-slate-600">
                <input
                  id="remember"
                  v-model="form.remember"
                  type="checkbox"
                  class="h-4 w-4 rounded border-slate-300 text-cyan-600 focus:ring-cyan-500"
                />
                Remember me
              </label>
            </div>

            <button
              type="submit"
              :disabled="form.processing"
              class="mt-2 inline-flex w-full items-center justify-center rounded-xl bg-gradient-to-r from-cyan-600 to-emerald-500 px-4 py-3 text-sm font-semibold text-white transition hover:from-cyan-700 hover:to-emerald-600 disabled:cursor-not-allowed disabled:opacity-60"
            >
              <span v-if="form.processing">Signing in...</span>
              <span v-else>Sign In</span>
            </button>
          </form>

          <p class="mt-6 text-center text-sm text-slate-500">
            Don&apos;t have an account?
            <Link href="/patient/register" class="font-semibold text-cyan-700 hover:text-cyan-800">Sign up</Link>
          </p>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';

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

  router.post('/patient/login', {
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
