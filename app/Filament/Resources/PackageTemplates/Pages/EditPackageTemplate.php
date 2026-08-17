<?php

namespace App\Filament\Resources\PackageTemplates\Pages;

use App\Filament\Resources\PackageTemplates\PackageTemplateResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPackageTemplate extends EditRecord
{
    protected static string $resource = PackageTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
