<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubmissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'assignment_id' => $this->assignment_id,
            'student' => [
                'id' => $this->studentUser?->id,
                'name' => $this->studentUser?->name,
            ],
            'status' => $this->status?->value ?? $this->status,
            'grade' => $this->grade,
            'teacher_notes' => $this->teacher_notes,
            'submitted_at' => $this->submitted_at?->toIso8601String(),
            'reviewed_at' => $this->reviewed_at?->toIso8601String(),
        ];
    }
}
