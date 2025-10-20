<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DossierRH extends Model
{
    use HasFactory;

    protected $table = 'dossier_rh'; // si ta table s'appelle dossier_rh (singulier/pluriel important)

    protected $fillable = [
        'user_id',
        'pourcentage_completude',
        'date_creation',
    ];

    // Relation avec User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
