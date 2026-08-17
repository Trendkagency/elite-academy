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

    public function canStudentAccessStream(?User $user = null): array
    {
        $user = $user ?: auth()->user();
        if (! $user) {
            return ['can_access' => false, 'reason' => 'unauthenticated'];
        }

        // 1. Time Check (30-Minute Rule): Link is only accessible within 30 minutes before scheduled start time
        $minutesUntilStart = $this->scheduled_at ? now()->diffInMinutes($this->scheduled_at, false) : 0;

        if ($minutesUntilStart > 30) {
            return [
                'can_access' => false,
                'reason' => 'time_gated',
                'message' => app()->getLocale() === 'ar'
                    ? 'رابط الحصة يتفعل قبل موعد البث بـ 30 دقيقة'
                    : 'Meeting link activates 30 mins before session start',
            ];
        }

        // 2. Assignment Prerequisite or Approved Exception Request Check
        if ($this->course_id) {
            $hasApprovedException = ExceptionRequest::where('student_user_id', $user->id)
                ->where('status', 'approved')
                ->where(function ($q) {
                    $q->where('is_global', true)
                      ->orWhere('scope', 'global')
                      ->orWhere('course_id', $this->course_id);
                })
                ->exists();

            if (! $hasApprovedException) {
                $previousSessions = CourseSession::where('course_id', $this->course_id)
                    ->orderBy('sort_order', 'asc')
                    ->get();

                foreach ($previousSessions as $prevSession) {
                    $assignments = Assignment::where('course_session_id', $prevSession->id)
                        ->where('status', '!=', 'draft')
                        ->get();

                    foreach ($assignments as $assignment) {
                        $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
                            ->where('student_user_id', $user->id)
                            ->first();

                        if (! $submission) {
                            return [
                                'can_access' => false,
                                'reason' => 'assignment_required',
                                'message' => app()->getLocale() === 'ar'
                                    ? 'مطلوب تسليم واجب الجلسة السابقة أو تقديم طلب استثناء لتفعيل الرابط'
                                    : 'Previous assignment submission or approved exception required to unlock link',
                            ];
                        }
                    }
                }
            }
        }

        return [
            'can_access' => true,
            'reason' => 'unlocked',
            'meeting_link' => $this->meeting_link,
        ];
    }
}
