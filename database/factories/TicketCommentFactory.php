<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\{Ticket, User};

class TicketCommentFactory extends Factory
{
    public function definition()
    {
        return [
            'ticket_id' => Ticket::factory(),
            'user_id' => User::factory(),
            'content' => $this->faker->sentence(),
        ];
    }
}
