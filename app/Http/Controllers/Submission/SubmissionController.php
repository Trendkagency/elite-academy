<?php

namespace App\Http\Controllers\Submission;

use App\Actions\Submission\GradeSubmissionAction;
use App\Enums\SubmissionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Submission\GradeSubmissionRequest;
use App\Http\Requests\Submission\SubmitAssignmentRequest;
use App\Models\AssignmentSubmission;
use App\Models\CourseEnrollment;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class SubmissionController extends Controller
{
    public function submit(SubmitAssignmentRequest $request): JsonResponse
    {
        $user = auth()->user();
        $validated = $request->validated();

        $enrollment = CourseEnrollment::where('student_user_id', $user->id)
            ->where('course_id', $validated['course_id'])
            ->first();

        if (! $enrollment) {
            return response()->json(['success' => false, 'message' => 'You must be enrolled in the course to submit assignments'], 403);
        }

        $submission = AssignmentSubmission::create([
            'assignment_id' => $validated['assignment_id'],
            'student_user_id' => $user->id,
            'course_enrollment_id' => $enrollment->id,
            'status' => SubmissionStatus::SUBMITTED->value,
            'submitted_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Assignment submitted successfully!',
            'submission_id' => $submission->id,
        ], 201);
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
