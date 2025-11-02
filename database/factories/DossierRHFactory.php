<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
class DossierRHFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => null,
            'pourcentage_completude' => fake()->numberBetween(60, 100),
            'date_creation' => now(),
        ];
    }
}
