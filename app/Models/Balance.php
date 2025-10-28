<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Balance extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['company_id','total_expenses','total_incomes'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
