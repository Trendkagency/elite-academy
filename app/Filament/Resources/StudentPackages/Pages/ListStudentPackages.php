<?php

namespace App\Filament\Resources\StudentPackages\Pages;

use App\Filament\Resources\StudentPackages\StudentPackageResource;
use App\Models\StudentPackage;
use Filament\Actions\CreateAction;
use Filament\Schemas\Components\Tabs\Tab;
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

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Packages (جميع الباقات)')
                ->badge(StudentPackage::count()),
            'active' => Tab::make('🟢 Active Packages (الباقات النشطة)')
                ->badge(StudentPackage::where('status', 'active')->count())
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'active')),
            'pending' => Tab::make('⏳ Pending Payments (المدفوعات المعلقة)')
                ->badge(StudentPackage::where('status', 'pending')->count())
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'pending')),
        ];
    }
}
