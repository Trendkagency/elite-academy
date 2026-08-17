<?php

namespace App\Policies;

use App\Enums\SessionProgressStatus;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\CourseEnrollment;
use App\Models\CourseSession;
use App\Models\CourseSessionProgress;
use App\Models\ExceptionRequest;
use App\Models\TeacherProfile;
use App\Models\User;

class CourseSessionPolicy
{
    public function view(User $user, CourseSession $session): bool
    {
        // Admins and course instructors always have access
        $teacherProfile = TeacherProfile::where('user_id', $user->id)->first();
        if ($user->isAdmin() || ($teacherProfile && $session->course->teacher_id === $teacherProfile->id)) {
            return true;
        }

        if ($session->is_free_demo) {
            return true;
        }

        // Student must have an active enrollment
        $enrollment = CourseEnrollment::where('student_user_id', $user->id)
            ->where('course_id', $session->course_id)
            ->where('status', 'active')
            ->first();

        if (! $enrollment) {
            return false;
        }

        // Check if student has an APPROVED Exception Request (Global OR specific to this course)
        $hasApprovedException = ExceptionRequest::where('student_user_id', $user->id)
            ->where('status', 'approved')
            ->where(function ($q) use ($session) {
                $q->where('is_global', true)
                  ->orWhere('scope', 'global')
                  ->orWhere('course_id', $session->course_id);
            })
            ->exists();

        if ($hasApprovedException) {
            return true; // Exemption granted by Admin/Teacher for this course or globally
        }

        // Mandatory Rule: Check if ALL previous session assignments are submitted before starting current session
        $previousSessions = CourseSession::where('course_id', $session->course_id)
            ->where('sort_order', '<', $session->sort_order)
            ->orderBy('sort_order', 'asc')
            ->get();

        foreach ($previousSessions as $prev) {
            $assignments = Assignment::where('course_session_id', $prev->id)
                ->where('status', '!=', 'draft')
                ->get();

            foreach ($assignments as $assignment) {
                $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
                    ->where('student_user_id', $user->id)
                    ->first();

                if (! $submission) {
                    return false; // Cannot start session without submitting previous assignment
                }
            }
        }

        return true;
    }
}
