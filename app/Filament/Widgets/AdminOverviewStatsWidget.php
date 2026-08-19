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
            'all' => app()->getLocale() === 'ar' ? '🔍 جميع المؤشرات (12)' : '🔍 All System KPIs (12)',
            'students' => app()->getLocale() === 'ar' ? '🎓 مؤشرات الطلاب والمنتسبين' : '🎓 Students & Members',
            'sessions' => app()->getLocale() === 'ar' ? '📅 تحليل الحصص والبث المباشر' : '📅 Live Sessions Analysis',
            'financials' => app()->getLocale() === 'ar' ? '💳 الماليات والباقات والاستثناءات' : '💳 Packages & Financials',
        ];
    }

    protected function getStats(): array
    {
        $allStats = [
            'total_students' => Stat::make(
                app()->getLocale() === 'ar' ? 'إجمالي الطلاب' : 'Total Students',
                User::whereHas('studentProfile')->count()
            )
            ->description(app()->getLocale() === 'ar' ? 'جميع الطلاب المسجلين' : 'All registered students')
            ->icon('heroicon-o-academic-cap')
            ->color('info')
            ->url(route('filament.admin.resources.student-profiles.index', ['activeTab' => 'all'])),

            'active_students' => Stat::make(
                app()->getLocale() === 'ar' ? 'الطلاب النشطين' : 'Active Students',
                User::whereHas('studentProfile')->where('status', AccountStatus::APPROVED)->count()
            )
            ->description(app()->getLocale() === 'ar' ? 'حسابات الطلاب المفعلة' : 'Approved active student accounts')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->url(route('filament.admin.resources.student-profiles.index', ['activeTab' => 'approved'])),

            'pending_students' => Stat::make(
                app()->getLocale() === 'ar' ? 'الطلاب قيد المراجعة' : 'Pending Students',
                User::whereHas('studentProfile')->where('status', AccountStatus::PENDING)->count()
            )
            ->description(app()->getLocale() === 'ar' ? 'انقر لعرض كافة الطلاب قيد المراجعة' : 'Click to view all pending students')
            ->icon('heroicon-o-clock')
            ->color('warning')
            ->url(route('filament.admin.resources.student-profiles.index', ['activeTab' => 'pending'])),

            'total_parents' => Stat::make(
                app()->getLocale() === 'ar' ? 'إجمالي أولياء الأمور' : 'Total Parents',
                User::whereHas('parentProfile')->count()
            )
            ->description(app()->getLocale() === 'ar' ? 'حسابات أولياء الأمور' : 'Registered parent accounts')
            ->icon('heroicon-o-user-group')
            ->color('primary')
            ->url(route('filament.admin.resources.parent-profiles.index')),

            'total_teachers' => Stat::make(
                app()->getLocale() === 'ar' ? 'إجمالي المدرسين' : 'Total Teachers',
                User::whereHas('teacherProfile')->count()
            )
            ->description(app()->getLocale() === 'ar' ? 'المعلمين والمحاضرين' : 'Instructors & Teachers')
            ->icon('heroicon-o-rectangle-group')
            ->color('info')
            ->url(route('filament.admin.resources.teacher-profiles.index')),

            'todays_sessions' => Stat::make(
                app()->getLocale() === 'ar' ? 'حصص اليوم المباشرة' : "Today's Live Sessions",
                LiveSession::whereDate('scheduled_at', today())->orWhereDate('start_at', today())->count()
            )
            ->description(app()->getLocale() === 'ar' ? 'الحصص المجدولة اليوم' : 'Sessions scheduled for today')
            ->icon('heroicon-o-calendar-days')
            ->color('primary')
            ->url(route('filament.admin.resources.course-sessions.index', ['activeTab' => 'all'])),

            'upcoming_sessions' => Stat::make(
                app()->getLocale() === 'ar' ? 'الحصص المباشرة القادمة' : 'Upcoming Live Sessions',
                LiveSession::where(function ($q) {
                    $q->where('scheduled_at', '>', now())->orWhere('start_at', '>', now());
                })->whereNotIn('status', ['completed', 'cancelled', 'cancelled_by_teacher'])->count()
            )
            ->description(app()->getLocale() === 'ar' ? 'الحصص المباشرة المستقبلية' : 'Future live sessions')
            ->icon('heroicon-o-arrow-trending-up')
            ->color('info')
            ->url(route('filament.admin.resources.course-sessions.index', ['activeTab' => 'all'])),

            'completed_sessions' => Stat::make(
                app()->getLocale() === 'ar' ? 'الحصص المجانية' : 'Free Demo Sessions',
                \App\Models\CourseSession::where('is_free_demo', true)->count()
            )
            ->description(app()->getLocale() === 'ar' ? 'حصص التجربة المجانية' : 'Sample trial demo sessions')
            ->icon('heroicon-o-check-badge')
            ->color('success')
            ->url(route('filament.admin.resources.course-sessions.index', ['activeTab' => 'free_demo'])),

            'cancelled_sessions' => Stat::make(
                app()->getLocale() === 'ar' ? 'الحصص الأساسية' : 'Regular Sessions',
                \App\Models\CourseSession::where('is_free_demo', false)->count()
            )
            ->description(app()->getLocale() === 'ar' ? 'حصص المنهج الأساسية' : 'Curriculum core sessions')
            ->icon('heroicon-o-book-open')
            ->color('warning')
            ->url(route('filament.admin.resources.course-sessions.index', ['activeTab' => 'regular'])),

            'active_packages' => Stat::make(
                app()->getLocale() === 'ar' ? 'الباقات النشطة' : 'Active Packages',
                StudentPackage::where('status', 'active')->count()
            )
            ->description(app()->getLocale() === 'ar' ? 'باقات الطلاب المفعلة' : 'Active student packages with credits')
            ->icon('heroicon-o-shopping-bag')
            ->color('success')
            ->url(route('filament.admin.resources.student-packages.index', ['activeTab' => 'active'])),

            'exception_requests' => Stat::make(
                app()->getLocale() === 'ar' ? 'طلبات الاستثناء' : 'Exception Requests',
                ExceptionRequest::count()
            )
            ->description(app()->getLocale() === 'ar' ? 'إجمالي طلبات أعذار الطلاب' : 'Total student exception requests')
            ->icon('heroicon-o-document-text')
            ->color('warning')
            ->url(route('filament.admin.resources.exception-requests.index')),

            'pending_payments' => Stat::make(
                app()->getLocale() === 'ar' ? 'المدفوعات المعلقة' : 'Pending Payments',
                StudentPackage::where('status', 'pending')->count()
            )
            ->description(app()->getLocale() === 'ar' ? 'اشتراكات الباقات قيد المراجعة' : 'Pending package subscriptions')
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
