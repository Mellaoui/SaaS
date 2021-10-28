<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduleFactory extends Factory
{
    public function definition()
    {
        return [
            'title' => $this->faker->text(10),
            'day' => $this->faker->dayOfWeek(),
            'start_time' => $this->faker->time(),
            'close_time' => $this->faker->time(),

            // task_id
        ];
    }
}
