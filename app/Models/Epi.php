<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Epi extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['name', 'user_id', 'issued_at', 'expires_at', 'document_id'];

    protected $casts = ['issued_at' => 'date', 'expires_at' => 'date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}
