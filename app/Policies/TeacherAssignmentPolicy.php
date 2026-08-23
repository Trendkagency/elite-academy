<?php

namespace App\Policies;

use App\Models\Assignment;
use App\Models\User;

class TeacherAssignmentPolicy
{
    public function view(User $user, Assignment $assignment): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        $teacherProfile = $user->teacherProfile;
        if (! $teacherProfile) {
            return false;
        }

        if ((int) $assignment->teacher_profile_id === (int) $teacherProfile->id) {
            return true;
        }

        return $assignment->course && (int) $assignment->course->teacher_id === (int) $teacherProfile->id;
    }

    public function update(User $user, Assignment $assignment): bool
    {
        return $this->view($user, $assignment);
    }

    public function delete(User $user, Assignment $assignment): bool
    {
        return $this->view($user, $assignment);
    }
}
