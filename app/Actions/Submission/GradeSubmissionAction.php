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
        $passingGrade = (float) ($assignment->passing_score ?? $assignment->passing_grade ?? 70.0);
        $passed = (float) $grade >= $passingGrade;
        $status = $passed ? SubmissionStatus::COMPLETED->value : SubmissionStatus::PENDING->value;

        $submission->update([
            'grade' => $grade,
            'score' => $grade,
            'percentage' => $grade,
            'passing_score' => $passingGrade,
            'status' => $status,
            'teacher_notes' => $feedback,
            'evaluation_notes' => $feedback,
            'reviewed_at' => now(),
        ]);

        if ($assignment->live_session_id) {
            \App\Models\StudentSession::updateOrCreate(
                [
                    'student_user_id' => $submission->student_user_id,
                    'live_session_id' => $assignment->live_session_id,
                ],
                [
                    'assignment_status' => $passed ? 'passed' : 'failed',
                    'assignment_score' => $grade,
                    'session_status' => $passed ? 'completed' : 'active',
                    'completed_at' => $passed ? now() : null,
                ]
            );
        }

        if ($passed && $submission->enrollment && $assignment->session) {
            $this->unlockNextSessionAction->execute(
                $submission->enrollment,
                $assignment->session
            );
        }

        return $submission;
    }
}
