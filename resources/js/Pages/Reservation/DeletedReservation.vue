<template>
    <Head title="Deleted Reservations" />
    <DataTable
        scrollable
        :value="reservations.data"
        tableStyle="min-width: 75rem"
        striped-rows
        class="bg-slate-100/80"
        :pt="{
            header: (options) => ({
                class: ['!py-3 !px-0'],
            }),
        }"
    >
        <template #header>
            <div class="flex justify-between gap-2 mb-3">
                <div class="">
                    <span class="text-5xl font-bold text-red-500 text-900"
                        >Deleted Reservations</span
                    >
                </div>
                <div class=""></div>
            </div>
        </template>
        <template #empty>
            <h3 class="text-lg font-normal text-center">
                No deleted reservation is found.
            </h3>
        </template>
        <Column field="id" header="id"></Column>
        <Column field="room_id" header="Room No">
            <template #body="slotProps">
                <span
                    v-for="(room, i) in slotProps.data.room_id"
                    :key="`${slotProps.index}${i}`"
                >
                    <template v-if="i < slotProps.data.room_id.length - 1">
                        {{ room }},
                    </template>
                    <template v-else>
                        {{ room }}
                    </template>
                </span>
            </template>
        </Column>

        <Column field="guest_name" header="Guest Name"></Column>

        <Column header="Total Person" field="total_person"> </Column>
        <Column field="total_price" header="Price">
            <template #body="slotProps">
                {{ formatCurrency(slotProps.data.total_price) }}
            </template>
        </Column>
        <Column field="from_date" header="Reserved From">
            <template #body="slotProps">
                {{ getDate(slotProps.data.from_date) }}
            </template>
        </Column>
        <Column field="to_date" header="Reserved To">
            <template #body="slotProps">
                {{ getDate(slotProps.data.to_date) }}
            </template>
        </Column>
        <Column field="checkin_time" header="Checkin Time">
            <template #body="slotProps">
                {{ getDateTime(slotProps.data.checkin_time) }}
            </template>
        </Column>
        <Column field="checkout_time" header="Checkout Time">
            <template #body="slotProps">
                {{ getDateTime(slotProps.data.checkout_time) }}
            </template>
        </Column>
        <Column header="Actions" class="w-52">
            <template #body="slotProps">
                <Button
                    icon="pi pi-undo"
                    aria-label="Submit"
                    size="small"
                    outlined
                    class="mr-2"
                    @click="
                        () =>
                            restoreSoftDelete(
                                'Reservation',
                                slotProps.data.id,
                                route(
                                    'admin.reservations.restore',
                                    slotProps.data.id
                                ),
                                confirm,
                                toast,
                                deleteRoomType,
                                router
                            )
                    "
                />
            </template>
        </Column>
        <template #footer>
            <div class="flex justify-between">
                <div class="">
                    <span
                        >Showing {{ reservations.from }} to
                        {{ reservations.to }} of
                        {{ reservations.total }} results.</span
                    >
                </div>
                <CustomPaginator
                    :current-page="reservations.current_page"
                    :total-pages="reservations.last_page"
                    route-name="admin.reservations.index"
                />
            </div>
        </template>
    </DataTable>
    <Toast position="bottom-right" />
    <DynamicDialog />
</template>

<script setup>
import CustomPaginator from "@/Components/CustomPaginator.vue";
import Filter from "@/Components/Filter.vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import Button from "primevue/button";
import Column from "primevue/column";
import DataTable from "primevue/datatable";
import DynamicDialog from "primevue/dynamicdialog";
import Toast from "primevue/toast";
import { useConfirm } from "primevue/useconfirm";
import { useDialog } from "primevue/usedialog";
import { useToast } from "primevue/usetoast";
import restoreSoftDelete from "@/functions/restoreSoftDelete";

const props = defineProps({
    reservations: {
        required: true,
    },
});

// BAGIAN YANG DIPERBAIKI 2: Fungsi formatCurrency
function formatCurrency(currency) {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(currency);
}

function getDate(date) {
    let newDate = new Date(date);
    return newDate.toLocaleDateString();
}
function getDateTime(date) {
    if (date) {
        let newDate = new Date(date);
        return newDate.toLocaleString();
    }
    return null;
}

const confirm = useConfirm();
const toast = useToast();
const deleteRoomType = useForm({});

const dialog = useDialog();
function showFilter() {
    dialog.open(Filter, {
        props: {
            modal: true,
        },
    });
}

function clearFilter() {
    router.visit(route("admin.reservations.index"));
}
</script>
<script>
import AdminLayout from "@/Layouts/AdminLayout.vue";
export default {
    layout: AdminLayout,
};
</script>
