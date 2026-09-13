<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = [
        'user_id',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getLocalizedContent(): string
    {
        return __($this->content ?? '');
    }

    public function getLocalizedCourseName(): string
    {
        return __($this->course_name ?? '');
    }

    public function getReviewerRoleLabel(): string
    {
        $isAr = app()->getLocale() === 'ar';
        return match($this->reviewer_type) {
            'parent'  => $isAr ? 'ولي أمر' : 'Parent',
            'teacher' => $isAr ? 'معلم / مدرب' : 'Teacher / Instructor',
            default   => $isAr ? 'طالب' : 'Student',
        };
    }

    public function getBadgeClass(): string
    {
        return match($this->reviewer_type) {
            'parent'  => 'bg-purple-50 text-purple-700 border-purple-200/80 dark:bg-purple-950/60 dark:text-purple-300 dark:border-purple-800/80',
            'teacher' => 'bg-amber-50 text-amber-700 border-amber-200/80 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800/80',
            default   => 'bg-teal-50 text-teal-700 border-teal-200/80 dark:bg-teal-950/60 dark:text-teal-300 dark:border-teal-800/80',
        };
    }
}