<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class ReviewFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'type' => $this->faker->randomElement(['annuel','professionnel']),
            'scheduled_at' => now()->addMonths(rand(1, 12)),
            'status' => 'pending',
        ];
    }
}
