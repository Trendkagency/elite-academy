<?php

namespace App\Filament\Resources\ParentProfiles\Pages;

use App\Enums\AccountStatus;
use App\Filament\Resources\ParentProfiles\ParentProfileResource;
use App\Services\Analytics\AdminDashboardMetricsService;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListParentProfiles extends ListRecords
{
    protected static string $resource = ParentProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $metrics = app(AdminDashboardMetricsService::class);
        $isAr = app()->getLocale() === 'ar';

        return [
            'all' => Tab::make($isAr ? 'كافة أولياء الأمور' : 'All Parents')
                ->icon('heroicon-o-users')
                ->badge($metrics->parentsQuery()->count())
                ->modifyQueryUsing(fn ($query) => $metrics->applyUserStatusFilter($query)),
            'approved' => Tab::make($isAr ? 'المعتمدون' : 'Approved')
                ->icon('heroicon-o-check-circle')
                ->badge($metrics->parentsQuery(AccountStatus::APPROVED)->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn ($query) => $metrics->applyUserStatusFilter($query, AccountStatus::APPROVED)),
            'pending' => Tab::make($isAr ? 'قيد المراجعة' : 'Pending')
                ->icon('heroicon-o-clock')
                ->badge($metrics->parentsQuery(AccountStatus::PENDING)->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn ($query) => $metrics->applyUserStatusFilter($query, AccountStatus::PENDING)),
        ];
    }
}
