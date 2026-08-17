<?php

namespace App\Repositories\Eloquent;

use App\Models\AssignmentSubmission;
use App\Repositories\Contracts\SubmissionRepositoryInterface;

class SubmissionRepository implements SubmissionRepositoryInterface
{
    public function find(int $id): ?AssignmentSubmission
    {
        return AssignmentSubmission::with(['assignment.session', 'studentUser', 'enrollment'])->find($id);
    }

    public function create(array $data): AssignmentSubmission
    {
        return AssignmentSubmission::create($data);
    }

    public function grade(AssignmentSubmission $submission, int $grade, string $status, ?string $feedback): AssignmentSubmission
    {
        $submission->update([
            'grade' => $grade,
            'status' => $status,
            'teacher_notes' => $feedback,
            'reviewed_at' => now(),
        ]);

        return $submission;
    }
}
