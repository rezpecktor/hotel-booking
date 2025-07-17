<template>
    <div class="flex flex-col h-full">
        <h1 class="mb-4 text-2xl font-semibold text-center">
            Rooms Status Today
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

const chartComponent = ref(null);

const props = defineProps({
    availableRooms: Object,
});

const mutatedAvailableRooms = computed(() => ({
    labels: Object.keys(props.availableRooms),
    data: Object.values(props.availableRooms),
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
        labels: mutatedAvailableRooms.value.labels,
        datasets: [
            {
                data: mutatedAvailableRooms.value.data,
                backgroundColor: [
                    documentStyle.getPropertyValue("--green-500"),
                    documentStyle.getPropertyValue("--red-500"),
                ],
                hoverBackgroundColor: [
                    documentStyle.getPropertyValue("--green-400"),
                    documentStyle.getPropertyValue("--red-400"),
                ],
            },
        ],
    };
};
</script>
