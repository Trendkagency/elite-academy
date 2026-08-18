<?php

namespace App\Http\Controllers\Submission;

use App\Actions\Submission\GradeSubmissionAction;
use App\Enums\SubmissionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Submission\GradeSubmissionRequest;
use App\Http\Requests\Submission\SubmitAssignmentRequest;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Services\Assignment\AssignmentEvaluationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class SubmissionController extends Controller
{
    /**
     * Display dedicated interactive animated assignment page.
     */
    public function take(int $id)
    {
        $user = auth()->user();
        $assignment = Assignment::with(['questions.options', 'course', 'liveSession'])->findOrFail($id);

        $savedAnswers = [];
        $remainingSeconds = ($assignment->duration_minutes ?? 30) * 60;
        $currentStepIndex = 0;
        $previousSubmission = null;

        if ($user) {
            $enrollment = \App\Models\CourseEnrollment::where('student_user_id', $user->id)
                ->where('course_id', $assignment->course_id)
                ->first();

            // Find or atomically create active in_progress attempt
            $previousSubmission = AssignmentSubmission::where('assignment_id', $assignment->id)
                ->where('student_user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->first();

            $isCompleted = $previousSubmission && in_array($previousSubmission->status, [SubmissionStatus::COMPLETED, SubmissionStatus::SUBMITTED, SubmissionStatus::REVIEWED]);

            if ($isCompleted) {
                return redirect()->route('student-portal')->with('info', 'You have already completed this assignment.');
            }

            if (! $previousSubmission) {
                $hasActivePackage = \App\Models\StudentPackage::where('student_user_id', $user->id)
                    ->where('status', 'active')
                    ->where('remaining_sessions', '>', 0)
                    ->where(function ($q) {
                        $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                    })
                    ->exists();

                if (! $hasActivePackage) {
                    return redirect()->route('student-portal')->with('error', 'An active package subscription with available session credits is required to access and solve assignments.');
                }

                if ($assignment->status !== 'published') {
                    return redirect()->route('student-portal')->with('error', 'This assignment is currently unavailable.');
                }

                if ($assignment->start_at && now()->lessThan($assignment->start_at)) {
                    return redirect()->route('student-portal')->with('error', 'This assignment start time has not arrived yet.');
                }

                if ($assignment->isExpired()) {
                    return redirect()->route('student-portal')->with('error', 'This assignment deadline has passed and can no longer be answered.');
                }

                $previousSubmission = AssignmentSubmission::create([
                    'assignment_id' => $assignment->id,
                    'student_user_id' => $user->id,
                    'course_enrollment_id' => $enrollment?->id ?? 1,
                    'status' => SubmissionStatus::IN_PROGRESS,
                    'started_at' => now(),
                    'attempt_number' => 1,
                    'current_step_index' => 0,
                ]);
            }

            if ($previousSubmission) {
                $previousSubmission->load('answers');
                foreach ($previousSubmission->answers as $ans) {
                    $rawOpts = $ans->selected_option_ids;
                    if (is_string($rawOpts)) {
                        $rawOpts = json_decode($rawOpts, true) ?: [$rawOpts];
                    }
                    if (! is_array($rawOpts)) {
                        $rawOpts = [$rawOpts];
                    }
                    $optsInt = array_values(array_filter(array_map('intval', $rawOpts)));
                    $savedAnswers[(string) $ans->question_id] = $optsInt;
                    $savedAnswers[(int) $ans->question_id] = $optsInt;
                }

                // Server-Authoritative Timer Calculation
                $startedAt = $previousSubmission->started_at ?: $previousSubmission->created_at ?: now();
                $durationSeconds = ($assignment->duration_minutes ?? 30) * 60;
                $elapsedSeconds = (int) abs(now()->diffInSeconds($startedAt));
                $remainingSeconds = max(0, $durationSeconds - $elapsedSeconds);
                $currentStepIndex = (int) ($previousSubmission->current_step_index ?? 0);
            }
        }

        return view('pages.student-assignment-take', [
            'pageTitle' => $assignment->title . ' — Interactive MSQ Examination',
            'activeNav' => 'portal',
            'assignment' => $assignment,
            'previousSubmission' => $previousSubmission,
            'savedAnswers' => $savedAnswers,
            'remainingSeconds' => $remainingSeconds,
            'currentStepIndex' => $currentStepIndex,
        ]);
    }

    /**
     * Persist current question step index when student navigates questions.
     */
    public function updateStepIndex(\Illuminate\Http\Request $request): JsonResponse
    {
        $user = auth()->user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $validated = $request->validate([
            'assignment_id' => 'required|integer|exists:assignments,id',
            'current_step_index' => 'required|integer|min:0',
        ]);

        $submission = AssignmentSubmission::where('assignment_id', $validated['assignment_id'])
            ->where('student_user_id', $user->id)
            ->whereNotIn('status', [SubmissionStatus::COMPLETED, SubmissionStatus::SUBMITTED, SubmissionStatus::REVIEWED])
            ->first();

        if ($submission) {
            $submission->update(['current_step_index' => $validated['current_step_index']]);
        }

        return response()->json(['success' => true, 'current_step_index' => $validated['current_step_index']]);
    }

    /**
     * Idempotently auto-save a single draft answer to backend database on change.
     */
    public function saveDraftAnswer(\Illuminate\Http\Request $request): JsonResponse
    {
        $user = auth()->user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $validated = $request->validate([
            'assignment_id' => 'required|integer|exists:assignments,id',
            'question_id' => 'required|integer|exists:assignment_questions,id',
            'selected_option_ids' => 'present|array',
            'selected_option_ids.*' => 'integer|exists:assignment_question_options,id',
        ]);

        $assignment = Assignment::findOrFail($validated['assignment_id']);

        if ($assignment->status === 'draft' || $assignment->status === 'closed') {
            return response()->json(['success' => false, 'message' => 'Assignment is closed.'], 403);
        }

        if ($assignment->isExpired()) {
            return response()->json(['success' => false, 'message' => 'Assignment deadline has passed.'], 403);
        }

        // Verify if student already submitted a completed attempt
        $completedSubmission = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('student_user_id', $user->id)
            ->whereIn('status', [SubmissionStatus::COMPLETED, SubmissionStatus::SUBMITTED, SubmissionStatus::REVIEWED])
            ->first();

        if ($completedSubmission) {
            return response()->json(['success' => false, 'message' => 'Assignment already submitted and finalized. Answer modifications are forbidden.'], 403);
        }

        // Verify question belongs to assignment
        $question = \App\Models\AssignmentQuestion::where('id', $validated['question_id'])
            ->where('assignment_id', $assignment->id)
            ->first();

        if (! $question) {
            return response()->json(['success' => false, 'message' => 'Question does not belong to assignment.'], 422);
        }

        $enrollment = \App\Models\CourseEnrollment::where('student_user_id', $user->id)
            ->where('course_id', $assignment->course_id)
            ->first();

        // Find or create active draft submission attempt cleanly
        $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('student_user_id', $user->id)
            ->whereNotIn('status', [SubmissionStatus::COMPLETED, SubmissionStatus::SUBMITTED, SubmissionStatus::REVIEWED])
            ->first();

        if (! $submission) {
            $submission = AssignmentSubmission::create([
                'assignment_id' => $assignment->id,
                'student_user_id' => $user->id,
                'course_enrollment_id' => $enrollment?->id ?? 1,
                'status' => SubmissionStatus::IN_PROGRESS,
                'started_at' => now(),
                'attempt_number' => 1,
            ]);
        }

        // Update/create draft answer idempotently
        $answer = \App\Models\AssignmentSubmissionAnswer::updateOrCreate(
            [
                'submission_id' => $submission->id,
                'question_id' => $question->id,
            ],
            [
                'selected_option_ids' => array_values(array_unique($validated['selected_option_ids'])),
                'is_correct' => null,
                'points_earned' => null,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Draft answer saved to server successfully.',
            'question_id' => $question->id,
            'selected_option_ids' => $answer->selected_option_ids,
            'saved_at' => now()->toIso8601String(),
        ]);
    }

    /**
     * Get details of an assignment including safe MSQ questions and options (without is_correct).
     */
    public function show(int $id): JsonResponse
    {
        $user = auth()->user();
        $assignment = Assignment::with(['questions.options', 'session', 'liveSession', 'course'])->find($id);

        if (! $assignment) {
            return response()->json(['success' => false, 'message' => 'Assignment not found'], 404);
        }

        // Check if student has already submitted or has draft saved answers
        $latestSubmission = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('student_user_id', $user->id)
            ->with('answers')
            ->orderBy('created_at', 'desc')
            ->first();

        $savedAnswers = [];
        if ($latestSubmission && $latestSubmission->answers) {
            foreach ($latestSubmission->answers as $ans) {
                $savedAnswers[$ans->question_id] = $ans->selected_option_ids ?: [];
            }
        }

        // Map safe questions (hide is_correct)
        $questions = $assignment->questions->map(function ($q) {
            return [
                'id' => $q->id,
                'question_text' => $q->question_text,
                'image_path' => $q->image_path ? asset('storage/' . $q->image_path) : null,
                'question_type' => $q->question_type,
                'points' => $q->points,
                'is_multiple_choice' => (bool) $q->is_multiple_choice,
                'options' => $q->options->map(function ($opt) {
                    return [
                        'id' => $opt->id,
                        'option_text' => $opt->option_text,
                        'image_path' => $opt->image_path ? asset('storage/' . $opt->image_path) : null,
                    ];
                }),
            ];
        });

        return response()->json([
            'success' => true,
            'assignment' => [
                'id' => $assignment->id,
                'title' => $assignment->title,
                'description' => $assignment->description,
                'duration_minutes' => $assignment->duration_minutes ?? 30,
                'due_at' => $assignment->due_at?->toIso8601String(),
                'passing_score' => $assignment->passing_score ?? $assignment->passing_grade ?? 70,
                'is_expired' => $assignment->isExpired(),
                'questions' => $questions,
            ],
            'saved_answers' => $savedAnswers,
            'previous_submission' => $latestSubmission ? [
                'id' => $latestSubmission->id,
                'status' => $latestSubmission->status->value ?? (string)$latestSubmission->status,
                'score' => $latestSubmission->score,
                'percentage' => $latestSubmission->percentage,
                'passing_score' => $latestSubmission->passing_score,
                'is_passed' => $latestSubmission->isPassed(),
                'submitted_at' => $latestSubmission->submitted_at?->diffForHumans(),
            ] : null,
        ]);
    }

    /**
     * Submit student MSQ answers and evaluate server-side.
     */
    public function submit(SubmitAssignmentRequest $request, AssignmentEvaluationService $evaluationService): JsonResponse
    {
        $user = auth()->user();
        $validated = $request->validated();

        $assignment = Assignment::findOrFail($validated['assignment_id']);

        try {
            $submission = $evaluationService->submitAndEvaluate($user, $assignment, $validated['answers'] ?? []);

            $isPassed = $submission->isPassed();
            $message = $isPassed
                ? (__('app.submissions.submitted_success') . ' ' . (app()->getLocale() === 'ar' ? 'اجتزت الواجب بنجاح!' : 'Passed successfully!'))
                : (app()->getLocale() === 'ar' ? 'لم تتجاوز درجة النجاح المطلوبة.' : 'Did not reach minimum passing grade.');

            return response()->json([
                'success' => true,
                'message' => $message,
                'is_passed' => $isPassed,
                'score' => $submission->score,
                'total_points' => $submission->total_points,
                'percentage' => $submission->percentage,
                'passing_score' => $submission->passing_score,
                'submission_id' => $submission->id,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'Error processing assignment evaluation.',
            ], 422);
        }
    }

    /**
     * Record anti-cheating security telemetry (tab switches, window blur, copy/screenshot attempts).
     */
    public function logSecurityAudit(\Illuminate\Http\Request $request, int $id, \App\Services\Assignment\AssignmentSecurityService $securityService): JsonResponse
    {
        $user = auth()->user();
        $assignment = Assignment::findOrFail($id);

        $eventType = $request->input('event_type', 'WINDOW_BLUR');
        $metadata = $request->input('metadata', []);

        $riskScores = [
            'TAB_SWITCH' => 2,
            'WINDOW_BLUR' => 1,
            'FULLSCREEN_EXITED' => 3,
            'COPY_ATTEMPT' => 3,
            'SCREENSHOT_ATTEMPT' => 5,
        ];

        $score = $riskScores[$eventType] ?? 1;

        $audit = $securityService->logEvent($user, $assignment, $eventType, $metadata, $score);

        return response()->json([
            'success' => true,
            'audit_id' => $audit->id,
            'message' => 'Security telemetry logged.',
        ]);
    }

    public function grade(GradeSubmissionRequest $request, int $id, GradeSubmissionAction $action): JsonResponse
    {
        $submission = AssignmentSubmission::with(['assignment.session.course', 'enrollment'])->find($id);

        if (! $submission) {
            return response()->json(['success' => false, 'message' => 'Submission not found'], 404);
        }

        if (Gate::denies('grade', $submission)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized to grade this submission'], 403);
        }

        $validated = $request->validated();
        $gradedSubmission = $action->execute(
            $submission,
            (int) $validated['grade'],
            $validated['teacher_notes'] ?? null
        );

        return response()->json([
            'success' => true,
            'message' => 'Submission graded successfully! Next session unlocked if passed.',
            'grade' => $gradedSubmission->grade,
            'status' => $gradedSubmission->status,
        ]);
    }
}
