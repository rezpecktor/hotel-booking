<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReservationRequest;
use App\Models\Reservation;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class ReservationController extends Controller
{
    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->authorizeResource(Reservation::class, 'reservation');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reservations = Reservation::with('user', 'rooms:room_number')
            ->search(request(['from_date', 'to_date']))
            ->latest()
            ->paginate(5)
            ->withQueryString()
            ->through(fn($reservation) => [
                'id' => $reservation->id,
                'guest_name' => $reservation->guest_name,
                'room_id' => $reservation->rooms->pluck('room_number'),
                'total_person' => $reservation->total_person,
                'total_price' => $reservation->total_price,
                'from_date' => $reservation->from_date,
                'to_date' => $reservation->to_date,
                'checkin_time' => $reservation->checkin_time,
                'checkout_time' => $reservation->checkout_time,
                'status' => $reservation->status,
            ]);

        return Inertia::render('Reservation/Index', [
            'reservations' => $reservations
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Reservation/Create', [
            'rooms' => Room::all(['id', 'room_number', "price"])
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ReservationRequest $request)
    {
        $availableRooms = Room::availableRooms($request->from_date, $request->to_date)->get();

        foreach($request->room_id as $room) {
            if(!$availableRooms->find($room)) {
                throw ValidationException::withMessages(['room_id' => "Some of the rooms are reserved on given date."]);
            }
        }

        DB::beginTransaction();

        try {
            $reservation = new Reservation();
            $reservation->user_id = Auth::user()->id;
            $reservation->guest_name = $request->guest_name;
            $reservation->total_person = $request->total_person;
            $reservation->total_price = $request->total_price;
            $reservation->from_date = date('Y-m-d', strtotime($request->from_date));
            $reservation->to_date = date('Y-m-d', strtotime($request->to_date));
            $reservation->checkin_time = $request->checkin_time ? (new Carbon($request->checkin_time))->toDateTimeString() : null;
            $reservation->checkout_time =$request->checkout_time? (new Carbon($request->checkout_time))->toDateTimeString(): null;
            $reservation->save();

            $reservation->rooms()->attach($request->room_id);

            DB::commit();
            Cache::flush();
            return redirect()->route('admin.reservations.index');

        } catch (\Exception $e) {
            DB::rollback();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Reservation $reservation)
    {
        $reservation->load('rooms');
        return Inertia::render('Reservation/Show', [
            'id' => $reservation->id,
            'guest_name' => $reservation->guest_name,
            'total_person' => $reservation->total_person,
            'total_price' => $reservation->total_price,
            'from_date' => $reservation->from_date,
            'to_date' => $reservation->to_date,
            'room_ids' => $reservation->rooms->pluck('id'),
            'checkin_time' => $reservation->checkin_time ?? Carbon::now(),
            'checkout_time' => $reservation->checkout_time ?? Carbon::now(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reservation $reservation)
    {
        return Inertia::render('Reservation/Edit', [
            'id' => $reservation->id,
            'guest_name' => $reservation->guest_name,
            'total_person' => $reservation->total_person,
            'total_price' => $reservation->total_price,
            'from_date' => $reservation->from_date,
            'to_date' => $reservation->to_date,
            'room_id' => $reservation->rooms()->pluck('room_id')->toArray(),
            'checkin_time' => $reservation->checkin_time ?? "",
            'checkout_time' => $reservation->checkout_time ?? "",
            'rooms' => Room::all(['id', 'room_number', "price"]),
            'reservation_details' => $reservation->reservationDetails,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ReservationRequest $request, Reservation $reservation)
    {
        $reservation->guest_name=$request->guest_name;
        $reservation->total_person=$request->total_person;
        $reservation->total_price=$request->total_price;
        $reservation->from_date = Carbon::parse($request->from_date);
        $reservation->to_date = Carbon::parse($request->to_date);

        if($request->has('checkin_time')){
            $reservation->checkin_time = Carbon::parse($request->checkin_time);
        }
        if($request->has('checkout_time')){
            $reservation->checkout_time = Carbon::parse($request->checkout_time);
        }

        DB::table('reservation_room')->where('reservation_id',$reservation->id)->delete();

        foreach($request->room_id as $room){
            DB::table('reservation_room')->insert([
                'room_id'=>$room,
                'reservation_id'=>$reservation->id,
                'updated_at'=>now()
            ]);
        }

        $reservation->save();
        Cache::flush();

        return redirect()->route('admin.reservations.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reservation $reservation)
    {
        DB::table('reservation_room')->where('reservation_id',$reservation->id)->delete();
        $reservation->delete();
        Cache::flush();

        return redirect()->route('admin.reservations.index')->isSuccessful();
    }

    public function archives() {
        return Inertia::render('Reservation/DeletedReservation',[
            'reservations' => Reservation::onlyTrashed()->paginate(5)
        ]);
    }

    public function restore($id) {
        $reservation = Reservation::onlyTrashed()->findOrFail($id);
        $reservation->restore();
        Cache::flush();
        return redirect()->route('admin.reservations.index');
    }

    // ====================================================================
    // == METHOD LAMA 'complete' DIGANTI MENJADI 'confirm' ==
    // ====================================================================
    public function confirm(Reservation $booking)
    {
        $this->authorize('update', $booking);

        $booking->status = 'confirmed'; // Status diubah menjadi 'confirmed'
        $booking->save();

        Cache::flush();

        return redirect()->route('admin.reservations.index')->with('message', 'Booking has been confirmed successfully.');
    }

    // ====================================================================
    // == METHOD BARU 'cancel' UNTUK MEMBATALKAN PESANAN ==
    // ====================================================================
    public function cancel(Reservation $booking)
    {
        $this->authorize('update', $booking);

        $booking->status = 'canceled'; // Status diubah menjadi 'canceled'
        $booking->save();

        Cache::flush();

        return redirect()->route('admin.reservations.index')->with('message', 'Booking has been canceled.');
    }
}
