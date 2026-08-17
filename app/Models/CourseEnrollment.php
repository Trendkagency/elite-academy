<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseEnrollment extends Model
{
    protected $fillable = [
        'student_user_id',
        'course_id',
        'cohort',
        'status',
        'progress_percent',
        'enrolled_at',
        'completed_at',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime',
        'progress_percent' => 'integer',
    ];

    public function studentUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function sessionProgress(): HasMany
    {
        return $this->hasMany(CourseSessionProgress::class, 'course_enrollment_id');
    }

    public function progress(): HasMany
    {
        return $this->hasMany(CourseSessionProgress::class, 'course_enrollment_id');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class, 'course_enrollment_id');
    }
}
