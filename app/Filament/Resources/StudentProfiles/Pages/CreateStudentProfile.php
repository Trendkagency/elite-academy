<?php

namespace App\Filament\Resources\StudentProfiles\Pages;

use App\Filament\Resources\StudentProfiles\StudentProfileResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStudentProfile extends CreateRecord
{
    protected static string $resource = StudentProfileResource::class;

    protected function afterCreate(): void
    {
        $status = $this->data['user_status'] ?? $this->form->getRawState()['user_status'] ?? null;
        if ($status && $this->record->user) {
            $this->record->user->update(['status' => $status]);
        }
    }
}
