<?php

namespace App\Repositories\Contracts;

use App\Models\AssignmentSubmission;

interface SubmissionRepositoryInterface
{
    public function find(int $id): ?AssignmentSubmission;
    public function create(array $data): AssignmentSubmission;
    public function grade(AssignmentSubmission $submission, int $grade, string $status, ?string $feedback): AssignmentSubmission;
}
