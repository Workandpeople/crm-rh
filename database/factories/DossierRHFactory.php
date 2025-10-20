<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class DossierRHFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::where('role', 'user')->inRandomOrder()->first()?->id ?? 1,
            'pourcentage_completude' => fake()->numberBetween(60, 100),
            'date_creation' => now(),
        ];
    }
}
