<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
class DocumentFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => null,
            'type' => $this->faker->randomElement(['CNI', 'Contrat', 'Carte Vitale', 'Fiche Fonction']),
            'file_path' => 'storage/docs/' . $this->faker->uuid() . '.pdf',
            'uploaded_at' => now(),
            'expires_at' => now()->addYear(),
            'signed' => $this->faker->boolean(70),
            'status' => 'valid',
            'metadata' => ['source' => 'auto'],
        ];
    }
}
