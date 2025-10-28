<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use App\Models\{Team, Company, Role};

class UserFactory extends Factory
{
    public function definition()
    {
        return [
            'team_id' => Team::factory(),
            'company_id' => Company::factory(),
            'role_id' => Role::inRandomOrder()->first()?->id ?? Role::factory(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'phone' => $this->faker->phoneNumber(),
            'status' => 'active',
            'onboarding_completed_at' => now(),
        ];
    }
}
