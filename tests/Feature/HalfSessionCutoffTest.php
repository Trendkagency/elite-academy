<?php

namespace Tests\Feature;

use App\Enums\LiveSessionState;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\LiveSession;
use App\Models\StudentPackage;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use App\Services\Session\LiveSessionService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HalfSessionCutoffTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;
    protected TeacherProfile $teacher;
    protected Course $course;
    protected LiveSession $session;
    protected LiveSessionService $liveSessionService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->student = User::factory()->create(['name' => 'Half Session Student', 'status' => \App\Enums\AccountStatus::APPROVED]);
        \App\Models\StudentProfile::create(['user_id' => $this->student->id]);
        StudentPackage::create([
            'student_user_id' => $this->student->id,
            'total_sessions' => 12,
            'used_sessions' => 0,
            'remaining_sessions' => 12,
            'status' => 'active',
            'activated_at' => now(),
        ]);

        $teacherUser = User::factory()->create();
        $this->teacher = TeacherProfile::create(['user_id' => $teacherUser->id, 'slug' => 'half-teacher', 'bio' => 'Teacher Bio']);

        $category = Category::create(['name' => 'Sciences', 'slug' => 'sciences']);
        $subject = Subject::create(['category_id' => $category->id, 'name' => 'Physics', 'slug' => 'physics']);
        $this->course = Course::create(['title' => 'Physics 101', 'slug' => 'physics-101', 'teacher_id' => $this->teacher->id, 'subject_id' => $subject->id]);

        CourseEnrollment::create([
            'student_user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'status' => 'active',
            'enrolled_at' => now(),
        ]);

        // Create a 60-minute live session starting at 10:00 AM today
        $startTime = Carbon::parse('2026-08-18 10:00:00');
        $this->session = LiveSession::create([
            'student_user_id' => $this->student->id,
            'teacher_profile_id' => $this->teacher->id,
            'subject_id' => $subject->id,
            'course_id' => $this->course->id,
            'title' => 'Electromagnetism Live Class',
            'scheduled_at' => $startTime,
            'start_at' => $startTime,
            'end_at' => $startTime->copy()->addMinutes(60),
            'duration_minutes' => 60,
            'meeting_link' => 'https://meet.google.com/test-half-session',
            'status' => 'scheduled',
        ]);

        $this->liveSessionService = app(LiveSessionService::class);
    }

    public function test_student_can_join_stream_before_half_session_mark(): void
    {
        // 10:15 AM (15 mins into a 60-min session — before 30-min halfway mark)
        $testTime = Carbon::parse('2026-08-18 10:15:00');
        Carbon::setTestNow($testTime);

        $state = $this->liveSessionService->evaluateState($this->session, $this->student, $testTime);
        $this->assertEquals(LiveSessionState::LIVE, $state);

        $access = $this->liveSessionService->canAccessStream($this->student, $this->session);
        $this->assertTrue($access['allowed'] ?? false);

        $this->actingAs($this->student);
        $response = $this->getJson("/ajax/live-sessions/{$this->session->id}/access");

        $response->assertStatus(200)
            ->assertJsonPath('can_access', true)
            ->assertJsonPath('meeting_link', 'https://meet.google.com/test-half-session');
    }

    public function test_student_cannot_join_stream_after_half_session_mark(): void
    {
        // 10:35 AM (35 mins into a 60-min session — past 30-min halfway mark)
        $testTime = Carbon::parse('2026-08-18 10:35:00');
        Carbon::setTestNow($testTime);

        $state = $this->liveSessionService->evaluateState($this->session, $this->student, $testTime);
        $this->assertEquals(LiveSessionState::ENDED, $state);

        $access = $this->liveSessionService->canAccessStream($this->student, $this->session);
        $this->assertFalse($access['allowed'] ?? true);
        $this->assertEquals('HALF_SESSION_EXPIRED', $access['reason_code']);

        $this->actingAs($this->student);
        $response = $this->getJson("/ajax/live-sessions/{$this->session->id}/access");

        $response->assertStatus(422)
            ->assertJsonPath('can_access', false)
            ->assertJsonPath('reason_code', 'HALF_SESSION_EXPIRED');
    }

    public function test_student_cannot_join_stream_after_full_session_end(): void
    {
        // 11:05 AM (Past 60-min session end time 11:00 AM)
        $testTime = Carbon::parse('2026-08-18 11:05:00');
        Carbon::setTestNow($testTime);

        $state = $this->liveSessionService->evaluateState($this->session, $this->student, $testTime);
        $this->assertEquals(LiveSessionState::ENDED, $state);

        $access = $this->liveSessionService->canAccessStream($this->student, $this->session);
        $this->assertFalse($access['allowed'] ?? true);
        $this->assertEquals('SESSION_ENDED', $access['reason_code']);
    }
}
