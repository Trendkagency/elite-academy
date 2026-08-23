<?php

namespace App\Filament\Widgets;

use App\Enums\AccountStatus;
use App\Models\ExceptionRequest;
use App\Models\LiveSession;
use App\Models\StudentPackage;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminOverviewStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    public ?string $filter = 'all';

    protected function getFilters(): ?array
    {
        return [
            'all' => __('🔍 All System KPIs (12)'),
            'students' => __('🎓 Students & Members'),
            'sessions' => __('📅 Live Sessions Analysis'),
            'financials' => __('💳 Packages & Financials'),
        ];
    }

    protected function getStats(): array
    {
        $allStats = [
            'total_students' => Stat::make(
                __('Total Students'),
                User::whereHas('studentProfile')->count()
            )
            ->description(__('All registered students'))
            ->icon('heroicon-o-academic-cap')
            ->color('info')
            ->url(route('filament.admin.resources.student-profiles.index', ['activeTab' => 'all'])),

            'active_students' => Stat::make(
                __('Active Students'),
                User::whereHas('studentProfile')->where('status', AccountStatus::APPROVED)->count()
            )
            ->description(__('Approved active student accounts'))
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->url(route('filament.admin.resources.student-profiles.index', ['activeTab' => 'approved'])),

            'pending_students' => Stat::make(
                __('Pending Students'),
                User::whereHas('studentProfile')->where('status', AccountStatus::PENDING)->count()
            )
            ->description(__('Click to view all pending students'))
            ->icon('heroicon-o-clock')
            ->color('warning')
            ->url(route('filament.admin.resources.student-profiles.index', ['activeTab' => 'pending'])),

            'total_parents' => Stat::make(
                __('Total Parents'),
                User::whereHas('parentProfile')->count()
            )
            ->description(__('Registered parent accounts'))
            ->icon('heroicon-o-user-group')
            ->color('primary')
            ->url(route('filament.admin.resources.parent-profiles.index')),

            'total_teachers' => Stat::make(
                __('Total Teachers'),
                User::whereHas('teacherProfile')->count()
            )
            ->description(__('Instructors & Teachers'))
            ->icon('heroicon-o-rectangle-group')
            ->color('info')
            ->url(route('filament.admin.resources.teacher-profiles.index')),

            'todays_sessions' => Stat::make(
                __("Today's Live Sessions"),
                LiveSession::whereDate('scheduled_at', today())->orWhereDate('start_at', today())->count()
            )
            ->description(__('Sessions scheduled for today'))
            ->icon('heroicon-o-calendar-days')
            ->color('primary')
            ->url(route('filament.admin.resources.course-sessions.index', ['activeTab' => 'all'])),

            'upcoming_sessions' => Stat::make(
                __('Upcoming Live Sessions'),
                LiveSession::where(function ($q) {
                    $q->where('scheduled_at', '>', now())->orWhere('start_at', '>', now());
                })->whereNotIn('status', ['completed', 'cancelled', 'cancelled_by_teacher'])->count()
            )
            ->description(__('Future live sessions'))
            ->icon('heroicon-o-arrow-trending-up')
            ->color('info')
            ->url(route('filament.admin.resources.course-sessions.index', ['activeTab' => 'all'])),

            'completed_sessions' => Stat::make(
                __('Free Demo Sessions'),
                \App\Models\CourseSession::where('is_free_demo', true)->count()
            )
            ->description(__('Sample trial demo sessions'))
            ->icon('heroicon-o-check-badge')
            ->color('success')
            ->url(route('filament.admin.resources.course-sessions.index', ['activeTab' => 'free_demo'])),

            'cancelled_sessions' => Stat::make(
                __('Regular Sessions'),
                \App\Models\CourseSession::where('is_free_demo', false)->count()
            )
            ->description(__('Curriculum core sessions'))
            ->icon('heroicon-o-book-open')
            ->color('warning')
            ->url(route('filament.admin.resources.course-sessions.index', ['activeTab' => 'regular'])),

            'active_packages' => Stat::make(
                __('Active Packages'),
                StudentPackage::where('status', 'active')->count()
            )
            ->description(__('Active student packages with credits'))
            ->icon('heroicon-o-shopping-bag')
            ->color('success')
            ->url(route('filament.admin.resources.student-packages.index', ['activeTab' => 'active'])),

            'exception_requests' => Stat::make(
                __('Exception Requests'),
                ExceptionRequest::count()
            )
            ->description(__('Total student exception requests'))
            ->icon('heroicon-o-document-text')
            ->color('warning')
            ->url(route('filament.admin.resources.exception-requests.index')),

            'pending_payments' => Stat::make(
                __('Pending Payments'),
                StudentPackage::where('status', 'pending')->count()
            )
            ->description(__('Pending package subscriptions'))
            ->icon('heroicon-o-credit-card')
            ->color('danger')
            ->url(route('filament.admin.resources.student-packages.index', ['activeTab' => 'pending'])),
        ];

        return match ($this->filter) {
            'students' => [
                $allStats['total_students'],
                $allStats['active_students'],
                $allStats['pending_students'],
                $allStats['total_parents'],
                $allStats['total_teachers'],
            ],
            'sessions' => [
                $allStats['todays_sessions'],
                $allStats['upcoming_sessions'],
                $allStats['completed_sessions'],
                $allStats['cancelled_sessions'],
            ],
            'financials' => [
                $allStats['active_packages'],
                $allStats['exception_requests'],
                $allStats['pending_payments'],
            ],
            default => array_values($allStats),
        };
    }
}
