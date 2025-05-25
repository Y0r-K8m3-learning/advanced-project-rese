<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'restaurant_id  ',
        'reservation_date',
        'reservation_time ',
        'number_of_people ',
        'is_verified',
        'verified_datetime',
    ];

    public function restaurant()
    {
        return $this->belongsTo('App\Models\Restaurant', "restaurant_id");
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function isPastReservationExists($restaurant_id, $user_id)
    {
        $currentDateTime = now();

        return Reservation::where('restaurant_id', $restaurant_id)
            ->where('user_id', $user_id)
            ->where(function ($query) use ($currentDateTime) {
                $query->where('reservation_date', '<', $currentDateTime->toDateString())
                    ->orWhere(function ($q) use ($currentDateTime) {
                        $q->where('reservation_date', $currentDateTime->toDateString());
                    });
            })
            ->exists();
    }
}
