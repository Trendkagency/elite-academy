<?php

namespace App\Policies;

use App\Models\AssignmentSubmission;
use App\Models\TeacherProfile;
use App\Models\User;

class AssignmentSubmissionPolicy
{
    public function view(User $user, AssignmentSubmission $submission): bool
    {
        if ($user->isAdmin() || (int) $submission->student_user_id === (int) $user->id) {
            return true;
        }

        $teacherProfile = TeacherProfile::where('user_id', $user->id)->first();
        if (! $teacherProfile) {
            return false;
        }

        $submission->loadMissing(['assignment.session.course', 'enrollment.course']);
        $courseTeacherId = $submission->assignment?->session?->course?->teacher_id
            ?? $submission->enrollment?->course?->teacher_id;

        return (int) $courseTeacherId === (int) $teacherProfile->id;
    }

    public function grade(User $user, AssignmentSubmission $submission): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        $teacherProfile = TeacherProfile::where('user_id', $user->id)->first();
        if (! $teacherProfile) {
            return false;
        }

        $submission->loadMissing(['assignment.session.course', 'enrollment.course']);
        $courseTeacherId = $submission->assignment?->session?->course?->teacher_id
            ?? $submission->enrollment?->course?->teacher_id;

        return (int) $courseTeacherId === (int) $teacherProfile->id;
    }
}
