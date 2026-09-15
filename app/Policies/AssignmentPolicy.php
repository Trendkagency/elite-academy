<?php

namespace App\Policies;

use App\Models\Assignment;
use App\Models\User;
use App\Permissions\PermissionsRegistry;

class AssignmentPolicy
{
    public function view(User $user, Assignment $assignment): bool
    {
        if (! $user->hasPermission(PermissionsRegistry::ASSIGNMENTS_VIEW)) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isTeacher()) {
            $teacherProfile = $user->teacherProfile;
            if (! $teacherProfile) {
                return false;
            }

            return (int) $assignment->teacher_profile_id === (int) $teacherProfile->id
                || ($assignment->course && (int) $assignment->course->teacher_id === (int) $teacherProfile->id);
        }

        if ($user->isStudent()) {
            if (! $assignment->course_id && ! $assignment->live_session_id) {
                return true;
            }

            if ($assignment->course_id) {
                $isEnrolled = \App\Models\CourseEnrollment::where('student_user_id', $user->id)
                    ->where('course_id', $assignment->course_id)
                    ->exists();
                if ($isEnrolled) {
                    return true;
                }

                $hasSessionInCourse = \App\Models\LiveSession::where('course_id', $assignment->course_id)
                    ->where(function ($q) use ($user) {
                        $q->where('student_user_id', $user->id)
                          ->orWhereHas('studentSessions', fn ($sq) => $sq->where('student_user_id', $user->id));
                    })->exists();
                if ($hasSessionInCourse) {
                    return true;
                }
            }

            if ($assignment->live_session_id) {
                $hasLiveSession = \App\Models\LiveSession::where('id', $assignment->live_session_id)
                    ->where(function ($q) use ($user) {
                        $q->where('student_user_id', $user->id)
                          ->orWhereHas('studentSessions', fn ($sq) => $sq->where('student_user_id', $user->id));
                    })->exists();
                if ($hasLiveSession) {
                    return true;
                }
            }

            return false;
        }

        return false;
    }

    public function take(User $user, Assignment $assignment): bool
    {
        if (! $user->hasPermission(PermissionsRegistry::ASSIGNMENTS_ANSWER)) {
            return false;
        }

        return $this->view($user, $assignment);
    }

    public function submit(User $user, Assignment $assignment): bool
    {
        if (! $user->hasPermission(PermissionsRegistry::ASSIGNMENTS_SUBMIT)) {
            return false;
        }

        return $this->view($user, $assignment);
    }
}
