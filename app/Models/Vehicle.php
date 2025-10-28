<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Vehicle extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['registration', 'brand', 'model', 'insurance_expiry', 'assigned_to'];

    protected $casts = ['insurance_expiry' => 'date'];

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
