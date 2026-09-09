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
        if ($role) {
            $this->record->syncAssignedRole($role, $this->data);
        }
    }
}
