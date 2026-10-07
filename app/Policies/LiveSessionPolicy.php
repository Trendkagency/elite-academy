<?php

namespace App\Policies;

use App\Models\CourseEnrollment;
use App\Models\LiveSession;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LiveSessionPolicy
{
    use HandlesAuthorization;

    public function view(User $user, LiveSession $session): bool
    {
        if ($user->isAdmin() || $user->isTeacher()) {
            return true;
        }

        return $this->studentMayAccess($user, $session);
    }

    public function join(User $user, LiveSession $session): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Status must not be cancelled or completed
        $status = is_object($session->status) ? $session->status->value : (string) $session->status;
        if (in_array($status, ['cancelled', 'completed', 'cancelled_by_teacher'], true)) {
            return false;
        }

        // Teacher assigned to session
        if ($user->isTeacher()) {
            if ($session->teacherProfile && (int) $session->teacherProfile->user_id === (int) $user->id) {
                return true;
            }
            return false;
        }

        return $this->studentMayAccess($user, $session);
    }

    /**
     * A student may only access their own 1:1 session, or a course-wide
     * session that is not assigned to somebody else.
     */
    protected function studentMayAccess(User $user, LiveSession $session): bool
    {
        // If student has an approved excuse for this session, course, or global, access is prohibited
        $hasApprovedExcuse = \App\Models\ExceptionRequest::where('student_user_id', $user->id)
            ->where('status', 'approved')
            ->where(function ($q) use ($session) {
                $q->where('live_session_id', $session->id);
                if ($session->course_id) {
                    $q->orWhere('course_id', $session->course_id);
                }
                $q->orWhere('is_global', true)->orWhere('scope', 'global');
            })
            ->exists();

        if ($hasApprovedExcuse) {
            return false;
        }

        if ($session->student_user_id) {
            return (int) $session->student_user_id === (int) $user->id;
        }

        if ($session->course_id) {
            return CourseEnrollment::where('student_user_id', $user->id)
                ->where('course_id', $session->course_id)
                ->exists();
        }

        return false;
    }
}
