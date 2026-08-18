<?php

namespace App\Models;

use App\Enums\LiveSessionState;
use App\Services\Session\LiveSessionService;
use Carbon\Carbon;
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
        'start_at',
        'end_at',
        'duration_minutes',
        'meeting_link',
        'meeting_platform',
        'status',
        'attendance_status',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'duration_minutes' => 'integer',
    ];

    public function studentUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function assignments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Assignment::class, 'live_session_id');
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

    public function getEffectiveStartAtAttribute(): ?Carbon
    {
        return $this->start_at ?: $this->scheduled_at;
    }

    public function getEffectiveEndAtAttribute(): ?Carbon
    {
        if ($this->end_at) {
            return $this->end_at;
        }

        $start = $this->effective_start_at;
        return $start ? $start->copy()->addMinutes($this->duration_minutes ?: 60) : null;
    }

    public function getJoinableAtAttribute(): ?Carbon
    {
        $start = $this->effective_start_at;
        return $start ? $start->copy()->subMinutes(30) : null;
    }

    public function evaluateState(?User $user = null, ?Carbon $now = null): LiveSessionState
    {
        return app(LiveSessionService::class)->evaluateState($this, $user, $now);
    }

    public function canStudentAccessStream(?User $user = null): array
    {
        $user = $user ?: auth()->user();
        if (! $user) {
            return ['can_access' => false, 'reason' => 'unauthenticated', 'message' => 'Unauthenticated'];
        }

        return app(LiveSessionService::class)->getStreamAccess($this, $user);
    }
}
