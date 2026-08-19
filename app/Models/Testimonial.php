<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = [
        'name',
        'avatar',
        'content',
        'course_name',
        'rating',
        'reviewer_type',
        'is_verified',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_verified' => 'boolean',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
    ];
}