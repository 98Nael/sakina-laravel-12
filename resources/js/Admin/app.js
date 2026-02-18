import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import AdminLayout from './Layouts/AdminLayout.vue';

const appName = import.meta.env.VITE_APP_NAME || 'Sakina Health';

createInertiaApp({
    title: (title) => `${title} - ${appName} | Admin`,
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue');
        const possiblePaths = [
            `./Pages/${name}.vue`,
            `./Pages/Auth/${name}.vue`,
            `./Pages/Admin/${name}.vue`,
        ];
        for (const path of possiblePaths) {
            if (pages[path]) {
                return pages[path]().then((module) => {
                    const page = module.default;
                    const isAuthPage = name.startsWith('Auth/');
                    if (!page.layout && !isAuthPage) {
                        page.layout = AdminLayout;
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
        color: '#ef4444', // Red for admin
        showSpinner: true,
    },
});
