<?php

namespace App\Actions\Course;

use App\Enums\SessionProgressStatus;
use App\Models\CourseEnrollment;
use App\Models\CourseSession;
use App\Models\CourseSessionProgress;

class UnlockNextSessionAction
{
    public function execute(CourseEnrollment $enrollment, CourseSession $currentSession): ?CourseSession
    {
        $nextSession = CourseSession::where('course_id', $currentSession->course_id)
            ->where('sort_order', '>', $currentSession->sort_order)
            ->orderBy('sort_order', 'asc')
            ->first();

        if (! $nextSession) {
            return null;
        }

        CourseSessionProgress::updateOrCreate(
            [
                'course_enrollment_id' => $enrollment->id,
                'course_session_id' => $nextSession->id,
            ],
            [
                'status' => SessionProgressStatus::UNLOCKED,
                'unlocked_at' => now(),
            ]
        );

        return $nextSession;
    }
}
