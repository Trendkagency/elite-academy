<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\AssignmentQuestion;
use App\Models\AssignmentQuestionOption;
use App\Models\AssignmentSubmission;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseSession;
use App\Models\ExceptionRequest;
use App\Models\LiveSession;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LiveSessionStream30MinAndAssignmentGatingTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;
    protected TeacherProfile $teacher;
    protected Course $course;
    protected LiveSession $session;
    protected Assignment $mandatoryAssignment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->student = User::factory()->create(['name' => 'Gating Student', 'status' => \App\Enums\AccountStatus::APPROVED]);
        \App\Models\StudentProfile::create(['user_id' => $this->student->id]);
        \App\Models\StudentPackage::create(['student_user_id' => $this->student->id, 'total_sessions' => 12, 'used_sessions' => 0, 'remaining_sessions' => 12, 'status' => 'active', 'activated_at' => now()]);
        $teacherUser = User::factory()->create();
        $this->teacher = TeacherProfile::create(['user_id' => $teacherUser->id, 'slug' => 'gating-teacher', 'bio' => 'Bio']);
        $category = Category::create(['name' => 'Science', 'slug' => 'science']);
        $subject = Subject::create(['category_id' => $category->id, 'name' => 'Physics', 'slug' => 'physics']);
        $this->course = Course::create(['title' => 'Physics 101', 'slug' => 'physics-101', 'teacher_id' => $this->teacher->id, 'subject_id' => $subject->id]);

        CourseEnrollment::create([
            'student_user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'status' => 'active',
            'enrolled_at' => now(),
        ]);

        $courseSession = CourseSession::create([
            'course_id' => $this->course->id,
            'title' => 'Lesson 1',
            'session_number' => 1,
        ]);

        $this->session = LiveSession::create([
            'student_user_id' => $this->student->id,
            'teacher_profile_id' => $this->teacher->id,
            'subject_id' => $subject->id,
            'course_id' => $this->course->id,
            'scheduled_at' => now()->addMinutes(15), // Stream starting in 15 mins (within 30m window)
            'start_at' => now()->addMinutes(15),
            'end_at' => now()->addHours(2),
            'status' => 'scheduled',
            'meeting_link' => 'https://meet.google.com/test-live-stream',
        ]);

        $this->mandatoryAssignment = Assignment::create([
            'course_id' => $this->course->id,
            'course_session_id' => $courseSession->id,
            'live_session_id' => $this->session->id,
            'title' => 'Mandatory Prerequisite MSQ',
            'status' => 'published',
            'is_mandatory' => true,
            'duration_minutes' => 30,
            'due_at' => now()->addHours(2),
            'passing_score' => 70.00,
        ]);

        $q = AssignmentQuestion::create([
            'assignment_id' => $this->mandatoryAssignment->id,
            'question_text' => 'Gating question',
            'points' => 1.0,
        ]);

        AssignmentQuestionOption::create([
            'question_id' => $q->id,
            'option_text' => 'Correct',
            'is_correct' => true,
        ]);
    }

    public function test_access_blocked_when_mandatory_assignment_uncompleted(): void
    {
        $response = $this->actingAs($this->student)->getJson(route('ajax.live-session.access', ['id' => $this->session->id]));

        $response->assertStatus(422);
        $response->assertJson([
            'can_access' => false,
            'reason_code' => 'ASSIGNMENT_REQUIRED',
        ]);
    }

    public function test_access_granted_when_mandatory_assignment_completed(): void
    {
        // Complete the mandatory assignment
        $this->mandatoryAssignment->load('questions.options');
        $question = $this->mandatoryAssignment->questions->first();
        $option = $question->options->first();

        $this->actingAs($this->student)->postJson(route('ajax.assignment.submit'), [
            'assignment_id' => $this->mandatoryAssignment->id,
            'answers' => [
                $question->id => [$option->id]
            ]
        ]);

        $response = $this->actingAs($this->student)->getJson(route('ajax.live-session.access', ['id' => $this->session->id]));
        $response->assertStatus(200);
        $response->assertJson([
            'can_access' => true,
            'state' => 'live',
        ]);
    }

    public function test_access_granted_when_approved_exception_exists(): void
    {
        // Create an approved exception request for course
        ExceptionRequest::create([
            'student_user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'scope' => 'course',
            'reason' => 'Emergency medical exception approved by admin',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($this->student)->getJson(route('ajax.live-session.access', ['id' => $this->session->id]));

        $response->assertStatus(200);
        $response->assertJson([
            'can_access' => true,
            'state' => 'live',
        ]);
    }
}
