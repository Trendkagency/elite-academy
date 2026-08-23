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
            if (! $assignment->course_id) {
                return true;
            }

            return \App\Models\CourseEnrollment::where('student_user_id', $user->id)
                ->where('course_id', $assignment->course_id)
                ->exists();
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
