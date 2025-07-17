<template>
    <div class="flex flex-col h-full">
        <h1 class="mb-4 text-2xl font-semibold text-center">
            Monthly Popular Room Types
        </h1>
        <div class="relative flex-1">
            <Chart
                ref="chartComponent"
                type="bar"
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

const chartComponent = ref(null);

const props = defineProps({
    popularRoomTypes: Object,
});

const mutatedPopularRoomTypes = computed(() => ({
    labels: Object.keys(props.popularRoomTypes),
    data: Object.values(props.popularRoomTypes),
}));

const chartData = ref({});
const chartOptions = ref({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: false, // Bar chart biasanya tidak butuh legend
        },
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                precision: 0, // Menampilkan angka bulat di sumbu Y
            },
        },
    },
});

const redrawChart = () => {
    if (chartComponent.value) {
        chartComponent.value.reinit();
    }
};

onMounted(() => {
    chartData.value = setChartData();
    window.addEventListener("resize", redrawChart);
});

onBeforeUnmount(() => {
    window.removeEventListener("resize", redrawChart);
});

const setChartData = () => {
    const documentStyle = getComputedStyle(document.documentElement);
    return {
        labels: mutatedPopularRoomTypes.value.labels,
        datasets: [
            {
                label: "Total Reservations",
                data: mutatedPopularRoomTypes.value.data,
                backgroundColor: [
                    "rgba(255, 159, 64, 0.2)",
                    "rgba(75, 192, 192, 0.2)",
                    "rgba(54, 162, 235, 0.2)",
                    "rgba(153, 102, 255, 0.2)",
                ],
                borderColor: [
                    "rgb(255, 159, 64)",
                    "rgb(75, 192, 192)",
                    "rgb(54, 162, 235)",
                    "rgb(153, 102, 255)",
                ],
                borderWidth: 1,
            },
        ],
    };
};
</script>
