<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
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
use App\Services\Exception\ExceptionRequestService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExceptionRequestCourseSessionAndDeductionTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;
    protected User $teacher;
    protected User $admin;
    protected Course $course;
    protected LiveSession $liveSession;
    protected StudentPackage $studentPackage;
    protected TeacherProfile $teacherProfile;

    protected function setUp(): void
    {
        parent::setUp();

        $gradeLevel = GradeLevel::create([
            'name' => 'Secondary 1',
            'slug' => 'secondary-1',
            'code' => 'SEC-1',
            'sort_order' => 1,
        ]);

        $category = \App\Models\Category::create([
            'name' => 'Science',
            'slug' => 'science',
        ]);

        $subject = Subject::create([
            'category_id' => $category->id,
            'name' => 'Physics',
            'slug' => 'physics',
        ]);

        // 1. Create Teacher
        $this->teacher = User::factory()->create([
            'status' => AccountStatus::APPROVED,
        ]);
        $this->teacherProfile = TeacherProfile::create([
            'user_id' => $this->teacher->id,
            'slug' => 'dr-smith',
            'title' => 'Dr. Smith',
            'specialization' => 'Physics',
        ]);

        // 2. Create Course
        $this->course = Course::create([
            'title' => 'Physics 101',
            'slug' => 'physics-101',
            'subject_id' => $subject->id,
            'teacher_id' => $this->teacherProfile->id,
            'grade_level_id' => $gradeLevel->id,
            'is_active' => true,
        ]);

        // 3. Create Live Session (in the future > 2h)
        $this->liveSession = LiveSession::create([
            'course_id' => $this->course->id,
            'teacher_profile_id' => $this->teacherProfile->id,
            'subject_id' => $subject->id,
            'title' => 'Quantum Mechanics Live Stream',
            'scheduled_at' => now()->addDays(3),
            'start_at' => now()->addDays(3),
            'status' => 'scheduled',
            'duration_minutes' => 60,
        ]);

        // 4. Create Student with Profile and Package
        $this->student = User::factory()->create([
            'status' => AccountStatus::APPROVED,
        ]);
        StudentProfile::create([
            'user_id' => $this->student->id,
            'grade_level_id' => $gradeLevel->id,
        ]);

        CourseEnrollment::create([
            'student_user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'status' => 'active',
        ]);

        $template = PackageTemplate::create([
            'name' => 'Gold Package',
            'sessions_count' => 10,
            'price' => 100,
            'currency' => 'EGP',
            'validity_days' => 60,
        ]);

        $this->studentPackage = StudentPackage::create([
            'student_user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'package_template_id' => $template->id,
            'total_sessions' => 10,
            'used_sessions' => 0,
            'remaining_sessions' => 10,
            'status' => 'active',
            'activated_at' => now(),
        ]);

        // 5. Create Admin
        $this->admin = User::factory()->create([
            'status' => AccountStatus::APPROVED,
        ]);
        \App\Models\AdminProfile::create([
            'user_id' => $this->admin->id,
        ]);
    }

    public function test_student_can_submit_absence_excuse_with_course_and_specific_session()
    {
        $response = $this->actingAs($this->student)->postJson(route('ajax.exception.submit'), [
            'course_id' => $this->course->id,
            'live_session_id' => $this->liveSession->id,
            'scope' => 'course',
            'reason' => 'I have a mandatory university entrance exam during this session.',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('exception_requests', [
            'student_user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'live_session_id' => $this->liveSession->id,
            'status' => 'pending',
        ]);
    }

    public function test_when_exception_is_approved_session_is_not_decreased_from_student()
    {
        $exception = ExceptionRequest::create([
            'student_user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'live_session_id' => $this->liveSession->id,
            'scope' => 'course',
            'reason' => 'Medical appointment during session hours.',
            'status' => 'pending',
        ]);

        $initialBalance = $this->studentPackage->fresh()->remaining_sessions;
        $this->assertEquals(10, $initialBalance);

        // Teacher approves exception
        $response = $this->actingAs($this->teacher)->postJson(route('ajax.teacher.exceptions.approve', ['id' => $exception->id]));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status' => 'approved',
            ]);

        $this->assertEquals('approved', $exception->fresh()->status);
        $this->assertEquals($this->teacher->id, $exception->fresh()->reviewed_by);

        // Balance must remain unchanged (10 sessions)
        $this->assertEquals(10, $this->studentPackage->fresh()->remaining_sessions);
    }

    public function test_when_exception_is_rejected_session_is_decreased_from_student_package()
    {
        $exception = ExceptionRequest::create([
            'student_user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'live_session_id' => $this->liveSession->id,
            'scope' => 'course',
            'reason' => 'Unjustified missed session reason.',
            'status' => 'pending',
        ]);

        $this->assertEquals(10, $this->studentPackage->fresh()->remaining_sessions);

        // Teacher rejects exception
        $response = $this->actingAs($this->teacher)->postJson(route('ajax.teacher.exceptions.reject', ['id' => $exception->id]));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status' => 'rejected',
            ]);

        $this->assertEquals('rejected', $exception->fresh()->status);

        // Balance MUST be decreased to 9 sessions
        $this->assertEquals(9, $this->studentPackage->fresh()->remaining_sessions);

        // Transaction must be logged
        $this->assertDatabaseHas('package_transactions', [
            'student_package_id' => $this->studentPackage->id,
            'type' => 'session_deduct',
            'sessions_delta' => -1,
            'balance_after' => 9,
        ]);
    }

    public function test_approving_a_previously_rejected_exception_refunds_the_student_session()
    {
        $exception = ExceptionRequest::create([
            'student_user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'live_session_id' => $this->liveSession->id,
            'scope' => 'course',
            'reason' => 'Initially rejected reason, later excused upon documentation submission.',
            'status' => 'pending',
        ]);

        $service = app(ExceptionRequestService::class);

        // 1. Reject first -> balance drops from 10 to 9
        $service->reject($exception, $this->teacher);
        $this->assertEquals(9, $this->studentPackage->fresh()->remaining_sessions);
        $this->assertEquals('rejected', $exception->fresh()->status);

        // 2. Now approve -> balance should be refunded back to 10
        $service->approve($exception->fresh(), $this->teacher);
        $this->assertEquals(10, $this->studentPackage->fresh()->remaining_sessions);
        $this->assertEquals('approved', $exception->fresh()->status);

        // Transaction refund must be logged
        $this->assertDatabaseHas('package_transactions', [
            'student_package_id' => $this->studentPackage->id,
            'type' => 'session_refund',
            'sessions_delta' => 1,
            'balance_after' => 10,
        ]);
    }
}
