<?php

namespace App\Policies;

use App\Models\AssignmentSubmission;
use App\Models\User;
use App\Permissions\PermissionsRegistry;

class TeacherSubmissionPolicy
{
    public function view(User $user, AssignmentSubmission $submission): bool
    {
        if (! $user->hasPermission(PermissionsRegistry::SUBMISSIONS_VIEW)) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        $teacherProfile = $user->teacherProfile;
        if (! $teacherProfile) {
            return false;
        }

        $assignment = $submission->assignment;
        if (! $assignment) {
            return false;
        }

        if ((int) $assignment->teacher_profile_id === (int) $teacherProfile->id) {
            return true;
        }

        return $assignment->course && (int) $assignment->course->teacher_id === (int) $teacherProfile->id;
    }

    public function grade(User $user, AssignmentSubmission $submission): bool
    {
        if (! $user->hasPermission(PermissionsRegistry::SUBMISSIONS_REVIEW)) {
            return false;
        }

        return $this->view($user, $submission);
    }
}
