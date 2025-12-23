<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Blog extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'company_id',
        'user_id',
        'title',
        'status',
        'main_image',
        'main_image_credit',
        'second_title',
        'second_image',
        'second_type',
        'second_image_credit',
        'second_content',
        'third_content',
        'third_image',
        'third_image_credit',
        'third_type',
        'fourth_image',
        'fourth_image_credit',
        'fourth_type',
        'fourth_content',
        'highlighted',
    ];

    protected $casts = [
        'highlighted' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
