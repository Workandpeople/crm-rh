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
        $rand = Str::random(4);

        return [
            'name' => $name,
            'domain' => '@' . $slug . $rand . '.fr',
            'email' => 'contact@' . $slug . '.fr',
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'logo_path' => null,
            'policies_json' => [
                'max_conges' => 25,
                'auto_validation' => false,
            ],
            'admin_user_id' => null,
        ];
    }
}
