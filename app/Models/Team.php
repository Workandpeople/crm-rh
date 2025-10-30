<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Team extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'company_id',
        'leader_user_id',
        'name',
        'description',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_user_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
