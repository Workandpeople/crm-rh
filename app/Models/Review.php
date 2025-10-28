<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Review extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id','type','scheduled_at','status','pdf_path','reminder_sent_at'
    ];

    protected $casts = ['scheduled_at' => 'date','reminder_sent_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
