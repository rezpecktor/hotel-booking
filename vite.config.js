import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import vueJsx from "@vitejs/plugin-vue-jsx"; // <-- Tambahan dari kita

export default defineConfig({
    plugins: [
        laravel({
            // Memastikan CSS dan JS diproses
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
        vue({
            // Konfigurasi asli yang penting, kita pertahankan
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        vueJsx(), // <-- Plugin baru untuk mengatasi error JSX
    ],
});
