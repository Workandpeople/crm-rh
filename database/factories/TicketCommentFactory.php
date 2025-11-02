<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
class TicketCommentFactory extends Factory
{
    public function definition()
    {
        return [
            'ticket_id' => null,
            'user_id' => null,
            'content' => $this->faker->sentence(),
        ];
    }
}
