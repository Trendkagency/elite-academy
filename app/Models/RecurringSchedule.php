<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecurringSchedule extends Model
{
    use SoftDeletes;

    protected $table = 'recurring_schedules';

    protected $fillable = [
        'teacher_profile_id',
        'course_id',
        'student_user_id',
        'title',
        'recurrence_type',
        'days_of_week',
        'monthly_pattern',
        'start_time',
        'end_time',
        'duration_minutes',
        'timezone',
        'start_date',
        'end_date',
        'status',
        'meeting_link',
        'meeting_platform',
        'notes',
        'created_by_user_id',
    ];

    protected $casts = [
        'days_of_week' => 'array',
        'monthly_pattern' => 'array',
        'duration_minutes' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function teacherProfile(): BelongsTo
    {
        return $this->belongsTo(TeacherProfile::class, 'teacher_profile_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function studentUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(LiveSession::class, 'recurring_schedule_id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(SessionAuditLog::class, 'recurring_schedule_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeForTeacher(Builder $query, int $teacherProfileId): Builder
    {
        return $query->where('teacher_profile_id', $teacherProfileId);
    }
}
