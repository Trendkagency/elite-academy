<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssignmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'course_session_id' => $this->course_session_id,
            'title' => $this->title,
            'description' => $this->description,
            'passing_grade' => $this->passing_grade,
            'max_grade' => $this->max_grade,
            'due_at' => $this->due_at?->toIso8601String(),
        ];
    }
}
