<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class EpiFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->randomElement(['Casque', 'Gants', 'Chaussures', 'Gilet']),
            'user_id' => User::factory(),
            'issued_at' => now()->subMonths(rand(1, 12)),
            'expires_at' => now()->addMonths(rand(6, 24)),
        ];
    }
}
