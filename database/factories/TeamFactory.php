<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\{Company, User};

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Team>
 */
class TeamFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_id'     => Company::factory(),
            'leader_user_id' => null, // défini plus tard dans le seeder
            'name'           => ucfirst($this->faker->word()) . ' Team',
            'description'    => $this->faker->sentence(),
        ];
    }
}
