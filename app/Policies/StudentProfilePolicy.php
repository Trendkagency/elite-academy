<?php

namespace App\Policies;

use App\Models\StudentProfile;
use App\Models\User;
use App\Permissions\PermissionsRegistry;

class StudentProfilePolicy
{
    public function view(User $user, StudentProfile $studentProfile): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Student viewing their own profile
        if ($user->isStudent() && (int) $user->id === (int) $studentProfile->user_id) {
            return $user->hasPermission(PermissionsRegistry::PROFILE_VIEW_OWN);
        }

        // Parent viewing their linked student's profile
        if ($user->isParent()) {
            if (! $user->hasPermission(PermissionsRegistry::STUDENTS_VIEW_OWN)) {
                return false;
            }

            return (int) $studentProfile->parent_user_id === (int) $user->id;
        }

        // Teacher viewing student enrolled in their courses or in their live sessions
        if ($user->isTeacher()) {
            if (! $user->hasPermission(PermissionsRegistry::STUDENTS_VIEW)) {
                return false;
            }

            $teacherProfile = $user->teacherProfile;
            if (! $teacherProfile) {
                return false;
            }

            $isEnrolledInCourse = \App\Models\CourseEnrollment::where('student_user_id', $studentProfile->user_id)
                ->whereHas('course', function ($q) use ($teacherProfile) {
                    $q->where('teacher_id', $teacherProfile->id);
                })->exists();

            if ($isEnrolledInCourse) {
                return true;
            }

            $hasDirectSession = \App\Models\LiveSession::where('teacher_profile_id', $teacherProfile->id)
                ->where('student_user_id', $studentProfile->user_id)
                ->exists();

            if ($hasDirectSession) {
                return true;
            }

            $hasGroupSession = \App\Models\StudentSession::where('student_user_id', $studentProfile->user_id)
                ->whereHas('liveSession', function ($q) use ($teacherProfile) {
                    $q->where('teacher_profile_id', $teacherProfile->id);
                })->exists();

            if ($hasGroupSession) {
                return true;
            }

            $hasSubmission = \App\Models\AssignmentSubmission::where('student_user_id', $studentProfile->user_id)
                ->whereHas('assignment', function ($q) use ($teacherProfile) {
                    $q->where('teacher_profile_id', $teacherProfile->id)
                        ->orWhereHas('course', fn ($c) => $c->where('teacher_id', $teacherProfile->id));
                })->exists();

            if ($hasSubmission) {
                return true;
            }

            return \App\Models\StudentEducationalNote::where('teacher_profile_id', $teacherProfile->id)
                ->where('student_user_id', $studentProfile->user_id)
                ->exists();
        }

        return false;
    }

    public function addNote(User $user, StudentProfile $studentProfile): bool
    {
        return $this->view($user, $studentProfile);
    }
}
