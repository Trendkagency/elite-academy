<?php

namespace App\Actions\Submission;

use App\Actions\Course\UnlockNextSessionAction;
use App\Enums\SubmissionStatus;
use App\Models\AssignmentSubmission;
use App\Repositories\Contracts\SubmissionRepositoryInterface;

class GradeSubmissionAction
{
    public function __construct(
        protected SubmissionRepositoryInterface $submissionRepository,
        protected UnlockNextSessionAction $unlockNextSessionAction
    ) {}

    public function execute(AssignmentSubmission $submission, int $grade, ?string $feedback = null): AssignmentSubmission
    {
        $assignment = $submission->assignment;
        $passingGrade = $assignment->passing_grade ?? 50;
        $passed = $grade >= $passingGrade;
        $status = $passed ? SubmissionStatus::COMPLETED->value : SubmissionStatus::PENDING->value;

        $updatedSubmission = $this->submissionRepository->grade($submission, $grade, $status, $feedback);

        if ($passed && $submission->enrollment && $assignment->session) {
            $this->unlockNextSessionAction->execute(
                $submission->enrollment,
                $assignment->session
            );
        }

        return $updatedSubmission;
    }
}
