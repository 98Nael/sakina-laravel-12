<template>
  <div class="flex min-h-screen bg-slate-100">
    <aside class="hidden w-72 flex-col border-r border-white/10 bg-[linear-gradient(180deg,#0b1020,#111827)] text-slate-100 md:flex">
      <div class="border-b border-white/10 p-6">
        <img src="/brand/sakina-logo-light.svg" alt="Sakina Health Logo" class="h-10 w-auto" />
        <p class="text-xs uppercase tracking-[0.22em] text-amber-300">Admin Console</p>
        <h1 class="mt-3 text-2xl font-bold text-white">Control Panel</h1>
        <p class="mt-2 text-sm text-slate-400">{{ auth?.user?.name ?? '' }}</p>
      </div>

      <nav class="flex-1 space-y-2 p-4">
        <Link
          href="/admin/dashboard"
          :class="navClass('/admin/dashboard')"
        >
          Dashboard
        </Link>
        <Link
          href="/admin/users"
          :class="navClass('/admin/users')"
        >
          Users
        </Link>
        <Link
          href="/admin/profile"
          :class="navClass('/admin/profile')"
        >
          Profile
        </Link>
        <Link
          href="/admin/settings"
          :class="navClass('/admin/settings')"
        >
          Settings
        </Link>
        <Link
          href="/admin/centers"
          :class="navClass('/admin/centers')"
        >
          Centers
        </Link>
        <Link
          href="/admin/reports"
          :class="navClass('/admin/reports')"
        >
          Reports
        </Link>
        <Link
          href="/admin/contact-messages"
          :class="navClass('/admin/contact-messages')"
        >
          Contact Control
        </Link>
      </nav>

      <div class="border-t border-white/10 p-4">
        <button
          type="button"
          class="w-full rounded-xl border border-red-400/30 bg-red-500/10 px-4 py-2.5 text-left text-sm font-medium text-red-200 transition hover:bg-red-500/20"
          @click="logout"
        >
          Logout
        </button>
      </div>
    </aside>

    <div class="flex min-w-0 flex-1 flex-col">
      <header class="sticky top-0 z-40 border-b border-slate-200 bg-white/90 backdrop-blur">
        <div class="mx-auto flex w-full max-w-7xl items-center justify-between px-5 py-4 md:px-6">
          <h2 class="text-xl font-semibold text-slate-900 md:text-2xl">
            <slot name="header">Dashboard</slot>
          </h2>
          <div class="flex items-center gap-4">
            <span class="hidden text-sm text-slate-600 md:block">{{ auth?.user?.email ?? '' }}</span>
            <button
              type="button"
              class="rounded-lg px-3 py-1.5 text-sm font-medium text-slate-700 transition hover:bg-slate-100 hover:text-slate-900 md:hidden"
              @click="logout"
            >
              Logout
            </button>
          </div>
        </div>
      </header>

      <main class="flex-1 p-5 md:p-6">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';

const page = usePage();
const auth = page.props?.auth ?? {};

function logout() {
  router.post('/admin/logout', {}, {
    onFinish: () => {
      window.location.href = '/admin/login';
    },
  });
}

function isActivePath(path) {
  const currentPath = (page.url ?? '').split('?')[0];
  return currentPath === path || currentPath.startsWith(`${path}/`);
}

function navClass(path) {
  return [
    'block rounded-xl px-4 py-2.5 text-sm font-medium transition',
    isActivePath(path)
      ? 'border border-amber-400/35 bg-amber-400/10 text-amber-300'
      : 'text-slate-300 hover:bg-white/5 hover:text-white',
  ];
}
</script>
