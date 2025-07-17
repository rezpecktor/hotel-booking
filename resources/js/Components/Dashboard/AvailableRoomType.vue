<template>
    <div class="flex flex-col h-full">
        <h1 class="mb-4 text-2xl font-semibold text-center">
            Available Room Types Today
        </h1>
        <div class="relative flex-1">
            <Chart
                ref="chartComponent"
                type="doughnut"
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
    availableRoomTypes: Object,
});

const mutatedAvailableRoomTypes = computed(() => ({
    labels: Object.keys(props.availableRoomTypes),
    data: Object.values(props.availableRoomTypes),
}));

const chartData = ref({});
const chartOptions = ref({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: "bottom",
            labels: {
                usePointStyle: true,
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
    const documentStyle = getComputedStyle(document.body);
    return {
        labels: mutatedAvailableRoomTypes.value.labels,
        datasets: [
            {
                data: mutatedAvailableRoomTypes.value.data,
                backgroundColor: [
                    documentStyle.getPropertyValue("--cyan-500"),
                    documentStyle.getPropertyValue("--orange-500"),
                    documentStyle.getPropertyValue("--gray-500"),
                    documentStyle.getPropertyValue("--red-500"),
                    documentStyle.getPropertyValue("--yellow-500"),
                ],
                hoverBackgroundColor: [
                    documentStyle.getPropertyValue("--cyan-400"),
                    documentStyle.getPropertyValue("--orange-400"),
                    documentStyle.getPropertyValue("--gray-400"),
                    documentStyle.getPropertyValue("--red-400"),
                    documentStyle.getPropertyValue("--yellow-400"),
                ],
            },
        ],
    };
};
</script>
