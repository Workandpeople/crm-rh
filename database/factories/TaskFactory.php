<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\{Company, User};

class TaskFactory extends Factory
{
    public function definition()
    {
        return [
            'company_id' => Company::factory(),
            'created_by' => User::factory(),
            'assigned_to' => null,
            'title' => ucfirst($this->faker->words(3, true)),
            'description' => $this->faker->paragraph(),
            'priority' => $this->faker->randomElement(['low','medium','high']),
            'status' => $this->faker->randomElement(['todo','doing','review']),
            'due_date' => now()->addDays(rand(5, 20)),
            'color_code' => $this->faker->hexColor(),
        ];
    }
}
