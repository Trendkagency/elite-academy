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
        'recurring_schedule_id',
        'is_override',
        'override_reason',
        'cancellation_reason',
        'lifecycle_state',
        'reminders_sent',
        'reminder_sent_at',
        'teacher_notes',
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
        'is_override' => 'boolean',
        'reminders_sent' => 'array',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (LiveSession $session) {
            static $columns = null;
            if ($columns === null) {
                try {
                    $columns = \Illuminate\Support\Facades\Schema::getColumnListing('live_sessions');
                } catch (\Throwable $e) {
                    $columns = null;
                }
            }

            if (! empty($columns)) {
                foreach ($session->attributes as $key => $val) {
                    if (! in_array($key, $columns, true)) {
                        unset($session->attributes[$key]);
                    }
                }
            }

            // 1. Synchronize scheduled_at, start_at, and end_at timestamps
            $duration = (int) ($session->duration_minutes ?: 60);

            if ($session->scheduled_at) {
                if (! in_array($session->status, ['in_progress', 'live', 'completed'], true)) {
                    // Automatically synchronize start_at with scheduled_at unless in progress or completed
                    if (! $session->isDirty('start_at') || ! $session->start_at) {
                        $session->start_at = $session->scheduled_at;
                    }
                }

                // Keep end_at in sync with effective start + duration
                if (! $session->isDirty('end_at') || ! $session->end_at) {
                    $effectiveStart = $session->start_at ?: $session->scheduled_at;
                    if ($effectiveStart) {
                        $session->end_at = $effectiveStart->copy()->addMinutes($duration);
                    }
                }
            } elseif ($session->start_at && ! $session->scheduled_at) {
                $session->scheduled_at = $session->start_at;
                if (! $session->end_at) {
                    $session->end_at = $session->start_at->copy()->addMinutes($duration);
                }
            }

            // 2. Reset reminder tracker when scheduled time is modified into the future so new reminders fire
            if ($session->isDirty('scheduled_at') || $session->isDirty('start_at')) {
                $targetTime = $session->scheduled_at ?: $session->start_at;
                if ($targetTime && $targetTime->isFuture()) {
                    $session->reminders_sent = [];
                    $session->reminder_sent_at = null;
                }
            }

            // 3. Keep lifecycle_state consistent with status
            if ($session->isDirty('status')) {
                match ($session->status) {
                    'cancelled', 'cancelled_by_teacher' => (function () use ($session) {
                        $session->lifecycle_state = 'cancelled';
                        if (! $session->cancelled_at) {
                            $session->cancelled_at = now();
                        }
                    })(),
                    'rescheduled' => $session->lifecycle_state = 'rescheduled',
                    'in_progress', 'live' => $session->lifecycle_state = 'live',
                    'completed' => $session->lifecycle_state = 'ended',
                    'scheduled' => $session->lifecycle_state = 'scheduled',
                    default => null,
                };
            }
        });

        static::created(function (LiveSession $session) {
            $service = app(\App\Services\Notification\FcmNotificationService::class);
            $service->notifyTeacherSessionAssigned($session);
            // Notify admins about new session creation (wrapped to not break session creation on failure)
            try {
                $service->notifyAdminSessionCreated($session);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('[FCM] notifyAdminSessionCreated failed: ' . $e->getMessage());
            }

            // Ensure StudentSession record exists if 1-to-1 session
            if ($session->student_user_id) {
                \App\Models\StudentSession::firstOrCreate(
                    [
                        'live_session_id' => $session->id,
                        'student_user_id' => $session->student_user_id,
                    ],
                    [
                        'session_status' => $session->status ?: 'scheduled',
                    ]
                );
            }

            // Two-way sync: If associated with course and course_session_id is unset, sync CourseSession
            if ($session->course_id && ! $session->course_session_id) {
                try {
                    $courseSession = CourseSession::create([
                        'course_id' => $session->course_id,
                        'title' => $session->title,
                        'scheduled_at' => $session->scheduled_at ?: $session->start_at,
                        'start_at' => $session->start_at ?: $session->scheduled_at,
                        'end_at' => $session->end_at,
                        'duration_minutes' => $session->duration_minutes ?: 60,
                        'is_free_demo' => (bool) $session->is_free_demo,
                        'video_url' => $session->meeting_link,
                    ]);
                    $session->updateQuietly(['course_session_id' => $courseSession->id]);
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('[LiveSession] CourseSession auto-sync skipped: ' . $e->getMessage());
                }
            }
        });

        static::deleted(function (LiveSession $session) {
            if ($session->course_session_id) {
                CourseSession::where('id', $session->course_session_id)->delete();
            }
        });

        static::restored(function (LiveSession $session) {
            if ($session->course_session_id) {
                CourseSession::withTrashed()->where('id', $session->course_session_id)->restore();
            }
        });

        static::updated(function (LiveSession $session) {
            $service = app(\App\Services\Notification\FcmNotificationService::class);

            // A. Status change handlers
            if ($session->wasChanged('status')) {
                // Sync status to associated student_sessions respecting enum: ['scheduled', 'active', 'completed', 'cancelled', 'closed']
                $studentSessionStatus = match ($session->status) {
                    'in_progress', 'link_visible' => 'active',
                    'completed' => 'completed',
                    'cancelled', 'cancelled_by_teacher' => 'cancelled',
                    'closed' => 'closed',
                    default => 'scheduled',
                };
                \App\Models\StudentSession::where('live_session_id', $session->id)
                    ->update(['session_status' => $studentSessionStatus]);

                match ($session->status) {
                    'in_progress', 'link_visible' => (function () use ($service, $session) {
                        $service->notifySessionOpened($session);
                        $service->notifyTeacherSessionOpened($session);
                    })(),
                    'completed' => $service->notifySessionClosed($session),
                    'cancelled', 'cancelled_by_teacher' => (function () use ($service, $session) {
                        $service->notifySessionCancelled($session);
                        $service->notifyTeacherSessionCancelled($session);
                        try {
                            $service->notifyAdminSessionCancelled($session);
                        } catch (\Throwable $e) {
                            \Illuminate\Support\Facades\Log::error('[FCM] notifyAdminSessionCancelled failed: ' . $e->getMessage());
                        }

                        // Auto-refund package credits if student was previously deducted
                        try {
                            $deductionService = app(\App\Services\Session\SessionAttendanceDeductionService::class);
                            $studentIds = $deductionService->getStudentIdsForSession($session);
                            foreach ($studentIds as $sId) {
                                $deductionService->refundIfPreviouslyDeducted($session, (int) $sId, auth()->user(), 'Auto-refund on session cancellation');
                            }
                        } catch (\Throwable $e) {
                            \Illuminate\Support\Facades\Log::error('[LiveSession] Refund on cancellation failed: ' . $e->getMessage());
                        }
                    })(),
                    'rescheduled' => (function () use ($service, $session) {
                        $service->notifySessionRescheduled($session);
                        $service->notifyTeacherSessionRescheduled($session);
                        try {
                            $service->notifyAdminSessionRescheduled($session);
                        } catch (\Throwable $e) {
                            \Illuminate\Support\Facades\Log::error('[FCM] notifyAdminSessionRescheduled failed: ' . $e->getMessage());
                        }
                    })(),
                    default => null,
                };
            }

            // B. Schedule / Time changes
            if ($session->wasChanged('scheduled_at') || $session->wasChanged('start_at') || $session->wasChanged('end_at') || $session->wasChanged('duration_minutes') || $session->wasChanged('is_free_demo')) {
                // If linked to a specific CourseSession, update it directly
                if ($session->course_session_id) {
                    CourseSession::where('id', $session->course_session_id)
                        ->update([
                            'scheduled_at' => $session->scheduled_at ?: $session->start_at,
                            'start_at' => $session->start_at ?: $session->scheduled_at,
                            'end_at' => $session->end_at,
                            'duration_minutes' => $session->duration_minutes ?: 60,
                            'is_free_demo' => (bool) $session->is_free_demo,
                        ]);
                }

                // Synchronize associated assignments due dates (due 24h before session start)
                $effectiveStart = $session->effective_start_at;
                if ($effectiveStart) {
                    \App\Models\Assignment::where('live_session_id', $session->id)
                        ->update(['due_at' => $effectiveStart->copy()->subDay()]);
                }

                // Trigger reschedule notification if not cancelled/completed and status didn't already trigger it
                if (! in_array($session->status, ['cancelled', 'cancelled_by_teacher', 'completed'], true) && ! $session->wasChanged('status')) {
                    $service->notifySessionRescheduled($session);
                    $service->notifyTeacherSessionRescheduled($session);
                    try {
                        $service->notifyAdminSessionRescheduled($session);
                    } catch (\Throwable $e) {
                        \Illuminate\Support\Facades\Log::error('[FCM] notifyAdminSessionRescheduled failed: ' . $e->getMessage());
                    }
                }
            }

            // C. Student reassignment synchronization
            if ($session->wasChanged('student_user_id')) {
                $oldStudentId = $session->getOriginal('student_user_id');
                if ($oldStudentId && (int) $oldStudentId !== (int) $session->student_user_id) {
                    \App\Models\StudentSession::where('live_session_id', $session->id)
                        ->where('student_user_id', $oldStudentId)
                        ->delete();
                }
                if ($session->student_user_id) {
                    \App\Models\StudentSession::updateOrCreate(
                        [
                            'live_session_id' => $session->id,
                            'student_user_id' => $session->student_user_id,
                        ],
                        [
                            'session_status' => $session->status ?: 'scheduled',
                        ]
                    );
                }
            }

            // D. Meeting link / platform change synchronization
            if ($session->wasChanged('meeting_link') || $session->wasChanged('meeting_platform')) {
                if ($session->sessionMeeting) {
                    $session->sessionMeeting->update([
                        'join_url' => $session->meeting_link,
                        'host_url' => $session->meeting_link,
                    ]);
                }
            }

            // E. Absent attendance alert
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

    /**
     * Sessions a student may see: their own 1:1 sessions, sessions assigned via studentSessions,
     * plus course-wide sessions that are not assigned to a different student.
     */
    public function scopeVisibleToStudent($query, int $userId, array $enrolledCourseIds = [])
    {
        return $query->where(function ($q) use ($userId, $enrolledCourseIds) {
            $q->where('student_user_id', $userId)
                ->orWhereHas('studentSessions', function ($sq) use ($userId) {
                    $sq->where('student_user_id', $userId);
                });

            if (! empty($enrolledCourseIds)) {
                $q->orWhere(function ($courseQuery) use ($userId, $enrolledCourseIds) {
                    $courseQuery->whereIn('course_id', $enrolledCourseIds)
                        ->where(function ($owner) use ($userId) {
                            $owner->whereNull('student_user_id')
                                ->orWhere('student_user_id', $userId)
                                ->orWhereHas('studentSessions', function ($sq) use ($userId) {
                                    $sq->where('student_user_id', $userId);
                                });
                        });
                });
            }
        });
    }

    public function isAssignedToOtherStudent(int $userId): bool
    {
        return $this->student_user_id !== null
            && (int) $this->student_user_id !== (int) $userId;
    }

    public function studentFacingTitle(?string $fallback = null): string
    {
        $title = trim((string) ($this->title ?? ''));
        $title = preg_replace('/\s*[-–—]\s*Student\s*#\d+\s*$/iu', '', $title) ?? $title;
        $title = trim($title);

        if ($title === '') {
            return $fallback ?: (app()->getLocale() === 'ar' ? 'حصة مباشرة' : 'Live Session');
        }

        return $title;
    }

    public function recurringSchedule(): BelongsTo
    {
        return $this->belongsTo(RecurringSchedule::class, 'recurring_schedule_id');
    }

    public function auditLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SessionAuditLog::class, 'live_session_id');
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

    public function studentSessions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StudentSession::class, 'live_session_id');
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
        if ($this->scheduled_at || $this->start_at) {
            if (in_array($this->status, ['in_progress', 'completed'], true) && $this->start_at) {
                return $this->start_at;
            }
            return $this->scheduled_at ?: $this->start_at;
        }

        if ($this->course_session_id) {
            $cs = CourseSession::find($this->course_session_id);
            if ($cs && ($cs->scheduled_at || $cs->start_at)) {
                return $cs->scheduled_at ?: $cs->start_at;
            }
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

        if ($this->course_session_id) {
            $cs = CourseSession::find($this->course_session_id);
            if ($cs?->end_at) {
                return $cs->end_at;
            }
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
