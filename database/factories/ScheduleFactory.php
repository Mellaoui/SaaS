<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduleFactory extends Factory
{
    public function definition()
    {
        return [
            'title' => $this->faker->text(10),
            'day' => Carbon::now()->format('Y-M-d H:i'),
            'start_time' => Carbon::parse('09:00')->format('H:i'),
            'close_time' => Carbon::parse('16:00')->format('H:i'),

            // task_id
        ];
    }
}
