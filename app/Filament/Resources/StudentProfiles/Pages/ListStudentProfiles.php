<?php

namespace App\Filament\Resources\StudentProfiles\Pages;

use App\Enums\AccountStatus;
use App\Filament\Resources\StudentProfiles\StudentProfileResource;
use App\Services\Analytics\AdminDashboardMetricsService;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListStudentProfiles extends ListRecords
{
    protected static string $resource = StudentProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $metrics = app(AdminDashboardMetricsService::class);

        return [
            'all' => Tab::make(__('All Students'))
                ->icon('heroicon-o-user-group')
                ->badge($metrics->studentsQuery()->count())
                ->modifyQueryUsing(fn ($query) => $metrics->applyUserStatusFilter($query)),
            'pending' => Tab::make(__('Pending Approval'))
                ->icon('heroicon-o-clock')
                ->badge($metrics->studentsQuery(AccountStatus::PENDING)->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn ($query) => $metrics->applyUserStatusFilter($query, AccountStatus::PENDING)),
            'approved' => Tab::make(__('Approved'))
                ->icon('heroicon-o-check-circle')
                ->badge($metrics->studentsQuery(AccountStatus::APPROVED)->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn ($query) => $metrics->applyUserStatusFilter($query, AccountStatus::APPROVED)),
        ];
    }
}
