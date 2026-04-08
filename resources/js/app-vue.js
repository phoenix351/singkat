import "./bootstrap";
// import "../css/app.css";
import "@fontsource/poppins/400.css";
import "@fontsource/poppins/500.css";
import "@fontsource/poppins/600.css";
import "@fontsource/poppins/700.css";

import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";

// const appName = import.meta.env.VITE_APP_NAME || "Laravel";
// const appName = 'MeetSulut';
const getPrefix = () => {
    const currentRoute = route().current()
    const prefix = currentRoute.split('.')[0]
    return prefix
}
const setNameApp = () => {
    let result = 'superapp'
    switch (getPrefix()) {
        case 'man-management':
            result = 'ManMent'
            break;
        case 'meeting':
            result = 'MeetSulut'
            break;
        default:
            result = 'superapp'
            break;
    }
    return result;
}
const colornProgress = () => {
    let result = "#4B5563"
    switch (getPrefix()) {
        case 'man-management':
            result = "#8B1E3F"
            break;
        case 'meeting':
            result = "#4B5563"
            break;
        default:
            result = "#4B5563"
            break;
    }
    return result;
}
createInertiaApp({
    title: (title) => `${title} - ${setNameApp()}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    async setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) })
        const prefix = getPrefix()
        vueApp.use(plugin)
        vueApp.use(ZiggyVue)
        if (prefix === 'meeting') {
            await import('../css/app.css')
            const { default: VueTailwindDatepicker } = await import("vue-tailwind-datepicker")
            vueApp.use(VueTailwindDatepicker)
        }
        if (prefix === "man-management") {
            await import('../css/manment/styles.scss')
            await import('../css/manment/tailwind.css')
            const { default: PrimeVue } = await import("primevue/config");
            const { default: Aura } = await import("@primeuix/themes/aura");

            vueApp.use(PrimeVue, {
                theme: {
                    preset: Aura,
                    options: {
                        darkModeSelector: ".app-dark",
                    },
                },
            });
        }
        //     .use(plugin)
        //     .use(ZiggyVue)
        //     .use(VueTailwindDatepicker)
        //     .use(PrimeVue, {
        //         theme: {
        //             preset: Aura,
        //             options: {
        //                 darkModeSelector: '.app-dark'
        //             }
        //         }
        //     })
        return vueApp.mount(el)
    },
    progress: {
        color: colornProgress(),
        showSpinner: true,
        delay: 250,
    },
});
