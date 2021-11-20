<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BranchFactory extends Factory
{
    public function definition()
    {
        return [
            'title' => $this->faker->text(10),
            'status' => $this->faker->text(6),
            'address' => $this->faker->address(),
            'phone' => $this->faker->phoneNumber(),

            // company_id
        ];
    }
}
