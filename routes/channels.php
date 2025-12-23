<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Ticket;

Broadcast::channel('ticket.{ticketId}', function ($user, $ticketId) {
    $ticket = Ticket::find($ticketId);
    if (! $ticket) {
        return false;
    }

    $role = $user?->role?->name;
    $isAdminLike = in_array($role, ['admin', 'chef_equipe', 'superadmin']);
    $isOwner = $role === 'employe' && (
        $ticket->created_by === $user->id || $ticket->assigned_to === $user->id
    );

    return $isAdminLike || $isOwner;
});
