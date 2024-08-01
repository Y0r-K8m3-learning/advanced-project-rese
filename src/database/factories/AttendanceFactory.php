<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = Carbon::today()->addDays(rand(-10, 10));
        // 9:00から17:00の間のランダムな時刻を生成
        $startTime = Carbon::createFromTime(rand(9, 16), rand(0, 59), rand(0, 59));
        $endTime = (clone $startTime)->addMinutes(rand(30, 480));;

        return [
            'user_id'  => User::inRandomOrder()->first()->id,
            'work_date' => $startDate->format('Y-m-d'),
            'start_time' => $startTime->format('H:i:s'),
            'end_time' => $endTime->format('H:i:s'),
        ];
    }
}
