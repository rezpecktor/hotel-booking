<script setup>
import LayoutButton from "@/Components/IndexLayout.vue/LayoutButton.vue";
import { Link, router, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import DynamicDialog from "primevue/dynamicdialog";
import Toast from "primevue/toast"; // Import Toast juga untuk notifikasi
import Footer from "@/Components/Footer.vue";

const user = computed(() => usePage().props.auth.user);
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <Toast position="top-right" />
        <DynamicDialog />
        <header
            class="sticky inset-0 top-0 z-20 px-3 m-3 bg-indigo-600 rounded drop-shadow-xl"
            v-memo="[user]"
        >
            <div
                class="flex max-w-2xl md:max-w-[75rem] items-center justify-between px-3 min-h-[4rem] mx-auto"
            >
                <Link href="/" class="block">
                    <h1 class="text-3xl font-extrabold text-white">
                        Raysa Homestay
                    </h1>
                </Link>
                <div class="flex items-center justify-end w-auto">
                    <div
                        class="flex items-center w-full h-full p-3 mr-3 transition-colors duration-300 rounded cursor-pointer"
                        v-if="user"
                    >
                        <LayoutButton
                            @click="router.visit(route('admin.index'))"
                            class="ml-3 mr-3"
                            v-if="user.role_id === 1"
                            >Go To Dashboard</LayoutButton
                        >
                        <LayoutButton
                            @click="router.visit(route('user.reservations'))"
                            class="mx-3 text-lg"
                            v-else
                            >My Reservations</LayoutButton
                        >
                        <div
                            class="w-10 h-10 ml-6 mr-4"
                            v-if="user.profile_image_path"
                        >
                            <img
                                :src="user.profile_image_path"
                                class="object-cover w-full h-full rounded-full"
                                :alt="`Profile image of ${user.name}`"
                                v-if="user.profile_image_path"
                            />
                        </div>
                        <Link
                            :href="
                                user.role_id === 1
                                    ? route('admin.profile.edit')
                                    : route('user.profile')
                            "
                        >
                            <span class="text-lg font-bold text-white">{{
                                user.name
                            }}</span>
                        </Link>
                    </div>
                    <div class="mx-3" v-else>
                        <LayoutButton
                            @click="router.visit('/login')"
                            class="mr-3"
                            >Login</LayoutButton
                        >
                        <LayoutButton @click="router.visit('/register')"
                            >Register</LayoutButton
                        >
                    </div>
                </div>
            </div>
        </header>

        <main class="px-3 rounded">
            <slot />
        </main>

        <Footer />
    </div>
</template>
