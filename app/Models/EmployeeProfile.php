<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class EmployeeProfile extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id', 'birth_date', 'address', 'city', 'postal_code',
        'social_security_number', 'contract_type', 'hire_date',
        'position', 'vehicle_id', 'completion', 'notes'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'hire_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
