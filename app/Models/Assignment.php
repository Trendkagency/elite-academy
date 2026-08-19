<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Assignment extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'course_session_id',
        'live_session_id',
        'teacher_profile_id',
        'course_id',
        'title',
        'description',
        'duration_minutes',
        'start_at',
        'due_at',
        'sort_order',
        'status',
        'max_attempts',
        'passing_score',
        'passing_grade',
        'total_questions',
        'is_mandatory',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'due_at' => 'datetime',
        'sort_order' => 'integer',
        'duration_minutes' => 'integer',
        'max_attempts' => 'integer',
        'total_questions' => 'integer',
        'passing_score' => 'float',
        'passing_grade' => 'float',
        'is_mandatory' => 'boolean',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(CourseSession::class, 'course_session_id');
    }

    public function liveSession(): BelongsTo
    {
        return $this->belongsTo(LiveSession::class, 'live_session_id');
    }

    public function teacherProfile(): BelongsTo
    {
        return $this->belongsTo(TeacherProfile::class, 'teacher_profile_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(AssignmentQuestion::class, 'assignment_id')->orderBy('sort_order', 'asc');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class, 'assignment_id');
    }

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (Assignment $assignment) {
            // Rule: Deadline for submitting assignment is 24 hours (One day) before the lesson
            if (! $assignment->due_at && $assignment->live_session_id) {
                $liveSession = $assignment->liveSession ?: LiveSession::find($assignment->live_session_id);
                if ($liveSession && $liveSession->effective_start_at) {
                    $assignment->due_at = $liveSession->effective_start_at->copy()->subDay();
                }
            }
        });

        static::created(function (Assignment $assignment) {
            if ($assignment->status === 'published' || ! $assignment->status) {
                app(\App\Services\Notification\FcmNotificationService::class)->notifyAssignmentAdded($assignment);
            }
        });

        static::updated(function (Assignment $assignment) {
            if ($assignment->wasChanged('status') && $assignment->status === 'published') {
                app(\App\Services\Notification\FcmNotificationService::class)->notifyAssignmentAdded($assignment);
            }
        });
    }

    public function getEffectiveDueAtAttribute(): ?\Carbon\Carbon
    {
        if ($this->due_at) {
            return $this->due_at;
        }

        $sessionStart = $this->liveSession?->effective_start_at;
        if ($sessionStart) {
            return $sessionStart->copy()->subDay(); // 24 hours (One day) before lesson
        }

        return null;
    }

    public function isExpired(): bool
    {
        $deadline = $this->effective_due_at;
        return $deadline && now()->greaterThan($deadline);
    }
}
