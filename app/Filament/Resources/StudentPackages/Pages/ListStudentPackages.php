<?php

namespace App\Filament\Resources\StudentPackages\Pages;

use App\Filament\Resources\StudentPackages\StudentPackageResource;
use App\Models\StudentPackage;
use App\Services\Analytics\AdminDashboardMetricsService;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

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
        $metrics = app(AdminDashboardMetricsService::class);

        return [
            'all' => Tab::make(__('All Packages'))
                ->icon('heroicon-o-ticket')
                ->badge(StudentPackage::count()),
            'healthy' => Tab::make(app()->getLocale() === 'ar' ? 'باقات نشطة برصيد متاح' : 'Healthy Active Credits')
                ->icon('heroicon-o-shield-check')
                ->badge($metrics->packagesHealthyQuery()->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn ($query) => $metrics->applyPackagesHealthy($query)),
            'active' => Tab::make(__('Active Packages'))
                ->icon('heroicon-o-check-circle')
                ->badge($metrics->packagesActiveQuery()->count())
                ->modifyQueryUsing(fn ($query) => $metrics->applyPackagesActive($query)),
            'pending' => Tab::make(__('Pending Payments'))
                ->icon('heroicon-o-clock')
                ->badge($metrics->packagesPendingQuery()->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn ($query) => $metrics->applyPackagesPending($query)),
            'exhausted' => Tab::make(app()->getLocale() === 'ar' ? 'مستنفدة / منتهية الرصيد' : 'Exhausted / Zero Credits')
                ->icon('heroicon-o-exclamation-circle')
                ->badge($metrics->packagesExhaustedQuery()->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn ($query) => $metrics->applyPackagesExhausted($query)),
        ];
    }
}
