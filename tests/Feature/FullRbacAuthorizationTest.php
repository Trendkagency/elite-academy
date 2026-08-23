<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\AdminProfile;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\LiveSession;
use App\Models\ParentProfile;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FullRbacAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected User $studentA;
    protected User $studentB;
    protected User $teacherA;
    protected User $teacherB;
    protected User $parentA;
    protected User $parentB;
    protected User $adminUser;

    protected TeacherProfile $teacherProfileA;
    protected TeacherProfile $teacherProfileB;
    protected Course $courseA;
    protected Course $courseB;

    protected function setUp(): void
    {
        parent::setUp();

        $category = Category::create(['name' => 'Sciences', 'slug' => 'sciences', 'sort_order' => 1]);
        $subject = Subject::create(['category_id' => $category->id, 'name' => 'Physics', 'slug' => 'physics', 'is_active' => true]);

        // 1. Students
        $this->studentA = User::create(['name' => 'Student Alice', 'email' => 'alice@student.com', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        StudentProfile::create(['user_id' => $this->studentA->id, 'school_name' => 'School A']);

        $this->studentB = User::create(['name' => 'Student Bob', 'email' => 'bob@student.com', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        StudentProfile::create(['user_id' => $this->studentB->id, 'school_name' => 'School B']);

        // 2. Teachers & Courses
        $this->teacherA = User::create(['name' => 'Teacher A', 'email' => 'teacherA@elite.edu', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        $this->teacherProfileA = TeacherProfile::create(['user_id' => $this->teacherA->id, 'title' => 'Prof. A', 'slug' => 'teacher-a']);
        $this->courseA = Course::create(['subject_id' => $subject->id, 'teacher_id' => $this->teacherProfileA->id, 'title' => 'Course A', 'slug' => 'course-a', 'is_active' => true]);

        $this->teacherB = User::create(['name' => 'Teacher B', 'email' => 'teacherB@elite.edu', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        $this->teacherProfileB = TeacherProfile::create(['user_id' => $this->teacherB->id, 'title' => 'Prof. B', 'slug' => 'teacher-b']);
        $this->courseB = Course::create(['subject_id' => $subject->id, 'teacher_id' => $this->teacherProfileB->id, 'title' => 'Course B', 'slug' => 'course-b', 'is_active' => true]);

        // Enroll Student A in Course A only
        CourseEnrollment::create(['student_user_id' => $this->studentA->id, 'course_id' => $this->courseA->id, 'status' => 'active']);

        // 3. Parents
        $this->parentA = User::create(['name' => 'Parent A', 'email' => 'parentA@elite.edu', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        ParentProfile::create(['user_id' => $this->parentA->id]);

        $this->parentB = User::create(['name' => 'Parent B', 'email' => 'parentB@elite.edu', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        ParentProfile::create(['user_id' => $this->parentB->id]);

        // Link Parent A -> Student A
        \Illuminate\Support\Facades\DB::table('parent_student')->insert([
            'parent_user_id' => $this->parentA->id,
            'student_user_id' => $this->studentA->id,
        ]);

        // 4. Admin
        $this->adminUser = User::create(['name' => 'Admin Boss', 'email' => 'admin@elite-academy.com', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        AdminProfile::create(['user_id' => $this->adminUser->id]);
    }

    public function test_unauthenticated_user_cannot_access_protected_portals(): void
    {
        $this->get('/student-portal')->assertRedirect(route('login'));
        $this->get('/teacher-portal')->assertRedirect(route('login'));
        $this->get('/parent-portal')->assertRedirect(route('login'));
    }

    public function test_authenticated_user_without_permission_receives_403(): void
    {
        $response = $this->actingAs($this->studentA)->get('/teacher-portal');
        $response->assertStatus(403);
        $response->assertSee('ACCESS FORBIDDEN');

        $responseParent = $this->actingAs($this->parentA)->get('/teacher-portal');
        $responseParent->assertStatus(403);
        $responseParent->assertSee('ACCESS FORBIDDEN');
    }

    public function test_teacher_idor_protection_for_live_session(): void
    {
        $sessionB = LiveSession::create([
            'teacher_profile_id' => $this->teacherProfileB->id,
            'course_id' => $this->courseB->id,
            'title' => 'Teacher B Private Stream',
            'scheduled_at' => now()->addHour(),
            'status' => 'scheduled',
        ]);

        // Teacher A trying to reschedule Teacher B's session -> 403
        $response = $this->actingAs($this->teacherA)
            ->postJson("/ajax/teacher/sessions/{$sessionB->id}/reschedule", [
                'scheduled_at' => now()->addHours(2)->format('Y-m-d\TH:i'),
            ]);

        $response->assertStatus(403);
    }

    public function test_teacher_idor_protection_for_submission_grading(): void
    {
        $assignmentB = Assignment::create([
            'course_id' => $this->courseB->id,
            'teacher_profile_id' => $this->teacherProfileB->id,
            'title' => 'Teacher B Homework',
            'due_at' => now()->addDays(2),
            'status' => 'published',
        ]);

        $submissionB = AssignmentSubmission::create([
            'assignment_id' => $assignmentB->id,
            'student_user_id' => $this->studentB->id,
            'submitted_at' => now(),
            'status' => 'submitted',
        ]);

        // Teacher A trying to review Teacher B's submission -> 403
        $response = $this->actingAs($this->teacherA)
            ->getJson("/ajax/teacher/submissions/{$submissionB->id}/review-details");

        $response->assertStatus(403);
    }

    public function test_parent_idor_protection_for_unlinked_student_progress(): void
    {
        // Parent A trying to fetch progress for Student B (linked to Parent B) -> 403
        $response = $this->actingAs($this->parentA)
            ->getJson("/ajax/parent/student/{$this->studentB->id}/progress");

        $response->assertStatus(403)
            ->assertJson(['success' => false]);
    }

    public function test_parent_can_fetch_linked_child_progress(): void
    {
        // Parent A fetching progress for Student A (linked) -> 200
        $response = $this->actingAs($this->parentA)
            ->getJson("/ajax/parent/student/{$this->studentA->id}/progress");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_api_403_json_response_structure(): void
    {
        // AJAX request without permission returns JSON with 403
        $response = $this->actingAs($this->studentA)
            ->postJson('/ajax/teacher/sessions/create', [
                'course_id' => $this->courseA->id,
                'title' => 'Unauthorized Session',
                'scheduled_at' => now()->addDay()->format('Y-m-d\TH:i'),
                'duration_minutes' => 60,
            ]);

        $response->assertStatus(403)
            ->assertJson(['success' => false]);
    }

    public function test_admin_can_bypass_scoping_and_manage_all_resources(): void
    {
        $sessionB = LiveSession::create([
            'teacher_profile_id' => $this->teacherProfileB->id,
            'course_id' => $this->courseB->id,
            'title' => 'Teacher B Private Stream',
            'scheduled_at' => now()->addHour(),
            'status' => 'scheduled',
        ]);

        // Admin rescheduling Teacher B's session -> 200
        $response = $this->actingAs($this->adminUser)
            ->postJson("/ajax/teacher/sessions/{$sessionB->id}/reschedule", [
                'scheduled_at' => now()->addHours(3)->format('Y-m-d\TH:i'),
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }
}
