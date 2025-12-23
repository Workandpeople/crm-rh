<?php

namespace App\Events;

use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketMessageSent implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public Ticket $ticket;
    public TicketMessage $message;

    public function __construct(Ticket $ticket, TicketMessage $message)
    {
        $this->ticket = $ticket;
        $this->message = $message;
    }

    public function broadcastOn(): Channel
    {
        return new PrivateChannel('ticket.' . $this->ticket->id);
    }

    public function broadcastAs(): string
    {
        return 'ticket.message';
    }

    public function broadcastWith(): array
    {
        $user = $this->message->user;

        return [
            'message' => [
                'id' => $this->message->id,
                'body' => $this->message->body,
                'created_at' => $this->message->created_at,
                'user' => $user ? [
                    'id' => $user->id,
                    'full_name' => trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')),
                    'email' => $user->email,
                ] : null,
            ],
            'ticket_id' => $this->ticket->id,
        ];
    }
}
