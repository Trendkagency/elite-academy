<?php

namespace App\Repositories\Contracts;

use App\Models\Course;
use Illuminate\Database\Eloquent\Collection;

interface CourseRepositoryInterface
{
    public function find(int $id): ?Course;
    public function getPublishedCourses(): Collection;
    public function getTeacherCourses(int $teacherProfileId): Collection;
    public function create(array $data): Course;
}
