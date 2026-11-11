<?php

namespace App\Services\Analytics;

use App\Enums\AccountStatus;
use App\Models\Course;
use App\Models\ExceptionRequest;
use App\Models\LiveSession;
use App\Models\ParentProfile;
use App\Models\RecurringSchedule;
use App\Models\StudentPackage;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminDashboardMetricsService
{
    /* =========================================================================
       SHARED QUERY SCOPES — SINGLE SOURCE OF TRUTH ACROSS KPIs, CHARTS & TABS
       ========================================================================= */

    public function applyUserStatusFilter(Builder $query, ?AccountStatus $status = null): Builder
    {
        return $query->whereHas('user', function (Builder $q) use ($status) {
            if ($status !== null) {
                $q->where('status', $status);
            }
        });
    }

    public function studentsQuery(?AccountStatus $status = null): Builder
    {
        return $this->applyUserStatusFilter(StudentProfile::query(), $status);
    }

    public function teachersQuery(?AccountStatus $status = null): Builder
    {
        return $this->applyUserStatusFilter(TeacherProfile::query(), $status);
    }

    public function parentsQuery(?AccountStatus $status = null): Builder
    {
        return $this->applyUserStatusFilter(ParentProfile::query(), $status);
    }

    public function applySessionsToday(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->whereDate('scheduled_at', today())
                ->orWhereDate('start_at', today());
        });
    }

    public function sessionsTodayQuery(): Builder
    {
        return $this->applySessionsToday(LiveSession::query());
    }

    public function applySessionsLiveNow(Builder $query): Builder
    {
        return $query->whereIn('status', ['in_progress', 'live', 'link_visible']);
    }

    public function sessionsLiveNowQuery(): Builder
    {
        return $this->applySessionsLiveNow(LiveSession::query());
    }

    public function applySessionsUpcoming(Builder $query): Builder
    {
        return $query
            ->where(function (Builder $q) {
                $q->where('scheduled_at', '>', now())
                    ->orWhere('start_at', '>', now());
            })
            ->whereNotIn('status', ['completed', 'cancelled', 'cancelled_by_teacher']);
    }

    public function sessionsUpcomingQuery(): Builder
    {
        return $this->applySessionsUpcoming(LiveSession::query());
    }

    public function applySessionsCompleted(Builder $query, ?string $timeframe = null): Builder
    {
        return $this->applySessionTimeframe(
            $query->where('status', 'completed'),
            $timeframe
        );
    }

    public function sessionsCompletedQuery(?string $timeframe = null): Builder
    {
        return $this->applySessionsCompleted(LiveSession::query(), $timeframe);
    }

    public function applySessionsCancelled(Builder $query, ?string $timeframe = null): Builder
    {
        return $this->applySessionTimeframe(
            $query->whereIn('status', ['cancelled', 'cancelled_by_teacher']),
            $timeframe
        );
    }

    public function sessionsCancelledQuery(?string $timeframe = null): Builder
    {
        return $this->applySessionsCancelled(LiveSession::query(), $timeframe);
    }

    public function applySessionsFreeDemo(Builder $query, ?string $timeframe = null): Builder
    {
        return $this->applySessionTimeframe(
            $query->where('is_free_demo', true),
            $timeframe
        );
    }

    public function sessionsFreeDemoQuery(?string $timeframe = null): Builder
    {
        return $this->applySessionsFreeDemo(LiveSession::query(), $timeframe);
    }

    public function applySessionsRegular(Builder $query, ?string $timeframe = null): Builder
    {
        return $this->applySessionTimeframe(
            $query->where('is_free_demo', false),
            $timeframe
        );
    }

    public function sessionsRegularQuery(?string $timeframe = null): Builder
    {
        return $this->applySessionsRegular(LiveSession::query(), $timeframe);
    }

    public function applyPackagesHealthy(Builder $query): Builder
    {
        return $query
            ->where('status', 'active')
            ->where('remaining_sessions', '>', 0)
            ->where(function (Builder $q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now());
            });
    }

    public function packagesHealthyQuery(): Builder
    {
        return $this->applyPackagesHealthy(StudentPackage::query());
    }

    public function applyPackagesActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function packagesActiveQuery(): Builder
    {
        return $this->applyPackagesActive(StudentPackage::query());
    }

    public function applyPackagesPending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function packagesPendingQuery(): Builder
    {
        return $this->applyPackagesPending(StudentPackage::query());
    }

    public function applyPackagesExhausted(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->where('remaining_sessions', '<=', 0)
                ->orWhere('status', 'exhausted');
        });
    }

    public function packagesExhaustedQuery(): Builder
    {
        return $this->applyPackagesExhausted(StudentPackage::query());
    }

    public function collectionsPackagesQuery(): Builder
    {
        $now = now();

        return StudentPackage::query()
            ->whereIn('status', ['active', 'exhausted'])
            ->where(function (Builder $q) use ($now) {
                $q->where('remaining_sessions', '<=', 3)
                    ->orWhere(function (Builder $eq) use ($now) {
                        $eq->whereNotNull('expires_at')
                            ->where('expires_at', '<=', $now->copy()->addDays(7));
                    });
            });
    }

    public function collectionsSchedulesQuery(): Builder
    {
        return RecurringSchedule::query()
            ->where('status', 'active')
            ->whereNotNull('end_date')
            ->where('end_date', '<=', now()->addDays(14));
    }

    public function getCollectionsDueCount(): int
    {
        return $this->collectionsPackagesQuery()->count() + $this->collectionsSchedulesQuery()->count();
    }

    public function applyExceptionsStatus(Builder $query, ?string $status = null): Builder
    {
        if ($status !== null) {
            $query->where('status', $status);
        }

        return $query;
    }

    public function exceptionsQuery(?string $status = null): Builder
    {
        return $this->applyExceptionsStatus(ExceptionRequest::query(), $status);
    }

    /* =========================================================================
       DASHBOARD OVERVIEW — 12 BALANCED EXECUTIVE & ACCOUNTING KPIs
       ========================================================================= */

    public function getDashboardOverview(string $timeframe = 'all'): array
    {
        $isAr = app()->getLocale() === 'ar';
        $periodStart = $this->periodStart($timeframe);
        $accounting = $this->computeAccountingSnapshot($periodStart);

        // --- Live Operations Snapshots ---
        $todayAllCount = $this->sessionsTodayQuery()->count();
        $todayActiveCount = $this->sessionsTodayQuery()
            ->whereNotIn('status', ['cancelled', 'cancelled_by_teacher'])
            ->count();
        $liveNowCount = $this->sessionsLiveNowQuery()->count();

        $upcomingQuery = $this->sessionsUpcomingQuery();
        $upcomingCount = (clone $upcomingQuery)->count();
        $upcomingHours = round(((int) (clone $upcomingQuery)->sum('duration_minutes')) / 60, 1);

        $completedCount = $this->sessionsCompletedQuery($timeframe)->count();
        $cancelledCount = $this->sessionsCancelledQuery($timeframe)->count();
        $freeDemoCount = $this->sessionsFreeDemoQuery($timeframe)->count();
        $regularCount = $this->sessionsRegularQuery($timeframe)->count();
        $terminalTotal = $completedCount + $cancelledCount;
        $completionRate = $terminalTotal > 0
            ? round(($completedCount / $terminalTotal) * 100, 1)
            : ($completedCount > 0 ? 100.0 : 0.0);

        $pendingExceptionsCount = $this->exceptionsQuery('pending')->count();
        $approvedExceptionsCount = $this->exceptionsQuery('approved')->count();
        $totalExceptionsCount = $this->exceptionsQuery()->count();

        // --- Academic Community Snapshots ---
        $totalStudents = $this->studentsQuery()->count();
        $approvedStudents = $this->studentsQuery(AccountStatus::APPROVED)->count();
        $pendingStudents = $this->studentsQuery(AccountStatus::PENDING)->count();

        $totalTeachers = $this->teachersQuery()->count();
        $approvedTeachers = $this->teachersQuery(AccountStatus::APPROVED)->count();
        $activeCoursesCount = Course::where('is_active', true)->count();

        $healthyPackagesQuery = $this->packagesHealthyQuery();
        $healthyPackagesCount = (clone $healthyPackagesQuery)->count();
        $remainingCreditsTotal = (int) (clone $healthyPackagesQuery)->sum('remaining_sessions');
        $totalPoolCapacity = (int) (clone $healthyPackagesQuery)->sum('total_sessions');

        $totalParents = $this->parentsQuery()->count();
        $linkedStudentsCount = Schema::hasTable('parent_student')
            ? (int) DB::table('parent_student')->distinct('student_user_id')->count('student_user_id')
            : 0;
        $parentCoverageRate = $totalStudents > 0
            ? min(100.0, round(($linkedStudentsCount / $totalStudents) * 100, 1))
            : 0.0;

        $collectionsPkgCount = $this->collectionsPackagesQuery()->count();
        $collectionsSchCount = $this->collectionsSchedulesQuery()->count();
        $collectionsTotalCount = $collectionsPkgCount + $collectionsSchCount;

        $kpis = [
            // ── SECTION 1: FINANCIAL & ACCOUNTING CONTROL (4 CARDS) ─────────
            'collected_revenue' => [
                'key' => 'collected_revenue',
                'section' => 'financials',
                'title' => $isAr ? 'إجمالي الإيرادات المحصلة والمعتمدة' : 'Gross Booked & Collected Revenue',
                'value' => format_currency($accounting['gross_revenue']),
                'raw_value' => $accounting['gross_revenue'],
                'subtitle' => $isAr ? 'قيمة الباقات المفعّلة والمدفوعات المؤكدة' : 'Confirmed cash payments & active package bookings',
                'badge_text' => $accounting['confirmed_payments_count'] > 0
                    ? ($isAr ? 'مطابق للخزينة' : 'Cash + Ledger')
                    : ($isAr ? 'دفتر الباقات' : 'Package Ledger'),
                'badge_variant' => $accounting['gross_revenue'] > 0 ? 'success' : 'neutral',
                'icon' => 'fa-sack-dollar',
                'accent' => 'emerald',
                'target_url' => route('filament.admin.resources.student-packages.index', ['tab' => 'all']),
                'target_label' => $isAr ? 'فتح سجل الباقات' : 'Open Packages Ledger',
                'sub_metrics' => [
                    ['label' => $isAr ? 'الاشتراكات' : 'Paid Packs', 'value' => (string) $accounting['booked_packages_count']],
                    ['label' => $isAr ? 'متوسط الباقة' : 'Avg Ticket', 'value' => format_currency($accounting['avg_ticket'])],
                ],
            ],

            'earned_deferred_revenue' => [
                'key' => 'earned_deferred_revenue',
                'section' => 'financials',
                'title' => $isAr ? 'الإيراد المحقق مقابل الرصيد المؤجل' : 'Earned vs Deferred Revenue',
                'value' => format_currency($accounting['earned_revenue']),
                'raw_value' => $accounting['earned_revenue'],
                'subtitle' => $isAr
                    ? 'إيراد فعلي مكتسب من الحصص المنفذة (أساس الاستحقاق)'
                    : 'Accrual revenue recognized from taught sessions',
                'badge_text' => $accounting['realization_rate'].'% '.($isAr ? 'تحقق' : 'Realized'),
                'badge_variant' => 'teal',
                'icon' => 'fa-scale-balanced',
                'accent' => 'teal',
                'target_url' => route('filament.admin.resources.student-packages.index', ['tab' => 'active']),
                'target_label' => $isAr ? 'مراجعة الباقات النشطة' : 'Inspect Active Credits',
                'sub_metrics' => [
                    ['label' => $isAr ? 'الالتزام المؤجل' : 'Deferred Liab.', 'value' => format_currency($accounting['deferred_liability'])],
                    ['label' => $isAr ? 'حصص مستهلكة' : 'Used Credits', 'value' => (string) $accounting['used_credits_count']],
                ],
            ],

            'pending_receivables' => [
                'key' => 'pending_receivables',
                'section' => 'financials',
                'title' => $isAr ? 'مدفوعات وموافقات معلقة' : 'Pending Payments & Approvals',
                'value' => format_currency($accounting['pending_amount']),
                'raw_value' => $accounting['pending_count'],
                'subtitle' => $isAr
                    ? 'اشتراكات قيد الانتظار تتطلب تأكيد التحصيل والتفعيل'
                    : 'Pending subscriptions awaiting confirmation',
                'badge_text' => $accounting['pending_count'] > 0
                    ? $accounting['pending_count'].' '.($isAr ? 'قيد الانتظار' : 'Pending')
                    : ($isAr ? 'خالية' : 'All Clear'),
                'badge_variant' => $accounting['pending_count'] > 0 ? 'warning' : 'success',
                'icon' => 'fa-credit-card',
                'accent' => 'amber',
                'target_url' => route('filament.admin.resources.student-packages.index', ['tab' => 'pending']),
                'target_label' => $isAr ? 'مراجعة المعلقات' : 'Review Pending Payments',
                'sub_metrics' => [
                    ['label' => $isAr ? 'باقات معلقة' : 'Pending Packs', 'value' => (string) $accounting['pending_packages_count']],
                    ['label' => $isAr ? 'تحويلات معلقة' : 'Cash Pending', 'value' => (string) $accounting['pending_payments_count']],
                ],
            ],

            'collections_due' => [
                'key' => 'collections_due',
                'section' => 'financials',
                'title' => $isAr ? 'تحصيلات وتجديدات مستحقة' : 'Collections & Renewals Due',
                'value' => (string) $collectionsTotalCount,
                'raw_value' => $collectionsTotalCount,
                'subtitle' => $isAr
                    ? 'باقات منتهية/حرج وجداول دورية تنتهي قريباً (مطابق للقائمة)'
                    : 'Exhausted/low packages + expiring cycles (matches sidebar)',
                'badge_text' => $collectionsTotalCount > 0
                    ? format_currency($accounting['collections_renewal_value'])
                    : ($isAr ? 'مستقر' : 'Healthy'),
                'badge_variant' => $collectionsTotalCount > 0 ? 'danger' : 'success',
                'icon' => 'fa-hand-holding-dollar',
                'accent' => 'rose',
                'target_url' => route('filament.admin.pages.student-collections'),
                'target_label' => $isAr ? 'فتح متابعة التحصيلات' : 'Open Collections Center',
                'sub_metrics' => [
                    ['label' => $isAr ? 'باقات للتجديد' : 'Due Packs', 'value' => (string) $collectionsPkgCount],
                    ['label' => $isAr ? 'جداول تنتهي' : 'Ending Cycles', 'value' => (string) $collectionsSchCount],
                ],
            ],

            // ── SECTION 2: LIVE OPERATIONS & UTILIZATION (4 CARDS) ──────────
            'todays_sessions' => [
                'key' => 'todays_sessions',
                'section' => 'operations',
                'title' => $isAr ? 'حصص اليوم والبث المباشر' : "Today's & Live Sessions",
                'value' => (string) $todayAllCount,
                'raw_value' => $todayAllCount,
                'subtitle' => $isAr
                    ? 'إجمالي الحصص المجدولة لتاريخ اليوم وحالة البث الفعلي'
                    : 'Sessions scheduled for today and real-time live broadcasts',
                'badge_text' => $liveNowCount > 0
                    ? $liveNowCount.' '.($isAr ? 'مباشر الآن' : 'Live Now')
                    : ($todayActiveCount > 0 ? ($isAr ? 'نشط اليوم' : 'Scheduled Today') : ($isAr ? 'لا توجد حصص' : 'Clear')),
                'badge_variant' => $liveNowCount > 0 ? 'danger' : ($todayActiveCount > 0 ? 'teal' : 'neutral'),
                'icon' => 'fa-tower-broadcast',
                'accent' => 'teal',
                'target_url' => route('filament.admin.resources.live-sessions.index', ['tab' => 'today']),
                'target_label' => $isAr ? 'عرض حصص اليوم' : "Open Today's Schedule",
                'sub_metrics' => [
                    ['label' => $isAr ? 'فعالة اليوم' : 'Active Today', 'value' => (string) $todayActiveCount],
                    ['label' => $isAr ? 'جارية الآن' : 'Live Now', 'value' => (string) $liveNowCount],
                ],
            ],

            'upcoming_sessions' => [
                'key' => 'upcoming_sessions',
                'section' => 'operations',
                'title' => $isAr ? 'الحصص القادمة المجدولة' : 'Upcoming Scheduled Pipeline',
                'value' => (string) $upcomingCount,
                'raw_value' => $upcomingCount,
                'subtitle' => $isAr
                    ? 'حصص مستقبلية مؤكدة بانتظار التنفيذ'
                    : 'Confirmed future sessions pending delivery',
                'badge_text' => $upcomingHours.' '.($isAr ? 'ساعة' : 'hrs'),
                'badge_variant' => 'indigo',
                'icon' => 'fa-calendar-days',
                'accent' => 'indigo',
                'target_url' => route('filament.admin.resources.live-sessions.index', ['tab' => 'upcoming']),
                'target_label' => $isAr ? 'فتح الحصص القادمة' : 'Open Upcoming Queue',
                'sub_metrics' => [
                    ['label' => $isAr ? 'ساعات مجدولة' : 'Sched. Hours', 'value' => $upcomingHours.'h'],
                    ['label' => $isAr ? 'أساسية / تجريبية' : 'Demo Split', 'value' => $freeDemoCount.' / '.$regularCount],
                ],
            ],

            'session_delivery' => [
                'key' => 'session_delivery',
                'section' => 'operations',
                'title' => $isAr ? 'كفاءة التسليم والإتمام' : 'Session Delivery & Completion',
                'value' => $completionRate.'%',
                'raw_value' => $completedCount,
                'subtitle' => $isAr
                    ? 'نسبة إتمام الحصص الفعلية مقارنة بالملغاة'
                    : 'Completed delivery efficiency vs cancelled sessions',
                'badge_text' => $completedCount.' '.($isAr ? 'مكتملة' : 'Completed'),
                'badge_variant' => $completionRate >= 80 ? 'success' : ($completionRate >= 50 ? 'warning' : 'danger'),
                'icon' => 'fa-chart-line',
                'accent' => 'sky',
                'target_url' => route('filament.admin.resources.live-sessions.index', ['tab' => 'completed']),
                'target_label' => $isAr ? 'فتح الحصص المكتملة' : 'Open Completed Log',
                'sub_metrics' => [
                    ['label' => $isAr ? 'مكتملة' : 'Completed', 'value' => (string) $completedCount],
                    ['label' => $isAr ? 'ملغاة' : 'Cancelled', 'value' => (string) $cancelledCount],
                ],
            ],

            'open_exceptions' => [
                'key' => 'open_exceptions',
                'section' => 'operations',
                'title' => $isAr ? 'طلبات الأعذار قيد المراجعة' : 'Open Exception Requests',
                'value' => (string) $pendingExceptionsCount,
                'raw_value' => $pendingExceptionsCount,
                'subtitle' => $isAr
                    ? 'طلبات استثناء وغياب الطلاب المنتظرة لقرار الإدارة'
                    : 'Pending student absence & credit-preserve requests',
                'badge_text' => $pendingExceptionsCount > 0
                    ? ($isAr ? 'يتطلب قرار' : 'Needs Review')
                    : ($isAr ? 'مكتمل' : 'Resolved'),
                'badge_variant' => $pendingExceptionsCount > 0 ? 'warning' : 'success',
                'icon' => 'fa-envelope-open-text',
                'accent' => 'amber',
                'target_url' => route('filament.admin.resources.exception-requests.index', ['tab' => 'pending']),
                'target_label' => $isAr ? 'مراجعة الأعذار المعلقة' : 'Review Pending Exceptions',
                'sub_metrics' => [
                    ['label' => $isAr ? 'تمت الموافقة' : 'Approved', 'value' => (string) $approvedExceptionsCount],
                    ['label' => $isAr ? 'إجمالي الطلبات' : 'All Total', 'value' => (string) $totalExceptionsCount],
                ],
            ],

            // ── SECTION 3: ACADEMIC COMMUNITY & CAPACITY (4 CARDS) ──────────
            'students_overview' => [
                'key' => 'students_overview',
                'section' => 'academic',
                'title' => $isAr ? 'الطلاب النشطين وحالة القبول' : 'Active & Pending Students',
                'value' => (string) $approvedStudents,
                'raw_value' => $approvedStudents,
                'subtitle' => $isAr
                    ? 'إجمالي الطلاب المعتمدين والمتاح لهم حضور الحصص'
                    : 'Approved active learners ready for live sessions',
                'badge_text' => $pendingStudents > 0
                    ? $pendingStudents.' '.($isAr ? 'بانتظار الاعتماد' : 'Pending')
                    : ($isAr ? '100% معتمد' : 'All Approved'),
                'badge_variant' => $pendingStudents > 0 ? 'warning' : 'success',
                'icon' => 'fa-user-graduate',
                'accent' => 'teal',
                'target_url' => route('filament.admin.resources.student-profiles.index', [
                    'tab' => $pendingStudents > 0 ? 'pending' : 'approved',
                ]),
                'target_label' => $pendingStudents > 0
                    ? ($isAr ? 'اعتماد الطلاب المعلقين' : 'Approve Pending Students')
                    : ($isAr ? 'عرض ملفات الطلاب' : 'Open Student Profiles'),
                'sub_metrics' => [
                    ['label' => $isAr ? 'إجمالي الطلاب' : 'Total Reg.', 'value' => (string) $totalStudents],
                    ['label' => $isAr ? 'قيد المراجعة' : 'Pending', 'value' => (string) $pendingStudents],
                ],
            ],

            'faculty_courses' => [
                'key' => 'faculty_courses',
                'section' => 'academic',
                'title' => $isAr ? 'هيئة التدريس والمناهج النشطة' : 'Faculty & Active Courses',
                'value' => (string) $approvedTeachers,
                'raw_value' => $approvedTeachers,
                'subtitle' => $isAr
                    ? 'المعلمون المعتمدون والكورسات المنشورة الفعالة'
                    : 'Approved faculty instructors & active published courses',
                'badge_text' => $activeCoursesCount.' '.($isAr ? 'كورس نشط' : 'Courses'),
                'badge_variant' => 'indigo',
                'icon' => 'fa-chalkboard-user',
                'accent' => 'indigo',
                'target_url' => route('filament.admin.resources.teacher-profiles.index', ['tab' => 'approved']),
                'target_label' => $isAr ? 'إدارة المعلمين' : 'Open Faculty Directory',
                'sub_metrics' => [
                    ['label' => $isAr ? 'إجمالي المعلمين' : 'All Faculty', 'value' => (string) $totalTeachers],
                    ['label' => $isAr ? 'كورسات نشطة' : 'Active Courses', 'value' => (string) $activeCoursesCount],
                ],
            ],

            'active_credits_pool' => [
                'key' => 'active_credits_pool',
                'section' => 'academic',
                'title' => $isAr ? 'الباقات النشطة ورصيد الحصص' : 'Active Packages & Credits Pool',
                'value' => (string) $healthyPackagesCount,
                'raw_value' => $healthyPackagesCount,
                'subtitle' => $isAr
                    ? 'باقات نشطة غير منتهية الصلاحية وبها رصيد حصص متاح'
                    : 'Unexpired active student packages with remaining credits',
                'badge_text' => $remainingCreditsTotal.' '.($isAr ? 'حصة متاحة' : 'Credits Left'),
                'badge_variant' => $healthyPackagesCount > 0 ? 'success' : 'warning',
                'icon' => 'fa-ticket',
                'accent' => 'emerald',
                'target_url' => route('filament.admin.resources.student-packages.index', ['tab' => 'healthy']),
                'target_label' => $isAr ? 'عرض الباقات السليمة' : 'Open Healthy Packages',
                'sub_metrics' => [
                    ['label' => $isAr ? 'رصيد متبقي' : 'Rem. Credits', 'value' => (string) $remainingCreditsTotal],
                    ['label' => $isAr ? 'السعة الإجمالية' : 'Total Cap.', 'value' => (string) $totalPoolCapacity],
                ],
            ],

            'parents_coverage' => [
                'key' => 'parents_coverage',
                'section' => 'academic',
                'title' => $isAr ? 'أولياء الأمور ونسبة الربط الأسري' : 'Parents & Family Linkage',
                'value' => (string) $totalParents,
                'raw_value' => $totalParents,
                'subtitle' => $isAr
                    ? 'أولياء الأمور المسجلون ونسبة الطلاب المرتبطين بحساب ولي أمر'
                    : 'Registered parent accounts & linked student coverage',
                'badge_text' => $parentCoverageRate.'% '.($isAr ? 'ربط' : 'Linked'),
                'badge_variant' => 'info',
                'icon' => 'fa-people-roof',
                'accent' => 'sky',
                'target_url' => route('filament.admin.resources.parent-profiles.index', ['tab' => 'all']),
                'target_label' => $isAr ? 'عرض أولياء الأمور' : 'Open Parents Directory',
                'sub_metrics' => [
                    ['label' => $isAr ? 'طلاب مرتبطين' : 'Linked Stu.', 'value' => (string) $linkedStudentsCount],
                    ['label' => $isAr ? 'نسبة التغطية' : 'Coverage', 'value' => $parentCoverageRate.'%'],
                ],
            ],
        ];

        return [
            'timeframe' => $timeframe,
            'kpis' => $kpis,
            'sections' => [
                'financials' => [
                    'title' => $isAr ? 'الرقابة المالية والمحاسبية' : 'Financial & Accounting Control',
                    'icon' => 'fa-coins',
                    'keys' => ['collected_revenue', 'earned_deferred_revenue', 'pending_receivables', 'collections_due'],
                ],
                'operations' => [
                    'title' => $isAr ? 'العمليات المباشرة وكفاءة الحصص' : 'Live Operations & Delivery',
                    'icon' => 'fa-bolt',
                    'keys' => ['todays_sessions', 'upcoming_sessions', 'session_delivery', 'open_exceptions'],
                ],
                'academic' => [
                    'title' => $isAr ? 'المجتمع الأكاديمي وسعة الرصيد' : 'Academic Community & Capacity',
                    'icon' => 'fa-graduation-cap',
                    'keys' => ['students_overview', 'faculty_courses', 'active_credits_pool', 'parents_coverage'],
                ],
            ],
        ];
    }

    /* =========================================================================
       LIVE DRILL-DOWN MODAL PAYLOAD (REAL DYNAMIC RECORDS + REAL STATUS CHECK)
       ========================================================================= */

    public function inspectKpi(string $key, string $timeframe = 'all'): array
    {
        $isAr = app()->getLocale() === 'ar';
        $overview = $this->getDashboardOverview($timeframe);
        $kpi = $overview['kpis'][$key] ?? null;

        if (! $kpi) {
            return [];
        }

        $periodStart = $this->periodStart($timeframe);
        $accounting = $this->computeAccountingSnapshot($periodStart);

        $detail = match ($key) {
            'collected_revenue' => $this->buildCollectedRevenueDrilldown($isAr, $periodStart, $accounting),
            'earned_deferred_revenue' => $this->buildEarnedDeferredDrilldown($isAr, $periodStart, $accounting),
            'pending_receivables' => $this->buildPendingReceivablesDrilldown($isAr, $accounting),
            'collections_due' => $this->buildCollectionsDueDrilldown($isAr, $accounting),
            'todays_sessions' => $this->buildTodaysSessionsDrilldown($isAr),
            'upcoming_sessions' => $this->buildUpcomingSessionsDrilldown($isAr),
            'session_delivery' => $this->buildSessionDeliveryDrilldown($isAr, $timeframe),
            'open_exceptions' => $this->buildOpenExceptionsDrilldown($isAr),
            'students_overview' => $this->buildStudentsDrilldown($isAr),
            'faculty_courses' => $this->buildFacultyDrilldown($isAr),
            'active_credits_pool' => $this->buildActiveCreditsDrilldown($isAr),
            'parents_coverage' => $this->buildParentsDrilldown($isAr),
            default => [],
        };

        return array_merge([
            'key' => $kpi['key'],
            'title' => $kpi['title'],
            'subtitle' => $kpi['subtitle'],
            'headline_value' => $kpi['value'],
            'badge_text' => $kpi['badge_text'],
            'badge_variant' => $kpi['badge_variant'],
            'icon' => $kpi['icon'],
            'accent' => $kpi['accent'],
            'target_url' => $kpi['target_url'],
            'target_label' => $kpi['target_label'],
            'timeframe_label' => $this->timeframeLabel($timeframe, $isAr),
        ], $detail);
    }

    /* =========================================================================
       CHART DATA PROVIDERS (100% ALIGNED WITH DASHBOARD KPIs)
       ========================================================================= */

    public function getSessionsChartData(): array
    {
        $isAr = app()->getLocale() === 'ar';

        $today = $this->sessionsTodayQuery()->count();
        $upcoming = $this->sessionsUpcomingQuery()->count();
        $completed = $this->sessionsCompletedQuery()->count();
        $cancelled = $this->sessionsCancelledQuery()->count();

        return [
            'datasets' => [
                [
                    'label' => $isAr ? 'الحصص المباشرة' : 'Live Sessions',
                    'data' => [$today, $upcoming, $completed, $cancelled],
                    'backgroundColor' => ['#f59e0b', '#3b82f6', '#10b981', '#ef4444'],
                ],
            ],
            'labels' => [
                $isAr ? 'حصص اليوم' : "Today's Sessions",
                $isAr ? 'الحصص القادمة' : 'Upcoming Sessions',
                $isAr ? 'الحصص المكتملة' : 'Completed Sessions',
                $isAr ? 'الحصص الملغاة' : 'Cancelled Sessions',
            ],
        ];
    }

    public function getStudentStatusChartData(): array
    {
        $isAr = app()->getLocale() === 'ar';

        $approved = $this->studentsQuery(AccountStatus::APPROVED)->count();
        $pending = $this->studentsQuery(AccountStatus::PENDING)->count();
        $other = StudentProfile::query()->whereHas('user', function (Builder $q) {
            $q->whereNotIn('status', [AccountStatus::APPROVED->value, AccountStatus::PENDING->value]);
        })->count();

        return [
            'datasets' => [
                [
                    'label' => $isAr ? 'الطلاب' : 'Students',
                    'data' => [$approved, $pending, $other],
                    'backgroundColor' => ['#10b981', '#f59e0b', '#6b7280'],
                ],
            ],
            'labels' => [
                $isAr ? 'الطلاب النشطين (المقبولين)' : 'Approved Active Students',
                $isAr ? 'الطلاب قيد المراجعة' : 'Pending Approval Students',
                $isAr ? 'حالات أخرى' : 'Other Statuses',
            ],
        ];
    }

    /* =========================================================================
       INTERNAL ACCOUNTING & DRILL-DOWN BUILDERS
       ========================================================================= */

    protected function periodStart(?string $timeframe): ?Carbon
    {
        return match ($timeframe) {
            'today' => now()->startOfDay(),
            'month' => now()->startOfMonth(),
            'year' => now()->startOfYear(),
            default => null,
        };
    }

    protected function periodEnd(?string $timeframe): ?Carbon
    {
        return match ($timeframe) {
            'today' => now()->endOfDay(),
            'month' => now()->endOfMonth(),
            'year' => now()->endOfYear(),
            default => null,
        };
    }

    protected function timeframeLabel(string $timeframe, bool $isAr): string
    {
        return match ($timeframe) {
            'today' => $isAr ? 'اليوم فقط' : 'Today',
            'month' => $isAr ? 'هذا الشهر' : 'This Month',
            'year' => $isAr ? 'هذا العام' : 'This Year',
            default => $isAr ? 'كل الفترات (شامل)' : 'All Time',
        };
    }

    protected function applySessionTimeframe(Builder $query, ?string $timeframe): Builder
    {
        $start = $this->periodStart($timeframe);
        $end = $this->periodEnd($timeframe);
        if (! $start || ! $end) {
            return $query;
        }

        return $query->whereRaw('COALESCE(scheduled_at, start_at, created_at) BETWEEN ? AND ?', [$start, $end]);
    }

    protected function computeAccountingSnapshot(?Carbon $periodStart = null): array
    {
        $hasPaymentsTable = Schema::hasTable('payments');
        $confirmedByPkg = [];
        $pendingByPkg = [];
        $unlinkedConfirmedAmount = 0.0;
        $unlinkedConfirmedCount = 0;
        $unlinkedPendingAmount = 0.0;
        $unlinkedPendingCount = 0;
        $confirmedPaymentsCount = 0;
        $pendingPaymentsCount = 0;

        if ($hasPaymentsTable) {
            $paymentsQuery = DB::table('payments');
            if ($periodStart) {
                $paymentsQuery->where('payment_date', '>=', $periodStart->toDateString());
            }

            foreach ($paymentsQuery->get() as $pay) {
                $amt = (float) $pay->amount;
                if ($pay->status === 'confirmed') {
                    $confirmedPaymentsCount++;
                    if ($pay->student_package_id) {
                        $confirmedByPkg[$pay->student_package_id] = ($confirmedByPkg[$pay->student_package_id] ?? 0.0) + $amt;
                    } else {
                        $unlinkedConfirmedAmount += $amt;
                        $unlinkedConfirmedCount++;
                    }
                } elseif ($pay->status === 'pending') {
                    $pendingPaymentsCount++;
                    if ($pay->student_package_id) {
                        $pendingByPkg[$pay->student_package_id] = ($pendingByPkg[$pay->student_package_id] ?? 0.0) + $amt;
                    } else {
                        $unlinkedPendingAmount += $amt;
                        $unlinkedPendingCount++;
                    }
                }
            }
        }

        $allPackages = StudentPackage::with(['packageTemplate', 'studentUser'])->get();

        $grossRevenue = $unlinkedConfirmedAmount;
        $earnedRevenue = 0.0;
        $deferredLiability = 0.0;
        $bookedPackagesCount = $unlinkedConfirmedCount;
        $usedCreditsCount = 0;
        $pendingAmount = $unlinkedPendingAmount;
        $pendingPackagesCount = 0;

        foreach ($allPackages as $pkg) {
            $templatePrice = (float) ($pkg->packageTemplate?->price ?? 0.0);
            $effectivePrice = $confirmedByPkg[$pkg->id] ?? $templatePrice;
            $pkgDate = $pkg->activated_at ?: $pkg->created_at;
            $inPeriod = ! $periodStart || ($pkgDate && $pkgDate->greaterThanOrEqualTo($periodStart));

            if (in_array($pkg->status, ['active', 'exhausted'], true) && $inPeriod) {
                $grossRevenue += $effectivePrice;
                $bookedPackagesCount++;

                $totalSessions = max(1, (int) $pkg->total_sessions);
                $usedSessions = min($totalSessions, max(0, (int) $pkg->used_sessions));
                $usedCreditsCount += $usedSessions;

                $earnedRevenue += $effectivePrice * ($usedSessions / $totalSessions);
            }

            // Deferred liability is ALWAYS a real-time Balance Sheet snapshot of unexpired active remaining credits
            if ($pkg->status === 'active' && $pkg->remaining_sessions > 0 && (! $pkg->expires_at || $pkg->expires_at->isFuture() || $pkg->expires_at->isToday())) {
                $totalSessions = max(1, (int) $pkg->total_sessions);
                $remainingSessions = min($totalSessions, max(0, (int) $pkg->remaining_sessions));
                $deferredLiability += $effectivePrice * ($remainingSessions / $totalSessions);
            }

            // Pending receivables is ALWAYS a real-time actionable snapshot
            if ($pkg->status === 'pending') {
                $pendingPackagesCount++;
                $pendingAmount += $pendingByPkg[$pkg->id] ?? $templatePrice;
            }
        }

        // Renewal potential for collections due packages
        $collectionsRenewalValue = 0.0;
        foreach ($this->collectionsPackagesQuery()->with('packageTemplate')->get() as $duePkg) {
            $collectionsRenewalValue += (float) ($duePkg->packageTemplate?->price ?? 0.0);
        }

        $grossRevenue = round($grossRevenue, 2);
        $earnedRevenue = round($earnedRevenue, 2);
        $deferredLiability = round($deferredLiability, 2);
        $pendingAmount = round($pendingAmount, 2);
        $collectionsRenewalValue = round($collectionsRenewalValue, 2);

        $avgTicket = $bookedPackagesCount > 0 ? round($grossRevenue / $bookedPackagesCount, 2) : 0.0;
        $realizationRate = $grossRevenue > 0 ? min(100.0, round(($earnedRevenue / $grossRevenue) * 100, 1)) : 0.0;

        return [
            'gross_revenue' => $grossRevenue,
            'earned_revenue' => $earnedRevenue,
            'deferred_liability' => $deferredLiability,
            'booked_packages_count' => $bookedPackagesCount,
            'used_credits_count' => $usedCreditsCount,
            'confirmed_payments_count' => $confirmedPaymentsCount,
            'avg_ticket' => $avgTicket,
            'realization_rate' => $realizationRate,
            'pending_amount' => $pendingAmount,
            'pending_packages_count' => $pendingPackagesCount,
            'pending_payments_count' => $pendingPaymentsCount,
            'pending_count' => $pendingPackagesCount + $unlinkedPendingCount,
            'collections_renewal_value' => $collectionsRenewalValue,
        ];
    }

    protected function buildCollectedRevenueDrilldown(bool $isAr, ?Carbon $periodStart, array $acc): array
    {
        $query = StudentPackage::with(['studentUser', 'packageTemplate'])
            ->whereIn('status', ['active', 'exhausted'])
            ->latest('activated_at')
            ->latest('id');

        if ($periodStart) {
            $query->whereRaw('COALESCE(activated_at, created_at) >= ?', [$periodStart]);
        }

        $rows = $query->take(20)->get()->map(function (StudentPackage $pkg) use ($isAr) {
            $price = (float) ($pkg->packageTemplate?->price ?? 0.0);

            return [
                'primary' => $pkg->studentUser?->name ?? ($isAr ? 'طالب' : 'Student'),
                'secondary' => $pkg->packageTemplate?->name ?? ($isAr ? 'باقة مخصصة' : 'Custom Plan'),
                'metric' => format_currency($price),
                'meta' => $pkg->total_sessions.' '.($isAr ? 'حصة' : 'sessions').' · '.($pkg->activated_at ?: $pkg->created_at)?->format('Y-m-d'),
                'status_label' => __(ucfirst($pkg->status)),
                'status_color' => $pkg->status === 'active' ? 'success' : 'info',
                'url' => route('filament.admin.resources.student-packages.edit', ['record' => $pkg->id]),
            ];
        })->all();

        return [
            'logic_rule' => $isAr
                ? 'صافي الإيراد المحجوز = مجموع المدفوعات المؤكدة + قيمة الباقات (Active / Exhausted) من خطط التسعير بدون ازدواجية.'
                : 'Gross Booked Revenue = Confirmed payments reconciled with Active & Exhausted package prices (zero double-counting).',
            'summary_pills' => [
                ['label' => $isAr ? 'إجمالي الإيراد' : 'Gross Booked', 'value' => format_currency($acc['gross_revenue']), 'color' => 'emerald'],
                ['label' => $isAr ? 'عدد الباقات المفعلة' : 'Booked Packages', 'value' => (string) $acc['booked_packages_count'], 'color' => 'teal'],
                ['label' => $isAr ? 'متوسط قيمة الباقة' : 'Avg Ticket Size', 'value' => format_currency($acc['avg_ticket']), 'color' => 'indigo'],
                ['label' => $isAr ? 'إيصالات نقدية مؤكدة' : 'Confirmed Cash', 'value' => (string) $acc['confirmed_payments_count'], 'color' => 'sky'],
            ],
            'rows' => $rows,
            'secondary_links' => [
                ['label' => $isAr ? 'الباقات النشطة' : 'Active Tab', 'url' => route('filament.admin.resources.student-packages.index', ['tab' => 'active'])],
                ['label' => $isAr ? 'الباقات المستنفدة' : 'Exhausted Tab', 'url' => route('filament.admin.resources.student-packages.index', ['tab' => 'exhausted'])],
            ],
        ];
    }

    protected function buildEarnedDeferredDrilldown(bool $isAr, ?Carbon $periodStart, array $acc): array
    {
        $query = StudentPackage::with(['studentUser', 'packageTemplate'])
            ->whereIn('status', ['active', 'exhausted'])
            ->latest('updated_at');

        if ($periodStart) {
            $query->whereRaw('COALESCE(activated_at, created_at) >= ?', [$periodStart]);
        }

        $rows = $query->take(20)->get()->map(function (StudentPackage $pkg) use ($isAr) {
            $price = (float) ($pkg->packageTemplate?->price ?? 0.0);
            $total = max(1, (int) $pkg->total_sessions);
            $earned = round($price * (min($total, $pkg->used_sessions) / $total), 2);
            $deferred = ($pkg->status === 'active' && (! $pkg->expires_at || $pkg->expires_at->isFuture() || $pkg->expires_at->isToday()))
                ? round($price * (min($total, $pkg->remaining_sessions) / $total), 2)
                : 0.0;

            return [
                'primary' => $pkg->studentUser?->name ?? ($isAr ? 'طالب' : 'Student'),
                'secondary' => ($pkg->packageTemplate?->name ?? __('Plan'))." ({$pkg->used_sessions}/{$pkg->total_sessions})",
                'metric' => format_currency($earned),
                'meta' => ($isAr ? 'مؤجل: ' : 'Deferred: ').format_currency($deferred),
                'status_label' => round(($pkg->used_sessions / $total) * 100).'% '.($isAr ? 'مستحق' : 'Earned'),
                'status_color' => $deferred > 0 ? 'teal' : 'success',
                'url' => route('filament.admin.resources.student-packages.edit', ['record' => $pkg->id]),
            ];
        })->all();

        return [
            'logic_rule' => $isAr
                ? 'المحقق (Earned) = (الحصص المستخدمة ÷ إجمالي الحصص) × سعر الباقة | المؤجل (Deferred) = الرصيد المتبقي الساري من الباقات النشطة.'
                : 'Accrual Recognition: Earned = (used_sessions / total_sessions) × price | Deferred Liability = unexpired active remaining value.',
            'summary_pills' => [
                ['label' => $isAr ? 'الإيراد المحقق (Earned)' : 'Earned Revenue', 'value' => format_currency($acc['earned_revenue']), 'color' => 'emerald'],
                ['label' => $isAr ? 'الالتزام المؤجل (Deferred)' : 'Deferred Liability', 'value' => format_currency($acc['deferred_liability']), 'color' => 'amber'],
                ['label' => $isAr ? 'معدل التحقق المحاسبي' : 'Realization Rate', 'value' => $acc['realization_rate'].'%', 'color' => 'teal'],
                ['label' => $isAr ? 'حصص منفذة' : 'Taught Credits', 'value' => (string) $acc['used_credits_count'], 'color' => 'indigo'],
            ],
            'rows' => $rows,
            'secondary_links' => [
                ['label' => $isAr ? 'الباقات السليمة' : 'Healthy Credits Tab', 'url' => route('filament.admin.resources.student-packages.index', ['tab' => 'healthy'])],
            ],
        ];
    }

    protected function buildPendingReceivablesDrilldown(bool $isAr, array $acc): array
    {
        $rows = $this->packagesPendingQuery()
            ->with(['studentUser', 'packageTemplate'])
            ->latest('created_at')
            ->take(20)
            ->get()
            ->map(function (StudentPackage $pkg) use ($isAr) {
                $price = (float) ($pkg->packageTemplate?->price ?? 0.0);

                return [
                    'primary' => $pkg->studentUser?->name ?? ($isAr ? 'طالب' : 'Student'),
                    'secondary' => $pkg->packageTemplate?->name ?? ($isAr ? 'باقة قيد الانتظار' : 'Pending Plan'),
                    'metric' => format_currency($price),
                    'meta' => $pkg->total_sessions.' '.($isAr ? 'حصة' : 'sessions').' · '.$pkg->created_at?->diffForHumans(),
                    'status_label' => $isAr ? 'بانتظار التفعيل' : 'Pending Payment',
                    'status_color' => 'warning',
                    'url' => route('filament.admin.resources.student-packages.edit', ['record' => $pkg->id]),
                ];
            })->all();

        return [
            'logic_rule' => $isAr
                ? 'تشمل الباقات ذات الحالة (pending) والمدفوعات غير المؤكدة في سجل التحصيل.'
                : 'Live count & EGP total of StudentPackage records with status = pending plus unconfirmed payments.',
            'summary_pills' => [
                ['label' => $isAr ? 'إجمالي المعلقات' : 'Pending Total', 'value' => format_currency($acc['pending_amount']), 'color' => 'amber'],
                ['label' => $isAr ? 'باقات قيد الانتظار' : 'Pending Packages', 'value' => (string) $acc['pending_packages_count'], 'color' => 'warning'],
                ['label' => $isAr ? 'مدفوعات قيد المراجعة' : 'Pending Cash Rows', 'value' => (string) $acc['pending_payments_count'], 'color' => 'sky'],
            ],
            'rows' => $rows,
            'secondary_links' => [],
        ];
    }

    protected function buildCollectionsDueDrilldown(bool $isAr, array $acc): array
    {
        $duePackages = $this->collectionsPackagesQuery()
            ->with(['studentUser', 'packageTemplate'])
            ->orderBy('remaining_sessions')
            ->take(12)
            ->get()
            ->map(function (StudentPackage $pkg) use ($isAr) {
                $price = (float) ($pkg->packageTemplate?->price ?? 0.0);
                $rem = (int) $pkg->remaining_sessions;

                return [
                    'primary' => $pkg->studentUser?->name ?? ($isAr ? 'طالب' : 'Student'),
                    'secondary' => $pkg->packageTemplate?->name ?? ($isAr ? 'باقة' : 'Package'),
                    'metric' => $rem.' / '.$pkg->total_sessions.' '.($isAr ? 'حصة' : 'left'),
                    'meta' => ($isAr ? 'قيمة التجديد: ' : 'Renewal: ').format_currency($price),
                    'status_label' => $rem <= 0 ? ($isAr ? 'رصيد 0 (نافد)' : 'Exhausted (0)') : ($isAr ? 'رصيد حرج / قارب الانتهاء' : 'Low / Expiring'),
                    'status_color' => $rem <= 0 ? 'danger' : 'warning',
                    'url' => route('filament.admin.pages.student-collections', ['tab' => 'packages']),
                ];
            });

        $dueSchedules = $this->collectionsSchedulesQuery()
            ->with(['studentUser', 'course'])
            ->orderBy('end_date')
            ->take(8)
            ->get()
            ->map(function (RecurringSchedule $sch) use ($isAr) {
                return [
                    'primary' => $sch->studentUser?->name ?? $sch->title,
                    'secondary' => $sch->course?->title ?? $sch->title,
                    'metric' => $sch->end_date ? $sch->end_date->format('Y-m-d') : '—',
                    'meta' => $isAr ? 'دورة جدول دوري' : 'Recurring Cycle',
                    'status_label' => $sch->end_date && $sch->end_date->isPast() ? ($isAr ? 'انتهت الدورة' : 'Cycle Ended') : ($isAr ? 'تنتهي خلال 14 يوم' : 'Ends <=14d'),
                    'status_color' => 'warning',
                    'url' => route('filament.admin.pages.student-collections', ['tab' => 'schedules']),
                ];
            });

        return [
            'logic_rule' => $isAr
                ? 'مطابق تماماً لشارة القائمة الجانبية: الباقات (Active/Exhausted) برصيد ≤ 3 حصص أو تنتهي خلال 7 أيام + الجداول الدورية النشطة المنتهية خلال 14 يوماً.'
                : 'Matches Sidebar Badge 1:1 — Packages (active/exhausted) with <=3 sessions left or expiring in <=7d + Active Recurring Schedules ending in <=14d.',
            'summary_pills' => [
                ['label' => $isAr ? 'باقات مستحقة التجديد' : 'Due Packages', 'value' => (string) $this->collectionsPackagesQuery()->count(), 'color' => 'rose'],
                ['label' => $isAr ? 'جداول تنتهي قريباً' : 'Ending Schedules', 'value' => (string) $this->collectionsSchedulesQuery()->count(), 'color' => 'amber'],
                ['label' => $isAr ? 'فرصة التجديد المتوقعة' : 'Renewal Opportunity', 'value' => format_currency($acc['collections_renewal_value']), 'color' => 'emerald'],
            ],
            'rows' => $duePackages->concat($dueSchedules)->values()->all(),
            'secondary_links' => [
                ['label' => $isAr ? 'تبويب الباقات' : 'Packages Tab', 'url' => route('filament.admin.pages.student-collections', ['tab' => 'packages'])],
                ['label' => $isAr ? 'تبويب الجداول الدورية' : 'Schedules Tab', 'url' => route('filament.admin.pages.student-collections', ['tab' => 'schedules'])],
            ],
        ];
    }

    protected function buildTodaysSessionsDrilldown(bool $isAr): array
    {
        $rows = $this->sessionsTodayQuery()
            ->with(['teacherProfile.user', 'student', 'course'])
            ->orderBy('scheduled_at')
            ->take(20)
            ->get()
            ->map(function (LiveSession $sess) use ($isAr) {
                $statusColor = match ($sess->status) {
                    'in_progress', 'live', 'link_visible' => 'danger',
                    'completed' => 'success',
                    'cancelled', 'cancelled_by_teacher' => 'neutral',
                    default => 'info',
                };

                return [
                    'primary' => $sess->title ?: ($sess->course?->title ?? ($isAr ? 'حصة مباشرة' : 'Live Session')),
                    'secondary' => ($sess->teacherProfile?->user?->name ?? '—').' → '.($sess->student?->name ?? ($isAr ? 'فصل جماعي' : 'Cohort')),
                    'metric' => ($sess->scheduled_at ?: $sess->start_at)?->format('h:i A') ?? '—',
                    'meta' => $sess->duration_minutes.' '.($isAr ? 'د' : 'm').' · '.__(ucfirst($sess->meeting_platform ?: 'online')),
                    'status_label' => __(ucfirst(str_replace('_', ' ', (string) $sess->status))),
                    'status_color' => $statusColor,
                    'url' => route('filament.admin.resources.live-sessions.edit', ['record' => $sess->id]),
                ];
            })->all();

        $liveCount = $this->sessionsLiveNowQuery()->count();
        $todayCount = $this->sessionsTodayQuery()->count();
        $completedToday = $this->sessionsTodayQuery()->where('status', 'completed')->count();

        return [
            'logic_rule' => $isAr
                ? 'جميع الحصص المجدولة أو التي تبدأ بتاريخ اليوم (scheduled_at أو start_at = اليوم)، مطابقة لتبويب Today.'
                : 'All sessions where scheduled_at OR start_at equals today — identical to LiveSessions ?tab=today.',
            'summary_pills' => [
                ['label' => $isAr ? 'إجمالي اليوم' : 'All Today', 'value' => (string) $todayCount, 'color' => 'teal'],
                ['label' => $isAr ? 'مباشر الآن' : 'Live Now', 'value' => (string) $liveCount, 'color' => 'rose'],
                ['label' => $isAr ? 'مكتمل اليوم' : 'Completed Today', 'value' => (string) $completedToday, 'color' => 'emerald'],
            ],
            'rows' => $rows,
            'secondary_links' => [
                ['label' => $isAr ? 'البث المباشر الآن' : 'Live In Progress Tab', 'url' => route('filament.admin.resources.live-sessions.index', ['tab' => 'live'])],
            ],
        ];
    }

    protected function buildUpcomingSessionsDrilldown(bool $isAr): array
    {
        $rows = $this->sessionsUpcomingQuery()
            ->with(['teacherProfile.user', 'student', 'course'])
            ->orderBy('scheduled_at')
            ->take(20)
            ->get()
            ->map(function (LiveSession $sess) use ($isAr) {
                return [
                    'primary' => $sess->title ?: ($sess->course?->title ?? ($isAr ? 'حصة قادمة' : 'Session')),
                    'secondary' => ($sess->teacherProfile?->user?->name ?? '—').' · '.($sess->student?->name ?? ($isAr ? 'فصل جماعي' : 'Cohort')),
                    'metric' => ($sess->scheduled_at ?: $sess->start_at)?->format('M d, h:i A') ?? '—',
                    'meta' => $sess->is_free_demo ? ($isAr ? 'حصة تجريبية' : 'Free Demo') : ($isAr ? 'حصة أساسية' : 'Regular'),
                    'status_label' => __(ucfirst(str_replace('_', ' ', (string) $sess->status))),
                    'status_color' => $sess->is_free_demo ? 'warning' : 'indigo',
                    'url' => route('filament.admin.resources.live-sessions.edit', ['record' => $sess->id]),
                ];
            })->all();

        $totalUpcoming = $this->sessionsUpcomingQuery()->count();
        $hours = round(((int) $this->sessionsUpcomingQuery()->sum('duration_minutes')) / 60, 1);

        return [
            'logic_rule' => $isAr
                ? 'الحصص التي موعدها في المستقبل (scheduled_at > الآن أو start_at > الآن) وليست مكتملة أو ملغاة — مطابقة لتبويب Upcoming.'
                : 'Future sessions (scheduled_at > now OR start_at > now) excluding completed/cancelled — identical to ?tab=upcoming.',
            'summary_pills' => [
                ['label' => $isAr ? 'إجمالي القادمة' : 'Total Upcoming', 'value' => (string) $totalUpcoming, 'color' => 'indigo'],
                ['label' => $isAr ? 'ساعات التدريس المجدولة' : 'Total Scheduled Hours', 'value' => $hours.' hrs', 'color' => 'sky'],
            ],
            'rows' => $rows,
            'secondary_links' => [
                ['label' => $isAr ? 'حصص تجريبية مجانية' : 'Free Demo Tab', 'url' => route('filament.admin.resources.live-sessions.index', ['tab' => 'free_demo'])],
                ['label' => $isAr ? 'الحصص الأساسية' : 'Regular Sessions Tab', 'url' => route('filament.admin.resources.live-sessions.index', ['tab' => 'regular'])],
            ],
        ];
    }

    protected function buildSessionDeliveryDrilldown(bool $isAr, string $timeframe): array
    {
        $completed = $this->sessionsCompletedQuery($timeframe)->count();
        $cancelled = $this->sessionsCancelledQuery($timeframe)->count();
        $freeDemo = $this->sessionsFreeDemoQuery($timeframe)->count();
        $regular = $this->sessionsRegularQuery($timeframe)->count();

        $rows = $this->applySessionTimeframe(
            LiveSession::with(['teacherProfile.user', 'student', 'course'])->whereIn('status', ['completed', 'cancelled', 'cancelled_by_teacher']),
            $timeframe
        )
            ->latest('scheduled_at')
            ->take(20)
            ->get()
            ->map(function (LiveSession $sess) use ($isAr) {
                $isComp = $sess->status === 'completed';

                return [
                    'primary' => $sess->title ?: ($sess->course?->title ?? '—'),
                    'secondary' => ($sess->teacherProfile?->user?->name ?? '—').' → '.($sess->student?->name ?? ($isAr ? 'فصل جماعي' : 'Cohort')),
                    'metric' => ($sess->scheduled_at ?: $sess->start_at)?->format('Y-m-d H:i') ?? '—',
                    'meta' => $sess->is_free_demo ? ($isAr ? 'تجريبية' : 'Free Demo') : ($isAr ? 'أساسية' : 'Regular'),
                    'status_label' => __(ucfirst(str_replace('_', ' ', (string) $sess->status))),
                    'status_color' => $isComp ? 'success' : 'danger',
                    'url' => route('filament.admin.resources.live-sessions.edit', ['record' => $sess->id]),
                ];
            })->all();

        return [
            'logic_rule' => $isAr
                ? 'نسبة الكفاءة = الحصص المكتملة ÷ (الحصص المكتملة + الحصص الملغاة). مطابقة لتبويبات Completed / Cancelled / Regular / Free Demo.'
                : 'Completion Rate = Completed ÷ (Completed + Cancelled). Sub-counts match ?tab=completed, ?tab=cancelled, ?tab=regular, ?tab=free_demo.',
            'summary_pills' => [
                ['label' => $isAr ? 'مكتملة' : 'Completed', 'value' => (string) $completed, 'color' => 'emerald'],
                ['label' => $isAr ? 'ملغاة' : 'Cancelled', 'value' => (string) $cancelled, 'color' => 'rose'],
                ['label' => $isAr ? 'حصة أساسية' : 'Regular', 'value' => (string) $regular, 'color' => 'indigo'],
                ['label' => $isAr ? 'تجريبية مجانية' : 'Free Demo', 'value' => (string) $freeDemo, 'color' => 'amber'],
            ],
            'rows' => $rows,
            'secondary_links' => [
                ['label' => $isAr ? 'الحصص الملغاة' : 'Cancelled Tab', 'url' => route('filament.admin.resources.live-sessions.index', ['tab' => 'cancelled'])],
                ['label' => $isAr ? 'التجريبية المجانية' : 'Free Demo Tab', 'url' => route('filament.admin.resources.live-sessions.index', ['tab' => 'free_demo'])],
                ['label' => $isAr ? 'الأساسية' : 'Regular Tab', 'url' => route('filament.admin.resources.live-sessions.index', ['tab' => 'regular'])],
            ],
        ];
    }

    protected function buildOpenExceptionsDrilldown(bool $isAr): array
    {
        $rows = $this->exceptionsQuery()
            ->with(['studentUser', 'course', 'liveSession'])
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->latest('created_at')
            ->take(20)
            ->get()
            ->map(function (ExceptionRequest $exc) use ($isAr) {
                $color = match ($exc->status) {
                    'approved' => 'success',
                    'rejected' => 'danger',
                    default => 'warning',
                };

                return [
                    'primary' => $exc->studentUser?->name ?? ($isAr ? 'طالب' : 'Student'),
                    'secondary' => $exc->liveSession?->title ?? $exc->course?->title ?? ($isAr ? 'استثناء عام' : 'Global Exception'),
                    'metric' => str($exc->reason ?: '—')->limit(32),
                    'meta' => $exc->created_at?->diffForHumans() ?? '—',
                    'status_label' => __(ucfirst((string) $exc->status)),
                    'status_color' => $color,
                    'url' => route('filament.admin.resources.exception-requests.edit', ['record' => $exc->id]),
                ];
            })->all();

        return [
            'logic_rule' => $isAr
                ? 'العدد الأساسي يعرض فقط الطلبات المعلقة (status = pending) التي تحتاج تدخلاً إدارياً، مع إمكانية استعراض المقبولة والمرفوضة.'
                : 'Primary KPI tracks actionable requests with status = pending. Approved/Rejected history is itemized below.',
            'summary_pills' => [
                ['label' => $isAr ? 'معلقة (قيد المراجعة)' : 'Pending', 'value' => (string) $this->exceptionsQuery('pending')->count(), 'color' => 'amber'],
                ['label' => $isAr ? 'مقبولة' : 'Approved', 'value' => (string) $this->exceptionsQuery('approved')->count(), 'color' => 'emerald'],
                ['label' => $isAr ? 'مرفوضة' : 'Rejected', 'value' => (string) $this->exceptionsQuery('rejected')->count(), 'color' => 'rose'],
            ],
            'rows' => $rows,
            'secondary_links' => [
                ['label' => $isAr ? 'كافة الطلبات' : 'All Exceptions Tab', 'url' => route('filament.admin.resources.exception-requests.index', ['tab' => 'all'])],
                ['label' => $isAr ? 'الطلبات المقبولة' : 'Approved Tab', 'url' => route('filament.admin.resources.exception-requests.index', ['tab' => 'approved'])],
            ],
        ];
    }

    protected function buildStudentsDrilldown(bool $isAr): array
    {
        $rows = $this->studentsQuery()
            ->with(['user', 'gradeLevel'])
            ->latest('id')
            ->take(20)
            ->get()
            ->map(function (StudentProfile $profile) use ($isAr) {
                $status = $profile->user?->status?->value ?? (string) ($profile->user?->status ?? 'pending');

                return [
                    'primary' => $profile->user?->name ?? '—',
                    'secondary' => $profile->user?->email ?? '—',
                    'metric' => $profile->gradeLevel?->name ?? ($isAr ? 'غير محدد' : 'General'),
                    'meta' => $profile->school_name ?: ($profile->user?->phone ?? '—'),
                    'status_label' => __(ucfirst($status)),
                    'status_color' => $status === 'approved' ? 'success' : ($status === 'pending' ? 'warning' : 'danger'),
                    'url' => route('filament.admin.resources.student-profiles.edit', ['record' => $profile->id]),
                ];
            })->all();

        return [
            'logic_rule' => $isAr
                ? 'مطابق لتبويبات StudentProfiles: يفصل الطلاب المعتمدين (approved) عن المنتظرين (pending) مع ربط مباشر لملف كل طالب.'
                : 'Synced 1:1 with ListStudentProfiles tabs (Approved vs Pending vs All active student profiles).',
            'summary_pills' => [
                ['label' => $isAr ? 'الطلاب المعتمدين' : 'Approved', 'value' => (string) $this->studentsQuery(AccountStatus::APPROVED)->count(), 'color' => 'emerald'],
                ['label' => $isAr ? 'بانتظار الاعتماد' : 'Pending', 'value' => (string) $this->studentsQuery(AccountStatus::PENDING)->count(), 'color' => 'amber'],
                ['label' => $isAr ? 'الإجمالي' : 'Total', 'value' => (string) $this->studentsQuery()->count(), 'color' => 'teal'],
            ],
            'rows' => $rows,
            'secondary_links' => [
                ['label' => $isAr ? 'كافة الطلاب' : 'All Students Tab', 'url' => route('filament.admin.resources.student-profiles.index', ['tab' => 'all'])],
                ['label' => $isAr ? 'بانتظار الموافقة' : 'Pending Approval Tab', 'url' => route('filament.admin.resources.student-profiles.index', ['tab' => 'pending'])],
                ['label' => $isAr ? 'المعتمدين' : 'Approved Tab', 'url' => route('filament.admin.resources.student-profiles.index', ['tab' => 'approved'])],
            ],
        ];
    }

    protected function buildFacultyDrilldown(bool $isAr): array
    {
        $rows = $this->teachersQuery()
            ->with(['user'])
            ->withCount('courses')
            ->latest('id')
            ->take(20)
            ->get()
            ->map(function (TeacherProfile $teacher) use ($isAr) {
                $status = $teacher->user?->status?->value ?? (string) ($teacher->user?->status ?? 'pending');

                return [
                    'primary' => $teacher->user?->name ?? '—',
                    'secondary' => $teacher->specialization ?: ($teacher->title ?: ($isAr ? 'معلم' : 'Instructor')),
                    'metric' => $teacher->courses_count.' '.($isAr ? 'كورس' : 'courses'),
                    'meta' => $teacher->user?->email ?? '—',
                    'status_label' => __(ucfirst($status)),
                    'status_color' => $status === 'approved' ? 'success' : 'warning',
                    'url' => route('filament.admin.resources.teacher-profiles.edit', ['record' => $teacher->id]),
                ];
            })->all();

        return [
            'logic_rule' => $isAr
                ? 'يعرض المعلمين النشطين والمعتمدين وعدد الكورسات الفعالة لكل منهم مع مطابقة تبويبات TeacherProfiles.'
                : 'Synced with TeacherProfiles tabs (?tab=approved / ?tab=pending / ?tab=all) and active Courses.',
            'summary_pills' => [
                ['label' => $isAr ? 'معلمين معتمدين' : 'Approved Faculty', 'value' => (string) $this->teachersQuery(AccountStatus::APPROVED)->count(), 'color' => 'indigo'],
                ['label' => $isAr ? 'قيد المراجعة' : 'Pending', 'value' => (string) $this->teachersQuery(AccountStatus::PENDING)->count(), 'color' => 'amber'],
                ['label' => $isAr ? 'كورسات منشورة نشطة' : 'Active Courses', 'value' => (string) Course::where('is_active', true)->count(), 'color' => 'teal'],
            ],
            'rows' => $rows,
            'secondary_links' => [
                ['label' => $isAr ? 'كافة المعلمين' : 'All Teachers Tab', 'url' => route('filament.admin.resources.teacher-profiles.index', ['tab' => 'all'])],
                ['label' => $isAr ? 'تقارير المعلمين' : 'Teacher Reports', 'url' => route('filament.admin.pages.teacher-reports-page')],
                ['label' => $isAr ? 'إدارة الكورسات' : 'Courses List', 'url' => route('filament.admin.resources.courses.index')],
            ],
        ];
    }

    protected function buildActiveCreditsDrilldown(bool $isAr): array
    {
        $rows = $this->packagesHealthyQuery()
            ->with(['studentUser', 'packageTemplate'])
            ->orderBy('remaining_sessions', 'desc')
            ->take(20)
            ->get()
            ->map(function (StudentPackage $pkg) use ($isAr) {
                return [
                    'primary' => $pkg->studentUser?->name ?? ($isAr ? 'طالب' : 'Student'),
                    'secondary' => $pkg->packageTemplate?->name ?? ($isAr ? 'باقة' : 'Plan'),
                    'metric' => $pkg->remaining_sessions.' / '.$pkg->total_sessions.' '.($isAr ? 'متبقي' : 'left'),
                    'meta' => $pkg->expires_at ? ($isAr ? 'ينتهي: ' : 'Exp: ').$pkg->expires_at->format('Y-m-d') : ($isAr ? 'بدون تاريخ انتهاء' : 'No Expiry'),
                    'status_label' => $isAr ? 'سليم ونشط' : 'Healthy Active',
                    'status_color' => $pkg->remaining_sessions <= 3 ? 'warning' : 'success',
                    'url' => route('filament.admin.resources.student-packages.edit', ['record' => $pkg->id]),
                ];
            })->all();

        $healthyQuery = $this->packagesHealthyQuery();

        return [
            'logic_rule' => $isAr
                ? 'باقات سليمة (status = active AND remaining_sessions > 0 AND غير منتهية الصلاحية) — مطابقة لتبويب Healthy Active Credits.'
                : 'Healthy Credit Packages: status = active AND remaining_sessions > 0 AND unexpired — identical to ?tab=healthy.',
            'summary_pills' => [
                ['label' => $isAr ? 'باقات سليمة' : 'Healthy Packages', 'value' => (string) (clone $healthyQuery)->count(), 'color' => 'emerald'],
                ['label' => $isAr ? 'حصص متبقية في الرصيد' : 'Remaining Credits', 'value' => (string) (int) (clone $healthyQuery)->sum('remaining_sessions'), 'color' => 'teal'],
                ['label' => $isAr ? 'إجمالي باقات active' : 'All status=active', 'value' => (string) $this->packagesActiveQuery()->count(), 'color' => 'sky'],
            ],
            'rows' => $rows,
            'secondary_links' => [
                ['label' => $isAr ? 'كل الباقات النشطة' : 'All Active Tab', 'url' => route('filament.admin.resources.student-packages.index', ['tab' => 'active'])],
            ],
        ];
    }

    protected function buildParentsDrilldown(bool $isAr): array
    {
        $rows = $this->parentsQuery()
            ->with(['user'])
            ->latest('id')
            ->take(20)
            ->get()
            ->map(function (ParentProfile $parent) use ($isAr) {
                $childrenCount = $parent->user
                    ? DB::table('parent_student')->where('parent_user_id', $parent->user->id)->count()
                    : 0;
                $status = $parent->user?->status?->value ?? (string) ($parent->user?->status ?? 'pending');

                return [
                    'primary' => $parent->user?->name ?? '—',
                    'secondary' => $parent->user?->email ?? ($parent->user?->phone ?? '—'),
                    'metric' => $childrenCount.' '.($isAr ? 'أبناء مرتبطين' : 'children'),
                    'meta' => $parent->user?->phone ?? '—',
                    'status_label' => __(ucfirst($status)),
                    'status_color' => $status === 'approved' ? 'success' : 'warning',
                    'url' => route('filament.admin.resources.parent-profiles.edit', ['record' => $parent->id]),
                ];
            })->all();

        $totalStudents = $this->studentsQuery()->count();
        $linkedStudents = Schema::hasTable('parent_student')
            ? (int) DB::table('parent_student')->distinct('student_user_id')->count('student_user_id')
            : 0;

        return [
            'logic_rule' => $isAr
                ? 'أولياء الأمور النشطين وحسابات الأبناء المرتبطة فعلياً في جدول parent_student.'
                : 'Active ParentProfiles reconciled against linked student_user_id records in parent_student.',
            'summary_pills' => [
                ['label' => $isAr ? 'إجمالي أولياء الأمور' : 'Total Parents', 'value' => (string) $this->parentsQuery()->count(), 'color' => 'sky'],
                ['label' => $isAr ? 'طلاب مرتبطين' : 'Linked Students', 'value' => "{$linkedStudents} / {$totalStudents}", 'color' => 'teal'],
            ],
            'rows' => $rows,
            'secondary_links' => [
                ['label' => $isAr ? 'تقارير الطلاب' : 'Student Reports', 'url' => route('filament.admin.pages.student-reports-page')],
            ],
        ];
    }
}
