<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class EmployeeProfileFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'birth_date' => $this->faker->date('Y-m-d', '-25 years'),
            'address' => $this->faker->address(),
            'city' => $this->faker->city(),
            'postal_code' => $this->faker->postcode(),
            'social_security_number' => $this->faker->numerify('###########'),
            'contract_type' => 'CDI',
            'hire_date' => $this->faker->dateTimeBetween('-5 years'),
            'position' => $this->faker->jobTitle(),
            'completion' => rand(70, 100),
        ];
    }
}
