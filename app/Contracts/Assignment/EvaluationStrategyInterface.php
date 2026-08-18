<?php

namespace App\Contracts\Assignment;

use App\Models\Assignment;
use App\Models\User;
use App\Models\AssignmentSubmission;

interface EvaluationStrategyInterface
{
    /**
     * Evaluate submitted answers against official correct answers server-side.
     *
     * @param User $student
     * @param Assignment $assignment
     * @param array $submittedAnswers
     * @param int|null $attemptNumber
     * @return AssignmentSubmission
     */
    public function evaluate(User $student, Assignment $assignment, array $submittedAnswers, ?int $attemptNumber = 1): AssignmentSubmission;
}
