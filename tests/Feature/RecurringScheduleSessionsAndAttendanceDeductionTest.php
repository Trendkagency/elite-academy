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

    public function test_can_create_recurring_schedule_with_auto_renew_package_when_student_has_no_package(): void
    {
        $newStudent = User::create([
            'name' => 'Sara New Student',
            'email' => 'sara_rec@elite.edu',
            'password' => bcrypt('StudentPass123!'),
            'role' => 'student',
            'status' => \App\Enums\AccountStatus::APPROVED,
        ]);
        StudentProfile::create(['user_id' => $newStudent->id, 'student_code' => 'STU-SARA-01']);

        // Student has NO package and NO enrollment initially
        $this->assertNull(StudentPackage::where('student_user_id', $newStudent->id)->first());

        $service = app(RecurringScheduleService::class);
        $schedule = $service->createSchedule([
            'title' => 'Weekly Physics with Sara',
            'course_id' => $this->course->id,
            'teacher_profile_id' => $this->teacherProfile->id,
            'student_user_id' => $newStudent->id,
            'start_date' => Carbon::tomorrow()->toDateString(),
            'start_time' => '11:00:00',
            'duration_minutes' => 60,
            'recurrence_type' => 'weekly',
            'days_of_week' => [1, 3], // 2 days a week
            'auto_renew_package' => true,
            'renew_sessions_count' => 12,
            'renew_action_type' => 'create_new',
        ], $this->adminUser);

        $this->assertNotNull($schedule);
        $this->assertEquals('Weekly Physics with Sara', $schedule->title);

        // Check package was created with 12 sessions
        $pkg = StudentPackage::where('student_user_id', $newStudent->id)->where('course_id', $this->course->id)->first();
        $this->assertNotNull($pkg);
        $this->assertEquals(12, $pkg->total_sessions);
        $this->assertEquals(12, $pkg->remaining_sessions);
        $this->assertEquals('active', $pkg->status);

        // Check enrollment was created
        $enrollment = CourseEnrollment::where('student_user_id', $newStudent->id)->where('course_id', $this->course->id)->first();
        $this->assertNotNull($enrollment);
        $this->assertEquals('active', $enrollment->status);

        // Check transaction log
        $txn = PackageTransaction::where('student_package_id', $pkg->id)->latest('created_at')->first();
        $this->assertNotNull($txn);
        $this->assertEquals('package_purchase', $txn->type);
        $this->assertEquals(12, $txn->sessions_delta);
    }

    public function test_can_renew_existing_exhausted_package_when_creating_schedule(): void
    {
        // Set student's existing package to 0 remaining sessions
        $this->studentPackage->update([
            'total_sessions' => 8,
            'remaining_sessions' => 0,
            'used_sessions' => 8,
            'status' => 'exhausted',
        ]);

        $service = app(RecurringScheduleService::class);
        $schedule = $service->createSchedule([
            'title' => 'Physics Renewal Schedule',
            'course_id' => $this->course->id,
            'teacher_profile_id' => $this->teacherProfile->id,
            'student_user_id' => $this->studentUser->id,
            'start_date' => Carbon::tomorrow()->toDateString(),
            'start_time' => '14:00:00',
            'duration_minutes' => 60,
            'recurrence_type' => 'weekly',
            'days_of_week' => [1, 3],
            'auto_renew_package' => true,
            'renew_sessions_count' => 10,
            'renew_action_type' => 'renew',
        ], $this->adminUser);

        $this->assertNotNull($schedule);

        $this->studentPackage->refresh();
        $this->assertEquals(10, $this->studentPackage->total_sessions);
        $this->assertEquals(10, $this->studentPackage->remaining_sessions);
        $this->assertEquals('active', $this->studentPackage->status);

        // Check renewal transaction
        $txn = PackageTransaction::where('student_package_id', $this->studentPackage->id)->latest('created_at')->first();
        $this->assertNotNull($txn);
        $this->assertEquals('renewal', $txn->type);
        $this->assertEquals(10, $txn->sessions_delta);
    }

    public function test_package_sessions_cannot_be_less_than_course_required_sessions(): void
    {
        // Course has 8 sessions required (or defined)
        $this->course->update(['sessions_count' => 10]);

        $service = app(RecurringScheduleService::class);

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        // Attempting to provision only 4 sessions when course requires 10
        $service->provisionOrRenewStudentPackage([
            'student_user_id' => $this->studentUser->id,
            'course_id' => $this->course->id,
            'sessions_count' => 4,
            'action_type' => 'renew',
        ], $this->adminUser);
    }

    public function test_existing_valid_package_unaffected_when_auto_renew_not_specified(): void
    {
        // Student already has 8 sessions available
        $this->assertEquals(8, $this->studentPackage->remaining_sessions);

        $service = app(RecurringScheduleService::class);
        $schedule = $service->createSchedule([
            'title' => 'Standard Physics Schedule',
            'course_id' => $this->course->id,
            'teacher_profile_id' => $this->teacherProfile->id,
            'student_user_id' => $this->studentUser->id,
            'start_date' => Carbon::tomorrow()->toDateString(),
            'start_time' => '10:00:00',
            'duration_minutes' => 60,
            'recurrence_type' => 'weekly',
            'days_of_week' => [1, 3], // 8 sessions target
        ], $this->adminUser);

        $this->assertNotNull($schedule);
        $this->assertEquals(8, $this->studentPackage->fresh()->remaining_sessions);
    }
}
