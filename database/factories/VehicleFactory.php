<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleFactory extends Factory
{
    public function definition()
    {
        return [
            'registration' => strtoupper($this->faker->bothify('??-###-??')),
            'brand' => $this->faker->company(),
            'model' => $this->faker->word(),
            'insurance_expiry' => now()->addMonths(rand(3, 24)),
        ];
    }
}
