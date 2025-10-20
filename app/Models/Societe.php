<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Societe extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'domaine_email',
        'logo',
        'politique_conges',
    ];

    protected $casts = [
        'politique_conges' => 'array',
    ];
}
