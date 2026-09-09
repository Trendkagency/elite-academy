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
        return __('Student Packages & Credits');
    }

    public function getSubheading(): ?string
    {
        return __('Manage session credits assigned to students. Use "Assign Package to Student" to issue a new package.');
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('Assign Package to Student'))
                ->icon('heroicon-o-plus-circle')
                ->color('primary'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('All Packages'))
                ->icon('heroicon-o-ticket')
                ->badge(StudentPackage::count()),
            'active' => Tab::make(__('Active Packages'))
                ->icon('heroicon-o-check-circle')
                ->badge(StudentPackage::where('status', 'active')->count())
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'active')),
            'pending' => Tab::make(__('Pending Payments'))
                ->icon('heroicon-o-clock')
                ->badge(StudentPackage::where('status', 'pending')->count())
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'pending')),
        ];
    }
}
