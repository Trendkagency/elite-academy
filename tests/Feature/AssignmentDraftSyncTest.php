<?php

namespace Tests\Feature;

use App\Enums\SubmissionStatus;
use App\Models\Assignment;
use App\Models\AssignmentQuestion;
use App\Models\AssignmentQuestionOption;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\LiveSession;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssignmentDraftSyncTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;
    protected Assignment $assignment;
    protected AssignmentQuestion $question;
    protected AssignmentQuestionOption $opt1;
    protected AssignmentQuestionOption $opt2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->student = User::factory()->create(['status' => \App\Enums\AccountStatus::APPROVED]);
        \App\Models\StudentProfile::create(['user_id' => $this->student->id]);
        \App\Models\StudentPackage::create(['student_user_id' => $this->student->id, 'total_sessions' => 12, 'used_sessions' => 0, 'remaining_sessions' => 12, 'status' => 'active', 'activated_at' => now()]);
        $teacherUser = User::factory()->create();
        $teacher = TeacherProfile::create(['user_id' => $teacherUser->id, 'slug' => 'draft-teacher', 'bio' => 'Teacher']);
        $category = \App\Models\Category::create(['name' => 'Draft Sciences', 'slug' => 'draft-sciences']);
        $subject = Subject::create(['category_id' => $category->id, 'name' => 'Physics Draft', 'slug' => 'physics-draft']);
        $course = Course::create(['title' => 'Physics Course', 'slug' => 'physics-course', 'teacher_id' => $teacher->id, 'subject_id' => $subject->id]);

        $enrollment = \App\Models\CourseEnrollment::create([
            'student_user_id' => $this->student->id,
            'course_id' => $course->id,
            'status' => 'active',
            'enrolled_at' => now(),
        ]);

        $liveSession = LiveSession::create([
            'student_user_id' => $this->student->id,
            'teacher_profile_id' => $teacher->id,
            'subject_id' => $subject->id,
            'course_id' => $course->id,
            'scheduled_at' => now()->addHours(24),
            'start_at' => now()->addHours(24),
            'end_at' => now()->addHours(26),
            'status' => 'scheduled',
        ]);

        $courseSession = \App\Models\CourseSession::create([
            'course_id' => $course->id,
            'title' => 'Draft Lesson 1',
            'session_number' => 1,
        ]);

        $this->assignment = Assignment::create([
            'course_id' => $course->id,
            'course_session_id' => $courseSession->id,
            'live_session_id' => $liveSession->id,
            'title' => 'Draft Sync MSQ Assignment',
            'status' => 'published',
            'duration_minutes' => 30,
            'passing_score' => 70.00,
            'due_at' => now()->addDays(7),
        ]);

        $this->question = AssignmentQuestion::create([
            'assignment_id' => $this->assignment->id,
            'question_text' => 'What is 2 + 2?',
            'question_type' => 'text',
            'points' => 5.00,
            'sort_order' => 1,
        ]);

        $this->opt1 = AssignmentQuestionOption::create([
            'question_id' => $this->question->id,
            'option_text' => '4',
            'is_correct' => true,
            'sort_order' => 1,
        ]);

        $this->opt2 = AssignmentQuestionOption::create([
            'question_id' => $this->question->id,
            'option_text' => '5',
            'is_correct' => false,
            'sort_order' => 2,
        ]);
    }

    public function test_student_can_auto_save_draft_answer_to_server(): void
    {
        $response = $this->actingAs($this->student)->postJson(route('ajax.assignment.save-answer'), [
            'assignment_id' => $this->assignment->id,
            'question_id' => $this->question->id,
            'selected_option_ids' => [$this->opt1->id],
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $submission = AssignmentSubmission::where('assignment_id', $this->assignment->id)
            ->where('student_user_id', $this->student->id)
            ->first();

        $this->assertNotNull($submission);
        $this->assertEquals(SubmissionStatus::IN_PROGRESS, $submission->status);

        $this->assertDatabaseHas('assignment_submission_answers', [
            'submission_id' => $submission->id,
            'question_id' => $this->question->id,
        ]);
    }

    public function test_saved_draft_answers_restored_on_page_load(): void
    {
        // First save a draft answer
        $this->actingAs($this->student)->postJson(route('ajax.assignment.save-answer'), [
            'assignment_id' => $this->assignment->id,
            'question_id' => $this->question->id,
            'selected_option_ids' => [$this->opt1->id],
        ]);

        // Load take page
        $response = $this->actingAs($this->student)->get(route('student.assignment.take', ['id' => $this->assignment->id]));

        $response->assertStatus(200);
        $response->assertViewHas('savedAnswers', function ($savedAnswers) {
            return isset($savedAnswers[$this->question->id]) && in_array($this->opt1->id, $savedAnswers[$this->question->id]);
        });
    }

    public function test_answer_edits_rejected_after_final_submission(): void
    {
        // Finalize submission
        $this->actingAs($this->student)->postJson(route('ajax.assignment.submit'), [
            'assignment_id' => $this->assignment->id,
            'answers' => [
                $this->question->id => [$this->opt1->id],
            ],
        ]);

        // Attempting to modify draft answer afterwards should return 403
        $response = $this->actingAs($this->student)->postJson(route('ajax.assignment.save-answer'), [
            'assignment_id' => $this->assignment->id,
            'question_id' => $this->question->id,
            'selected_option_ids' => [$this->opt2->id],
        ]);

        $response->assertStatus(403);
    }

    public function test_server_authoritative_timer_and_step_index_resume_on_refresh(): void
    {
        // 1. Initial start creates attempt with started_at
        $this->actingAs($this->student)->get(route('student.assignment.take', ['id' => $this->assignment->id]));

        $submission = AssignmentSubmission::where('assignment_id', $this->assignment->id)
            ->where('student_user_id', $this->student->id)
            ->first();

        $this->assertNotNull($submission);
        $this->assertEquals(0, $submission->current_step_index);

        // 2. Student updates current step index to 1
        $this->actingAs($this->student)->postJson(route('ajax.assignment.update-step'), [
            'assignment_id' => $this->assignment->id,
            'current_step_index' => 1,
        ])->assertStatus(200);

        $submission->refresh();
        $this->assertEquals(1, $submission->current_step_index);

        // 3. Fast-forward time by 5 minutes (300s)
        $this->travel(5)->minutes();

        // 4. Page refresh loads attempt, restores remainingSeconds (1800 - 300 = 1500) and stepIndex (1)
        $response = $this->actingAs($this->student)->get(route('student.assignment.take', ['id' => $this->assignment->id]));
        $response->assertStatus(200);
        $response->assertViewHas('currentStepIndex', 1);
        $response->assertViewHas('remainingSeconds', function ($secs) {
            return $secs >= 1490 && $secs <= 1505; // 25 mins remaining
        });

        // 5. Ensure only 1 attempt exists for student/assignment
        $attemptCount = AssignmentSubmission::where('assignment_id', $this->assignment->id)
            ->where('student_user_id', $this->student->id)
            ->count();
        $this->assertEquals(1, $attemptCount);
    }
}
