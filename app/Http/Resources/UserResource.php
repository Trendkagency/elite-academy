<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status?->value ?? $this->status,
            'is_admin' => $this->isAdmin(),
            'is_teacher' => $this->isTeacher(),
            'is_student' => $this->isStudent(),
            'is_parent' => $this->isParent(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
