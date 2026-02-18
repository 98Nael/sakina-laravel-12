import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import PatientLayout from './Layouts/PatientLayout.vue';

const appName = import.meta.env.VITE_APP_NAME || 'Sakina Health';

createInertiaApp({
    title: (title) => `${title} - ${appName} | Patient`,
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue');
        const possiblePaths = [
            `./Pages/${name}.vue`,
            `./Pages/Auth/${name}.vue`,
            `./Pages/Patient/${name}.vue`,
            `./Pages/Patient/Pages/${name}.vue`,
        ];
        for (const path of possiblePaths) {
            if (pages[path]) {
                return pages[path]().then((module) => {
                    const page = module.default;
                    const isAuthPage = name.startsWith('Auth/');
                    if (!page.layout && !isAuthPage) {
                        page.layout = PatientLayout;
                    }
                    return page;
                });
            }
        }
        throw new Error(`Page not found: ${name}`);
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
    progress: {
        color: '#10b981', // Green for patient
    },
});
