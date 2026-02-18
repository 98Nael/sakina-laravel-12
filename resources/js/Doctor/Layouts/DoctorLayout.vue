<template>
  <div class="relative min-h-screen overflow-hidden bg-[#eefcf9] text-slate-800">
    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_10%_10%,rgba(20,184,166,0.18),transparent_40%),radial-gradient(circle_at_90%_90%,rgba(16,185,129,0.16),transparent_44%)]"></div>

    <div class="relative mx-auto flex min-h-screen w-full max-w-7xl gap-6 px-4 py-5 md:px-6 lg:px-8">
      <aside class="hidden w-72 flex-col rounded-3xl border border-white/40 bg-gradient-to-b from-teal-700 to-teal-900 p-6 text-white shadow-2xl lg:flex">
        <div>
          <p class="text-xs uppercase tracking-[0.24em] text-teal-100/90">Doctor Workspace</p>
          <h1 class="mt-4 text-2xl font-bold">Doctor Portal</h1>
          <p class="mt-2 text-sm text-teal-100/85">{{ auth?.user?.name ?? 'Doctor' }}</p>
        </div>

        <nav class="mt-8 space-y-2">
          <Link
            v-for="item in navItems"
            :key="item.href"
            :href="item.href"
            :class="[
              'block rounded-xl px-4 py-3 text-sm font-semibold transition',
              isActivePath(item.href)
                ? 'bg-white text-teal-800 shadow-lg shadow-teal-950/25'
                : 'text-teal-100 hover:bg-white/15 hover:text-white',
            ]"
          >
            {{ item.label }}
          </Link>
        </nav>

        <div class="mt-auto rounded-2xl border border-white/20 bg-white/10 p-4 backdrop-blur">
          <p class="text-xs uppercase tracking-[0.18em] text-teal-100">Account</p>
          <p class="mt-1 text-sm text-white">{{ auth?.user?.email ?? '' }}</p>
          <button
            type="button"
            class="mt-4 inline-flex w-full items-center justify-center rounded-xl border border-white/35 bg-white/10 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-white/20"
            @click="logout"
          >
            Logout
          </button>
        </div>
      </aside>

      <div class="flex min-w-0 flex-1 flex-col gap-4">
        <header class="rounded-3xl border border-teal-900/10 bg-white/85 p-5 shadow-xl shadow-teal-900/5 backdrop-blur">
          <div class="mb-4 flex items-center justify-between gap-4 lg:mb-0">
            <div>
              <p class="text-xs uppercase tracking-[0.2em] text-teal-700">Clinical Overview</p>
              <h2 class="mt-1 text-2xl font-bold text-slate-900">
                <slot name="header">Dashboard</slot>
              </h2>
            </div>
            <button
              type="button"
              class="rounded-xl bg-teal-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-teal-800 lg:hidden"
              @click="logout"
            >
              Logout
            </button>
          </div>

          <nav class="flex gap-2 overflow-x-auto pb-1 lg:hidden">
            <Link
              v-for="item in navItems"
              :key="`mobile-${item.href}`"
              :href="item.href"
              :class="[
                'whitespace-nowrap rounded-lg px-3 py-2 text-sm font-semibold transition',
                isActivePath(item.href)
                  ? 'bg-teal-700 text-white'
                  : 'bg-teal-50 text-teal-700 hover:bg-teal-100',
              ]"
            >
              {{ item.label }}
            </Link>
          </nav>
        </header>

        <main class="flex-1">
          <slot />
        </main>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';

const page = usePage();
const auth = computed(() => page.props.auth);

const navItems = [
  { href: '/doctor/dashboard', label: 'Dashboard' },
  { href: '/doctor/patients', label: 'Patients' },
  { href: '/doctor/appointments', label: 'Appointments' },
  { href: '/doctor/prescriptions', label: 'Prescriptions' },
  { href: '/doctor/profile', label: 'Profile' },
];

function logout() {
  router.post('/doctor/logout', {}, {
    onFinish: () => {
      window.location.href = '/doctor/login';
    },
  });
}

function isActivePath(path) {
  const currentPath = page.url.split('?')[0];
  return currentPath === path || currentPath.startsWith(`${path}/`);
}
</script>
