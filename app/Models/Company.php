<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Company extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['name', 'domain', 'logo_path', 'policies_json'];

    protected $casts = ['policies_json' => 'array'];

    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
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
