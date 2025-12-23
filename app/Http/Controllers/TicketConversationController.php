<?php

namespace App\Http\Controllers;

use App\Events\TicketMessageSent;
use App\Models\Ticket;
use App\Models\TicketConversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketConversationController extends Controller
{
    public function show(Ticket $ticket)
    {
        $user = Auth::user();

        if (! $this->canAccessTicket($ticket, $user)) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        $conversation = TicketConversation::firstOrCreate(
            ['ticket_id' => $ticket->id],
            ['created_by' => $user?->id]
        );

        $messages = $conversation->messages()
            ->with('user:id,first_name,last_name,email')
            ->orderBy('created_at')
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'body' => $message->body,
                    'created_at' => $message->created_at,
                    'user' => $message->user ? [
                        'id' => $message->user->id,
                        'full_name' => trim(($message->user->first_name ?? '') . ' ' . ($message->user->last_name ?? '')),
                        'email' => $message->user->email,
                    ] : null,
                ];
            });

        return response()->json([
            'conversation_id' => $conversation->id,
            'ticket_id' => $ticket->id,
            'messages' => $messages,
        ]);
    }

    public function storeMessage(Request $request, Ticket $ticket)
    {
        $user = Auth::user();

        if (! $this->canAccessTicket($ticket, $user)) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $conversation = TicketConversation::firstOrCreate(
            ['ticket_id' => $ticket->id],
            ['created_by' => $user?->id]
        );

        $message = $conversation->messages()->create([
            'user_id' => $user->id,
            'body' => $data['body'],
        ]);

        $message->load('user:id,first_name,last_name,email');

        broadcast(new TicketMessageSent($ticket, $message))->toOthers();

        return response()->json([
            'message' => [
                'id' => $message->id,
                'body' => $message->body,
                'created_at' => $message->created_at,
                'user' => $message->user ? [
                    'id' => $message->user->id,
                    'full_name' => trim(($message->user->first_name ?? '') . ' ' . ($message->user->last_name ?? '')),
                    'email' => $message->user->email,
                ] : null,
            ],
        ], 201);
    }

    private function canAccessTicket(Ticket $ticket, $user): bool
    {
        $role = $user?->role?->name;
        $isAdminLike = in_array($role, ['admin', 'chef_equipe', 'superadmin']);
        $isOwner = $role === 'employe' && (
            $ticket->created_by === $user->id || $ticket->assigned_to === $user->id
        );

        return $isAdminLike || $isOwner;
    }
}
