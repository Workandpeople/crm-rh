<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
class UserFactory extends Factory
{
    public function definition()
    {
        return [
            // ⚠️ Ces colonnes seront définies dans le seeder, on ne veut pas qu’elles génèrent d’autres entités.
            'team_id' => null,
            'company_id' => null,
            'role_id' => null,

            'first_name' => $this->faker->firstName(),
            'last_name'  => $this->faker->lastName(),
            'email'      => $this->faker->unique()->safeEmail(),
            'password'   => Hash::make('password'),
            'phone'      => $this->faker->phoneNumber(),
            'status'     => 'active',
            'onboarding_completed_at' => now(),
        ];
    }
}
