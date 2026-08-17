<?php

namespace App\Policies;

use App\Enums\SessionProgressStatus;
use App\Models\CourseEnrollment;
use App\Models\CourseSession;
use App\Models\CourseSessionProgress;
use App\Models\User;

class SessionPolicy
{
    public function view(User $user, CourseSession $session): bool
    {
        if ($user->isAdmin() || ($user->isTeacher() && $session->course->teacher_id === $user->teacherProfile?->id)) {
            return true;
        }

        if ($session->is_free_demo) {
            return true;
        }

        $enrollment = CourseEnrollment::where('student_user_id', $user->id)
            ->where('course_id', $session->course_id)
            ->where('status', 'active')
            ->first();

        if (! $enrollment) {
            return false;
        }

        $progress = CourseSessionProgress::where('course_enrollment_id', $enrollment->id)
            ->where('course_session_id', $session->id)
            ->first();

        return $progress && in_array($progress->status, [
            SessionProgressStatus::UNLOCKED,
            SessionProgressStatus::IN_PROGRESS,
            SessionProgressStatus::COMPLETED,
        ]);
    }
}
