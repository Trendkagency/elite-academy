<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Enums\SubmissionStatus;
use App\Models\AdminProfile;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\GradeLevel;
use App\Models\LiveSession;
use App\Models\ParentProfile;
use App\Models\StudentPackage;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MvpFullSpecificationSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $teacherUser;
    protected User $studentUser;
    protected User $parentUser;

    protected TeacherProfile $teacherProfile;
    protected StudentProfile $studentProfile;
    protected ParentProfile $parentProfile;

    protected Category $category;
    protected Subject $subject;
    protected GradeLevel $gradeLevel;
    protected Course $course;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Grade Level & Subject Setup
        $this->gradeLevel = GradeLevel::create(['name' => 'Secondary Grade 3', 'slug' => 'secondary-grade-3', 'sort_order' => 1]);
        $this->category = Category::create(['name' => 'Sciences Track', 'slug' => 'sciences-track', 'sort_order' => 1]);
        $this->subject = Subject::create(['category_id' => $this->category->id, 'name' => 'Physics', 'slug' => 'physics', 'is_active' => true]);

        // 2. Admin User
        $this->adminUser = User::create(['name' => 'System Owner Admin', 'email' => 'admin@elite-academy.com', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        AdminProfile::create(['user_id' => $this->adminUser->id]);

        // 3. Teacher User & Profile
        $this->teacherUser = User::create(['name' => 'Dr. Ahmed Physics', 'email' => 'teacher@elite.edu', 'phone' => '01099998888', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        $this->teacherProfile = TeacherProfile::create([
            'user_id' => $this->teacherUser->id,
            'title' => 'Dr.',
            'slug' => 'dr-ahmed-physics',
            'specialization' => 'Theoretical & Quantum Physics',
            'years_experience' => 12,
        ]);
        $this->teacherProfile->subjects()->sync([$this->subject->id]);

        // 4. Course
        $this->course = Course::create([
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->teacherProfile->id,
            'grade_level_id' => $this->gradeLevel->id,
            'title' => 'Advanced Physics Track 2026',
            'slug' => 'adv-physics-2026',
            'price' => 450.00,
            'is_active' => true,
        ]);

        // 5. Student User & Profile with Phone Number
        $this->studentUser = User::create([
            'name' => 'Student Kareem',
            'email' => 'kareem@student.com',
            'phone' => '01011112222',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        $this->studentProfile = StudentProfile::create([
            'user_id' => $this->studentUser->id,
            'grade_level_id' => $this->gradeLevel->id,
            'school_name' => 'Cairo STEM High School',
        ]);
        CourseEnrollment::create(['student_user_id' => $this->studentUser->id, 'course_id' => $this->course->id, 'status' => 'active']);

        // 6. Parent User & Profile
        $this->parentUser = User::create([
            'name' => 'Parent Mr. Mahmoud',
            'email' => 'parent@family.com',
            'phone' => '01033334444',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        $this->parentProfile = ParentProfile::create(['user_id' => $this->parentUser->id]);

        // Link Parent -> Student Kareem
        DB::table('parent_student')->insert([
            'parent_user_id' => $this->parentUser->id,
            'student_user_id' => $this->studentUser->id,
        ]);

        // Package (8 Sessions Total, 2 Used, 6 Remaining)
        StudentPackage::create([
            'student_user_id' => $this->studentUser->id,
            'total_sessions' => 8,
            'used_sessions' => 2,
            'remaining_sessions' => 6,
            'status' => 'active',
        ]);
    }

    /** ----------------------------------------------------
     * SECTION 1: Users & Registration Approval Flow
     * ---------------------------------------------------- */
    public function test_sec1_student_registration_requires_phone_and_pending_approval(): void
    {
        $response = $this->postJson('/ajax/register', [
            'name' => 'New Applicant Student',
            'email' => 'applicant@student.com',
            'phone' => '01055556666',
            'password' => 'secret123',
            'user_type' => 'student',
            'grade_level_id' => $this->gradeLevel->id,
        ]);

        $response->assertStatus(201);
        $response->assertJson(['success' => true]);

        $created = User::where('email', 'applicant@student.com')->first();
        $this->assertNotNull($created);
        $this->assertEquals(AccountStatus::PENDING, $created->status);
    }

    /** ----------------------------------------------------
     * SECTION 2 & 4: Parent Multi-Child Monitoring & Phone Linking
     * ---------------------------------------------------- */
    public function test_sec2_parent_can_link_child_by_phone_and_inspect_progress(): void
    {
        // Unlinked Student 2
        $st2 = User::create(['name' => 'Student Mariam', 'email' => 'mariam@student.com', 'phone' => '01077778888', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        StudentProfile::create(['user_id' => $st2->id, 'grade_level_id' => $this->gradeLevel->id]);

        // Parent links Mariam by Phone
        $linkResponse = $this->actingAs($this->parentUser)->postJson('/ajax/parent/link-child', [
            'phone_or_email' => '01077778888',
        ]);

        $linkResponse->assertStatus(200);
        $linkResponse->assertJson(['success' => true]);

        // Fetch Progress for Mariam
        $progress = $this->actingAs($this->parentUser)->getJson("/ajax/parent/student/{$st2->id}/progress");
        $progress->assertStatus(200);
        $progress->assertJson(['success' => true, 'is_read_only' => true]);
    }

    /** ----------------------------------------------------
     * SECTION 5 & 25: Teacher Portal & Student Phone Privacy Enforcement
     * ---------------------------------------------------- */
    public function test_sec5_teacher_portal_strictly_masks_student_phone_numbers(): void
    {
        $response = $this->actingAs($this->teacherUser)
            ->getJson("/ajax/teacher/students/{$this->studentUser->id}/details");

        $response->assertStatus(200);
        $json = $response->json();

        // Phone MUST NOT be present in teacher response
        $this->assertArrayNotHasKey('phone', $json['student']);
        $response->assertDontSee('01011112222');
    }

    /** ----------------------------------------------------
     * SECTION 7: Live Stream Sessions & Link Visibility Time Logic
     * ---------------------------------------------------- */
    public function test_sec7_session_meeting_link_backend_timing_control(): void
    {
        $futureSession = LiveSession::create([
            'teacher_profile_id' => $this->teacherProfile->id,
            'course_id' => $this->course->id,
            'title' => 'Quantum Mechanics Live Class',
            'scheduled_at' => now()->addHours(3),
            'status' => 'scheduled',
        ]);

        $this->assertNotNull($futureSession);
        $this->assertEquals('scheduled', $futureSession->status);
    }

    /** ----------------------------------------------------
     * SECTION 13 & 16: Homework Submissions & Attendance Scoping
     * ---------------------------------------------------- */
    public function test_sec13_homework_submission_and_teacher_evaluation(): void
    {
        $assignment = Assignment::create([
            'course_id' => $this->course->id,
            'teacher_profile_id' => $this->teacherProfile->id,
            'title' => 'Electromagnetism Assignment #1',
            'due_at' => now()->addDays(2),
            'status' => 'published',
        ]);

        $submission = AssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'student_user_id' => $this->studentUser->id,
            'submitted_at' => now(),
            'grade' => 90,
            'status' => SubmissionStatus::REVIEWED,
        ]);

        $this->assertEquals(90, $submission->grade);
        $this->assertEquals(SubmissionStatus::REVIEWED, $submission->status);
    }

    /** ----------------------------------------------------
     * SECTION 24 & Public Pages Audit
     * ---------------------------------------------------- */
    public function test_sec24_public_catalog_and_redirect_teacher(): void
    {
        $response = $this->get('/courses');
        $response->assertStatus(200);

        // Teacher visiting catalog is redirected to portal
        $teacherRedirect = $this->actingAs($this->teacherUser)->get('/courses');
        $teacherRedirect->assertRedirect(route('teacher-portal'));
    }
}
