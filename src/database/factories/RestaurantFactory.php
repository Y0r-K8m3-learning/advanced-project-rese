<?php

namespace Database\Factories;

use App\Models\Restaurant;
use App\Models\Area;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RestaurantFactory extends Factory
{
    protected $model = Restaurant::class;

    public function definition()
    {
        return [
            'name' => $this->faker->company . '店',
            'description' => $this->faker->realText(200),
            'image_url' => $this->faker->imageUrl(640, 480, 'food', true),
            'area_id' => Area::inRandomOrder()->first()->id ?? 1,
            'genre_id' => Genre::inRandomOrder()->first()->id ?? 1,
            'owner_id' => User::where('role_id', '20')->inRandomOrder()->first()->id ?? 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}