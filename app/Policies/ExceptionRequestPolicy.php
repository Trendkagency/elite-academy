<?php

namespace App\Policies;

use App\Models\ExceptionRequest;
use App\Models\TeacherProfile;
use App\Models\User;

class ExceptionRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isTeacher();
    }

    public function view(User $user, ExceptionRequest $exceptionRequest): bool
    {
        if ($user->isAdmin() || $exceptionRequest->student_user_id === $user->id) {
            return true;
        }

        $teacherProfile = TeacherProfile::where('user_id', $user->id)->first();
        if ($teacherProfile && $exceptionRequest->liveSession?->teacher_profile_id === $teacherProfile->id) {
            return true;
        }

        return false;
    }

    public function update(User $user, ExceptionRequest $exceptionRequest): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        $teacherProfile = TeacherProfile::where('user_id', $user->id)->first();

        return $teacherProfile && $exceptionRequest->liveSession?->teacher_profile_id === $teacherProfile->id;
    }
}
