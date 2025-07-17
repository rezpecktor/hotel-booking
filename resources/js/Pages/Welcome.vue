<script setup>
import WelcomeFilter from "@/Components/Welcome/WelcomeFilter.vue";
import BookingForm from "@/Components/BookingForm.vue";
import { Head, Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import { useDialog } from "primevue/usedialog";
import Button from "primevue/button";

const props = defineProps({
    errors: Object,
    roomTypes: Object,
    searchRooms: {
        type: [Object, Array], // Bisa array atau object, kita buat fleksibel
        required: false,
    },
    searchParams: {
        type: Object,
        required: false,
    },
});

// Mengecek apakah pengguna sudah login
const user = computed(() => usePage().props.auth.user);

// Mengecek apakah pencarian sedang aktif
const isSearching = computed(() => {
    return props.searchRooms !== null && props.searchRooms !== undefined;
});

const dialog = useDialog();

// Fungsi untuk membuka modal pemesanan
function openBookingDialog(roomType) {
    // Jika user belum login, arahkan ke halaman login
    if (!user.value) {
        window.location.href = route("login");
        return;
    }

    // Jika sudah login, buka modal
    dialog.open(BookingForm, {
        props: {
            header: "Formulir Pemesanan",
            modal: true,
            draggable: false,
        },
        data: {
            roomType: roomType,
            searchParams: props.searchParams,
        },
    });
}
</script>

<template>
    <Head title="Welcome" />
    <div class="p-3 rounded bg-slate-100">
        <div
            class="md:max-w-[75rem] max-w-2xl flex-col items-center justify-center mx-auto py-24 px-3"
        >
            <div class="mb-20">
                <h1 class="mb-3 text-6xl font-bold">
                    Welcome to
                    <span class="text-6xl font-extrabold text-indigo-600"
                        >Raysa Homestay Syariah</span
                    >
                </h1>
                <h2 class="text-3xl font-normal">
                    We offer the world-class hospitality with luxury experience.
                </h2>
            </div>

            <WelcomeFilter :room-types="roomTypes" />

            <div v-if="isSearching" class="mt-12">
                <div v-if="searchRooms.length > 0">
                    <h3 class="mb-6 text-3xl font-bold text-gray-800">
                        Kamar Tersedia
                    </h3>
                    <div class="p-6 space-y-5 bg-white rounded-lg shadow-lg">
                        <div
                            v-for="roomType in searchRooms"
                            :key="roomType.id"
                            class="grid items-center grid-cols-3 gap-4 pb-4 border-b last:border-b-0"
                        >
                            <div class="col-span-2">
                                <h4 class="text-xl font-semibold">
                                    {{ roomType.name }}
                                </h4>
                                <p class="text-gray-600">
                                    {{ roomType.available_count }} kamar
                                    tersedia
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-indigo-600">
                                    {{
                                        new Intl.NumberFormat("id-ID", {
                                            style: "currency",
                                            currency: "IDR",
                                            minimumFractionDigits: 0,
                                        }).format(roomType.price)
                                    }}
                                </p>
                                <p class="mb-2 text-sm text-gray-500">
                                    per malam
                                </p>

                                <Button
                                    v-if="roomType.available_count > 0"
                                    label="Pesan Sekarang"
                                    @click="openBookingDialog(roomType)"
                                    class="p-button-sm"
                                />
                                <span
                                    v-else
                                    class="px-3 py-1 text-xs font-semibold text-white bg-red-500 rounded-full"
                                    >Penuh</span
                                >
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    v-else
                    class="py-16 text-center bg-white rounded-lg shadow-md"
                >
                    <h1 class="text-2xl font-semibold text-gray-600">
                        Maaf, Tidak Ada Kamar yang Tersedia
                    </h1>
                    <p class="text-gray-500">
                        Silakan coba cari untuk tanggal atau tipe kamar yang
                        lain.
                    </p>
                </div>
            </div>

            <div v-else class="mt-16">
                <h3 class="mb-6 text-4xl font-bold text-gray-800">
                    Tipe Kamar Kami
                </h3>
                <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                    <div
                        v-for="roomType in roomTypes"
                        :key="roomType.id"
                        class="overflow-hidden bg-white rounded-lg shadow-lg"
                    >
                        <img
                            :src="roomType.image_url"
                            :alt="roomType.name"
                            class="object-cover w-full h-56"
                        />
                        <div class="p-6">
                            <h4 class="text-2xl font-semibold text-gray-900">
                                {{ roomType.name }}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import IndexLayout from "@/Layouts/IndexLayout.vue";
import DynamicDialog from "primevue/dynamicdialog";

export default {
    layout: IndexLayout,
    components: {
        DynamicDialog, // Daftarkan komponen DynamicDialog agar bisa digunakan
    },
};
</script>
