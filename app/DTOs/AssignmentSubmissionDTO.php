<?php

namespace App\DTOs;

readonly class AssignmentSubmissionDTO
{
    public function __construct(
        public int $assignmentId,
        public int $studentUserId,
        public int $courseEnrollmentId,
        public ?string $content = null,
        public ?string $filePath = null,
    ) {}

    public static function fromRequest(array $data, int $studentUserId, int $courseEnrollmentId): self
    {
        return new self(
            assignmentId: (int) $data['assignment_id'],
            studentUserId: $studentUserId,
            courseEnrollmentId: $courseEnrollmentId,
            content: $data['content'] ?? null,
            filePath: $data['file_path'] ?? null,
        );
    }
}
