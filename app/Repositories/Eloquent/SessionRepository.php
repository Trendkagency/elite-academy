<?php

namespace App\Repositories\Eloquent;

use App\Models\CourseSession;
use App\Repositories\Contracts\SessionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class SessionRepository implements SessionRepositoryInterface
{
    public function find(int $id): ?CourseSession
    {
        return CourseSession::with(['course', 'assignments'])->find($id);
    }

    public function getCourseSessions(int $courseId): Collection
    {
        return CourseSession::where('course_id', $courseId)
            ->orderBy('sort_order', 'asc')
            ->get();
    }

    public function getNextSession(int $courseId, int $currentOrder): ?CourseSession
    {
        return CourseSession::where('course_id', $courseId)
            ->where('sort_order', '>', $currentOrder)
            ->orderBy('sort_order', 'asc')
            ->first();
    }
}
