<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Filament\Pages\StudentCollectionsPage;
use App\Filament\Resources\ExceptionRequests\Pages\ListExceptionRequests;
use App\Filament\Resources\LiveSessions\Pages\ListLiveSessions;
use App\Filament\Resources\ParentProfiles\Pages\ListParentProfiles;
use App\Filament\Resources\StudentPackages\Pages\ListStudentPackages;
use App\Filament\Resources\StudentProfiles\Pages\ListStudentProfiles;
use App\Filament\Resources\TeacherProfiles\Pages\ListTeacherProfiles;
use App\Filament\Widgets\AdminOverviewStatsWidget;
use App\Filament\Widgets\SessionsAnalyticsChartWidget;
use App\Filament\Widgets\StudentAccountStatusChartWidget;
use App\Models\AdminProfile;
use App\Models\Category;
use App\Models\Course;
use App\Models\ExceptionRequest;
use App\Models\LiveSession;
use App\Models\PackageTemplate;
use App\Models\ParentProfile;
use App\Models\RecurringSchedule;
use App\Models\StudentPackage;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use App\Services\Analytics\AdminDashboardMetricsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminDashboardOverviewStatsTest extends TestCase
{
    use RefreshDatabase;

    protected function createAdminUser(): User
    {
        $admin = User::factory()->create(['status' => AccountStatus::APPROVED]);
        AdminProfile::create(['user_id' => $admin->id]);
        $role = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $admin->assignRole($role);

        return $admin;
    }

    public function test_admin_dashboard_computes_12_balanced_kpis_dual_accounting_and_fixed_tab_urls(): void
    {
        // 1. Students (1 Approved, 1 Pending)
        $activeStudent = User::factory()->create(['name' => 'Omar Active', 'status' => AccountStatus::APPROVED]);
        StudentProfile::create(['user_id' => $activeStudent->id]);

        $pendingStudent = User::factory()->create(['name' => 'Sara Pending', 'status' => AccountStatus::PENDING]);
        StudentProfile::create(['user_id' => $pendingStudent->id]);

        // 2. Parent linked to active student
        $parentUser = User::factory()->create(['status' => AccountStatus::APPROVED]);
        ParentProfile::create(['user_id' => $parentUser->id]);
        DB::table('parent_student')->insert([
            'parent_user_id' => $parentUser->id,
            'student_user_id' => $activeStudent->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Teacher, Category, Subject & Course
        $teacherUser = User::factory()->create(['status' => AccountStatus::APPROVED]);
        $teacher = TeacherProfile::create(['user_id' => $teacherUser->id, 'slug' => 'stat-teacher']);
        $cat = Category::create(['name' => 'Stat Cat', 'slug' => 'stat-cat']);
        $sub = Subject::create(['category_id' => $cat->id, 'name' => 'Stat Sub', 'slug' => 'stat-sub']);
        $course = Course::create([
            'title' => 'Stat Course',
            'slug' => 'stat-course',
            'teacher_id' => $teacher->id,
            'subject_id' => $sub->id,
            'is_active' => true,
        ]);

        // 4. Sessions (1 today completed regular, 1 upcoming regular, 1 cancelled free demo)
        LiveSession::create([
            'student_user_id' => $activeStudent->id,
            'teacher_profile_id' => $teacher->id,
            'subject_id' => $sub->id,
            'course_id' => $course->id,
            'scheduled_at' => now(),
            'start_at' => now(),
            'end_at' => now()->addHour(),
            'status' => 'completed',
            'is_free_demo' => false,
        ]);

        LiveSession::create([
            'student_user_id' => $activeStudent->id,
            'teacher_profile_id' => $teacher->id,
            'subject_id' => $sub->id,
            'course_id' => $course->id,
            'scheduled_at' => now()->addDays(2),
            'start_at' => now()->addDays(2),
            'end_at' => now()->addDays(2)->addMinutes(90),
            'status' => 'scheduled',
            'is_free_demo' => false,
        ]);

        LiveSession::create([
            'student_user_id' => $activeStudent->id,
            'teacher_profile_id' => $teacher->id,
            'subject_id' => $sub->id,
            'course_id' => $course->id,
            'scheduled_at' => now()->addDays(3),
            'start_at' => now()->addDays(3),
            'end_at' => now()->addDays(3)->addHour(),
            'status' => 'cancelled_by_teacher',
            'is_free_demo' => true,
        ]);

        // 5. Dual Accounting Fixtures:
        // Plan A (price = 1000, 10 sessions): Active healthy (4 used, 6 left) -> Earned = 400, Deferred = 600
        $templateA = PackageTemplate::create([
            'name' => 'Gold 10',
            'sessions_count' => 10,
            'price' => 1000.00,
            'is_active' => true,
        ]);
        StudentPackage::create([
            'student_user_id' => $activeStudent->id,
            'course_id' => $course->id,
            'package_template_id' => $templateA->id,
            'total_sessions' => 10,
            'used_sessions' => 4,
            'remaining_sessions' => 6,
            'status' => 'active',
            'activated_at' => now()->subDays(5),
        ]);

        // Plan B (price = 500, 4 sessions): Exhausted (4 used, 0 left) with Confirmed Cash Payment of 450 (must NOT double count!)
        $templateB = PackageTemplate::create([
            'name' => 'Starter 4',
            'sessions_count' => 4,
            'price' => 500.00,
            'is_active' => true,
        ]);
        $pkgB = StudentPackage::create([
            'student_user_id' => $activeStudent->id,
            'course_id' => $course->id,
            'package_template_id' => $templateB->id,
            'total_sessions' => 4,
            'used_sessions' => 4,
            'remaining_sessions' => 0,
            'status' => 'exhausted',
            'activated_at' => now()->subMonth(),
        ]);
        DB::table('payments')->insert([
            'student_user_id' => $activeStudent->id,
            'student_package_id' => $pkgB->id,
            'amount' => 450.00,
            'sessions_count' => 4,
            'payment_method' => 'bank_transfer',
            'payment_date' => now()->subMonth()->toDateString(),
            'status' => 'confirmed',
            'created_at' => now()->subMonth(),
            'updated_at' => now()->subMonth(),
        ]);

        // Plan C (price = 800, 8 sessions): Pending payment -> Pending Receivables = 800
        $templateC = PackageTemplate::create([
            'name' => 'VIP 8',
            'sessions_count' => 8,
            'price' => 800.00,
            'is_active' => true,
        ]);
        StudentPackage::create([
            'student_user_id' => $pendingStudent->id,
            'course_id' => $course->id,
            'package_template_id' => $templateC->id,
            'total_sessions' => 8,
            'used_sessions' => 0,
            'remaining_sessions' => 8,
            'status' => 'pending',
        ]);

        // Expiring recurring schedule (within 14 days) for collections parity check
        RecurringSchedule::create([
            'student_user_id' => $activeStudent->id,
            'teacher_profile_id' => $teacher->id,
            'course_id' => $course->id,
            'title' => 'Expiring Cohort Cycle',
            'days_of_week' => [1, 3],
            'start_time' => '16:00',
            'duration_minutes' => 60,
            'start_date' => now()->subWeeks(3)->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
            'status' => 'active',
        ]);

        // Pending exception
        ExceptionRequest::create([
            'student_user_id' => $activeStudent->id,
            'reason' => 'Absence excuse',
            'status' => 'pending',
        ]);

        $widget = new AdminOverviewStatsWidget;
        $overview = $widget->getKpiData();

        // 12 KPIs split across 3 balanced 4-card sections
        $this->assertCount(12, $overview['kpis']);
        $this->assertCount(4, $overview['sections']['financials']['keys']);
        $this->assertCount(4, $overview['sections']['operations']['keys']);
        $this->assertCount(4, $overview['sections']['academic']['keys']);

        // Accounting accuracy:
        // Gross = 1000 (templateA) + 450 (confirmed payment B overriding template 500) = 1450
        $this->assertSame(1450.0, $overview['kpis']['collected_revenue']['raw_value']);
        // Earned = (4/10 * 1000) + (4/4 * 450) = 400 + 450 = 850
        $this->assertSame(850.0, $overview['kpis']['earned_deferred_revenue']['raw_value']);
        // Pending = 800
        $this->assertSame(1, $overview['kpis']['pending_receivables']['raw_value']);
        $this->assertStringContainsString('800', $overview['kpis']['pending_receivables']['value']);

        // Collections parity with sidebar badge (1 exhausted package + 1 expiring schedule = 2)
        $this->assertSame(2, $overview['kpis']['collections_due']['raw_value']);
        $this->assertSame('2', StudentCollectionsPage::getNavigationBadge());

        // Timeframe filtering ('today' narrows gross revenue flow to 0 since packages were activated 5 days / 1 month ago)
        $service = app(AdminDashboardMetricsService::class);
        $todayOverview = $service->getDashboardOverview('today');
        $this->assertSame(0.0, $todayOverview['kpis']['collected_revenue']['raw_value']);
        // Delivery efficiency today = 1 completed / 1 total today = 100%
        $this->assertSame('100%', $todayOverview['kpis']['session_delivery']['value']);

        // All target URLs must use Filament v5 ?tab= and NEVER activeTab=
        foreach ($overview['kpis'] as $kpi) {
            $this->assertStringNotContainsString('activeTab=', $kpi['target_url']);
        }
        $this->assertStringContainsString('tab=today', $overview['kpis']['todays_sessions']['target_url']);
        $this->assertStringContainsString('tab=pending', $overview['kpis']['open_exceptions']['target_url']);
        $this->assertStringContainsString('tab=healthy', $overview['kpis']['active_credits_pool']['target_url']);
        $this->assertStringContainsString('tab=pending', $overview['kpis']['students_overview']['target_url']);
    }

    public function test_livewire_kpi_widget_drilldown_modal_filtering_charts_and_resource_tabs_work(): void
    {
        $admin = $this->createAdminUser();
        $this->actingAs($admin);

        $student = User::factory()->create(['name' => 'Ziad Drilldown', 'status' => AccountStatus::APPROVED]);
        StudentProfile::create(['user_id' => $student->id]);

        $template = PackageTemplate::create([
            'name' => 'Pro Monthly 12',
            'sessions_count' => 12,
            'price' => 1200.00,
            'is_active' => true,
        ]);
        StudentPackage::create([
            'student_user_id' => $student->id,
            'package_template_id' => $template->id,
            'total_sessions' => 12,
            'used_sessions' => 3,
            'remaining_sessions' => 9,
            'status' => 'active',
            'activated_at' => now(),
        ]);

        ExceptionRequest::create([
            'student_user_id' => $student->id,
            'reason' => 'Family travel',
            'status' => 'pending',
        ]);

        // Test interactive Livewire widget: Smart Glass Bento panels + category filter + drill-down + close modal
        Livewire::test(AdminOverviewStatsWidget::class)
            ->assertSee('Executive Financial & Operations Telemetry')
            ->assertSee('sg-bento-row')
            ->assertSee('sg-mosaic-grid')
            ->assertSee('sg-eq-bars')
            ->assertSee('sg-donut')
            ->assertSee('sg-heat-strip')
            ->call('setCategoryFilter', 'financials')
            ->assertSet('categoryFilter', 'financials')
            ->call('inspectKpi', 'earned_deferred_revenue')
            ->assertSet('showDetailModal', true)
            ->assertSet('inspectedKpiKey', 'earned_deferred_revenue')
            ->assertSee('Ziad Drilldown')
            ->assertSee('Pro Monthly 12 (3/12)')
            ->assertSee('Accrual Recognition')
            ->call('closeDetailModal')
            ->assertSet('showDetailModal', false)
            ->assertSet('inspectedKpiKey', null);

        // Chart parity check
        $sessionsChart = app(AdminDashboardMetricsService::class)->getSessionsChartData();
        $widgetSessionsChart = new class extends SessionsAnalyticsChartWidget
        {
            public function exposedData(): array
            {
                return $this->getData();
            }
        };
        $this->assertSame($sessionsChart['datasets'][0]['data'], $widgetSessionsChart->exposedData()['datasets'][0]['data']);

        $studentChart = app(AdminDashboardMetricsService::class)->getStudentStatusChartData();
        $widgetStudentChart = new class extends StudentAccountStatusChartWidget
        {
            public function exposedData(): array
            {
                return $this->getData();
            }
        };
        $this->assertSame($studentChart['datasets'][0]['data'], $widgetStudentChart->exposedData()['datasets'][0]['data']);

        // Resource Tabs existence & HTTP 200 verification with ?tab=...
        $this->assertArrayHasKey('regular', (new ListLiveSessions)->getTabs());
        $this->assertArrayHasKey('healthy', (new ListStudentPackages)->getTabs());
        $this->assertArrayHasKey('exhausted', (new ListStudentPackages)->getTabs());
        $this->assertArrayHasKey('pending', (new ListExceptionRequests)->getTabs());
        $this->assertArrayHasKey('approved', (new ListTeacherProfiles)->getTabs());
        $this->assertArrayHasKey('all', (new ListParentProfiles)->getTabs());
        $this->assertArrayHasKey('pending', (new ListStudentProfiles)->getTabs());

        $this->get('/admin')
            ->assertOk()
            ->assertSee('--sg-canvas-bg')
            ->assertSee('--sys-primary-rgb')
            ->assertSee('backdrop-filter');

        $this->get('/admin/live-sessions?tab=today')->assertOk();
        $this->get('/admin/student-packages?tab=healthy')->assertOk();
        $this->get('/admin/exception-requests?tab=pending')->assertOk();
        $this->get('/admin/student-profiles?tab=approved')->assertOk();
        $this->get('/admin/teacher-profiles?tab=approved')->assertOk();
        $this->get('/admin/parent-profiles?tab=all')->assertOk();
    }
}
