<template>
  <div class="space-y-6">
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <div class="mb-4 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900">Doctor Details</h1>
        <div class="flex gap-2">
          <a :href="`/admin/doctors/${doctor.id}/export-pdf`" target="_blank" class="rounded-lg border border-emerald-300 px-3 py-2 text-sm text-emerald-700 hover:bg-emerald-50">PDF</a>
          <Link href="/admin/doctors" class="rounded-lg bg-slate-900 px-3 py-2 text-sm text-white">Back</Link>
        </div>
      </div>

      <div class="grid grid-cols-1 gap-3 text-sm md:grid-cols-2">
        <div class="md:col-span-2">
          <span class="mb-2 block font-semibold text-slate-700">Photo:</span>
          <img
            :src="doctor.photo_url || placeholderAvatar"
            alt="Doctor photo"
            class="h-24 w-24 rounded-xl border border-slate-200 object-cover"
          />
        </div>
        <p><span class="font-semibold text-slate-700">Name:</span> {{ doctor.name }}</p>
        <p><span class="font-semibold text-slate-700">Email:</span> {{ doctor.email }}</p>
        <p><span class="font-semibold text-slate-700">Phone:</span> {{ doctor.phone || '-' }}</p>
        <p><span class="font-semibold text-slate-700">License:</span> {{ doctor.license_number }}</p>
        <p><span class="font-semibold text-slate-700">Specialization:</span> {{ doctor.specialization }}</p>
        <p><span class="font-semibold text-slate-700">Hospital:</span> {{ doctor.hospital_name || '-' }}</p>
        <p><span class="font-semibold text-slate-700">Experience:</span> {{ doctor.years_experience ?? 0 }} years</p>
        <p><span class="font-semibold text-slate-700">Verified:</span> {{ doctor.verified ? 'Yes' : 'No' }}</p>
      </div>

      <div class="mt-4 text-sm">
        <p class="font-semibold text-slate-700">Bio</p>
        <p class="mt-1 text-slate-600">{{ doctor.bio || '-' }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

const placeholderAvatar = 'https://ui-avatars.com/api/?name=Doctor&background=0f172a&color=ffffff';

defineProps({
  doctor: {
    type: Object,
    required: true,
  },
});
</script>
