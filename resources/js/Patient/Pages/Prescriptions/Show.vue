<template>
  <div class="space-y-6 pb-6">
    <section class="rounded-3xl border border-white/35 bg-gradient-to-r from-cyan-700 via-cyan-600 to-emerald-500 p-6 text-white shadow-2xl shadow-cyan-900/20">
      <p class="text-xs uppercase tracking-[0.22em] text-cyan-100">Prescription</p>
      <h1 class="mt-2 text-2xl font-bold">Prescription Details</h1>
      <p class="mt-1 text-sm text-cyan-50/90">Detailed view for a selected medication plan.</p>
    </section>

    <section class="rounded-3xl border border-cyan-100/70 bg-white/90 p-6 shadow-lg shadow-cyan-900/5">
      <p class="text-sm text-slate-500">Details page is ready. You can bind full data and actions next.</p>
    </section>
  </div>
</template>

<script setup>
defineProps({
  prescription: {
    type: Object,
    default: () => ({}),
  },
  pharmacy_suggestions: {
    type: Array,
    default: () => [],
  },
});
</script>
