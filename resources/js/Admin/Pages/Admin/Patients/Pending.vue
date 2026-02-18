<template>
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h2 class="text-xl font-semibold text-gray-800">Pending Patients</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700">Name</th>
                        <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700">Email</th>
                        <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700">Created By</th>
                        <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="patient in patients.data" :key="patient.id" class="border-t">
                        <td class="px-6 py-3">{{ patient.name }}</td>
                        <td class="px-6 py-3">{{ patient.email }}</td>
                        <td class="px-6 py-3">{{ patient.created_by_role || '-' }}</td>
                        <td class="px-6 py-3 space-x-2">
                            <button
                                class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm"
                                @click="approve(patient.id)"
                            >
                                Approve
                            </button>
                            <button
                                class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm"
                                @click="reject(patient.id)"
                            >
                                Reject
                            </button>
                        </td>
                    </tr>
                    <tr v-if="!patients.data.length">
                        <td colspan="4" class="px-6 py-6 text-center text-gray-500">No pending patients.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3';

defineProps({
    patients: {
        type: Object,
        required: true,
    },
});

function approve(id) {
    router.patch(`/admin/patients/${id}/approve`);
}

function reject(id) {
    router.patch(`/admin/patients/${id}/reject`);
}
</script>
