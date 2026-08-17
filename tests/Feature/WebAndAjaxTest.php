<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Enums\SessionProgressStatus;
use App\Enums\SubmissionStatus;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseSession;
use App\Models\GradeLevel;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebAndAjaxTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_blade_pages_render_successfully(): void
    {
        $this->get('/')->assertStatus(200);
        $this->get('/courses')->assertStatus(200);
        $this->get('/teachers')->assertStatus(200);
        $this->get('/about')->assertStatus(200);
    }

    public function test_user_can_login_via_ajax(): void
    {
        $user = User::create([
            'name' => 'Ajax Student',
            'email' => 'ajax_student@test.com',
            'password' => bcrypt('password123'),
            'status' => AccountStatus::APPROVED,
        ]);

        $response = $this->postJson('/ajax/login', [
            'email' => 'ajax_student@test.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertAuthenticatedAs($user);
    }

    public function test_student_can_enroll_and_submit_assignment_via_ajax(): void
    {
        $grade = GradeLevel::create(['name' => 'High School', 'slug' => 'hs']);
        $studentUser = User::create([
            'name' => 'John Student',
            'email' => 'john_ajax@test.com',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);

        $teacherUser = User::create([
            'name' => 'Jane Teacher',
            'email' => 'jane_ajax@test.com',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        $teacherProfile = TeacherProfile::create([
            'user_id' => $teacherUser->id,
            'slug' => 'jane-teacher',
            'title' => 'Physics Teacher',
        ]);

        $category = Category::create(['name' => 'Science', 'slug' => 'science']);
        $subject = Subject::create(['category_id' => $category->id, 'name' => 'Physics', 'slug' => 'physics']);
        $course = Course::create([
            'subject_id' => $subject->id,
            'teacher_id' => $teacherProfile->id,
            'grade_level_id' => $grade->id,
            'title' => 'Physics 101',
            'slug' => 'physics-101',
            'is_active' => true,
        ]);

        $session1 = CourseSession::create(['course_id' => $course->id, 'title' => 'Session 1', 'sort_order' => 1]);
        $session2 = CourseSession::create(['course_id' => $course->id, 'title' => 'Session 2', 'sort_order' => 2]);
        $assignment = Assignment::create(['course_session_id' => $session1->id, 'title' => 'Homework 1', 'passing_grade' => 70]);

        // 1. Student Enroll via Ajax
        $this->actingAs($studentUser);
        $enrollResponse = $this->postJson("/ajax/courses/{$course->id}/enroll");
        $enrollResponse->assertStatus(201)->assertJson(['success' => true]);

        $this->assertDatabaseHas('course_enrollments', [
            'student_user_id' => $studentUser->id,
            'course_id' => $course->id,
        ]);

        // 2. Student Submit Assignment via Ajax
        $submitResponse = $this->postJson('/ajax/assignments/submit', [
            'assignment_id' => $assignment->id,
            'course_id' => $course->id,
        ]);
        $submitResponse->assertStatus(201)->assertJson(['success' => true]);

        $submission = AssignmentSubmission::first();
        $this->assertNotNull($submission);

        // 3. Teacher Grade Submission via Ajax
        $teacherUser->refresh();
        $this->actingAs($teacherUser);
        $gradeResponse = $this->postJson("/ajax/submissions/{$submission->id}/grade", [
            'grade' => 85,
            'teacher_notes' => 'Passed!',
        ]);

        $gradeResponse->assertStatus(200)->assertJson(['success' => true]);

        // Verify Session 2 is UNLOCKED for Student!
        $enrollment = CourseEnrollment::where('student_user_id', $studentUser->id)->where('course_id', $course->id)->first();
        $this->assertDatabaseHas('course_session_progress', [
            'course_enrollment_id' => $enrollment->id,
            'course_session_id' => $session2->id,
            'status' => SessionProgressStatus::UNLOCKED->value,
        ]);
    }
}
