// vite.config.js

import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
// --- TAMBAHKAN BARIS DI BAWAH INI ---
import path from "path";
// ------------------------------------

export default defineConfig({
    plugins: [
        laravel({
            input: "resources/js/app.js",
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
    ],
    resolve: {
        alias: {
            // --- GANTI BARIS DI BAWAH INI ---
            // 'ziggy-js': '/vendor/tightenco/ziggy/dist/vue.m',
            // --- MENJADI SEPERTI INI ---
            "ziggy-js": path.resolve(
                __dirname,
                "vendor/tightenco/ziggy/dist/vue.m"
            ),
            // ----------------------------
        },
    },
});
