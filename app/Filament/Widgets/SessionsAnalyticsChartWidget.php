<?php

namespace App\Filament\Widgets;

use App\Models\LiveSession;
use Filament\Widgets\ChartWidget;

class SessionsAnalyticsChartWidget extends ChartWidget
{
    protected ?string $heading = 'تحليل أداء الحصص المباشرة — Live Sessions Analysis';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $today = LiveSession::whereDate('scheduled_at', today())->orWhereDate('start_at', today())->count();
        $upcoming = LiveSession::where(function ($q) {
            $q->where('scheduled_at', '>', now())->orWhere('start_at', '>', now());
        })->whereNotIn('status', ['completed', 'cancelled', 'cancelled_by_teacher'])->count();
        $completed = LiveSession::where('status', 'completed')->count();
        $cancelled = LiveSession::whereIn('status', ['cancelled', 'cancelled_by_teacher'])->count();

        return [
            'datasets' => [
                [
                    'label' => 'Live Sessions',
                    'data' => [$today, $upcoming, $completed, $cancelled],
                    'backgroundColor' => ['#f59e0b', '#3b82f6', '#10b981', '#ef4444'],
                ],
            ],
            'labels' => [
                app()->getLocale() === 'ar' ? 'حصص اليوم' : "Today's Sessions",
                app()->getLocale() === 'ar' ? 'الحصص القادمة' : 'Upcoming Sessions',
                app()->getLocale() === 'ar' ? 'الحصص المكتملة' : 'Completed Sessions',
                app()->getLocale() === 'ar' ? 'الحصص الملغاة' : 'Cancelled Sessions',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
