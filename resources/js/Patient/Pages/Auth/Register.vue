<template>
  <div class="min-h-screen bg-slate-100">
    <div class="mx-auto grid min-h-screen w-full max-w-7xl grid-cols-1 lg:grid-cols-2">
      <aside class="hidden lg:flex flex-col justify-between bg-gradient-to-br from-cyan-700 via-cyan-600 to-emerald-500 p-10 text-white">
        <div>
          <img src="/brand/sakina-logo-light.svg" alt="Sakina Health Logo" class="h-11 w-auto" />
          <h1 class="mt-6 text-4xl font-bold leading-tight">Create your patient account with confidence</h1>
          <p class="mt-4 max-w-md text-cyan-50/90">
            Secure registration, easy profile setup, and a modern medical portal experience.
          </p>
        </div>
        <div class="rounded-2xl border border-white/20 bg-white/10 p-5 backdrop-blur">
          <p class="text-sm text-cyan-50">Already registered?</p>
          <Link href="/patient/login" class="mt-2 inline-flex text-sm font-semibold text-white underline-offset-4 hover:underline">
            Go to login
          </Link>
        </div>
      </aside>

      <main class="flex items-center justify-center p-5 md:p-10">
        <div class="w-full max-w-2xl rounded-3xl bg-white p-6 shadow-2xl ring-1 ring-slate-900/5 md:p-10">
          <div class="mb-8">
            <h2 class="text-3xl font-bold text-slate-900">Patient Registration</h2>
            <p class="mt-2 text-sm text-slate-500">Please fill in your details to create your account.</p>
          </div>

          <form @submit.prevent="submit" class="space-y-5">
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
              <div>
                <label for="name" class="mb-2 block text-sm font-semibold text-slate-700">Full Name</label>
                <input
                  id="name"
                  v-model="form.name"
                  type="text"
                  class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
                />
                <p v-if="form.errors.name" class="mt-1 text-xs text-red-600">{{ form.errors.name }}</p>
              </div>

              <div>
                <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">Email</label>
                <input
                  id="email"
                  v-model="form.email"
                  type="email"
                  class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
                />
                <p v-if="form.errors.email" class="mt-1 text-xs text-red-600">{{ form.errors.email }}</p>
              </div>
            </div>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
              <div>
                <label for="password" class="mb-2 block text-sm font-semibold text-slate-700">Password</label>
                <div class="relative">
                  <input
                    id="password"
                    v-model="form.password"
                    :type="showPassword ? 'text' : 'password'"
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
                <p v-if="form.errors.password" class="mt-1 text-xs text-red-600">{{ form.errors.password }}</p>
              </div>

              <div>
                <label for="password_confirmation" class="mb-2 block text-sm font-semibold text-slate-700">Confirm Password</label>
                <div class="relative">
                  <input
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    :type="showConfirmPassword ? 'text' : 'password'"
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 pr-12 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
                  />
                  <button
                    type="button"
                    class="absolute inset-y-0 right-0 flex w-11 items-center justify-center text-slate-500 hover:text-slate-700"
                    @click="showConfirmPassword = !showConfirmPassword"
                  >
                    <svg v-if="showConfirmPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18M10.6 10.6a2 2 0 102.8 2.8M9.88 4.24A10.94 10.94 0 0112 4c7 0 10 8 10 8a17.12 17.12 0 01-4.05 5.8M6.1 6.1A16.98 16.98 0 002 12s3 8 10 8a9.76 9.76 0 005.22-1.56" />
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.46 12C3.73 7.94 7.52 5 12 5c4.48 0 8.27 2.94 9.54 7-1.27 4.06-5.06 7-9.54 7-4.48 0-8.27-2.94-9.54-7z" />
                    </svg>
                  </button>
                </div>
              </div>
            </div>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
              <div>
                <label for="phone" class="mb-2 block text-sm font-semibold text-slate-700">Phone</label>
                <input
                  id="phone"
                  v-model="form.phone"
                  type="text"
                  class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
                />
              </div>

              <div>
                <label for="age" class="mb-2 block text-sm font-semibold text-slate-700">Age</label>
                <input
                  id="age"
                  v-model="form.age"
                  type="number"
                  min="0"
                  class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
                />
              </div>

              <div>
                <label for="gender" class="mb-2 block text-sm font-semibold text-slate-700">Gender</label>
                <select
                  id="gender"
                  v-model="form.gender"
                  class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
                >
                  <option value="">Select</option>
                  <option value="male">Male</option>
                  <option value="female">Female</option>
                  <option value="other">Other</option>
                </select>
              </div>
            </div>

            <div>
              <label for="city" class="mb-2 block text-sm font-semibold text-slate-700">City</label>
              <input
                id="city"
                v-model="form.city"
                type="text"
                class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
              />
            </div>

            <button
              type="submit"
              :disabled="form.processing"
              class="mt-2 inline-flex w-full items-center justify-center rounded-xl bg-gradient-to-r from-cyan-600 to-emerald-500 px-4 py-3 text-sm font-semibold text-white transition hover:from-cyan-700 hover:to-emerald-600 disabled:cursor-not-allowed disabled:opacity-60"
            >
              <span v-if="form.processing">Creating account...</span>
              <span v-else>Create Account</span>
            </button>

            <p class="text-center text-sm text-slate-500">
              Already have an account?
              <Link href="/patient/login" class="font-semibold text-cyan-700 hover:text-cyan-800">Login</Link>
            </p>
          </form>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';

const showPassword = ref(false);
const showConfirmPassword = ref(false);

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  phone: '',
  age: '',
  gender: '',
  city: '',
});

function submit() {
  form.post('/patient/register');
}
</script>
