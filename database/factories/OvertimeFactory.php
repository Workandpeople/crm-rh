<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
class OvertimeFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => null,
            'hours' => $this->faker->randomFloat(2, 1, 10),
            'reason' => $this->faker->sentence(),
            'status' => 'approved',
        ];
    }
}
