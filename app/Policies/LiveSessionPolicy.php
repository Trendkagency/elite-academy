<?php

namespace App\Policies;

use App\Models\CourseEnrollment;
use App\Models\LiveSession;
use App\Models\StudentPackage;
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

        // Student assigned directly
        if ($session->student_user_id && (int) $session->student_user_id === (int) $user->id) {
            return true;
        }

        // Student enrolled in the course
        if ($session->course_id && CourseEnrollment::where('student_user_id', $user->id)->where('course_id', $session->course_id)->exists()) {
            return true;
        }

        // Free demo session or open session
        if ($session->is_free_demo || (isset($session->is_free_demo_session) && $session->is_free_demo_session)) {
            return true;
        }

        // Active student package with remaining sessions
        if (StudentPackage::where('student_user_id', $user->id)->where('status', 'active')->where('remaining_sessions', '>', 0)->exists()) {
            return true;
        }

        return false;
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

        // Student assigned directly
        if ($session->student_user_id && (int) $session->student_user_id === (int) $user->id) {
            return true;
        }

        // Student enrolled in the course
        if ($session->course_id && CourseEnrollment::where('student_user_id', $user->id)->where('course_id', $session->course_id)->exists()) {
            return true;
        }

        // Free demo session or open session
        if ($session->is_free_demo || (isset($session->is_free_demo_session) && $session->is_free_demo_session)) {
            return true;
        }

        // Student with active package credit
        if (StudentPackage::where('student_user_id', $user->id)->where('status', 'active')->where('remaining_sessions', '>', 0)->exists()) {
            return true;
        }

        return false;
    }
}
