<?php

namespace App\Filament\Resources\ExceptionRequests\Pages;

use App\Filament\Resources\ExceptionRequests\ExceptionRequestResource;
use App\Services\Analytics\AdminDashboardMetricsService;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListExceptionRequests extends ListRecords
{
    protected static string $resource = ExceptionRequestResource::class;

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
            'all' => Tab::make($isAr ? 'كافة الطلبات' : 'All Exceptions')
                ->icon('heroicon-o-inbox-stack')
                ->badge($metrics->exceptionsQuery()->count()),
            'pending' => Tab::make($isAr ? 'قيد المراجعة' : 'Pending Review')
                ->icon('heroicon-o-clock')
                ->badge($metrics->exceptionsQuery('pending')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn ($query) => $metrics->applyExceptionsStatus($query, 'pending')),
            'approved' => Tab::make($isAr ? 'مقبولة' : 'Approved')
                ->icon('heroicon-o-check-circle')
                ->badge($metrics->exceptionsQuery('approved')->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn ($query) => $metrics->applyExceptionsStatus($query, 'approved')),
            'rejected' => Tab::make($isAr ? 'مرفوضة' : 'Rejected')
                ->icon('heroicon-o-x-circle')
                ->badge($metrics->exceptionsQuery('rejected')->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn ($query) => $metrics->applyExceptionsStatus($query, 'rejected')),
        ];
    }
}
