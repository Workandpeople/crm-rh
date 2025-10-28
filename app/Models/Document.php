<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Document extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id', 'type', 'file_path', 'uploaded_at',
        'expires_at', 'signed', 'signed_at', 'status', 'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'uploaded_at' => 'datetime',
        'expires_at' => 'datetime',
        'signed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
