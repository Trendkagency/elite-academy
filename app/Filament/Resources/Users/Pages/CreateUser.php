<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\AdminProfile;
use App\Models\ParentProfile;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        $role = $this->data['assigned_role'] ?? 'student';
        $user = $this->record;

        if ($role === 'student') {
            $profile = StudentProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'grade_level_id' => $this->data['grade_level_id'] ?? null,
                    'school_name'    => $this->data['school_name'] ?? null,
                ]
            );
            if (! empty($this->data['student_subjects'])) {
                $profile->subjects()->sync($this->data['student_subjects']);
            }
        } elseif ($role === 'teacher') {
            TeacherProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'slug'              => Str::slug($user->name) . '-' . $user->id,
                    'title'             => $this->data['teacher_title'] ?? null,
                    'specialization'    => $this->data['teacher_specialization'] ?? null,
                    'years_experience'  => $this->data['teacher_experience'] ?? 5,
                ]
            );
        } elseif ($role === 'parent') {
            ParentProfile::firstOrCreate(['user_id' => $user->id]);
            if (! empty($this->data['parent_students'])) {
                $user->children()->sync($this->data['parent_students']);
            }
        } elseif ($role === 'admin') {
            AdminProfile::firstOrCreate(['user_id' => $user->id]);
        }
    }
}
