<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class OvertimeFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'hours' => $this->faker->randomFloat(2, 1, 10),
            'reason' => $this->faker->sentence(),
            'status' => 'approved',
        ];
    }
}
