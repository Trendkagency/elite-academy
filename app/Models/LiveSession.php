<?php

namespace App\Models;

use App\Enums\LiveSessionState;
use App\Services\Session\LiveSessionService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LiveSession extends Model
{
    use SoftDeletes;

    protected $table = 'live_sessions';

    protected $fillable = [
        'title',
        'student_user_id',
        'teacher_profile_id',
        'subject_id',
        'course_id',
        'course_session_id',
        'scheduled_at',
        'start_at',
        'end_at',
        'duration_minutes',
        'meeting_link',
        'meeting_platform',
        'status',
        'attendance_status',
        'is_free_demo',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'duration_minutes' => 'integer',
        'is_free_demo' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::created(function (LiveSession $session) {
            app(\App\Services\Notification\FcmNotificationService::class)->notifyTeacherSessionAssigned($session);
        });

        static::updated(function (LiveSession $session) {
            $service = app(\App\Services\Notification\FcmNotificationService::class);

            if ($session->wasChanged('status')) {
                match ($session->status) {
                    'in_progress', 'link_visible' => (function () use ($service, $session) {
                        $service->notifySessionOpened($session);
                        $service->notifyTeacherSessionOpened($session);
                    })(),
                    'completed' => $service->notifySessionClosed($session),
                    'cancelled', 'cancelled_by_teacher' => (function () use ($service, $session) {
                        $service->notifySessionCancelled($session);
                        $service->notifyTeacherSessionCancelled($session);
                    })(),
                    'rescheduled' => (function () use ($service, $session) {
                        $service->notifySessionRescheduled($session);
                        $service->notifyTeacherSessionRescheduled($session);
                    })(),
                    default => null,
                };
            } elseif ($session->wasChanged('scheduled_at') || $session->wasChanged('start_at') || $session->wasChanged('end_at') || $session->wasChanged('is_free_demo')) {
                CourseSession::where('course_id', $session->course_id)
                    ->update([
                        'scheduled_at' => $session->scheduled_at ?: $session->start_at,
                        'start_at' => $session->start_at ?: $session->scheduled_at,
                        'end_at' => $session->end_at,
                        'duration_minutes' => $session->duration_minutes ?: 60,
                        'is_free_demo' => (bool) $session->is_free_demo,
                    ]);

                if (! in_array($session->status, ['cancelled', 'cancelled_by_teacher', 'completed'], true)) {
                    $service->notifySessionRescheduled($session);
                    $service->notifyTeacherSessionRescheduled($session);
                }
            }

            if ($session->wasChanged('attendance_status') && $session->attendance_status === 'absent') {
                $student = $session->studentUser ?: $session->student;
                if ($student) {
                    $service->notifyTeacherStudentAbsent($session, $student);
                }
            }
        });
    }

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

    public function sessionMeeting(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(SessionMeeting::class, 'live_session_id');
    }

    public function attendances(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MeetingAttendance::class, 'live_session_id');
    }

    public function securityEvents(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MeetingSecurityEvent::class, 'live_session_id');
    }

    public function getEffectiveStartAtAttribute(): ?Carbon
    {
        if ($this->start_at || $this->scheduled_at) {
            return $this->start_at ?: $this->scheduled_at;
        }

        $courseSession = CourseSession::where('course_id', $this->course_id)
            ->where(function ($q) {
                $q->whereNotNull('scheduled_at')->orWhereNotNull('start_at');
            })
            ->first();

        return $courseSession?->start_at ?: $courseSession?->scheduled_at;
    }

    public function getEffectiveEndAtAttribute(): ?Carbon
    {
        if ($this->end_at) {
            return $this->end_at;
        }

        $start = $this->effective_start_at;
        if ($start) {
            return $start->copy()->addMinutes($this->duration_minutes ?: 60);
        }

        $courseSession = CourseSession::where('course_id', $this->course_id)
            ->whereNotNull('end_at')
            ->first();

        return $courseSession?->end_at;
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

    public function getIsFreeDemoSessionAttribute(): bool
    {
        if (array_key_exists('is_free_demo', $this->attributes) && $this->attributes['is_free_demo'] !== null) {
            return (bool) $this->attributes['is_free_demo'];
        }

        $course = $this->course;
        if (! $course || ! (bool) $course->has_free_demo) {
            return false;
        }

        $firstSessionId = static::where('course_id', $course->id)
            ->orderBy('scheduled_at')
            ->orderBy('id')
            ->value('id');

        return $firstSessionId && (int) $this->id === (int) $firstSessionId;
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
