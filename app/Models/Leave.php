<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Leave extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id','type','start_date','end_date',
        'justification_path','status','validated_by','comments'
    ];

    protected $casts = ['start_date' => 'date', 'end_date' => 'date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}
