<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
class ReviewFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => null,
            'type' => $this->faker->randomElement(['annuel','professionnel']),
            'scheduled_at' => now()->addMonths(rand(1, 12)),
            'status' => 'pending',
        ];
    }
}
