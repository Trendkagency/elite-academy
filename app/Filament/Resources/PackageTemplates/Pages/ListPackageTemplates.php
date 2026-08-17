<?php

namespace App\Filament\Resources\PackageTemplates\Pages;

use App\Filament\Resources\PackageTemplates\PackageTemplateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPackageTemplates extends ListRecords
{
    protected static string $resource = PackageTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
