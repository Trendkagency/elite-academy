<?php

namespace App\Policies;

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

        return (int) $session->student_user_id === (int) $user->id;
    }

    public function join(User $user, LiveSession $session): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Status must be active (scheduled, link_visible, or in_progress)
        if (in_array($session->status, ['cancelled', 'completed', 'cancelled_by_teacher'], true)) {
            return false;
        }

        // Teacher assigned to session
        if ($user->isTeacher()) {
            if ($session->teacherProfile && (int) $session->teacherProfile->user_id === (int) $user->id) {
                return true;
            }
            return false;
        }

        // Student assigned directly
        if ($session->student_user_id && (int) $session->student_user_id === (int) $user->id) {
            return true;
        }

        // Course-wide session check
        if ($session->student_user_id === null && $session->course_id) {
            return \App\Models\CourseEnrollment::where('student_user_id', $user->id)
                ->where('course_id', $session->course_id)
                ->exists();
        }

        return false;
    }
}
