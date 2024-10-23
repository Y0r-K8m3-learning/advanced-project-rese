<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MyPageController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $now = Carbon::now();

        $reservations = Reservation::where('user_id', $user->id)->where('reservation_date', '>', $now->format('Y-m-d'))
            ->orWhere(function ($query) use ($now) {
                $query->where(
                    'reservation_date',
                    '=',
                    $now->format('Y-m-d')
                )
                    ->where(
                        'reservation_time',
                        '>',
                        $now->format('H:i:s')
                    );
            })
            ->with('restaurant')
            ->get();

        $favorites = Favorite::where('user_id', $user->id)->with('restaurant')->get();

        foreach ($reservations as $reservation) {
            $reservation->formatted_time = Carbon::createFromFormat('H:i:s', $reservation->reservation_time)->format('H:i');
        }

        return view('mypage', compact('reservations', 'favorites'));
    }

    public function destroy($id)
    {
        $user = Auth::user();

        Reservation::where('user_id', $user->id)
            ->where('id', $id)
            ->delete();

        return response()->json(['status' => 'deleted']);
    }
}
