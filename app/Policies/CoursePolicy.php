<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Course $course): bool
    {
        return $course->is_active || ($user && ($user->isAdmin() || ($user->isTeacher() && $course->teacher_id === $user->teacherProfile?->id)));
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isTeacher();
    }

    public function update(User $user, Course $course): bool
    {
        return $user->isAdmin() || ($user->isTeacher() && $course->teacher_id === $user->teacherProfile?->id);
    }

    public function delete(User $user, Course $course): bool
    {
        return $user->isAdmin();
    }
}
