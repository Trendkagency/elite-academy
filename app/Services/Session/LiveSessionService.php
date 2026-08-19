<?php

namespace App\Services\Session;

use App\Enums\LiveSessionState;
use App\Enums\SubmissionStatus;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\ExceptionRequest;
use App\Models\LiveSession;
use App\Models\StudentSession;
use App\Models\User;
use Carbon\Carbon;

class LiveSessionService
{
    /**
     * Evaluate the exact state of a live session for a student.
     */
    public function evaluateState(LiveSession $session, User $student, ?Carbon $now = null): LiveSessionState
    {
        $currentTime = $now ?: now();
        $startWindow = Carbon::parse($session->scheduled_at ?? $session->start_at)->subMinutes(30);
        $durationMinutes = $session->duration_minutes ?: 60;
        $sessionStart = Carbon::parse($session->scheduled_at ?? $session->start_at);
        $halfTime = $sessionStart->copy()->addMinutes((int) ceil($durationMinutes / 2));
        $endTime = Carbon::parse($session->end_at ?? $sessionStart->copy()->addMinutes($durationMinutes));

        if ($session->status === 'cancelled') {
            return LiveSessionState::CANCELLED;
        }

        // Half-session cutoff: If current time exceeds halfway mark or session end time/completed status
        if ($currentTime->gte($endTime) || $currentTime->gte($halfTime) || $session->status === 'completed') {
            return LiveSessionState::ENDED;
        }

        // Active package requirement check: If session is NOT a free trial demo, student MUST have an active package
        if (! $this->isSessionFreeDemo($session, $student)) {
            $hasActivePackage = \App\Models\StudentPackage::where('student_user_id', $student->id)
                ->where('status', 'active')
                ->where('remaining_sessions', '>', 0)
                ->where(function ($q) use ($currentTime) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', $currentTime);
                })
                ->exists();

            if (! $hasActivePackage) {
                return LiveSessionState::PACKAGE_REQUIRED;
            }
        }

        if ($currentTime->lt($startWindow)) {
            return LiveSessionState::BEFORE_JOINABLE;
        }

        // Check if student has pending prerequisite assignments
        $pendingAssignments = $this->getRemainingGatingAssignments($student, $session);
        if ($pendingAssignments->isNotEmpty()) {
            $hasApprovedException = ExceptionRequest::where('student_user_id', $student->id)
                ->where(function ($query) use ($session) {
                    $query->where('live_session_id', $session->id)
                        ->orWhere(function ($q) use ($session) {
                            $q->where('scope', 'course')->where('course_id', $session->course_id);
                        });
                })
                ->where('status', 'approved')
                ->exists();

            if (! $hasApprovedException) {
                return LiveSessionState::PREREQUISITE_REQUIRED;
            }
        }

