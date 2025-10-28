<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\{User, Company};

class ExpenseFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'company_id' => Company::factory(),
            'type' => $this->faker->randomElement(['peage','repas','hebergement','km']),
            'amount' => $this->faker->randomFloat(2, 5, 150),
            'description' => $this->faker->sentence(),
            'status' => $this->faker->randomElement(['pending','approved','paid']),
        ];
    }
}
