<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class TicketConversation extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'ticket_id',
        'created_by',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function messages()
    {
        return $this->hasMany(TicketMessage::class, 'conversation_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
