<?php

namespace App\Repositories\Contracts;

use App\Models\CourseSession;
use Illuminate\Database\Eloquent\Collection;

interface SessionRepositoryInterface
{
    public function find(int $id): ?CourseSession;
    public function getCourseSessions(int $courseId): Collection;
    public function getNextSession(int $courseId, int $currentOrder): ?CourseSession;
}
