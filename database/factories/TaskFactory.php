<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    public function definition()
    {
        return [
            'title' => $this->faker->text(10),
            'description' => $this->faker->text(30),
            'start_date' => Carbon::now()->subDays(2)->format('Y-M-d H:i'),
            'due_date' => Carbon::now()->addDays(10)->format('Y-M-d H:i'),
            'progress' => $this->faker->numberBetween(1, 100),
            'priority' => $this->faker->numberBetween(1, 10),
            'media' => $this->faker->text(10),

            // branch_id
        ];
    }
}
