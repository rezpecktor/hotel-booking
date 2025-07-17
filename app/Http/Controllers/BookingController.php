<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Jobs\SendBookingMailJob;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Carbon\Carbon; // <-- Pastikan use Carbon ditambahkan di atas

class BookingController extends Controller
{
    /**
     * Menampilkan halaman utama dengan filter pencarian dan hasil.
     */
    public function index()
    {
        // Selalu ambil semua tipe kamar untuk ditampilkan di filter dan galeri
        $allRoomTypes = RoomType::all()->map(function ($type) {
            $imageName = strtolower($type->name) . '.jpg';
            $type->image_url = asset('images/' . $imageName);
            return $type;
        });

        $searchParams = request(['from_date', 'to_date', 'room_type_id']);

        // Jika ada parameter pencarian di URL
        if (request()->has("from_date") || request()->has("to_date") || request()->has("room_type_id")) {

            // 1. Ambil semua tipe kamar yang dicari oleh pengguna
            $query = RoomType::query();
            if (request("room_type_id")) {
                $query->whereIn('id', explode(",", request("room_type_id")));
            }
            $searchedRoomTypes = $query->get();

            // 2. Ambil semua ID kamar yang tersedia pada rentang tanggal tersebut, lalu kelompokkan berdasarkan tipe kamar
            $availableRooms = Room::availableRooms(request('from_date'), request('to_date'))
                ->get()
                ->groupBy('room_type_id');

            // 3. Gabungkan data dan hitung stok yang tersedia untuk setiap tipe kamar yang dicari
            $searchRooms = $searchedRoomTypes->map(function ($type) use ($availableRooms) {
                $availableCount = isset($availableRooms[$type->id]) ? $availableRooms[$type->id]->count() : 0;
                return [
                    'id' => $type->id,
                    'name' => $type->name,
                    'price' => $type->price,
                    'available_count' => $availableCount,
                ];
            });

            return Inertia::render('Welcome', [
                'searchRooms' => $searchRooms,
                'roomTypes' => $allRoomTypes,
                'searchParams' => $searchParams, // Kirim parameter pencarian untuk form modal
            ]);
        }

        // Jika tidak ada pencarian, tampilkan halaman biasa
        return Inertia::render("Welcome", [
            "roomTypes" => $allRoomTypes,
            "searchRooms" => null, // Pastikan searchRooms null jika tidak ada pencarian
            'searchParams' => $searchParams,
        ]);
    }

    /**
     * Menyimpan reservasi baru ke dalam database.
     */
   public function store(StoreBookingRequest $request)
{
    // Hapus baris dd() dari sini

    // 1. Cari SATU kamar yang tersedia dari tipe yang diminta pada tanggal tersebut
    $availableRoom = Room::availableRooms($request->from_date, $request->to_date)
        ->where('room_type_id', $request->room_type_id)
        ->first();

    // 2. Jika tidak ada kamar tersedia, kembalikan error validasi
    if (!$availableRoom) {
        throw ValidationException::withMessages([
            'room_type_id' => 'Maaf, kamar untuk tipe ini sudah penuh dipesan pada tanggal tersebut.',
        ]);
    }

    $roomType = RoomType::find($request->room_type_id);

    // --- PERUBAHAN DI SINI ---
    $fromDate = Carbon::parse($request->from_date);
    $toDate = Carbon::parse($request->to_date);
    $duration = $toDate->diffInDays($fromDate) + 1;
    // --- AKHIR PERUBAHAN ---

    $totalPrice = $roomType->price * $duration;

    DB::beginTransaction();
    try {
        $reservation = new Reservation();
        $reservation->user_id = Auth::id();
        $reservation->guest_name = $request->guest_name;
        $reservation->total_person = $request->total_person;
        $reservation->total_price = $totalPrice;
        $reservation->from_date = $request->from_date;
        $reservation->to_date = $request->to_date;
        $reservation->save();

        // 3. Lampirkan satu ID kamar yang tersedia ke reservasi
        $reservation->rooms()->attach($availableRoom->id);

        Cache::flush();
        DB::commit();

        SendBookingMailJob::dispatch($reservation, Auth::user()->email);
        return redirect()->back()->with('message', 'Reservasi berhasil dibuat!');

    } catch (Exception $e) {
        DB::rollBack();
        return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat memproses pesanan.']);
    }
}

    /**
     * Menampilkan daftar reservasi milik pengguna.
     */
    public function reservations()
    {
        $reservations = Cache::remember("user.reservations." . Auth::id(), now()->addMinutes(30), function () {
            return Reservation::where("user_id", Auth::id())->latest()->get();
        });

        return Inertia::render("User/Reservations", [
            "reservations" => $reservations
        ]);
    }
}
