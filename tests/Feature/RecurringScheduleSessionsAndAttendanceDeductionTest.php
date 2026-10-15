<?php

namespace Tests\Feature;

use App\Filament\Resources\RecurringSchedules\Pages\EditRecurringSchedule;
use App\Filament\Resources\RecurringSchedules\RecurringScheduleResource;
use App\Filament\Resources\RecurringSchedules\RelationManagers\SessionsRelationManager;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\GradeLevel;
use App\Models\LiveSession;
use App\Models\MeetingAttendance;
use App\Models\PackageTemplate;
use App\Models\PackageTransaction;
use App\Models\RecurringSchedule;
use App\Models\StudentPackage;
use App\Models\StudentProfile;
use App\Models\StudentSession;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use App\Services\Session\RecurringScheduleService;
use App\Services\Session\SessionAttendanceDeductionService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecurringScheduleSessionsAndAttendanceDeductionTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $teacherUser;
    protected TeacherProfile $teacherProfile;
    protected User $studentUser;
    protected Course $course;
    protected StudentPackage $studentPackage;

    protected function setUp(): void
    {
        parent::setUp();

        $gradeLevel = GradeLevel::create(['name' => 'Secondary 3', 'slug' => 'sec-3', 'sort_order' => 1]);
        $category = Category::create(['name' => 'Sciences', 'slug' => 'sciences']);
        $subject = Subject::create(['name' => 'Physics', 'slug' => 'physics', 'category_id' => $category->id]);

        $this->adminUser = User::create([
            'name' => 'Super Administrator',
            'email' => 'admin_rec@elite.edu',
            'password' => bcrypt('AdminPass123!'),
            'role' => 'super_admin',
            'status' => \App\Enums\AccountStatus::APPROVED,
        ]);
        \App\Models\AdminProfile::create(['user_id' => $this->adminUser->id]);

        $this->teacherUser = User::create([
            'name' => 'Dr. Hazem Physics',
            'email' => 'hazem_rec@elite.edu',
            'password' => bcrypt('TeacherPass123!'),
            'role' => 'teacher',
            'status' => \App\Enums\AccountStatus::APPROVED,
        ]);
        $this->teacherProfile = TeacherProfile::create([
            'user_id' => $this->teacherUser->id,
            'title' => 'Senior Physics Specialist',
            'slug' => 'dr-hazem-physics-rec',
            'hourly_rate' => 75.0,
        ]);

        $this->studentUser = User::create([
            'name' => 'Ali Student',
            'email' => 'ali_rec@elite.edu',
            'password' => bcrypt('StudentPass123!'),
            'role' => 'student',
            'status' => \App\Enums\AccountStatus::APPROVED,
        ]);
        StudentProfile::create([
            'user_id' => $this->studentUser->id,
            'student_code' => 'STU-99001',
            'grade_level_id' => $gradeLevel->id,
        ]);

        $this->course = Course::create([
            'title' => 'Advanced Physics Cohort 2026',
            'slug' => 'adv-physics-2026',
            'subject_id' => $subject->id,
            'teacher_id' => $this->teacherProfile->id,
            'price' => 500,
            'sessions_count' => 8,
            'status' => 'published',
        ]);

        CourseEnrollment::create([
            'course_id' => $this->course->id,
            'student_user_id' => $this->studentUser->id,
            'status' => 'active',
            'enrolled_at' => now()->subMonth(),
        ]);

        $template = PackageTemplate::create([
            'name' => '8-Session Physics Package',
            'sessions_count' => 8,
            'price' => 450,
            'is_active' => true,
        ]);

        $this->studentPackage = StudentPackage::create([
            'student_user_id' => $this->studentUser->id,
            'course_id' => $this->course->id,
            'package_template_id' => $template->id,
            'total_sessions' => 8,
            'used_sessions' => 0,
            'remaining_sessions' => 8,
            'status' => 'active',
            'activated_at' => now()->subMonth(),
            'expires_at' => now()->addMonths(2),
        ]);
    }

    public function test_recurring_schedule_resource_has_sessions_relation_manager(): void
    {
        $relations = RecurringScheduleResource::getRelations();
        $this->assertContains(SessionsRelationManager::class, $relations);
    }

    public function test_admin_can_render_edit_recurring_schedule_page_with_sessions(): void
    {
        $service = app(RecurringScheduleService::class);
        $schedule = $service->createSchedule([
            'course_id' => $this->course->id,
            'teacher_profile_id' => $this->teacherProfile->id,
            'student_user_id' => $this->studentUser->id,
            'title' => 'Test Schedule For Edit Page',
            'recurrence_type' => 'weekly',
            'days_of_week' => [1, 3],
            'start_time' => '17:00',
            'duration_minutes' => 60,
            'start_date' => Carbon::today()->toDateString(),
        ], $this->adminUser);

        $url = RecurringScheduleResource::getUrl('edit', ['record' => $schedule]);
        $response = $this->actingAs($this->adminUser)->get($url);
        $response->assertSuccessful();
    }


    public function test_creating_schedule_with_past_start_date_generates_past_sessions(): void
    {
        $service = app(RecurringScheduleService::class);
        $pastStartDate = Carbon::today()->subWeeks(2)->format('Y-m-d');

        $schedule = $service->createSchedule([
            'course_id' => $this->course->id,
            'teacher_profile_id' => $this->teacherProfile->id,
            'student_user_id' => $this->studentUser->id,
            'title' => 'Physics Cohort (Past & Future)',
            'recurrence_type' => 'weekly',
            'days_of_week' => [1, 3], // Mon & Wed
            'start_time' => '17:00',
            'duration_minutes' => 60,
            'start_date' => $pastStartDate,
        ], $this->adminUser);

        $this->assertGreaterThan(0, $schedule->liveSessions()->count());

        // Check that sessions with scheduled_at in the past exist
        $pastSessionsCount = $schedule->liveSessions()
            ->where('scheduled_at', '<', now())
            ->count();

        $this->assertGreaterThan(0, $pastSessionsCount);
    }

    public function test_record_past_attendance_deducts_student_package_and_marks_completed(): void
    {
        $service = app(SessionAttendanceDeductionService::class);

        $pastDate = Carbon::today()->subDays(5)->setTime(18, 0);

        $session = LiveSession::create([
            'title' => 'Physics Class Session #1',
            'student_user_id' => $this->studentUser->id,
            'teacher_profile_id' => $this->teacherProfile->id,
            'course_id' => $this->course->id,
            'subject_id' => $this->course->subject_id,
            'scheduled_at' => $pastDate,
            'duration_minutes' => 60,
            'status' => 'scheduled',
            'lifecycle_state' => 'scheduled',
        ]);

        $this->assertEquals(8, $this->studentPackage->fresh()->remaining_sessions);

        // Record past attendance
        $result = $service->recordAttendanceAndDeduct(
            $session,
            $this->studentUser->id,
            'present',
            $pastDate,
            'Student attended on past date via schedule register',
            $this->adminUser,
            true
        );

        $this->assertTrue($result['success']);
        $this->assertTrue($result['deducted']);
        $this->assertEquals(7, $result['balance_after']);

        // Check LiveSession updated
        $session->refresh();
        $this->assertEquals('completed', $session->status);
        $this->assertEquals('ended', $session->lifecycle_state);
        $this->assertEquals('present', $session->attendance_status);

        // Check StudentSession updated
        $studentSession = StudentSession::where('live_session_id', $session->id)
            ->where('student_user_id', $this->studentUser->id)
            ->first();
        $this->assertNotNull($studentSession);
        $this->assertEquals('present', $studentSession->attendance_status);
        $this->assertEquals('completed', $studentSession->session_status);

        // Check MeetingAttendance updated
        $meetingAttendance = MeetingAttendance::where('live_session_id', $session->id)
            ->where('student_user_id', $this->studentUser->id)
            ->first();
        $this->assertNotNull($meetingAttendance);
        $this->assertEquals('present', $meetingAttendance->status);

        // Check PackageTransaction created
        $transaction = PackageTransaction::where('live_session_id', $session->id)
            ->where('type', 'session_deduct')
            ->first();
        $this->assertNotNull($transaction);
        $this->assertEquals(-1, $transaction->sessions_delta);
        $this->assertEquals(8, $transaction->balance_before);
        $this->assertEquals(7, $transaction->balance_after);

        // Check idempotency (calling again does NOT deduct twice)
        $secondResult = $service->recordAttendanceAndDeduct(
            $session,
            $this->studentUser->id,
            'present',
            $pastDate,
            'Duplicate call test',
            $this->adminUser,
            true
        );
        $this->assertEquals(7, $this->studentPackage->fresh()->remaining_sessions);
    }

    public function test_excused_past_attendance_preserves_package_balance(): void
    {
        $service = app(SessionAttendanceDeductionService::class);
        $pastDate = Carbon::today()->subDays(3)->setTime(16, 0);

        $session = LiveSession::create([
            'title' => 'Physics Class Session #2',
            'student_user_id' => $this->studentUser->id,
            'teacher_profile_id' => $this->teacherProfile->id,
            'course_id' => $this->course->id,
            'subject_id' => $this->course->subject_id,
            'scheduled_at' => $pastDate,
            'duration_minutes' => 60,
            'status' => 'scheduled',
            'lifecycle_state' => 'scheduled',
        ]);

        $balanceBefore = $this->studentPackage->fresh()->remaining_sessions;

        $result = $service->recordAttendanceAndDeduct(
            $session,
            $this->studentUser->id,
            'excused',
            $pastDate,
            'Medical Excuse Approved',
            $this->adminUser,
            true
        );

        $this->assertTrue($result['success']);
        $this->assertFalse($result['deducted']);
        $this->assertEquals('excused', $result['status']);
        $this->assertEquals($balanceBefore, $this->studentPackage->fresh()->remaining_sessions);

        $session->refresh();
        $this->assertEquals('excused', $session->attendance_status);
        $this->assertEquals('completed', $session->status);
    }
}
