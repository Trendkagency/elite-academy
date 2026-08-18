<?php

namespace App\Services\Assignment\Strategies;

use App\Contracts\Assignment\EvaluationStrategyInterface;
use App\Enums\SubmissionStatus;
use App\Models\Assignment;
use App\Models\AssignmentQuestion;
use App\Models\AssignmentSubmission;
use App\Models\AssignmentSubmissionAnswer;
use App\Models\CourseEnrollment;
use App\Models\StudentSession;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MSQEvaluationStrategy implements EvaluationStrategyInterface
{
    public function evaluate(User $student, Assignment $assignment, array $submittedAnswers, ?int $attemptNumber = 1): AssignmentSubmission
    {
        $questions = AssignmentQuestion::with('options')
            ->where('assignment_id', $assignment->id)
            ->orderBy('sort_order', 'asc')
            ->get();

        if ($questions->isEmpty()) {
            $enrollment = CourseEnrollment::where('student_user_id', $student->id)
                ->where('course_id', $assignment->course_id)
                ->first();

            return AssignmentSubmission::updateOrCreate(
                [
                    'assignment_id' => $assignment->id,
                    'student_user_id' => $student->id,
                ],
                [
                    'live_session_id' => $assignment->live_session_id,
                    'course_enrollment_id' => $enrollment?->id ?? 1,
                    'submitted_at' => now(),
                    'status' => SubmissionStatus::SUBMITTED,
                    'score' => 100,
                    'total_points' => 100,
                    'percentage' => 100,
                    'passing_score' => (float) ($assignment->passing_score ?? $assignment->passing_grade ?? 70.0),
                    'grade' => 100,
                    'attempt_number' => $attemptNumber ?? 1,
                    'evaluation_notes' => 'Submitted without MSQ questions.',
                ]
            );
        }

        return DB::transaction(function () use ($student, $assignment, $submittedAnswers, $questions, $attemptNumber) {
            $totalPoints = 0.0;
            $earnedPoints = 0.0;
            $answerRecords = [];

            foreach ($questions as $question) {
                $questionPoints = (float) ($question->points ?? 1.0);
                $totalPoints += $questionPoints;

                // Extract official correct option IDs
                $correctOptionIds = $question->options
                    ->filter(fn ($opt) => (bool) $opt->is_correct)
                    ->pluck('id')
                    ->map(fn ($id) => (int) $id)
                    ->sort()
                    ->values()
                    ->toArray();

                // Extract student selection
                $rawSelected = $submittedAnswers[$question->id] ?? $submittedAnswers[(string)$question->id] ?? [];
                if (! is_array($rawSelected)) {
                    $rawSelected = $rawSelected ? [$rawSelected] : [];
                }

                // Verify options belong to current question (Anti-Injection validation)
                $validOptionIds = $question->options->pluck('id')->toArray();
                $selectedOptionIds = collect($rawSelected)
                    ->map(fn ($id) => (int) $id)
                    ->filter(fn ($id) => in_array($id, $validOptionIds, true))
                    ->sort()
                    ->values()
                    ->toArray();

                // Strict MSQ comparison
                $isCorrect = ($correctOptionIds === $selectedOptionIds);
                $pointsEarned = $isCorrect ? $questionPoints : 0.0;
                $earnedPoints += $pointsEarned;

                $answerRecords[] = [
                    'question_id' => $question->id,
                    'selected_option_ids' => $selectedOptionIds,
                    'is_correct' => $isCorrect,
                    'points_earned' => $pointsEarned,
                ];
            }

            $percentage = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100, 2) : 0.0;
            $passingScore = (float) ($assignment->passing_score ?? $assignment->passing_grade ?? 70.0);
            $isPassed = $percentage >= $passingScore;

            $enrollment = CourseEnrollment::where('student_user_id', $student->id)
                ->where('course_id', $assignment->course_id)
                ->first();

            // Store or finalize atomic submission record
            $submission = AssignmentSubmission::updateOrCreate(
                [
                    'assignment_id' => $assignment->id,
                    'student_user_id' => $student->id,
                ],
                [
                    'live_session_id' => $assignment->live_session_id,
                    'course_enrollment_id' => $enrollment?->id ?? 1,
                    'submitted_at' => now(),
                    'status' => SubmissionStatus::COMPLETED,
                    'score' => $earnedPoints,
                    'total_points' => $totalPoints,
                    'percentage' => $percentage,
                    'passing_score' => $passingScore,
                    'grade' => $percentage,
                    'attempt_number' => $attemptNumber ?? 1,
                    'evaluation_notes' => $isPassed
                        ? 'PASSED — Minimum required score achieved.'
                        : 'FAILED — Below required passing threshold.',
                ]
            );

            // Save question answer details idempotently
            foreach ($answerRecords as $record) {
                AssignmentSubmissionAnswer::updateOrCreate(
                    [
                        'submission_id' => $submission->id,
                        'question_id' => $record['question_id'],
                    ],
                    [
                        'selected_option_ids' => $record['selected_option_ids'],
                        'is_correct' => $record['is_correct'],
                        'points_earned' => $record['points_earned'],
                    ]
                );
            }

            // Sync relational StudentSession state
            if ($assignment->live_session_id) {
                StudentSession::updateOrCreate(
                    [
                        'student_user_id' => $student->id,
                        'live_session_id' => $assignment->live_session_id,
                    ],
                    [
                        'assignment_status' => $isPassed ? 'passed' : 'failed',
                        'assignment_score' => $percentage,
                        'session_status' => $isPassed ? 'completed' : 'active',
                        'completed_at' => $isPassed ? now() : null,
                    ]
                );
            }

            return $submission;
        });
    }
}
