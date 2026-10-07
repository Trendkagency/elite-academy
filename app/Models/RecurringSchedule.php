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
        'day_start_times',
        'day_meeting_links',
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
        'day_start_times' => 'array',
        'day_meeting_links' => 'array',
        'monthly_pattern' => 'array',
        'duration_minutes' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::saved(function (RecurringSchedule $schedule) {
            if ($schedule->wasChanged([
                'teacher_profile_id',
                'student_user_id',
                'course_id',
                'meeting_link',
                'meeting_platform',
                'duration_minutes',
                'title',
            ])) {
                $futureSessions = $schedule->liveSessions()
                    ->where('scheduled_at', '>=', now())
                    ->whereNotIn('status', ['completed', 'cancelled', 'cancelled_by_teacher'])
                    ->get();

                foreach ($futureSessions as $session) {
                    $updates = [];

                    if ($schedule->wasChanged('teacher_profile_id')) {
                        $updates['teacher_profile_id'] = $schedule->teacher_profile_id;
                    }
                    if ($schedule->wasChanged('course_id')) {
                        $updates['course_id'] = $schedule->course_id;
                    }
                    if ($schedule->wasChanged('student_user_id')) {
                        $updates['student_user_id'] = $schedule->student_user_id;
                    }
                    if ($schedule->wasChanged('meeting_link') && ! $session->is_override) {
                        $updates['meeting_link'] = $schedule->meeting_link;
                    }
                    if ($schedule->wasChanged('meeting_platform') && ! $session->is_override) {
                        $updates['meeting_platform'] = $schedule->meeting_platform;
                    }
                    if ($schedule->wasChanged('duration_minutes') && ! $session->is_override) {
                        $updates['duration_minutes'] = $schedule->duration_minutes;
                        $sessionStart = $session->scheduled_at ?: $session->start_at;
                        if ($sessionStart) {
                            $updates['end_at'] = $sessionStart->copy()->addMinutes($schedule->duration_minutes);
                        }
                    }
                    if ($schedule->wasChanged('title') && ! $session->is_override) {
                        $updates['title'] = $schedule->title;
                    }

                    if (! empty($updates)) {
                        $session->update($updates);
                    }
                }
            }
        });
    }

    public function teacherProfile(): BelongsTo
    {
        return $this->belongsTo(TeacherProfile::class, 'teacher_profile_id');
    }

    public function teacher(): BelongsTo
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

    public function student(): BelongsTo
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

    public function liveSessions(): HasMany
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
