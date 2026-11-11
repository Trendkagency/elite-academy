<?php

namespace App\Filament\Widgets;

use App\Services\Analytics\AdminDashboardMetricsService;
use Filament\Widgets\ChartWidget;

class SessionsAnalyticsChartWidget extends ChartWidget
{
    protected ?string $heading = 'تحليل أداء الحصص المباشرة — Live Sessions Analysis';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        return app(AdminDashboardMetricsService::class)->getSessionsChartData();
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
