<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class LeaveFactory extends Factory
{
    public function definition()
    {
        $start = $this->faker->dateTimeBetween('-1 month', '+1 month');
        return [
            'user_id' => User::factory(),
            'type' => $this->faker->randomElement(['CP','SansSolde','Exceptionnel','Maladie']),
            'start_date' => $start,
            'end_date' => (clone $start)->modify('+'.rand(1,5).' days'),
            'status' => $this->faker->randomElement(['pending','approved']),
        ];
    }
}
