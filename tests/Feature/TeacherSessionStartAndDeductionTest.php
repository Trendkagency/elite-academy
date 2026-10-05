<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\ExceptionRequest;
use App\Models\GradeLevel;
use App\Models\LiveSession;
use App\Models\PackageTemplate;
use App\Models\PackageTransaction;
use App\Models\StudentPackage;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use App\Services\Session\SessionAttendanceDeductionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherSessionStartAndDeductionTest extends TestCase
{
    use RefreshDatabase;

    private User $teacher;
    private TeacherProfile $teacherProfile;
    private Course $course;
    private LiveSession $session;

    private User $studentNoExcuse;
    private StudentPackage $packageNoExcuse;

    private User $studentApprovedExcuse;
    private StudentPackage $packageApprovedExcuse;

    private User $studentRejectedExcuse;
    private StudentPackage $packageRejectedExcuse;

    protected function setUp(): void
    {
        parent::setUp();

        $grade = GradeLevel::create(['name' => 'الثانوية العامة', 'slug' => 'secondary']);
        $cat = Category::create(['name' => 'الفيزياء', 'slug' => 'physics']);
        $subject = Subject::create(['category_id' => $cat->id, 'name' => 'الفيزياء الحديثة', 'slug' => 'modern-physics']);

        // Teacher
        $this->teacher = User::create([
            'name' => 'د. محمد مصطفى',
            'email' => 'teacher@elite.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        $this->teacherProfile = TeacherProfile::create([
            'user_id' => $this->teacher->id,
            'slug' => 'mohamed-mostafa',
        ]);

        // Course
        $this->course = Course::create([
            'teacher_id' => $this->teacherProfile->id,
            'grade_level_id' => $grade->id,
            'subject_id' => $subject->id,
            'title' => 'كورس الفيزياء المتقدمة',
            'slug' => 'advanced-physics',
            'price' => 500,
            'total_sessions' => 12,
            'is_published' => true,
        ]);

        // Session
        $this->session = LiveSession::create([
            'title' => 'محاضرة فيزياء الكم التفاعلية',
            'teacher_profile_id' => $this->teacherProfile->id,
            'course_id' => $this->course->id,
            'subject_id' => $subject->id,
            'status' => 'scheduled',
            'scheduled_at' => now(),
            'start_at' => now(),
            'duration_minutes' => 60,
        ]);

        $pkgTemplate = PackageTemplate::create([
            'name' => 'باقة 10 حصص',
            'slug' => '10-sessions',
            'sessions_count' => 10,
            'price' => 1000,
            'is_active' => true,
        ]);

        // 1. Student without excuse
        $this->studentNoExcuse = User::create([
            'name' => 'طالب بدون عذر',
            'email' => 'no_excuse@elite.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        StudentProfile::create(['user_id' => $this->studentNoExcuse->id, 'grade_level_id' => $grade->id]);
        CourseEnrollment::create(['course_id' => $this->course->id, 'student_user_id' => $this->studentNoExcuse->id, 'status' => 'active']);
        $this->packageNoExcuse = StudentPackage::create([
            'student_user_id' => $this->studentNoExcuse->id,
            'package_template_id' => $pkgTemplate->id,
            'course_id' => $this->course->id,
            'total_sessions' => 10,
            'used_sessions' => 0,
            'remaining_sessions' => 10,
            'status' => 'active',
            'activated_at' => now(),
        ]);

        // 2. Student with APPROVED excuse
        $this->studentApprovedExcuse = User::create([
            'name' => 'طالب عذر مقبول',
            'email' => 'approved_excuse@elite.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        StudentProfile::create(['user_id' => $this->studentApprovedExcuse->id, 'grade_level_id' => $grade->id]);
        CourseEnrollment::create(['course_id' => $this->course->id, 'student_user_id' => $this->studentApprovedExcuse->id, 'status' => 'active']);
        $this->packageApprovedExcuse = StudentPackage::create([
            'student_user_id' => $this->studentApprovedExcuse->id,
            'package_template_id' => $pkgTemplate->id,
            'course_id' => $this->course->id,
            'total_sessions' => 10,
            'used_sessions' => 0,
            'remaining_sessions' => 10,
            'status' => 'active',
            'activated_at' => now(),
        ]);
        ExceptionRequest::create([
            'student_user_id' => $this->studentApprovedExcuse->id,
            'live_session_id' => $this->session->id,
            'course_id' => $this->course->id,
            'scope' => 'session',
            'reason' => 'ظرف طارئ رسمي معتمد',
            'status' => 'approved',
            'reviewed_by' => $this->teacher->id,
            'reviewed_at' => now(),
        ]);

        // 3. Student with REJECTED excuse
        $this->studentRejectedExcuse = User::create([
            'name' => 'طالب عذر مرفوض',
            'email' => 'rejected_excuse@elite.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        StudentProfile::create(['user_id' => $this->studentRejectedExcuse->id, 'grade_level_id' => $grade->id]);
        CourseEnrollment::create(['course_id' => $this->course->id, 'student_user_id' => $this->studentRejectedExcuse->id, 'status' => 'active']);
        $this->packageRejectedExcuse = StudentPackage::create([
            'student_user_id' => $this->studentRejectedExcuse->id,
            'package_template_id' => $pkgTemplate->id,
            'course_id' => $this->course->id,
            'total_sessions' => 10,
            'used_sessions' => 0,
            'remaining_sessions' => 10,
            'status' => 'active',
            'activated_at' => now(),
        ]);
        ExceptionRequest::create([
            'student_user_id' => $this->studentRejectedExcuse->id,
            'live_session_id' => $this->session->id,
            'course_id' => $this->course->id,
            'scope' => 'session',
            'reason' => 'عذر غير مدعم بمستندات',
            'status' => 'rejected',
            'reviewed_by' => $this->teacher->id,
            'reviewed_at' => now(),
        ]);
    }

    public function test_teacher_session_start_deducts_unexcused_and_preserves_approved_excuse(): void
    {
        $service = app(SessionAttendanceDeductionService::class);
        $result = $service->processTeacherSessionStart($this->session, $this->teacher);

        $this->assertTrue($result['success']);
        $this->assertEquals(3, $result['total_students']);
        $this->assertEquals(2, $result['deducted_count']); // Student 1 (no excuse) + Student 3 (rejected excuse)
        $this->assertEquals(1, $result['excused_count']);  // Student 2 (approved excuse)

        // Verify Student 1 (No Excuse) -> Deducted from 10 to 9
        $this->assertEquals(9, $this->packageNoExcuse->fresh()->remaining_sessions);
        $this->assertEquals(1, $this->packageNoExcuse->fresh()->used_sessions);

        // Verify Student 2 (Approved Excuse) -> PRESERVED at 10 sessions!
        $this->assertEquals(10, $this->packageApprovedExcuse->fresh()->remaining_sessions);
        $this->assertEquals(0, $this->packageApprovedExcuse->fresh()->used_sessions);

        // Verify Student 3 (Rejected Excuse) -> Deducted from 10 to 9
        $this->assertEquals(9, $this->packageRejectedExcuse->fresh()->remaining_sessions);
        $this->assertEquals(1, $this->packageRejectedExcuse->fresh()->used_sessions);

        // Verify Package Transactions
        $this->assertDatabaseHas('package_transactions', [
            'student_package_id' => $this->packageNoExcuse->id,
            'live_session_id' => $this->session->id,
            'type' => 'session_deduct',
            'sessions_delta' => -1,
        ]);

        $this->assertDatabaseMissing('package_transactions', [
            'student_package_id' => $this->packageApprovedExcuse->id,
            'live_session_id' => $this->session->id,
            'type' => 'session_deduct',
        ]);

        $this->assertDatabaseHas('package_transactions', [
            'student_package_id' => $this->packageRejectedExcuse->id,
            'live_session_id' => $this->session->id,
            'type' => 'session_deduct',
        ]);
    }

    public function test_deduction_is_idempotent_and_will_not_double_deduct(): void
    {
        $service = app(SessionAttendanceDeductionService::class);

        // Run 1st time
        $service->processTeacherSessionStart($this->session, $this->teacher);
        $this->assertEquals(9, $this->packageNoExcuse->fresh()->remaining_sessions);

        // Run 2nd time
        $secondResult = $service->processTeacherSessionStart($this->session, $this->teacher);
        $this->assertEquals(2, $secondResult['already_deducted_count']);
        $this->assertEquals(0, $secondResult['deducted_count']);
        $this->assertEquals(9, $this->packageNoExcuse->fresh()->remaining_sessions);
    }

    public function test_free_demo_session_does_not_deduct_any_package(): void
    {
        $this->session->update(['is_free_demo' => true]);

        $service = app(SessionAttendanceDeductionService::class);
        $result = $service->processTeacherSessionStart($this->session, $this->teacher);

        $this->assertTrue($result['is_free_demo']);
        $this->assertEquals(0, $result['deducted_count']);

        $this->assertEquals(10, $this->packageNoExcuse->fresh()->remaining_sessions);
        $this->assertEquals(10, $this->packageApprovedExcuse->fresh()->remaining_sessions);
        $this->assertEquals(10, $this->packageRejectedExcuse->fresh()->remaining_sessions);
    }

    public function test_ajax_teacher_start_and_deduct_endpoint(): void
    {
        $response = $this->actingAs($this->teacher)
            ->postJson("/ajax/teacher/sessions/{$this->session->id}/start-and-deduct");

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'total_students' => 3,
                'deducted_count' => 2,
                'excused_count' => 1,
            ]);

        $this->assertEquals(9, $this->packageNoExcuse->fresh()->remaining_sessions);
        $this->assertEquals(10, $this->packageApprovedExcuse->fresh()->remaining_sessions);
        $this->assertEquals(9, $this->packageRejectedExcuse->fresh()->remaining_sessions);
    }

    public function test_roster_endpoint_accurately_reports_approved_and_rejected_excuses(): void
    {
        $response = $this->actingAs($this->teacher)
            ->getJson("/ajax/teacher/sessions/{$this->session->id}/attendance-roster");

        $response->assertOk()->assertJson(['success' => true]);

        $data = $response->json('students');
        $studentMap = collect($data)->keyBy('id');

        // Approved excuse student
        $approvedStudent = $studentMap->get($this->studentApprovedExcuse->id);
        $this->assertNotNull($approvedStudent);
        $this->assertTrue($approvedStudent['is_excused_by_exception']);
        $this->assertEquals('excused', $approvedStudent['status']);

        // Rejected excuse student
        $rejectedStudent = $studentMap->get($this->studentRejectedExcuse->id);
        $this->assertNotNull($rejectedStudent);
        $this->assertTrue($rejectedStudent['is_rejected_exception']);

        // No excuse student
        $noExcuseStudent = $studentMap->get($this->studentNoExcuse->id);
        $this->assertNotNull($noExcuseStudent);
        $this->assertFalse($noExcuseStudent['has_exception']);
    }
}
