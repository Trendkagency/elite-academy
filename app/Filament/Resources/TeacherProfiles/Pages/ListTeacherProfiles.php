<?php

namespace App\Filament\Resources\TeacherProfiles\Pages;

use App\Enums\AccountStatus;
use App\Filament\Resources\TeacherProfiles\TeacherProfileResource;
use App\Services\Analytics\AdminDashboardMetricsService;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListTeacherProfiles extends ListRecords
{
    protected static string $resource = TeacherProfileResource::class;

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
            'all' => Tab::make($isAr ? 'كافة المعلمين' : 'All Teachers')
                ->icon('heroicon-o-academic-cap')
                ->badge($metrics->teachersQuery()->count())
                ->modifyQueryUsing(fn ($query) => $metrics->applyUserStatusFilter($query)),
            'approved' => Tab::make($isAr ? 'المعتمدون' : 'Approved')
                ->icon('heroicon-o-check-circle')
                ->badge($metrics->teachersQuery(AccountStatus::APPROVED)->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn ($query) => $metrics->applyUserStatusFilter($query, AccountStatus::APPROVED)),
            'pending' => Tab::make($isAr ? 'قيد المراجعة' : 'Pending Approval')
                ->icon('heroicon-o-clock')
                ->badge($metrics->teachersQuery(AccountStatus::PENDING)->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn ($query) => $metrics->applyUserStatusFilter($query, AccountStatus::PENDING)),
        ];
    }
}
