<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MyPageConttoller extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // ユーザーの予約情報を取得
        $reservations = Reservation::where('user_id', $user->id)->with('restaurant')->get();

        $favorites = Favorite::where('user_id', $user->id)->with('restaurant')->get();

        foreach ($reservations as $reservation) {
            $reservation->formatted_time = Carbon::createFromFormat('H:i:s', $reservation->reservation_time)->format('H:i');
        }

        return view('mypage', compact('reservations', 'favorites'));
    }

    public function destroy($id)
    {
        $user = Auth::user();

        // 予約を削除
        Reservation::where('user_id', $user->id)
            ->where('id', $id)
            ->delete();

        return response()->json(['status' => 'deleted']);
    }
}
