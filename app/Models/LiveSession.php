<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiveSession extends Model
{
    protected $table = 'live_sessions';

    protected $fillable = [
        'student_user_id',
        'teacher_profile_id',
        'subject_id',
        'course_id',
        'scheduled_at',
        'duration_minutes',
        'meeting_link',
        'meeting_platform',
        'status',
        'attendance_status',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'duration_minutes' => 'integer',
    ];

    public function studentUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function teacherProfile(): BelongsTo
    {
        return $this->belongsTo(TeacherProfile::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
