<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'name',
        'domain',
        'email',
        'phone',
        'address',
        'logo_path',
        'policies_json',
        'admin_user_id',
    ];

    protected $casts = [
        'policies_json' => 'array',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function balances()
    {
        return $this->hasMany(Balance::class);
    }
}
