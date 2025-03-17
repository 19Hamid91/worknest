require("./bootstrap");

import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/inertia-vue3";
import { InertiaProgress } from "@inertiajs/progress";
import PrimeVue from "primevue/config";
import Lara from "@primeuix/themes/lara";
import Nora from "@primeuix/themes/nora";
import Aura from "@primeuix/themes/aura";
import Material from "@primeuix/themes/material";
import "primeicons/primeicons.css";
import ToastService from "primevue/toastservice";

const appName =
    window.document.getElementsByTagName("title")[0]?.innerText || "Laravel";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => require(`./Pages/${name}.vue`),
    setup({ el, app, props, plugin }) {
        return createApp({ render: () => h(app, props) })
            .use(plugin)
            .use(PrimeVue, {
                theme: {
                    preset: Aura, // Lara, Nora, Aura, Material
                    options: {
                        darkModeSelector: false || "none",
                    },
                },
            })
            .use(ToastService)
            .mixin({ methods: { route } })
            .mount(el);
    },
});

InertiaProgress.init({ color: "#4B5563" });
