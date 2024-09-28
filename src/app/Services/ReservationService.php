<?php

namespace App\Services;

use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ReservationService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function createReservation(Request $request)
    {
        dd($request);
        // 予約データの作成
        $reservation = new Reservation();
        $reservation->user_id = Auth::id(); // 現在のユーザーID
        $reservation->restaurant_id = $request->input('restaurant_id'); // 隠しフィールドまたは別の方法で渡されたレストランID
        $reservation->reservation_date = $request->input('date');
        $reservation->reservation_time = $request->input('time');
        $reservation->number_of_people = $request->input('number');
        $reservation->save();
    }
}
