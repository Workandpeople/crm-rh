<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Task extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'company_id','created_by','assigned_to','title','description',
        'priority','status','due_date','color_code'
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

    public function tickets()
    {
        return $this->belongsToMany(Ticket::class);
    }
}
