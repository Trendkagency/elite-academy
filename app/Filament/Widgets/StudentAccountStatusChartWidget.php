<?php

namespace App\Filament\Widgets;

use App\Enums\AccountStatus;
use App\Models\User;
use Filament\Widgets\ChartWidget;

class StudentAccountStatusChartWidget extends ChartWidget
{
    protected ?string $heading = 'توزيع حالات حسابات الطلاب — Student Status Breakdown';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $approved = User::whereHas('studentProfile')->where('status', AccountStatus::APPROVED)->count();
        $pending  = User::whereHas('studentProfile')->where('status', AccountStatus::PENDING)->count();
        $other    = User::whereHas('studentProfile')->whereNotIn('status', [AccountStatus::APPROVED, AccountStatus::PENDING])->count();

        return [
            'datasets' => [
                [
                    'label' => 'Students',
                    'data' => [$approved, $pending, $other],
                    'backgroundColor' => ['#10b981', '#f59e0b', '#6b7280'],
                ],
            ],
            'labels' => [
                app()->getLocale() === 'ar' ? 'الطلاب النشطين (المقبولين)' : 'Approved Active Students',
                app()->getLocale() === 'ar' ? 'الطلاب قيد المراجعة' : 'Pending Approval Students',
                app()->getLocale() === 'ar' ? 'حالات أخرى' : 'Other Statuses',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
