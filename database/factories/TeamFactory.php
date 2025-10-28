<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Company;

class TeamFactory extends Factory
{
    public function definition()
    {
        return [
            'company_id' => Company::factory(),
            'name' => $this->faker->word() . ' Team',
            'description' => $this->faker->sentence(),
        ];
    }
}
