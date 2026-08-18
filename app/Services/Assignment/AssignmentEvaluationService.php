<?php

namespace App\Services\Assignment;

use App\Contracts\Assignment\EvaluationStrategyInterface;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\User;
use App\Services\Assignment\Strategies\MSQEvaluationStrategy;
use Illuminate\Validation\ValidationException;

class AssignmentEvaluationService
{
    protected EvaluationStrategyInterface $strategy;
    protected AssignmentSecurityService $securityService;

    public function __construct(?EvaluationStrategyInterface $strategy = null, ?AssignmentSecurityService $securityService = null)
    {
        $this->strategy = $strategy ?? new MSQEvaluationStrategy();
        $this->securityService = $securityService ?? new AssignmentSecurityService();
    }

    /**
     * Evaluate and process a student's MSQ assignment submission with full security constraints.
     */
    public function submitAndEvaluate(User $student, Assignment $assignment, array $submittedAnswers): AssignmentSubmission
    {
        // 1. Lifecycle validations
        if ($assignment->status === 'draft' || $assignment->status === 'closed') {
            throw ValidationException::withMessages([
                'assignment' => ['Assignment is not available for submission.']
            ]);
        }

        if ($assignment->isExpired()) {
            $this->securityService->logEvent($student, $assignment, 'EXPIRED_SUBMISSION_ATTEMPT', [], 5);
            $deadlineStr = $assignment->effective_due_at ? $assignment->effective_due_at->format('Y-m-d H:i') : '';
            $msg = app()->getLocale() === 'ar'
                ? "تجاوزت الموعد النهائي لتسليم الواجب (الموعد المحدد هو قبل موعد الدرس بـ 24 ساعة: {$deadlineStr}). يرجى تقديم طلب استثناء لتسليم الواجب."
                : "Assignment deadline expired (Deadline is 24 hours before the lesson: {$deadlineStr}). Please submit an exception request.";

            throw ValidationException::withMessages([
                'assignment' => [$msg]
            ]);
        }

        // 2. Strict One Attempt DB & Model Verification
        $existingSubmission = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('student_user_id', $student->id)
            ->whereIn('status', [\App\Enums\SubmissionStatus::COMPLETED, \App\Enums\SubmissionStatus::SUBMITTED, \App\Enums\SubmissionStatus::REVIEWED])
            ->first();

        if ($existingSubmission) {
            $this->securityService->logEvent($student, $assignment, 'DUPLICATE_SUBMISSION_ATTEMPT', [
                'existing_submission_id' => $existingSubmission->id,
            ], 10);

            throw ValidationException::withMessages([
                'assignment' => ['You have already submitted an attempt for this assignment. Re-submissions are strictly forbidden.']
            ]);
        }

        // 3. Delegate to Strategy
        $submission = $this->strategy->evaluate($student, $assignment, $submittedAnswers, 1);

        // 4. Audit Log Successful Submission
        $this->securityService->logEvent($student, $assignment, 'ASSIGNMENT_SUBMITTED', [
            'submission_id' => $submission->id,
            'score' => $submission->score,
            'percentage' => $submission->percentage,
            'is_passed' => $submission->isPassed(),
        ], 0);

        return $submission;
    }
}
