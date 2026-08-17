<?php

namespace App\Actions\Course;

use App\Enums\SessionProgressStatus;
use App\Models\CourseEnrollment;
use App\Models\CourseSession;
use App\Models\CourseSessionProgress;
use App\Repositories\Contracts\SessionRepositoryInterface;

class UnlockNextSessionAction
{
    public function __construct(
        protected SessionRepositoryInterface $sessionRepository
    ) {}

    public function execute(CourseEnrollment $enrollment, CourseSession $currentSession): ?CourseSession
    {
        $nextSession = $this->sessionRepository->getNextSession(
            $currentSession->course_id,
            $currentSession->sort_order
        );

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
