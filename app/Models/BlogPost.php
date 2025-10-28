<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class BlogPost extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'company_id','title','content','image_path','published_at','created_by'
    ];

    protected $casts = ['published_at' => 'datetime'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
