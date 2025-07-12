<template>
    <Head title="Dashboard" />
    <h1 class="mb-6 text-5xl font-bold text-900">Dashboard</h1>
    <div class="max-w-7xl">
        <div class="flex flex-wrap w-full gap-3 mb-9">
            <div
                class="flex-1 w-1/2 p-3 transition-colors duration-300 rounded shadow bg-slate-100/80 hover:bg-slate-200/80 lg:w-1/3"
            >
                <h1 class="mb-3 text-2xl font-bold text-center">
                    Total Guest This Month
                </h1>
                <h2 class="text-5xl font-semibold text-center text-indigo-500">
                    {{ Number(monthlyGuests).toLocaleString("en") }}
                </h2>
            </div>
            <div
                class="flex-1 w-1/2 p-3 transition-colors duration-300 rounded shadow bg-slate-100/80 hover:bg-slate-200/80 lg:w-1/3"
            >
                <h1 class="mb-3 text-2xl font-bold text-center">
                    Total Number Of Rooms
                </h1>
                <h2 class="text-5xl font-semibold text-center text-indigo-500">
                    {{ totalRooms }}
                </h2>
            </div>
            <div
                class="w-full p-3 transition-colors duration-300 rounded shadow bg-slate-100/80 hover:bg-slate-200/80 lg:w-1/3 lg:flex-1"
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
        <div class="flex flex-wrap w-full gap-3">
            <AvailableRoomType
                :available-room-types="todayAvailableRoomTypes"
                class="flex-1 w-full p-3 transition-colors duration-300 rounded shadow md:w-1/3 bg-slate-100/80 hover:bg-slate-200/80"
            />
            <AvailableRoom
                :available-rooms="{
                    'Available Rooms': props.todayAvailableRooms?.length,
                    'Reserved Rooms': props.todayReservedRooms?.length,
                }"
                class="flex-1 w-full p-3 transition-colors duration-300 rounded shadow md:w-1/3 bg-slate-100/80 hover:bg-slate-200/80"
            />
            <ReservationByRole
                class="flex-1 w-full p-3 transition-colors duration-300 rounded shadow md:w-1/3 bg-slate-100/80 hover:bg-slate-200/80"
                :reservation-by-role="{
                    User: props.userReservedReservations,
                    Admin: props.adminReservedReservations,
                }"
            />
            <PopularRoomType
                :popular-room-types="monthlyPopularRoomTypes"
                class="w-full p-3 transition-colors duration-300 rounded shadow bg-slate-100/80 hover:bg-slate-200/80"
            />
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
