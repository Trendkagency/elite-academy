<?php

namespace App\Filament\Resources\StudentPackages\Pages;

use App\Filament\Resources\StudentPackages\StudentPackageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStudentPackages extends ListRecords
{
    protected static string $resource = StudentPackageResource::class;

    public function getTitle(): string
    {
        return '🎟️ Student Packages & Credits';
    }

    public function getSubheading(): ?string
    {
        return 'Manage session credits assigned to students. Use "Assign Package to Student" to issue a new package.';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('📦 Assign Package to Student')
                ->color('primary'),
        ];
    }
}
