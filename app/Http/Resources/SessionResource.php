<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SessionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'course_id' => $this->course_id,
            'title' => $this->title,
            'description' => $this->description,
            'sort_order' => $this->sort_order,
            'duration_minutes' => $this->duration_minutes,
            'is_free_demo' => $this->is_free_demo,
            'video_url' => $this->video_url,
            'assignments' => AssignmentResource::collection($this->whenLoaded('assignments')),
        ];
    }
}
