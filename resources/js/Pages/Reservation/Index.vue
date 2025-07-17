<template>
    <Head title="Reservations" />
    <DataTable
        scrollable
        :value="reservations.data"
        tableStyle="min-width: 75rem"
        striped-rows
        class="bg-slate-100/80"
    >
        <template #header>
            <div class="flex justify-between gap-2 mb-3">
                <div class="">
                    <span class="text-5xl font-bold text-900"
                        >Reservations</span
                    >
                </div>
                <div class="">
                    <Button
                        label="Clear Filter"
                        class="mr-3"
                        outlined
                        icon="pi pi-filter-slash"
                        @click="clearFilter"
                    />
                    <Button
                        label="Filter"
                        icon="pi pi-filter"
                        class="mr-3"
                        @click="showFilter"
                        outlined
                    />
                    <Link :href="route('admin.reservations.create')">
                        <Button
                            label="Create"
                            icon="pi pi-plus"
                            outlined
                            class="mr-3"
                        />
                    </Link>
                    <Link :href="route('admin.reservations.archives')">
                        <Button
                            label="Deleted Reservations"
                            icon=""
                            severity="danger"
                            text
                        />
                    </Link>
                </div>
            </div>
        </template>
        <template #empty>
            <h3 class="text-lg font-normal text-center">
                No reservation is found.
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
        <Column field="status" header="Status">
            <template #body="slotProps">
                <span :class="['p-tag', getStatusClass(slotProps.data.status)]">
                    {{ slotProps.data.status }}
                </span>
            </template>
        </Column>
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

        <!-- Kolom Actions yang Diperbarui dengan Logika Check-in -->
        <Column header="Actions" class="w-52">
            <template #body="slotProps">
                <div
                    v-if="slotProps.data.status === 'pending'"
                    class="flex gap-2"
                >
                    <Button
                        icon="pi pi-check"
                        aria-label="Confirm"
                        size="small"
                        class="p-button-success"
                        @click="confirmBooking(slotProps.data.id, 'confirm')"
                    />
                    <Button
                        icon="pi pi-times"
                        aria-label="Cancel"
                        size="small"
                        class="p-button-danger"
                        @click="confirmBooking(slotProps.data.id, 'cancel')"
                    />
                </div>

                <div
                    v-else-if="slotProps.data.status === 'confirmed'"
                    class="flex gap-2"
                >
                    <Button
                        v-if="!slotProps.data.checkin_time"
                        label="Check-in"
                        icon="pi pi-sign-in"
                        size="small"
                        class="p-button-primary"
                        @click="checkinBooking(slotProps.data.id)"
                    />

                    <Button
                        v-else-if="!slotProps.data.checkout_time"
                        label="Check-out"
                        icon="pi pi-sign-out"
                        size="small"
                        class="p-button-warning"
                        @click="checkoutBooking(slotProps.data.id)"
                    />

                    <Button
                        v-else
                        aria-label="Delete"
                        icon="pi pi-trash"
                        severity="danger"
                        size="small"
                        outlined
                        @click="confirmDelete(slotProps.data.id)"
                    />
                </div>

                <div v-else class="flex gap-2">
                    <Button
                        aria-label="Delete"
                        icon="pi pi-trash"
                        severity="danger"
                        size="small"
                        outlined
                        @click="confirmDelete(slotProps.data.id)"
                    />
                </div>
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
import { Link, router, Head } from "@inertiajs/vue3";
import axios from "axios";
import Button from "primevue/button";
import Column from "primevue/column";
import DataTable from "primevue/datatable";
import DynamicDialog from "primevue/dynamicdialog";
import Toast from "primevue/toast";
import { useConfirm } from "primevue/useconfirm";
import { useDialog } from "primevue/usedialog";
import { useToast } from "primevue/usetoast";

const props = defineProps({
    reservations: {
        required: true,
    },
});

const confirm = useConfirm();
const toast = useToast();

function confirmBooking(bookingId, action) {
    const message =
        action === "confirm"
            ? "Are you sure you want to confirm this booking?"
            : "Are you sure you want to cancel this booking?";

    confirm.require({
        message: message,
        header: "Confirmation",
        icon: "pi pi-info-circle",
        acceptClass:
            action === "confirm" ? "p-button-success" : "p-button-danger",
        accept: () => {
            const routeName =
                action === "confirm"
                    ? "admin.reservations.confirm"
                    : "admin.reservations.cancel";
            router.put(
                route(routeName, bookingId),
                {},
                {
                    preserveScroll: true,
                }
            );
        },
    });
}

// FUNGSI BARU UNTUK CHECK-IN
function checkinBooking(bookingId) {
    confirm.require({
        message: "Are you sure you want to check-in this guest?",
        header: "Check-in Confirmation",
        icon: "pi pi-info-circle",
        acceptClass: "p-button-primary",
        accept: () => {
            router.put(
                route("admin.reservations.checkin", bookingId),
                {},
                {
                    preserveScroll: true,
                }
            );
        },
    });
}

function checkoutBooking(bookingId) {
    confirm.require({
        message: "Are you sure this guest is checking out?",
        header: "Check-out Confirmation",
        icon: "pi pi-info-circle",
        acceptClass: "p-button-warning",
        accept: () => {
            router.put(
                route("admin.reservations.checkout", bookingId),
                {},
                {
                    preserveScroll: true,
                }
            );
        },
    });
}

function getStatusClass(status) {
    if (status === "pending") return "p-tag-warning";
    if (status === "confirmed") return "p-tag-success";
    if (status === "canceled") return "p-tag-danger";
    return "";
}

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
        return newDate.toLocaleString("id-ID"); // Menggunakan format Indonesia
    }
    return "N/A"; // Menampilkan N/A jika kosong
}

function confirmDelete(id) {
    confirm.require({
        message: `Are you sure you want to delete reservation #${id}?`,
        header: `Delete Reservation #${id}`,
        icon: "pi pi-info-circle",
        acceptClass: "p-button-danger",
        accept: () => {
            axios
                .delete(route("admin.reservations.destroy", id))
                .then((data) => {
                    toast.add({
                        severity: "success",
                        summary: "Deleted successfully",
                        detail: `Reservation #${id} is deleted successfully`,
                        life: 3000,
                    });
                    router.reload({ preserveState: false });
                });
        },
    });
}

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
