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
}
