import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // Main App
                'resources/css/app.css',
                'resources/js/app.js',

                // Admin
                'resources/js/Admin/app.js',
                'resources/css/admin-app.css',

                // Doctor
                'resources/js/Doctor/app.js',
                'resources/css/doctor-app.css',

                // Patient
                'resources/js/Patient/app.js',
                'resources/css/patient-app.css',
            ],
            refresh: true,
        }),

        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),

        tailwindcss(),
    ],
})
