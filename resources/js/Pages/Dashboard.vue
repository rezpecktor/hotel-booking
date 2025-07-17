<template>
    <Head title="Dashboard" />
    <h1 class="mb-6 text-5xl font-bold text-900">Dashboard</h1>
    <div class="max-w-7xl">
        <div class="grid grid-cols-1 gap-3 mb-9 md:grid-cols-2 lg:grid-cols-3">
            <div
                class="p-3 transition-colors duration-300 rounded shadow bg-slate-100/80 hover:bg-slate-200/80"
            >
                <h1 class="mb-3 text-2xl font-bold text-center">
                    Total Guest This Month
                </h1>
                <h2 class="text-5xl font-semibold text-center text-indigo-500">
                    {{ Number(monthlyGuests).toLocaleString("en") }}
                </h2>
            </div>
            <div
                class="p-3 transition-colors duration-300 rounded shadow bg-slate-100/80 hover:bg-slate-200/80"
            >
                <h1 class="mb-3 text-2xl font-bold text-center">
                    Total Number Of Rooms
                </h1>
                <h2 class="text-5xl font-semibold text-center text-indigo-500">
                    {{ totalRooms }}
                </h2>
            </div>
            <div
                class="p-3 transition-colors duration-300 rounded shadow md:col-span-2 lg:col-span-1 bg-slate-100/80 hover:bg-slate-200/80"
            >
                <h1 class="mb-3 text-2xl font-bold text-center">
                    Total Earned This Month
                </h1>
                <h2 class="text-5xl font-semibold text-center text-indigo-500">
                    {{
                        new Intl.NumberFormat("id-ID", {
                            style: "currency",
                            currency: "IDR",
                            minimumFractionDigits: 0,
                        }).format(monthlyAmount)
                    }}
                </h2>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-3">
            <div
                class="w-full p-4 transition-colors duration-300 rounded shadow h-96 bg-slate-100/80 hover:bg-slate-200/80"
            >
                <AvailableRoomType
                    :available-room-types="todayAvailableRoomTypes"
                />
            </div>

            <div
                class="w-full p-4 transition-colors duration-300 rounded shadow h-96 bg-slate-100/80 hover:bg-slate-200/80"
            >
                <AvailableRoom
                    :available-rooms="{
                        'Available Rooms': props.todayAvailableRooms?.length,
                        'Reserved Rooms': props.todayReservedRooms?.length,
                    }"
                />
            </div>

            <div
                class="w-full p-4 transition-colors duration-300 rounded shadow h-96 bg-slate-100/80 hover:bg-slate-200/80"
            >
                <ReservationByRole
                    :reservation-by-role="{
                        User: props.userReservedReservations,
                        Admin: props.adminReservedReservations,
                    }"
                />
            </div>

            <div
                class="w-full p-4 transition-colors duration-300 rounded shadow h-96 lg:col-span-3 bg-slate-100/80 hover:bg-slate-200/80"
            >
                <PopularRoomType
                    :popular-room-types="monthlyPopularRoomTypes"
                />
            </div>
        </div>
    </div>
</template>

<script setup>
import AvailableRoom from "@/Components/Dashboard/AvailableRoom.vue";
import AvailableRoomType from "@/Components/Dashboard/AvailableRoomType.vue";
import PopularRoomType from "@/Components/Dashboard/PopularRoomType.vue";
import ReservationByRole from "@/Components/Dashboard/ReservationByRole.vue";
import { Head } from "@inertiajs/vue3";

const props = defineProps({
    todayAvailableRoomTypes: Object,
    todayAvailableRooms: Array,
    todayReservedRooms: Array,
    monthlyPopularRoomTypes: Object,
    monthlyGuests: String,
    monthlyAmount: String,
    totalRooms: Number,
    adminReservedReservations: Number,
    userReservedReservations: Number,
});
</script>

<script>
import AdminLayout from "@/Layouts/AdminLayout.vue";
export default {
    layout: AdminLayout,
};
</script>
