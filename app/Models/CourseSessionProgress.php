<?php

namespace App\Models;

use App\Enums\SessionProgressStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseSessionProgress extends Model
{
    protected $table = 'course_session_progress';

    protected $fillable = [
        'course_enrollment_id',
        'course_session_id',
        'status',
        'unlocked_at',
        'completed_at',
    ];

    protected $casts = [
        'status' => SessionProgressStatus::class,
        'unlocked_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(CourseEnrollment::class, 'course_enrollment_id');
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(CourseSession::class, 'course_session_id');
    }
}
