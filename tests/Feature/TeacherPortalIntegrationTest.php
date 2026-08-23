<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Category;
use App\Models\Course;
use App\Models\LiveSession;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherPortalIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $teacherUser;
    protected TeacherProfile $teacherProfile;
    protected Course $course;

    protected function setUp(): void
    {
        parent::setUp();

        $category = Category::create(['name' => 'Sciences', 'slug' => 'sciences', 'sort_order' => 1]);
        $subject = Subject::create(['category_id' => $category->id, 'name' => 'Physics', 'slug' => 'physics', 'is_active' => true]);

        $this->teacherUser = User::create([
            'name' => 'Prof. Richard Feynman',
            'email' => 'feynman@elite-academy.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);

        $this->teacherProfile = TeacherProfile::create([
            'user_id' => $this->teacherUser->id,
            'slug' => 'richard-feynman',
            'title' => 'Professor of Quantum Electrodynamics',
            'specialization' => 'Theoretical Physics',
            'years_experience' => 15,
            'rating_avg' => 4.95,
            'students_count' => 1200,
        ]);

        $this->course = Course::create([
            'subject_id' => $subject->id,
            'teacher_id' => $this->teacherProfile->id,
            'title' => 'Quantum Electrodynamics 101',
            'slug' => 'qed-101',
            'sessions_count' => 10,
            'is_active' => true,
        ]);
    }

    public function test_unauthenticated_user_cannot_access_teacher_portal(): void
    {
        $response = $this->get('/teacher-portal');
        $response->assertRedirect('/login');
    }

    public function test_student_or_parent_cannot_access_teacher_portal(): void
    {
        $studentUser = User::create([
            'name' => 'Student Alex',
            'email' => 'alex@student.com',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);

        \App\Models\StudentProfile::create(['user_id' => $studentUser->id]);

        $response = $this->actingAs($studentUser)->get('/teacher-portal');
        $response->assertStatus(403);
    }

    public function test_authenticated_teacher_can_access_teacher_portal(): void
    {
        $response = $this->actingAs($this->teacherUser)->get('/teacher-portal');

        $response->assertStatus(200)
            ->assertSee('Teacher Portal')
            ->assertSee('Prof. Richard Feynman')
            ->assertSee('Quantum Electrodynamics 101');
    }

    public function test_teacher_can_create_live_session(): void
    {
        $payload = [
            'course_id' => $this->course->id,
            'title' => 'Lecture 1: Photons and Electrons',
            'scheduled_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'duration_minutes' => 60,
            'meeting_link' => 'https://zoom.us/j/123456789',
        ];

        $response = $this->actingAs($this->teacherUser)
            ->postJson('/ajax/teacher/sessions/create', $payload);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('live_sessions', [
            'teacher_profile_id' => $this->teacherProfile->id,
            'course_id' => $this->course->id,
            'title' => 'Lecture 1: Photons and Electrons',
        ]);
    }

    public function test_teacher_cannot_cancel_or_modify_another_teachers_session(): void
    {
        $otherTeacherUser = User::create([
            'name' => 'Dr. Marie Curie',
            'email' => 'curie@elite-academy.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);

        $otherProfile = TeacherProfile::create([
            'user_id' => $otherTeacherUser->id,
            'slug' => 'marie-curie',
            'title' => 'Nobel Laureate',
            'specialization' => 'Radioactivity',
        ]);

        $otherSession = LiveSession::create([
            'teacher_profile_id' => $otherProfile->id,
            'title' => 'Radioactivity & Nuclear Reactions',
            'scheduled_at' => now()->addDays(2),
            'status' => 'scheduled',
        ]);

        // Attempt cancellation by Feynman
        $response = $this->actingAs($this->teacherUser)
            ->postJson("/ajax/teacher/sessions/{$otherSession->id}/cancel");

        $response->assertStatus(403);

        $this->assertEquals('scheduled', $otherSession->fresh()->status);
    }

    public function test_teacher_can_grade_submission(): void
    {
        $assignment = Assignment::create([
            'teacher_profile_id' => $this->teacherProfile->id,
            'course_id' => $this->course->id,
            'title' => 'QED Problem Set #1',
            'due_at' => now()->addDays(3),
            'status' => 'published',
        ]);

        $studentUser = User::create([
            'name' => 'Student Bob',
            'email' => 'bob@student.com',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);

        $submission = AssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'student_user_id' => $studentUser->id,
            'submitted_at' => now(),
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($this->teacherUser)
            ->postJson("/ajax/teacher/submissions/{$submission->id}/review", [
                'score' => 95.5,
                'evaluation_notes' => 'Excellent derivation of Feynman diagrams!',
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertEquals(95.5, $submission->fresh()->score);
    }

    public function test_teacher_can_mark_attendance(): void
    {
        $session = LiveSession::create([
            'teacher_profile_id' => $this->teacherProfile->id,
            'course_id' => $this->course->id,
            'title' => 'Live Quantum Lab',
            'scheduled_at' => now(),
            'status' => 'scheduled',
        ]);

        $studentUser = User::create([
            'name' => 'Student Alice',
            'email' => 'alice@student.com',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);

        $payload = [
            'attendance' => [
                [
                    'student_user_id' => $studentUser->id,
                    'status' => 'present',
                ],
            ],
        ];

        $response = $this->actingAs($this->teacherUser)
            ->postJson("/ajax/teacher/sessions/{$session->id}/attendance", $payload);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertEquals('completed', $session->fresh()->status);
    }

    public function test_teacher_can_fetch_student_details_academic_records(): void
    {
        $studentUser = User::create([
            'name' => 'Student Clara',
            'email' => 'clara@student.com',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        \App\Models\StudentProfile::create(['user_id' => $studentUser->id, 'school_name' => 'MIT Prep']);

        $response = $this->actingAs($this->teacherUser)
            ->getJson("/ajax/teacher/students/{$studentUser->id}/details");

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonPath('student.name', 'Student Clara')
            ->assertJsonPath('student.school', 'MIT Prep');
    }

    public function test_teacher_can_fetch_submission_review_question_breakdown(): void
    {
        $assignment = Assignment::create([
            'teacher_profile_id' => $this->teacherProfile->id,
            'course_id' => $this->course->id,
            'title' => 'QED Quiz #2',
            'due_at' => now()->addDays(5),
            'status' => 'published',
        ]);

        $question = \App\Models\AssignmentQuestion::create([
            'assignment_id' => $assignment->id,
            'question_text' => 'What is the coupling constant in QED?',
            'points' => 10,
        ]);

        \App\Models\AssignmentQuestionOption::create([
            'question_id' => $question->id,
            'option_text' => 'Alpha ~ 1/137',
            'is_correct' => true,
            'explanation' => 'Fine-structure constant is approx 1/137.',
        ]);

        $studentUser = User::create([
            'name' => 'Student Dave',
            'email' => 'dave@student.com',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);

        $submission = AssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'student_user_id' => $studentUser->id,
            'submitted_at' => now(),
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($this->teacherUser)
            ->getJson("/ajax/teacher/submissions/{$submission->id}/review-details");

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonPath('submission.assignment_title', 'QED Quiz #2')
            ->assertJsonPath('questions.0.question_text', 'What is the coupling constant in QED?');
    }

    public function test_teacher_portal_renders_in_arabic_when_switching_locale(): void
    {
        $this->get(route('lang.switch', ['locale' => 'ar']))->assertRedirect();

        \Illuminate\Support\Facades\App::setLocale('ar');

        $response = $this->actingAs($this->teacherUser)
            ->withSession(['locale' => 'ar'])
            ->get('/teacher-portal');

        $response->assertStatus(200);
        $this->assertEquals('ar', app()->getLocale());
    }
}
