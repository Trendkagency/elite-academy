<?php

namespace App\Filament\Resources\ParentProfiles\Pages;

use App\Filament\Resources\ParentProfiles\ParentProfileResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditParentProfile extends EditRecord
{
    protected static string $resource = ParentProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
