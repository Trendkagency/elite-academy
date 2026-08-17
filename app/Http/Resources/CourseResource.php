<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'image' => $this->image ? asset($this->image) : null,
            'is_active' => $this->is_active,
            'subject' => new SubjectResource($this->whenLoaded('subject')),
            'teacher' => [
                'id' => $this->teacher?->id,
                'name' => $this->teacher?->user?->name,
                'title' => $this->teacher?->title,
                'rating_avg' => $this->teacher?->rating_avg,
            ],
            'grade_level' => $this->gradeLevel?->name,
            'sessions' => SessionResource::collection($this->whenLoaded('sessions')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
