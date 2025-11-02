<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
class EpiFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->randomElement(['Casque', 'Gants', 'Chaussures', 'Gilet']),
            'user_id' => null,
            'issued_at' => now()->subMonths(rand(1, 12)),
            'expires_at' => now()->addMonths(rand(6, 24)),
        ];
    }
}
