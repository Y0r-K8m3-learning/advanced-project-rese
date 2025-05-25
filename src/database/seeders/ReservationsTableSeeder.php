<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class ReservationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 例として30件のダミーデータを作成
        for ($i = 1; $i <= 20; $i++) {
            DB::table('reservations')->insert([
                'user_id'           => 1,
                'restaurant_id'     => $i,
                'reservation_date'  => Carbon::today()->addDays(rand(-1, -30))->toDateString(),
                'reservation_time'  => fake()->time(),
                'number_of_people'  => rand(1, 6),
                'is_verified'       => 1,
                'verified_datetime' => Carbon::now()->subMinutes(rand(0, 1440)),
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }
    }
}
