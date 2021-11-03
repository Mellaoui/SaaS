<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    public function definition()
    {
        return [
            'title' => $this->faker->text(10),
            'description' => $this->faker->text(30),
            'start_date' => $this->faker->numberBetween(1, 100),
            'due_date' => $this->faker->numberBetween(1, 100),
            'progress' => $this->faker->numberBetween(1, 100),
            'priority' => $this->faker->numberBetween(1, 10),
            'media' => $this->faker->text(10),

            // branch_id
        ];
    }
}
