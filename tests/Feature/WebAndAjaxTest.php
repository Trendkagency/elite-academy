<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\AssignmentQuestion;
use App\Models\AssignmentQuestionOption;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseSession;
use App\Models\LiveSession;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebAndAjaxTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;
    protected Course $course;
    protected LiveSession $liveSession;
    protected Assignment $assignment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->student = User::factory()->create(['name' => 'Web Test Student', 'status' => \App\Enums\AccountStatus::APPROVED]);
        \App\Models\StudentProfile::create(['user_id' => $this->student->id]);
        \App\Models\StudentPackage::create(['student_user_id' => $this->student->id, 'total_sessions' => 12, 'used_sessions' => 0, 'remaining_sessions' => 12, 'status' => 'active', 'activated_at' => now()]);
        $teacherUser = User::factory()->create();
        $teacher = TeacherProfile::create(['user_id' => $teacherUser->id, 'slug' => 'web-teacher', 'bio' => 'Teacher Bio']);

        $category = Category::create(['name' => 'Web Category', 'slug' => 'web-category']);
        $subject = Subject::create(['category_id' => $category->id, 'name' => 'Web Subject', 'slug' => 'web-subject']);
        $this->course = Course::create(['title' => 'Web Course', 'slug' => 'web-course', 'teacher_id' => $teacher->id, 'subject_id' => $subject->id]);

        CourseEnrollment::create([
            'student_user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'status' => 'active',
            'enrolled_at' => now(),
        ]);

        $courseSession = CourseSession::create([
            'course_id' => $this->course->id,
            'title' => 'Session 1',
            'session_number' => 1,
        ]);

        $this->liveSession = LiveSession::create([
            'student_user_id' => $this->student->id,
            'teacher_profile_id' => $teacher->id,
            'subject_id' => $subject->id,
            'course_id' => $this->course->id,
            'scheduled_at' => now()->addMinutes(15),
            'start_at' => now()->addMinutes(15),
            'end_at' => now()->addHours(2),
            'status' => 'scheduled',
            'meeting_link' => 'https://meet.google.com/test-live-stream',
        ]);

        $this->assignment = Assignment::create([
            'course_id' => $this->course->id,
            'course_session_id' => $courseSession->id,
            'live_session_id' => $this->liveSession->id,
            'title' => 'Web Assignment',
            'status' => 'published',
            'due_at' => now()->addDays(2),
            'duration_minutes' => 30,
            'passing_score' => 70.00,
        ]);

        $q = AssignmentQuestion::create([
            'assignment_id' => $this->assignment->id,
            'question_text' => 'Question 1',
            'points' => 5.0,
        ]);

        AssignmentQuestionOption::create([
            'question_id' => $q->id,
            'option_text' => 'Option 1',
            'is_correct' => true,
        ]);
    }

    public function test_student_portal_renders_successfully(): void
    {
        $response = $this->actingAs($this->student)->get(route('student-portal'));
        $response->assertStatus(200);
        $response->assertSee('Student Portal');
    }

    public function test_assignment_take_page_renders_successfully(): void
    {
        $response = $this->actingAs($this->student)->get(route('student.assignment.take', ['id' => $this->assignment->id]));
        $response->assertStatus(200);
        $response->assertSee($this->assignment->title);
    }
}
