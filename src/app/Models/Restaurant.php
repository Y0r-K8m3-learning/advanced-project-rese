<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = ['genre_id', 'area_id', 'owner_id', 'name', 'description', 'image_url'];



    public function user()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }

    public function favorite()
    {
        return $this->hasMany(Favorite::class)->withTimestamps();
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    //平均値丸め
    public function getReviewsAvgRatingAttribute($value)
    {
        return is_null($value) ? null : number_format($value,2, '.', '');
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }
    public static function getRestaurant() {}
}
