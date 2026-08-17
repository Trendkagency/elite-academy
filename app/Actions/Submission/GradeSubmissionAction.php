<?php

namespace App\Actions\Submission;

use App\Actions\Course\UnlockNextSessionAction;
use App\Enums\SubmissionStatus;
use App\Models\AssignmentSubmission;

class GradeSubmissionAction
{
    public function __construct(
        protected UnlockNextSessionAction $unlockNextSessionAction
    ) {}

    public function execute(AssignmentSubmission $submission, int $grade, ?string $feedback = null): AssignmentSubmission
    {
        $assignment = $submission->assignment;
        $passingGrade = $assignment->passing_grade ?? 50;
        $passed = $grade >= $passingGrade;
        $status = $passed ? SubmissionStatus::COMPLETED->value : SubmissionStatus::PENDING->value;

        $submission->update([
            'grade' => $grade,
            'status' => $status,
            'teacher_notes' => $feedback,
            'reviewed_at' => now(),
        ]);

        if ($passed && $submission->enrollment && $assignment->session) {
            $this->unlockNextSessionAction->execute(
                $submission->enrollment,
                $assignment->session
            );
        }

        return $submission;
    }
}
