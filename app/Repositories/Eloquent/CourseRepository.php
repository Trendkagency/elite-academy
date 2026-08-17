<?php

namespace App\Repositories\Eloquent;

use App\Enums\CourseStatus;
use App\Models\Course;
use App\Repositories\Contracts\CourseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CourseRepository implements CourseRepositoryInterface
{
    public function find(int $id): ?Course
    {
        return Course::with(['subject', 'teacher.user', 'gradeLevel', 'sessions'])->find($id);
    }

    public function getPublishedCourses(): Collection
    {
        return Course::where('is_active', true)
            ->with(['subject', 'teacher.user', 'gradeLevel'])
            ->get();
    }

    public function getTeacherCourses(int $teacherProfileId): Collection
    {
        return Course::where('teacher_id', $teacherProfileId)->get();
    }

    public function create(array $data): Course
    {
        return Course::create($data);
    }
}
