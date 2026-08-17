<?php

namespace App\Http\Controllers\Api;

use App\Actions\Submission\GradeSubmissionAction;
use App\DTOs\AssignmentSubmissionDTO;
use App\DTOs\GradeSubmissionDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Submission\GradeSubmissionRequest;
use App\Http\Requests\Submission\SubmitAssignmentRequest;
use App\Http\Resources\SubmissionResource;
use App\Models\CourseEnrollment;
use App\Repositories\Contracts\SubmissionRepositoryInterface;
use Illuminate\Support\Facades\Gate;

class SubmissionController extends Controller
{
    public function __construct(
        protected SubmissionRepositoryInterface $submissionRepository,
        protected GradeSubmissionAction $gradeSubmissionAction
    ) {}

    public function submit(SubmitAssignmentRequest $request)
    {
        $user = auth()->user();
        $validated = $request->validated();

        $enrollment = CourseEnrollment::where('student_user_id', $user->id)
            ->where('course_id', $validated['course_id'])
            ->first();

        if (! $enrollment) {
            return response()->json(['message' => 'You must be enrolled in the course to submit assignments'], 403);
        }

        $dto = AssignmentSubmissionDTO::fromRequest($validated, $user->id, $enrollment->id);

        $submission = $this->submissionRepository->create([
            'assignment_id' => $dto->assignmentId,
            'student_user_id' => $dto->studentUserId,
            'course_enrollment_id' => $dto->courseEnrollmentId,
            'status' => \App\Enums\SubmissionStatus::SUBMITTED->value,
            'submitted_at' => now(),
        ]);

        return response()->json([
            'message' => 'Assignment submitted successfully',
            'submission' => new SubmissionResource($submission),
        ], 201);
    }

    public function grade(GradeSubmissionRequest $request, int $id)
    {
        $submission = $this->submissionRepository->find($id);

        if (! $submission) {
            return response()->json(['message' => 'Submission not found'], 404);
        }

        if (Gate::denies('grade', $submission)) {
            return response()->json(['message' => 'Unauthorized to grade this submission'], 403);
        }

        $dto = GradeSubmissionDTO::fromRequest($id, $request->validated());

        $gradedSubmission = $this->gradeSubmissionAction->execute(
            $submission,
            $dto->grade,
            $dto->feedback
        );

        return response()->json([
            'message' => 'Submission graded successfully. Next session unlocked if passed.',
            'submission' => new SubmissionResource($gradedSubmission),
        ]);
    }
}
