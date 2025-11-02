<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Company;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Team>
 */
class TeamFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_id'     => null,
            'leader_user_id' => null, // défini plus tard dans le seeder
            'name'           => ucfirst($this->faker->word()) . ' Team',
            'description'    => $this->faker->sentence(),
        ];
    }
}
