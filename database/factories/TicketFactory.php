<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\{Company, User};

class TicketFactory extends Factory
{
    public function definition()
    {
        return [
            'company_id' => Company::factory(),
            'created_by' => User::factory(),
            'assigned_to' => null,
            'type' => $this->faker->randomElement(['rh','incident','materiel','autre']),
            'title' => ucfirst($this->faker->words(3, true)),
            'description' => $this->faker->paragraph(),
            'priority' => $this->faker->randomElement(['low','medium','high']),
            'status' => 'open',
            'due_date' => now()->addDays(rand(3, 15)),
        ];
    }
}
