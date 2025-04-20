<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    
    protected $fillable = ['user_id', 'restaurant_id', 'rating', 'comment'];

    public function restaurants()
    {
        return $this->belongsTo('App\Models\Restaurant', "restaurant_id");
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
