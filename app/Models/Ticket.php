<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Ticket extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'company_id','created_by','assigned_to','type',
        'title','description','priority','status','due_date','related_user_id'
    ];

    protected $casts = ['due_date' => 'date'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function relatedUser()
    {
        return $this->belongsTo(User::class, 'related_user_id');
    }

    public function comments()
    {
        return $this->hasMany(TicketComment::class);
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class);
    }
}
