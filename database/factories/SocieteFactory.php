<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SocieteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nom' => fake()->company(),
            'domaine_email' => fake()->unique()->domainName(),
            'logo' => null,
            'politique_conges' => json_encode([
                'cp' => 25,
                'rtt' => 10,
                'sans_solde' => true,
            ]),
        ];
    }
}
