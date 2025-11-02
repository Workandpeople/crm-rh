<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
class NotificationFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => null,
            'type' => $this->faker->randomElement(['alert','info','warning']),
            'content' => $this->faker->sentence(),
            'read_at' => null,
        ];
    }
}
