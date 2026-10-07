<?php

namespace App\Filament\Resources\StudentPackageStatuses\Pages;

use App\Filament\Resources\StudentPackageStatuses\StudentPackageStatusResource;
use App\Filament\Widgets\StudentPackageStatusStatsWidget;
use App\Models\StudentPackage;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListStudentPackageStatuses extends ListRecords
{
    protected static string $resource = StudentPackageStatusResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            StudentPackageStatusStatsWidget::class,
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'all_expired';
    }

    public function getTabs(): array
    {
        $allExpiredCount = StudentPackage::query()
            ->where(function ($q) {
                $q->where('remaining_sessions', '<=', 0)
                    ->orWhere('status', 'exhausted')
                    ->orWhere(function ($sub) {
                        $sub->whereNotNull('expires_at')
                            ->where('expires_at', '<', now());
                    });
            })
            ->count();

        $exhaustedCount = StudentPackage::query()
            ->where(function ($q) {
                $q->where('remaining_sessions', '<=', 0)
                    ->orWhere('status', 'exhausted');
            })
            ->count();

        $expiredDateCount = StudentPackage::query()
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->count();

        $lowBalanceCount = StudentPackage::query()
            ->where('status', 'active')
            ->where('remaining_sessions', '>', 0)
            ->where('remaining_sessions', '<=', 2)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now());
            })
            ->count();

        $allCount = StudentPackage::count();

        return [
            'all_expired' => Tab::make(__('🔴 المنتهية والمنفذة بالكامل'))
                ->badge($allExpiredCount)
                ->badgeColor('danger')
                ->modifyQueryUsing(fn(Builder $query) => $query->where(function ($q) {
                    $q->where('remaining_sessions', '<=', 0)
                        ->orWhere('status', 'exhausted')
                        ->orWhere(function ($sub) {
                            $sub->whereNotNull('expires_at')
                                ->where('expires_at', '<', now());
                        });
                })),

            'exhausted' => Tab::make(__('نفد الرصيد (0 حصص)'))
                ->badge($exhaustedCount)
                ->badgeColor('danger')
                ->modifyQueryUsing(fn(Builder $query) => $query->where(function ($q) {
                    $q->where('remaining_sessions', '<=', 0)
                        ->orWhere('status', 'exhausted');
                })),

            'expired_date' => Tab::make(__('تجاوزت الصلاحية (بالتاريخ)'))
                ->badge($expiredDateCount)
                ->badgeColor('warning')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNotNull('expires_at')->where('expires_at', '<', now())),

            'low_balance' => Tab::make(__('⚠️ رصيد حرج (1-2 حصة)'))
                ->badge($lowBalanceCount)
                ->badgeColor('warning')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'active')->where('remaining_sessions', '>', 0)->where('remaining_sessions', '<=', 2)),

            'all' => Tab::make(__('كافة الاشتراكات'))
                ->badge($allCount)
                ->badgeColor('gray'),
        ];
    }
}
