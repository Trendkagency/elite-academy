<?php

namespace App\Filament\Widgets;

use App\Models\StudentPackage;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StudentPackageStatusStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // 1. Total Expired / Exhausted Packages
        $totalExpiredOrExhausted = StudentPackage::query()
            ->where(function ($q) {
                $q->where('remaining_sessions', '<=', 0)
                  ->orWhere('status', 'exhausted')
                  ->orWhere(function ($sub) {
                      $sub->whereNotNull('expires_at')
                          ->where('expires_at', '<', now());
                  });
            })
            ->count();

        // 2. Zero Sessions Remaining (Fully Exhausted)
        $zeroCreditsCount = StudentPackage::query()
            ->where(function ($q) {
                $q->where('remaining_sessions', '<=', 0)
                  ->orWhere('status', 'exhausted');
            })
            ->count();

        // 3. Past Expiration Date (Expired by Date)
        $pastExpiryDateCount = StudentPackage::query()
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->count();

        // 4. Low Balance Alert (1-2 sessions left, active)
        $lowBalanceCount = StudentPackage::query()
            ->where('status', 'active')
            ->where('remaining_sessions', '>', 0)
            ->where('remaining_sessions', '<=', 2)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>=', now());
            })
            ->count();

        return [
            Stat::make(
                __('الطلاب منتهيي الباقات (الإجمالي)'),
                $totalExpiredOrExhausted
            )
            ->description(__('طلاب بحاجة لتجديد عاجل وتحصيل الرسوم'))
            ->descriptionIcon('heroicon-m-exclamation-triangle')
            ->color('danger')
            ->chart([$totalExpiredOrExhausted + 4, $totalExpiredOrExhausted + 2, $totalExpiredOrExhausted + 1, $totalExpiredOrExhausted]),

            Stat::make(
                __('نفد الرصيد بالكامل (0 حصص)'),
                $zeroCreditsCount
            )
            ->description(__('استهلكوا 100% من حصص الباقة'))
            ->descriptionIcon('heroicon-m-x-circle')
            ->color('danger')
            ->chart([$zeroCreditsCount + 3, $zeroCreditsCount + 1, $zeroCreditsCount]),

            Stat::make(
                __('تجاوزت تاريخ الصلاحية'),
                $pastExpiryDateCount
            )
            ->description(__('انتهت المدة الزمنية المحددة للباقة'))
            ->descriptionIcon('heroicon-m-clock')
            ->color('warning')
            ->chart([$pastExpiryDateCount + 2, $pastExpiryDateCount + 1, $pastExpiryDateCount]),

            Stat::make(
                __('أوشكت على النفاد (1-2 حصة)'),
                $lowBalanceCount
            )
            ->description(__('متابعة استباقية قبل توقف الطالب'))
            ->descriptionIcon('heroicon-m-bolt')
            ->color('warning')
            ->chart([$lowBalanceCount + 1, $lowBalanceCount + 2, $lowBalanceCount]),
        ];
    }
}