        return LiveSessionState::LIVE;
    }

    /**
     * Get stream access data with HTTP status code for API endpoints.
     */
    public function getStreamAccess(LiveSession $session, User $student): array
    {
        $state = $this->evaluateState($session, $student);
        $canAccess = ($state === LiveSessionState::LIVE);

        $statusCode = match ($state) {
            LiveSessionState::LIVE => 200,
            default => 422,
        };

        $access = $this->canAccessStream($student, $session);

        return [
            'can_access' => $canAccess,
            'status_code' => $statusCode,
            'state' => $state->value,
            'reason_code' => $access['reason_code'] ?? $state->value,
            'message' => $state->label(),
            'stream_url' => $canAccess ? ($session->stream_url ?? 'https://stream.elite-academy.com/live/' . $session->id) : null,
            'start_window' => $session->scheduled_at ? Carbon::parse($session->scheduled_at)->subMinutes(30)->toIso8601String() : null,
        ];
    }
    /**
     * Determine if a live session is a free demo session.
     */
    public function isSessionFreeDemo(LiveSession $session, ?User $student = null): bool
    {
        return (bool) $session->is_free_demo_session;
    }

    /**
     * Determine if a student can access the live session stream.
     *
     * Rules:
     * 1. Access is allowed within 30 minutes before scheduled start time up to end time.
     * 2. Prerequisite MSQ assignments for the course/session must be completed, unless an approved exception request exists.
     * 3. First session of a course with has_free_demo = true or is_free_demo = true is FREE and does NOT require an active package.
     */
    public function canAccessStream(User $student, LiveSession $session): array
    {
        $now = now();

        $isFreeDemo = $this->isSessionFreeDemo($session, $student);

        // 0. Active package check (Bypassed if session is a free demo / 1st free session)
        if (! $isFreeDemo) {
            $hasActivePackage = \App\Models\StudentPackage::where('student_user_id', $student->id)
                ->where('status', 'active')
                ->where('remaining_sessions', '>', 0)
                ->where(function ($q) use ($now) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', $now);
                })
                ->exists();

            if (! $hasActivePackage) {
                return [
                    'allowed' => false,
                    'reason_code' => 'PACKAGE_REQUIRED',
                    'message' => app()->getLocale() === 'ar'
                        ? 'يلزم الاشتراك في باقة حصص نشطة تحتوي على رصيد للوصول للبث المباشر والكورسات.'
                        : 'An active package subscription with session credits is required to access live streams and courses.',
                ];
            }
        }

        $sessionStart = Carbon::parse($session->scheduled_at ?? $session->start_at);
        $startWindow = $sessionStart->copy()->subMinutes(30);
        $durationMinutes = $session->duration_minutes ?: 60;
        $halfTime = $sessionStart->copy()->addMinutes((int) ceil($durationMinutes / 2));
        $endTime = Carbon::parse($session->end_at ?? $sessionStart->copy()->addMinutes($durationMinutes));

        // 1. Time window check
        if ($now->lt($startWindow)) {
            return [
                'allowed' => false,
                'reason_code' => 'NOT_STARTED_YET',
                'message' => app()->getLocale() === 'ar'
                    ? 'البث المباشر لم يبدأ بعد. يمكنك الانضمام قبل 30 دقيقة من الموعد المحدد.'
                    : 'Live stream has not started yet. Access opens 30 minutes before the scheduled time.',
                'start_window' => $startWindow->toIso8601String(),
            ];
        }

        if ($now->gt($endTime)) {
            return [
                'allowed' => false,
                'reason_code' => 'SESSION_ENDED',
                'message' => app()->getLocale() === 'ar'
                    ? 'انتهت هذه الجلسة المباشرة.'
                    : 'This live session has ended.',
            ];
        }

        if ($now->gt($halfTime)) {
            return [
                'allowed' => false,
                'reason_code' => 'HALF_SESSION_EXPIRED',
                'message' => app()->getLocale() === 'ar'
                    ? 'عذراً! انتهت مهلة الانضمام للحصة (تجاوزت منتصف وقت الحصة).'
                    : 'Joining window has expired (half of the session duration has passed).',
            ];
        }

        // 2. Check approved exception requests for this session or course
        $hasApprovedException = ExceptionRequest::where('student_user_id', $student->id)
            ->where(function ($query) use ($session) {
                $query->where('live_session_id', $session->id)
                    ->orWhere(function ($q) use ($session) {
                        $q->where('scope', 'course')->where('course_id', $session->course_id);
                    });
            })
            ->where('status', 'approved')
            ->exists();

        if ($hasApprovedException) {
            return [
                'allowed' => true,
                'reason_code' => 'APPROVED_EXCEPTION',
                'message' => app()->getLocale() === 'ar'
                    ? 'تم منحك إذناً استثنائياً لدخول البث المباشر.'
                    : 'You have been granted an approved exception to join the stream.',
            ];
        }

        // 3. Check prerequisite assignments gating
        $pendingAssignments = $this->getRemainingGatingAssignments($student, $session);

        if ($pendingAssignments->isNotEmpty()) {
            return [
                'allowed' => false,
                'reason_code' => 'ASSIGNMENT_REQUIRED',
                'message' => app()->getLocale() === 'ar'
                    ? 'يجب إكمال الواجبات المطلوبة أولاً قبل الانضمام للبث المباشر.'
                    : 'You must complete required prerequisite assignments before joining the live stream.',
                'pending_assignments' => $pendingAssignments->values(),
            ];
        }

        return [
            'allowed' => true,
            'reason_code' => 'ACCESS_GRANTED',
            'message' => app()->getLocale() === 'ar'
                ? 'أهلاً بك! يمكنك الآن الانضمام للبث المباشر.'
                : 'Welcome! You can now join the live stream.',
        ];
    }

    /**
     * Get list of pending mandatory assignments required before joining the session.
     */
    public function getRemainingGatingAssignments(User $student, LiveSession $session)
    {
        // First Free Demo Session has NO mandatory prerequisite assignment requirements
        if ($this->isSessionFreeDemo($session, $student)) {
            return collect();
        }

        $mandatoryAssignments = Assignment::where('course_id', $session->course_id)
            ->where('status', 'published')
            ->where('is_mandatory', true)
            ->get();

        $completedAssignmentIds = AssignmentSubmission::where('student_user_id', $student->id)
            ->whereIn('status', [
                SubmissionStatus::COMPLETED,
                SubmissionStatus::SUBMITTED,
                SubmissionStatus::REVIEWED,
                'completed',
                'submitted',
                'reviewed',
            ])
            ->pluck('assignment_id')
            ->toArray();

        $completedAssignmentIds = array_map('strval', $completedAssignmentIds);

        return $mandatoryAssignments->reject(fn ($assignment) => in_array((string) $assignment->id, $completedAssignmentIds, true));
    }
}
