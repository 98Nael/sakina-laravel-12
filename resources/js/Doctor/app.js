import '../bootstrap';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

const appName = import.meta.env.VITE_APP_NAME || 'Sakina Health';

createInertiaApp({
    title: (title) => `${title} - ${appName} | Doctor`,
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue');
        const possiblePaths = [
            `./Pages/${name}.vue`,
            `./Pages/Auth/${name}.vue`,
            `./Pages/Doctor/${name}.vue`,
        ];
        for (const path of possiblePaths) {
            if (pages[path]) {
                return pages[path]();
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
        color: '#3b82f6', // Blue for doctor
    },
});
