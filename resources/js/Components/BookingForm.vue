<template>
    <div class="p-6 min-w-[30rem]">
        <h3 class="mb-1 text-2xl font-bold">Konfirmasi Pesanan</h3>
        <p class="mb-4 text-gray-600">
            Untuk tipe kamar:
            <span class="font-semibold">{{ roomType.name }}</span>
        </p>

        <form @submit.prevent="submitBooking">
            <div class="space-y-4">
                <div class="flex flex-col">
                    <label for="guest_name" class="font-medium text-gray-700"
                        >Nama Tamu</label
                    >
                    <InputText
                        id="guest_name"
                        v-model="form.guest_name"
                        :invalid="form.errors.guest_name"
                    />
                    <small
                        v-if="form.errors.guest_name"
                        class="mt-1 text-red-600"
                        >{{ form.errors.guest_name }}</small
                    >
                </div>
                <div class="flex flex-col">
                    <label for="total_person" class="font-medium text-gray-700"
                        >Jumlah Orang</label
                    >
                    <InputNumber
                        id="total_person"
                        v-model="form.total_person"
                        :min="1"
                        :invalid="form.errors.total_person"
                    />
                    <small
                        v-if="form.errors.total_person"
                        class="mt-1 text-red-600"
                        >{{ form.errors.total_person }}</small
                    >
                </div>
            </div>
            <div class="flex justify-end mt-6">
                <Button
                    label="Batalkan"
                    severity="secondary"
                    @click="dialogRef.close()"
                    class="mr-2"
                    type="button"
                />
                <Button
                    type="submit"
                    label="Buat Pesanan"
                    :loading="form.processing"
                />
            </div>
        </form>
    </div>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
import Button from "primevue/button";
import InputText from "primevue/inputtext";
import InputNumber from "primevue/inputnumber";
import { inject } from "vue";
import { useToast } from "primevue/usetoast";

// Mengambil referensi dialog dari parent
const dialogRef = inject("dialogRef");
const toast = useToast();

// Mengambil data yang dikirim dari Welcome.vue
const { roomType, searchParams } = dialogRef.value.data;

const form = useForm({
    guest_name: "",
    total_person: 1,
    from_date: searchParams.from_date,
    to_date: searchParams.to_date,
    room_type_id: roomType.id, // Mengirim ID tipe kamar
});

function submitBooking() {
    // GANTI 'booking.store' MENJADI 'user.reserve'
    form.post(route("user.reserve"), {
        preserveScroll: true,
        onSuccess: () => {
            dialogRef.value.close();
            toast.add({
                severity: "success",
                summary: "Berhasil",
                detail: "Reservasi Anda telah dibuat!",
                life: 3000,
            });
            // Refresh halaman untuk menampilkan data terbaru
            router.visit(window.location.href, {
                preserveScroll: true,
                preserveState: true,
            });
        },
        onError: (errors) => {
            toast.add({
                severity: "error",
                summary: "Gagal",
                detail: "Silakan periksa kembali isian Anda.",
                life: 3000,
            });
        },
    });
}
</script>
