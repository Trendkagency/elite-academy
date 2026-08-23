<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\AdminProfile;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Category;
use App\Models\Course;
use App\Models\ParentProfile;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StudentPhonePrivacyTest extends TestCase
{
    use RefreshDatabase;

    protected User $studentUser;
    protected User $teacherUser;
    protected User $parentUser;
    protected User $adminUser;

    protected TeacherProfile $teacherProfile;
    protected AssignmentSubmission $submission;

    protected function setUp(): void
    {
        parent::setUp();

        $category = Category::create(['name' => 'Sciences', 'slug' => 'sciences', 'sort_order' => 1]);
        $subject = Subject::create(['category_id' => $category->id, 'name' => 'Physics', 'slug' => 'physics', 'is_active' => true]);

        // 1. Teacher
        $this->teacherUser = User::create(['name' => 'Teacher Bob', 'email' => 'teacher@elite.edu', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        $this->teacherProfile = TeacherProfile::create(['user_id' => $this->teacherUser->id, 'title' => 'Dr.', 'slug' => 'dr-bob']);
        $course = Course::create(['subject_id' => $subject->id, 'teacher_id' => $this->teacherProfile->id, 'title' => 'Physics Track', 'slug' => 'physics-track', 'is_active' => true]);

        // 2. Student with Phone Number
        $this->studentUser = User::create([
            'name' => 'Student Alice',
            'email' => 'alice@student.com',
            'phone' => '01012345678',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        StudentProfile::create(['user_id' => $this->studentUser->id, 'school_name' => 'STEM School']);

        // 3. Parent (Linked to Alice)
        $this->parentUser = User::create(['name' => 'Parent Charlie', 'email' => 'charlie@parent.com', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        ParentProfile::create(['user_id' => $this->parentUser->id]);
        DB::table('parent_student')->insert(['parent_user_id' => $this->parentUser->id, 'student_user_id' => $this->studentUser->id]);

        // 4. Admin
        $this->adminUser = User::create(['name' => 'Admin Dave', 'email' => 'admin@elite-academy.com', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        AdminProfile::create(['user_id' => $this->adminUser->id]);

        // Submission
        $assignment = Assignment::create(['course_id' => $course->id, 'teacher_profile_id' => $this->teacherProfile->id, 'title' => 'Physics Homework', 'status' => 'published']);
        $this->submission = AssignmentSubmission::create(['assignment_id' => $assignment->id, 'student_user_id' => $this->studentUser->id, 'status' => \App\Enums\SubmissionStatus::SUBMITTED]);
    }

    public function test_student_registration_requires_phone_number(): void
    {
        $response = $this->postJson('/ajax/register', [
            'name' => 'New Student',
            'email' => 'newstudent@email.com',
            'password' => 'password123',
            'user_type' => 'student',
            // Missing phone field -> validation error
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['phone']);
    }

    public function test_teacher_cannot_see_student_phone_number_in_details_endpoint(): void
    {
        $response = $this->actingAs($this->teacherUser)
            ->getJson("/ajax/teacher/students/{$this->studentUser->id}/details");

        $response->assertStatus(200);
        $json = $response->json();

        $this->assertArrayNotHasKey('phone', $json['student']);
        $response->assertDontSee('01012345678');
    }

    public function test_teacher_cannot_see_student_phone_number_in_submission_review(): void
    {
        $response = $this->actingAs($this->teacherUser)
            ->getJson("/ajax/teacher/submissions/{$this->submission->id}/review-details");

        $response->assertStatus(200);
        $response->assertDontSee('01012345678');
    }
}
