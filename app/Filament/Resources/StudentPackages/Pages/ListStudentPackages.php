<?php

namespace App\Filament\Resources\StudentPackages\Pages;

use App\Filament\Resources\StudentPackages\StudentPackageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStudentPackages extends ListRecords
{
    protected static string $resource = StudentPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
