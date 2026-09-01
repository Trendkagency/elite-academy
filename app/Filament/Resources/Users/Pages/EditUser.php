<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\AdminProfile;
use App\Models\ParentProfile;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $role = $this->data['assigned_role'] ?? null;
        $user = $this->record;

        if ($role === 'student') {
            $profile = StudentProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'grade_level_id' => $this->data['grade_level_id'] ?? null,
                    'school_name'    => $this->data['school_name'] ?? null,
                ]
            );
            if (isset($this->data['student_subjects'])) {
                $profile->subjects()->sync($this->data['student_subjects']);
            }
        } elseif ($role === 'teacher') {
            TeacherProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'slug'              => $user->teacherProfile?->slug ?: (Str::slug($user->name) . '-' . $user->id),
                    'title'             => $this->data['teacher_title'] ?? null,
                    'specialization'    => $this->data['teacher_specialization'] ?? null,
                    'years_experience'  => $this->data['teacher_experience'] ?? 5,
                ]
            );
        } elseif ($role === 'parent') {
            ParentProfile::firstOrCreate(['user_id' => $user->id]);
            if (isset($this->data['parent_students'])) {
                $user->children()->sync($this->data['parent_students']);
            }
        } elseif ($role === 'admin') {
            AdminProfile::firstOrCreate(['user_id' => $user->id]);
        }
    }
}
