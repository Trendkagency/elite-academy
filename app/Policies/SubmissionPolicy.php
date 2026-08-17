<?php

namespace App\Policies;

use App\Models\AssignmentSubmission;
use App\Models\User;

class SubmissionPolicy
{
    public function view(User $user, AssignmentSubmission $submission): bool
    {
        return $user->isAdmin() ||
            $submission->student_user_id === $user->id ||
            ($user->isTeacher() && $submission->assignment->session->course->teacher_id === $user->teacherProfile?->id);
    }

    public function grade(User $user, AssignmentSubmission $submission): bool
    {
        return $user->isAdmin() ||
            ($user->isTeacher() && $submission->assignment->session->course->teacher_id === $user->teacherProfile?->id);
    }
}
