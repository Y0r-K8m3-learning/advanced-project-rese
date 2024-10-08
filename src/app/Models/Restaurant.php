<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = ['genre_id', 'area_id', 'user_id', 'name', 'description', 'image_url'];



    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }

    public function favorite()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function review()
    {
        return $this->belongsToMany(User::class, 'reviews')->withTimestamps();
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }
    public static function getRestaurant() {}
}
