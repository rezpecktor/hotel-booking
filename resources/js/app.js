import "primeicons/primeicons.css";
import "primevue/resources/themes/md-light-indigo/theme.css";
import "../css/app.css";
import "./bootstrap";

import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import PrimeVue from "primevue/config";
import ConfirmationService from "primevue/confirmationservice";
import DialogService from "primevue/dialogservice";
import ToastService from "primevue/toastservice";
import { createApp, h } from "vue";
import { ZiggyVue } from "../../vendor/tightenco/ziggy/dist/vue.m";
import Layout from "./Layouts/Layout.vue";

createInertiaApp({
    title: (title) => `${title} - Laracamp Booking`,
    // resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .use(PrimeVue)
            .use(ToastService)
            .use(DialogService)
            .use(ConfirmationService)
            .mount(el);
    },
    progress: {
        color: "#4B5563",
    },
    resolve: (name) => {
        const page = resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        );
        page.then((module) => {
            if (name.startsWith("Room/")) {
                // Jika halaman ada di folder Room
                module.default.layout = AdminLayout; // Terapkan AdminLayout
            }
        });
        return page;
    },
});
