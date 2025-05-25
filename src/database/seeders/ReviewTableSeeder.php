<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReviewTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        for ($i = 1; $i <= 5; $i++) {
            DB::table('reviews')->insert([
                'user_id' => 1,
                'restaurant_id' => $i,
                'rating' => rand(1, 5),
                'comment' => "{$i}:レビューダミーデータ",
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
