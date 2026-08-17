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
        string $scope = 'course'
    ): ExceptionRequest {
        if ($liveSession) {
            // Rule: Exception requests must be submitted at least 2 hours (120 minutes) prior to scheduled session time
            $minutesUntilStart = now()->diffInMinutes($liveSession->scheduled_at, false);

            if ($minutesUntilStart < 120) {
                throw ValidationException::withMessages([
                    'live_session_id' => ['Requests for absence excuses must be submitted at least 2 hours prior to the live session scheduled time.'],
                ]);
            }
        }

        $isGlobalScope = $isGlobal || $scope === 'global';

        $primaryException = ExceptionRequest::create([
            'student_user_id' => $student->id,
            'live_session_id' => $liveSession?->id,
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
