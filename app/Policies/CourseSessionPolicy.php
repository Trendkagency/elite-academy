<?php

namespace App\Policies;

use App\Enums\SessionProgressStatus;
use App\Enums\SubmissionStatus;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\CourseEnrollment;
use App\Models\CourseSession;
use App\Models\CourseSessionProgress;
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

        // Mandatory Rule: Check if previous session homework is completed before starting current session
        $previousSession = CourseSession::where('course_id', $session->course_id)
            ->where('sort_order', '<', $session->sort_order)
            ->orderBy('sort_order', 'desc')
            ->first();

        if ($previousSession) {
            $previousAssignments = Assignment::where('course_session_id', $previousSession->id)->get();

            foreach ($previousAssignments as $assignment) {
                $completedSubmission = AssignmentSubmission::where('assignment_id', $assignment->id)
                    ->where('student_user_id', $user->id)
                    ->where('status', SubmissionStatus::COMPLETED->value)
                    ->exists();

                if (! $completedSubmission) {
                    return false; // Cannot start session without completing previous assignment
                }
            }
        }

        // Verify session progress status
        $progress = CourseSessionProgress::where('course_enrollment_id', $enrollment->id)
            ->where('course_session_id', $session->id)
            ->first();

        return $progress && in_array($progress->status, [
            SessionProgressStatus::UNLOCKED,
            SessionProgressStatus::IN_PROGRESS,
            SessionProgressStatus::COMPLETED,
        ]);
    }
}
