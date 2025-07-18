<?php

use App\Http\Controllers\Admin\ReportingController;
use App\Http\Controllers\Admin\ReservationController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\RoomTypeController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get("/", [BookingController::class, "index"])->name("index");

Route::middleware(["auth"])->as("user.")->group(function () {
    Route::get("/my-reservations", [BookingController::class, "reservations"])->name("reservations");
    Route::inertia("/profile", "User/Profile")->name("profile");
    Route::patch("/profile", [ProfileController::class, "update"])->name("profile.update");
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post("/profile/image", [ProfileController::class, 'updateProfileImage'])->name("profile.updateProfileImage");
    Route::post("/reserve", [BookingController::class, "store"])->name("reserve");
});

Route::middleware('auth')->resource('/', BookingController::class)->only(['create', 'store']);

Route::middleware(['auth', 'admin'])->prefix("admin")->as("admin.")->group(function () {
    Route::get("/", ReportingController::class)->name("index");

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post("/profile/image", [ProfileController::class, 'updateProfileImage'])->name("profile.updateProfileImage");

    // Reservation
    Route::get("/reservations/archives", [ReservationController::class, 'archives'])->name("reservations.archives");
    Route::patch("/reservations/{reservations}/restore", [ReservationController::class, "restore"])->name("reservations.restore");
    Route::resource('reservations', ReservationController::class);

    // ====================================================================
    // == ROUTE BARU UNTUK CHECK-IN ADA DI BAWAH INI ==
    // ====================================================================
    Route::put('/reservations/{booking}/confirm', [ReservationController::class, 'confirm'])->name('reservations.confirm');
    Route::put('/reservations/{booking}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');
    Route::put('/reservations/{booking}/checkin', [ReservationController::class, 'checkin'])->name('reservations.checkin');
    Route::put('/reservations/{booking}/checkout', [ReservationController::class, 'checkout'])->name('reservations.checkout');


    // Room
    Route::get("/rooms/archives", [RoomController::class, "archives"])->name("rooms.archives");
    Route::patch("/rooms/{rooms}/restore", [RoomController::class, "restore"])->name("rooms.restore");
    Route::resource('rooms', RoomController::class);

    // Room Type
    Route::get('/room-types/archives', [RoomTypeController::class, 'archives'])->name('room-types.archives');
    Route::patch("/room-types/{room_types}/restore", [RoomTypeController::class, "restore"])->name("room-types.restore");
    Route::resource('room-types', RoomTypeController::class)->except(['create', 'edit', 'show']);
});

require __DIR__ . '/auth.php';
