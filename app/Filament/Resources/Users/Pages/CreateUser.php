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

        $user->syncAssignedRole($role, $this->data);
    }
}
