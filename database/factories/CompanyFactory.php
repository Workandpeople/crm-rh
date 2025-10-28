<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CompanyFactory extends Factory
{
    public function definition()
    {
        $name = $this->faker->unique()->company();
        $slug = Str::slug($name);
        $rand = Str::random(5); // ajoute un suffixe pour garantir l'unicité

        return [
            'name' => $name,
            'domain' => '@' . $slug . $rand . '.fr',
            'logo_path' => null,
            'policies_json' => ['max_conges' => 25, 'auto_validation' => false],
        ];
    }
}
