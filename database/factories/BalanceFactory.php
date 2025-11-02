<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
class BalanceFactory extends Factory
{
    public function definition()
    {
        return [
            'company_id' => null,
            'total_expenses' => $this->faker->randomFloat(2, 1000, 10000),
            'total_incomes' => $this->faker->randomFloat(2, 2000, 20000),
        ];
    }
}
