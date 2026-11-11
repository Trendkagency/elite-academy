<?php

namespace App\Filament\Widgets;

use App\Services\Analytics\AdminDashboardMetricsService;
use Filament\Widgets\Widget;

class AdminOverviewStatsWidget extends Widget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.admin-overview-stats-widget';

    public string $categoryFilter = 'all';

    public string $timeframe = 'all';

    public ?string $inspectedKpiKey = null;

    public bool $showDetailModal = false;

    public function setCategoryFilter(string $filter): void
    {
        if (in_array($filter, ['all', 'financials', 'operations', 'academic'], true)) {
            $this->categoryFilter = $filter;
        }
    }

    public function setTimeframe(string $timeframe): void
    {
        if (in_array($timeframe, ['all', 'today', 'month', 'year'], true)) {
            $this->timeframe = $timeframe;
        }
    }

    public function inspectKpi(string $key): void
    {
        $overview = $this->getKpiData();

        if (! isset($overview['kpis'][$key])) {
            return;
        }

        $this->inspectedKpiKey = $key;
        $this->showDetailModal = true;
    }

    public function closeDetailModal(): void
    {
        $this->showDetailModal = false;
        $this->inspectedKpiKey = null;
    }

    public function getKpiData(): array
    {
        return app(AdminDashboardMetricsService::class)->getDashboardOverview($this->timeframe);
    }

    protected function getViewData(): array
    {
        $metrics = app(AdminDashboardMetricsService::class);
        $overview = $metrics->getDashboardOverview($this->timeframe);
        $inspectedDetail = ($this->showDetailModal && $this->inspectedKpiKey)
            ? $metrics->inspectKpi($this->inspectedKpiKey, $this->timeframe)
            : [];

        return [
            'overview' => $overview,
            'categoryFilter' => $this->categoryFilter,
            'timeframe' => $this->timeframe,
            'showDetailModal' => $this->showDetailModal,
            'inspectedDetail' => $inspectedDetail,
        ];
    }
}
