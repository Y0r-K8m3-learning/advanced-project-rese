<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    
    protected $fillable = ['user_id', 'restaurant_id', 'rating', 'comment'];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, "restaurant_id");
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function image()
    {
        //1対1
        return $this->hasOne(ReviewImage::class,'review_id');
    }

    public static function isExists($restaurant_id, $user_id)
    {
        return Review::where('restaurant_id', $restaurant_id)
            ->where('user_id', $user_id)
            ->exists();
    }
}
