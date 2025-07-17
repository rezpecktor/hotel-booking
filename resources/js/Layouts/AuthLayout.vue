<script setup>
import { Link } from "@inertiajs/vue3";
</script>
<template>
    <div
        class="flex items-center justify-center w-screen h-screen bg-slate-100"
    >
        <div
            class="w-full p-6 m-4 bg-white rounded-lg shadow-lg md:w-1/2 xl:w-1/3"
        >
            <div class="mb-6 text-center">
                <!-- PERUBAHAN ADA DI BARIS INI -->
                <Link
                    :href="route('index')"
                    class="text-5xl font-extrabold text-indigo-600"
                    >Raysa Homestay Syariah</Link
                >
            </div>
            <slot />
        </div>
    </div>
</template>
