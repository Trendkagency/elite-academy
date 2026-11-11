<?php

namespace App\Filament\Widgets;

use App\Services\Analytics\AdminDashboardMetricsService;
use Filament\Widgets\ChartWidget;

class StudentAccountStatusChartWidget extends ChartWidget
{
    protected ?string $heading = 'توزيع حالات حسابات الطلاب — Student Status Breakdown';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        return app(AdminDashboardMetricsService::class)->getStudentStatusChartData();
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
