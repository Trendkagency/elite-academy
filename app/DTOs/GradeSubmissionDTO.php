<?php

namespace App\DTOs;

readonly class GradeSubmissionDTO
{
    public function __construct(
        public int $submissionId,
        public int $grade,
        public ?string $feedback = null,
    ) {}

    public static function fromRequest(int $submissionId, array $data): self
    {
        return new self(
            submissionId: $submissionId,
            grade: (int) $data['grade'],
            feedback: $data['teacher_notes'] ?? $data['feedback'] ?? null,
        );
    }
}
