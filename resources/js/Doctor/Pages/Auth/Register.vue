<template>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-500 to-blue-700">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Doctor Registration</h1>
                <p class="text-gray-600 mt-2">Create your medical professional account</p>
            </div>

            <!-- Errors -->
            <div v-if="Object.keys(errors).length > 0" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <p v-for="(error, field) in errors" :key="field" class="text-red-600 text-sm">
                    {{ error }}
                </p>
            </div>

            <!-- Form -->
            <form @submit.prevent="handleRegister" class="space-y-4">
                <!-- Name Input -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Full Name
                    </label>
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="Dr. John Doe"
                    />
                </div>

                <!-- Email Input -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email Address
                    </label>
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="doctor@example.com"
                    />
                </div>

                <!-- Specialization Input -->
                <div>
                    <label for="specialization" class="block text-sm font-medium text-gray-700 mb-2">
                        Specialization
                    </label>
                    <select
                        id="specialization"
                        v-model="form.specialization"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    >
                        <option value="">Select a specialization</option>
                        <option value="Cardiology">Cardiology</option>
                        <option value="Dermatology">Dermatology</option>
                        <option value="Neurology">Neurology</option>
                        <option value="Orthopedics">Orthopedics</option>
                        <option value="Pediatrics">Pediatrics</option>
                        <option value="General Practice">General Practice</option>
                    </select>
                </div>

                <!-- License Number Input -->
                <div>
                    <label for="license_number" class="block text-sm font-medium text-gray-700 mb-2">
                        Medical License Number
                    </label>
                    <input
                        id="license_number"
                        v-model="form.license_number"
                        type="text"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="MED-123456"
                    />
                </div>

                <!-- Password Input -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password
                    </label>
                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="••••••••"
                    />
                </div>

                <!-- Password Confirmation Input -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        Confirm Password
                    </label>
                    <input
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="••••••••"
                    />
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full py-2 px-4 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span v-if="form.processing">Registering...</span>
                    <span v-else>Create Account</span>
                </button>
            </form>

            <!-- Footer -->
            <div class="mt-6 text-center text-sm text-gray-600">
                <p>
                    Already have an account?
                    <a href="/doctor/login" class="text-blue-600 hover:text-blue-700 font-medium">
                        Sign in
                    </a>
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive } from 'vue';
import { router } from '@inertiajs/vue3';

defineProps({
    errors: {
        type: Object,
        default: () => ({}),
    },
});

const form = reactive({
    name: '',
    email: '',
    specialization: '',
    license_number: '',
    password: '',
    password_confirmation: '',
    processing: false,
});

const handleRegister = async () => {
    form.processing = true;
    router.post('/doctor/register', {
        name: form.name,
        email: form.email,
        specialization: form.specialization,
        license_number: form.license_number,
        password: form.password,
        password_confirmation: form.password_confirmation,
    }, {
        onFinish: () => {
            form.processing = false;
        },
    });
};
</script>

<style scoped>
input:focus, select:focus {
    transition: all 0.2s ease;
}
</style>
