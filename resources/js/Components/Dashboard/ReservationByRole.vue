<template>
    <div class="flex flex-col h-full">
        <h1 class="mb-4 text-2xl font-semibold text-center">
            Reservation By Role
        </h1>
        <div class="relative flex-1">
            <Chart
                ref="chartComponent"
                type="pie"
                :data="chartData"
                :options="chartOptions"
                class="absolute top-0 left-0 w-full h-full"
            />
        </div>
    </div>
</template>

<script setup>
import Chart from "primevue/chart";
import { ref, onMounted, onBeforeUnmount, computed } from "vue";

// 1. Buat ref untuk komponen Chart
const chartComponent = ref(null);

const props = defineProps({
    reservationByRole: Object,
});

const mutatedAvailableRoomTypes = computed(() => ({
    labels: Object.keys(props.reservationByRole),
    data: Object.values(props.reservationByRole),
}));

const chartData = ref({});
const chartOptions = ref({
    responsive: true,
    maintainAspectRatio: false,
});

// 2. Buat fungsi untuk merender ulang chart
const redrawChart = () => {
    // Pastikan komponen chart sudah ada sebelum memanggil method-nya
    if (chartComponent.value) {
        chartComponent.value.reinit(); // reinit() adalah method dari PrimeVue untuk menggambar ulang
    }
};

onMounted(() => {
    chartData.value = setChartData();
    // 3. Tambahkan listener saat komponen selesai di-mount
    window.addEventListener("resize", redrawChart);
});

onBeforeUnmount(() => {
    // 4. Hapus listener saat komponen akan di-unmount untuk mencegah memory leak
    window.removeEventListener("resize", redrawChart);
});

const setChartData = () => {
    const documentStyle = getComputedStyle(document.body);
    return {
        labels: mutatedAvailableRoomTypes.value.labels,
        datasets: [
            {
                data: mutatedAvailableRoomTypes.value.data,
                backgroundColor: [
                    documentStyle.getPropertyValue("--blue-500"),
                    documentStyle.getPropertyValue("--yellow-500"),
                    documentStyle.getPropertyValue("--green-500"),
                ],
                hoverBackgroundColor: [
                    documentStyle.getPropertyValue("--blue-400"),
                    documentStyle.getPropertyValue("--yellow-400"),
                    documentStyle.getPropertyValue("--green-400"),
                ],
            },
        ],
    };
};
</script>
