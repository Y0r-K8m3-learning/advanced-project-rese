<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Models\Attendance;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BreakTime>
 */
class BreakTimeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startTime = Carbon::createFromTime(rand(9, 16), rand(0, 59), rand(0, 59));
        $endTime = (clone $startTime)->addMinutes(rand(30, 480));

        return [
            'attendance_id'  => Attendance::inRandomOrder()->first()->id,
            'start_time' => $startTime->format('H:i:s'),
            'end_time' => $endTime->format('H:i:s'),
        ];
    }
}
