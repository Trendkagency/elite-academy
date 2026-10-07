<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\AdminProfile;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\LiveSession;
use App\Models\PackageTemplate;
use App\Models\RecurringSchedule;
use App\Models\StudentPackage;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use App\Services\Session\RecurringScheduleService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class StudentCollectionsAndRenewalsPageTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::create([
            'name' => 'System Admin',
            'email' => 'admin@collections.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        AdminProfile::create(['user_id' => $this->adminUser->id]);
    }

    public function test_admin_can_access_student_collections_page(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/student-collections');
        $response->assertStatus(200);
    }

    public function test_page_detects_exhausted_and_low_balance_packages(): void
    {
        $category = Category::create(['name' => 'Science', 'slug' => 'sci']);
        $subject = Subject::create(['name' => 'Physics', 'slug' => 'phys', 'category_id' => $category->id]);

        $student1 = User::create(['name' => 'Exhausted Student', 'email' => 'ex@student.com', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        StudentProfile::create(['user_id' => $student1->id]);

        $student2 = User::create(['name' => 'Low Balance Student', 'email' => 'low@student.com', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        StudentProfile::create(['user_id' => $student2->id]);

        $student3 = User::create(['name' => 'Healthy Balance Student', 'email' => 'healthy@student.com', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        StudentProfile::create(['user_id' => $student3->id]);

        // Exhausted: 0 remaining
        StudentPackage::create([
            'student_user_id' => $student1->id,
            'total_sessions' => 12,
            'used_sessions' => 12,
            'remaining_sessions' => 0,
            'status' => 'exhausted',
        ]);

        // Low balance: 2 remaining
        StudentPackage::create([
            'student_user_id' => $student2->id,
            'total_sessions' => 12,
            'used_sessions' => 10,
            'remaining_sessions' => 2,
            'status' => 'active',
        ]);

        // Healthy balance: 10 remaining
        StudentPackage::create([
            'student_user_id' => $student3->id,
            'total_sessions' => 12,
            'used_sessions' => 2,
            'remaining_sessions' => 10,
            'status' => 'active',
        ]);

        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/student-collections?tab=packages');
        $response->assertStatus(200);
        $response->assertSee('Exhausted Student');
        $response->assertSee('Low Balance Student');
        $response->assertDontSee('Healthy Balance Student');
    }

    public function test_page_detects_recurring_schedules_ending_soon(): void
    {
        $category = Category::create(['name' => 'Math Cat', 'slug' => 'math-cat']);
        $subject = Subject::create(['name' => 'Calculus', 'slug' => 'calc', 'category_id' => $category->id]);

        $teacherUser = User::create(['name' => 'Teacher Math', 'email' => 'tmath@edu.com', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        $teacher = TeacherProfile::create(['user_id' => $teacherUser->id, 'slug' => 'tmath']);

        $student = User::create(['name' => 'Monthly Schedule Student', 'email' => 'monthly@edu.com', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        StudentProfile::create(['user_id' => $student->id]);

        $course = Course::create(['title' => 'Calculus Track', 'slug' => 'calc-track', 'subject_id' => $subject->id, 'teacher_id' => $teacher->id]);

        // Ending in 5 days (e.g. 5/11)
        $scheduleEndingSoon = RecurringSchedule::create([
            'teacher_profile_id' => $teacher->id,
            'course_id' => $course->id,
            'student_user_id' => $student->id,
            'title' => 'جدول كالكولاس لشهر أكتوبر',
            'start_time' => '10:00',
            'duration_minutes' => 60,
            'days_of_week' => [0, 2],
            'start_date' => now()->subDays(25)->format('Y-m-d'),
            'end_date' => now()->addDays(5)->format('Y-m-d'),
            'status' => 'active',
            'created_by_user_id' => $this->adminUser->id,
        ]);

        // Another schedule ending in 3 months
        $scheduleFar = RecurringSchedule::create([
            'teacher_profile_id' => $teacher->id,
            'course_id' => $course->id,
            'student_user_id' => $student->id,
            'title' => 'جدول سنوي بعيد',
            'start_time' => '12:00',
            'duration_minutes' => 60,
            'days_of_week' => [1],
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addMonths(3)->format('Y-m-d'),
            'status' => 'active',
            'created_by_user_id' => $this->adminUser->id,
        ]);

        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/student-collections?tab=schedules');
        $response->assertStatus(200);
        $response->assertSee('جدول كالكولاس لشهر أكتوبر');
        $response->assertDontSee('جدول سنوي بعيد');
    }

    public function test_extend_schedule_cycle_generates_new_session_instances(): void
    {
        $category = Category::create(['name' => 'Lang Cat', 'slug' => 'lang-cat']);
        $subject = Subject::create(['name' => 'Arabic', 'slug' => 'ar', 'category_id' => $category->id]);

        $teacherUser = User::create(['name' => 'Teacher Arabic', 'email' => 'tarabic@edu.com', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        $teacher = TeacherProfile::create(['user_id' => $teacherUser->id, 'slug' => 'tarabic']);

        $student = User::create(['name' => 'Student Youssef', 'email' => 'youssef@edu.com', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        StudentProfile::create(['user_id' => $student->id]);

        $course = Course::create(['title' => 'Arabic Course', 'slug' => 'ar-course', 'subject_id' => $subject->id, 'teacher_id' => $teacher->id]);

        $oldEndDate = Carbon::today()->addDays(2);

        $schedule = RecurringSchedule::create([
            'teacher_profile_id' => $teacher->id,
            'course_id' => $course->id,
            'student_user_id' => $student->id,
            'title' => 'دورة النحو الدورية',
            'start_time' => '11:00',
            'duration_minutes' => 60,
            'days_of_week' => [0, 2, 4], // Sun, Tue, Thu
            'start_date' => Carbon::today()->subDays(26)->format('Y-m-d'),
            'end_date' => $oldEndDate->format('Y-m-d'),
            'status' => 'active',
            'created_by_user_id' => $this->adminUser->id,
        ]);

        $service = app(RecurringScheduleService::class);
        $newEndDate = $oldEndDate->copy()->addMonth()->format('Y-m-d');

        $generated = $service->extendScheduleCycle($schedule, $newEndDate, $this->adminUser);

        $this->assertGreaterThan(0, $generated);
        $this->assertEquals($newEndDate, $schedule->fresh()->end_date->format('Y-m-d'));
        $this->assertDatabaseHas('session_audit_logs', [
            'recurring_schedule_id' => $schedule->id,
            'action' => 'extended',
        ]);
    }

    public function test_livewire_can_add_credits_to_package(): void
    {
        $student = User::create(['name' => 'Credits Student', 'email' => 'credits@student.com', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        StudentProfile::create(['user_id' => $student->id]);

        $pkg = StudentPackage::create([
            'student_user_id' => $student->id,
            'total_sessions' => 10,
            'used_sessions' => 10,
            'remaining_sessions' => 0,
            'status' => 'exhausted',
        ]);

        $this->actingAs($this->adminUser);

        Livewire::test(\App\Filament\Pages\StudentCollectionsPage::class)
            ->set('addCreditsPackageId', $pkg->id)
            ->set('creditsToAdd', 5)
            ->call('submitAddCredits')
            ->assertHasNoErrors();

        $pkg->refresh();
        $this->assertEquals(15, $pkg->total_sessions);
        $this->assertEquals(5, $pkg->remaining_sessions);
        $this->assertEquals('active', $pkg->status);
    }

    public function test_livewire_can_extend_recurring_schedule(): void
    {
        $category = Category::create(['name' => 'Sci Cat', 'slug' => 'sci-cat']);
        $subject = Subject::create(['name' => 'Bio', 'slug' => 'bio', 'category_id' => $category->id]);
        $teacherUser = User::create(['name' => 'Teacher Bio', 'email' => 'tbio@edu.com', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        $teacher = TeacherProfile::create(['user_id' => $teacherUser->id, 'slug' => 'tbio']);
        $student = User::create(['name' => 'Bio Student', 'email' => 'bio@edu.com', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        StudentProfile::create(['user_id' => $student->id]);
        $course = Course::create(['title' => 'Biology Course', 'slug' => 'bio-course', 'subject_id' => $subject->id, 'teacher_id' => $teacher->id]);

        $schedule = RecurringSchedule::create([
            'teacher_profile_id' => $teacher->id,
            'course_id' => $course->id,
            'student_user_id' => $student->id,
            'title' => 'جدول الأحياء الشهري',
            'start_time' => '09:00',
            'duration_minutes' => 60,
            'days_of_week' => [1],
            'start_date' => now()->subMonth()->format('Y-m-d'),
            'end_date' => now()->addDays(2)->format('Y-m-d'),
            'status' => 'active',
            'created_by_user_id' => $this->adminUser->id,
        ]);

        $newEndDate = now()->addMonth()->format('Y-m-d');

        $this->actingAs($this->adminUser);

        Livewire::test(\App\Filament\Pages\StudentCollectionsPage::class)
            ->set('extendingScheduleId', $schedule->id)
            ->set('newScheduleEndDate', $newEndDate)
            ->call('submitExtendSchedule')
            ->assertHasNoErrors();

        $schedule->refresh();
        $this->assertEquals($newEndDate, $schedule->end_date->format('Y-m-d'));
    }

    public function test_interactive_kpis_can_be_clicked_and_display_details_logic(): void
    {
        $this->actingAs($this->adminUser);

        $component = Livewire::test(\App\Filament\Pages\StudentCollectionsPage::class);

        // 1. Initial State: activeKpi is null
        $this->assertNull($component->get('activeKpi'));

        // 2. Click KPI 1 (Exhausted): switches tab to 'packages' and filter to 'exhausted'
        $component->call('selectKpi', 'exhausted')
            ->assertSet('activeTab', 'packages')
            ->assertSet('packageFilter', 'exhausted');
        $this->assertEquals('exhausted', $component->get('activeKpi'));

        // 3. Click KPI 4 (Ending Soon schedules): switches tab to 'schedules' and filter to 'ending_soon'
        $component->call('selectKpi', 'ending_soon')
            ->assertSet('activeTab', 'schedules')
            ->assertSet('scheduleFilter', 'ending_soon');
        $this->assertEquals('ending_soon', $component->get('activeKpi'));

        // 4. Test Inspecting KPI Logic Modal
        $component->call('inspectKpi', 'exhausted')
            ->assertSet('showKpiDetailModal', true)
            ->assertSet('inspectedKpi', 'exhausted');

        // 5. Close Modal
        $component->call('closeKpiModal')
            ->assertSet('showKpiDetailModal', false);
        $this->assertNull($component->get('inspectedKpi'));

        // 6. Test Clear Filter
        $component->call('clearKpiFilter')
            ->assertSet('scheduleFilter', 'all');
        $this->assertNull($component->get('inspectedKpi'));
    }

    public function test_multi_field_search_filters_packages_and_schedules_accurately(): void
    {
        $category = Category::create(['name' => 'Math', 'slug' => 'math']);
        $subject = Subject::create(['name' => 'Calculus', 'slug' => 'calc', 'category_id' => $category->id]);
        $teacherUser = User::create(['name' => 'Teacher Math', 'email' => 'tmath@edu.com', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        $teacher = TeacherProfile::create(['user_id' => $teacherUser->id, 'slug' => 'tmath']);

        $course = Course::create([
            'title' => 'دورة التفاضل المتقدم',
            'slug' => 'calc-adv',
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'is_published' => true,
        ]);

        $studentA = User::create(['name' => 'طارق السعدني', 'email' => 'tarek@test.com', 'phone' => '01011112222', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        StudentProfile::create(['user_id' => $studentA->id]);

        $studentB = User::create(['name' => 'ياسمين خليل', 'email' => 'yasmin@test.com', 'phone' => '01033334444', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        StudentProfile::create(['user_id' => $studentB->id]);

        $parent = User::create(['name' => 'حسام السعدني', 'email' => 'hossam@test.com', 'phone' => '01099998888', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        \Illuminate\Support\Facades\DB::table('parent_student')->insert([
            'parent_user_id' => $parent->id,
            'student_user_id' => $studentA->id,
            'relationship' => 'father',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $template = PackageTemplate::create([
            'name' => 'باقة التميز الفصلي الخارقة',
            'sessions_count' => 12,
            'price' => 1200,
            'is_active' => true,
        ]);

        StudentPackage::create([
            'student_user_id' => $studentA->id,
            'package_template_id' => $template->id,
            'course_id' => $course->id,
            'total_sessions' => 12,
            'used_sessions' => 12,
            'remaining_sessions' => 0,
            'status' => 'exhausted',
        ]);

        StudentPackage::create([
            'student_user_id' => $studentB->id,
            'total_sessions' => 8,
            'used_sessions' => 8,
            'remaining_sessions' => 0,
            'status' => 'exhausted',
        ]);

        $this->actingAs($this->adminUser);

        // 1. Search by Parent Name ('حسام')
        $comp = Livewire::test(\App\Filament\Pages\StudentCollectionsPage::class)
            ->set('activeTab', 'packages')
            ->set('searchQuery', 'حسام');

        $packages = $comp->get('expiringPackages');
        $this->assertCount(1, $packages);
        $this->assertEquals('طارق السعدني', $packages->first()->student->name);

        // 2. Search by Package Template Name ('الخارقة')
        $comp->set('searchQuery', 'الخارقة');
        $packages = $comp->get('expiringPackages');
        $this->assertCount(1, $packages);
        $this->assertEquals('طارق السعدني', $packages->first()->student->name);

        // 3. Search by Student Phone ('01033334444')
        $comp->set('searchQuery', '01033334444');
        $packages = $comp->get('expiringPackages');
        $this->assertCount(1, $packages);
        $this->assertEquals('ياسمين خليل', $packages->first()->student->name);
    }
}
