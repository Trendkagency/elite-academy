<?php

namespace App\Actions\Session;

use App\Models\CourseEnrollment;
use App\Models\ExceptionRequest;
use App\Models\LiveSession;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class SubmitExceptionRequestAction
{
    public function execute(
        User $student,
        ?LiveSession $liveSession,
        string $reason,
        ?string $attachmentPath = null,
        ?int $courseId = null,
        bool $isGlobal = false,
        string $scope = 'course',
        ?int $homeworkAssignmentId = null
    ): ExceptionRequest {
        if ($liveSession && $liveSession->scheduled_at && $liveSession->scheduled_at->isFuture()) {
            // Rule: Upcoming session absence excuses should be submitted at least 2 hours (120 minutes) prior to scheduled session time
            $minutesUntilStart = now()->diffInMinutes($liveSession->scheduled_at, false);

            if ($minutesUntilStart >= 0 && $minutesUntilStart < 120) {
                throw ValidationException::withMessages([
                    'live_session_id' => [__('Requests for absence excuses must be submitted at least 2 hours prior to the live session scheduled time.')],
                ]);
            }
        }

        // Duplicate Check for Live Session Excuse
        if ($liveSession) {
            $existingSessionException = ExceptionRequest::where('student_user_id', $student->id)
                ->where('live_session_id', $liveSession->id)
                ->whereIn('status', ['pending', 'approved'])
                ->first();

            if ($existingSessionException) {
                $statusText = $existingSessionException->status === 'approved' ? __('already approved') : __('pending review');
                throw ValidationException::withMessages([
                    'live_session_id' => ["An exception request for this session is {$statusText}. Duplicate requests cannot be submitted."],
                ]);
            }
        }

        // Duplicate Check for Homework / Assignment Exception
        if ($homeworkAssignmentId) {
            $existingHwException = ExceptionRequest::where('student_user_id', $student->id)
                ->where('homework_assignment_id', $homeworkAssignmentId)
                ->whereIn('status', ['pending', 'approved'])
                ->first();

            if ($existingHwException) {
                $statusText = $existingHwException->status === 'approved' ? __('already approved') : __('pending review');
                throw ValidationException::withMessages([
                    'homework_assignment_id' => ["An exception request for this homework/assignment is {$statusText}. Duplicate requests cannot be submitted."],
                ]);
            }
        }

        // Duplicate Check for Course-level Pending Exception (when no specific session or homework specified)
        if (! $liveSession && ! $homeworkAssignmentId && $courseId && ! $isGlobal && $scope !== 'global') {
            $existingCourseException = ExceptionRequest::where('student_user_id', $student->id)
                ->where('course_id', $courseId)
                ->whereNull('live_session_id')
                ->whereNull('homework_assignment_id')
                ->where('status', 'pending')
                ->first();

            if ($existingCourseException) {
                throw ValidationException::withMessages([
                    'course_id' => [__('You already have a pending exception request for this course. Please wait for admin review.')],
                ]);
            }
        }

        $isGlobalScope = $isGlobal || $scope === 'global';

        $primaryException = ExceptionRequest::create([
            'student_user_id' => $student->id,
            'live_session_id' => $liveSession?->id,
            'homework_assignment_id' => $homeworkAssignmentId,
            'course_id' => $courseId ?: $liveSession?->course_id,
            'is_global' => $isGlobalScope,
            'scope' => $isGlobalScope ? 'global' : 'course',
            'reason' => $reason,
            'attachment_path' => $attachmentPath,
            'status' => 'pending',
        ]);

        // If Global Exception, create related course exception records for all student enrolled courses
        if ($isGlobalScope) {
            $enrolledCourseIds = CourseEnrollment::where('student_user_id', $student->id)
                ->where('status', 'active')
                ->pluck('course_id');

            foreach ($enrolledCourseIds as $enrolledCourseId) {
                if ($enrolledCourseId != $courseId) {
                    ExceptionRequest::firstOrCreate([
                        'student_user_id' => $student->id,
                        'course_id' => $enrolledCourseId,
                        'scope' => 'global',
                    ], [
                        'live_session_id' => $liveSession?->id,
                        'homework_assignment_id' => $homeworkAssignmentId,
                        'is_global' => true,
                        'reason' => "Global Exception (All Enrolled Courses): {$reason}",
                        'attachment_path' => $attachmentPath,
                        'status' => 'pending',
                    ]);
                }
            }
        }

        return $primaryException;
    }
}
