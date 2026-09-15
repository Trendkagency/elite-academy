<?php

namespace Tests\Feature;

use App\Enums\LiveSessionState;
use App\Models\Category;
use App\Models\Course;
use App\Models\LiveSession;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use App\Services\Session\LiveSessionService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LiveSessionTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;
    protected User $otherStudent;
    protected LiveSession $session;
    protected LiveSessionService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new LiveSessionService();

        $this->student = User::factory()->create(['name' => 'Test Student', 'status' => 'approved']);
        \App\Models\StudentProfile::create(['user_id' => $this->student->id]);
        $this->otherStudent = User::factory()->create(['name' => 'Other Student', 'status' => 'approved']);
        \App\Models\StudentProfile::create(['user_id' => $this->otherStudent->id]);

        $teacherUser = User::factory()->create(['name' => 'Dr. Teacher']);
        $teacherProfile = TeacherProfile::create(['user_id' => $teacherUser->id, 'slug' => 'dr-teacher']);

        $category = Category::create([
            'name' => 'General Science',
            'slug' => 'general-science',
        ]);

        $subject = Subject::create([
            'category_id' => $category->id,
            'name' => 'Physics',
            'slug' => 'physics',
        ]);

        $course = Course::create([
            'subject_id' => $subject->id,
            'teacher_id' => $teacherProfile->id,
            'title' => 'Physics 101',
            'slug' => 'physics-101',
            'is_active' => true,
            'has_free_demo' => true,
        ]);

        // Create a live session: start_at = 08:38 AM, duration = 60 mins -> end_at = 09:38 AM
        $scheduledAt = Carbon::parse('2026-08-18 08:38:00');

        $this->session = LiveSession::create([
            'student_user_id' => $this->student->id,
            'teacher_profile_id' => $teacherProfile->id,
            'subject_id' => $subject->id,
            'course_id' => $course->id,
            'scheduled_at' => $scheduledAt,
            'start_at' => $scheduledAt,
            'end_at' => $scheduledAt->copy()->addMinutes(60), // 09:38 AM
            'duration_minutes' => 60,
            'meeting_link' => 'https://meet.google.com/test-live-stream',
            'status' => 'scheduled',
            'is_free_demo' => true,
        ]);
    }

    public function test_session_is_ended_when_current_time_is_past_end_at(): void
    {
        // Test at 11:40 AM (Current time in example prompt, past 09:38 AM end_at)
        $now = Carbon::parse('2026-08-18 11:40:00');
        Carbon::setTestNow($now);

        $state = $this->service->evaluateState($this->session, $this->student, $now);
        $this->assertEquals(LiveSessionState::ENDED, $state);

        $response = $this->actingAs($this->student)
            ->getJson(route('ajax.live-session.access', ['id' => $this->session->id]));

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'state' => 'ended',
            ]);

        Carbon::setTestNow();
    }

    public function test_session_is_locked_before_joinable_time(): void
    {
        // 07:00 AM (Before 08:08 AM joinable window)
        $now = Carbon::parse('2026-08-18 07:00:00');
        Carbon::setTestNow($now);

        $state = $this->service->evaluateState($this->session, $this->student, $now);
        $this->assertEquals(LiveSessionState::BEFORE_JOINABLE, $state);

        $response = $this->actingAs($this->student)
            ->getJson(route('ajax.live-session.access', ['id' => $this->session->id]));

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'state' => 'before_joinable',
            ]);

        Carbon::setTestNow();
    }

    public function test_session_is_live_during_valid_window(): void
    {
        // 08:45 AM (Within 08:38 AM to 09:38 AM window)
        $now = Carbon::parse('2026-08-18 08:45:00');
        Carbon::setTestNow($now);

        $state = $this->service->evaluateState($this->session, $this->student, $now);
        $this->assertEquals(LiveSessionState::LIVE, $state);

        $response = $this->actingAs($this->student)
            ->getJson(route('ajax.live-session.access', ['id' => $this->session->id]));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'state' => 'live',
                'meeting_link' => 'https://meet.google.com/test-live-stream',
            ]);

        Carbon::setTestNow();
    }

    public function test_session_remains_before_joinable_when_more_than_30_minutes_remain(): void
    {
        // Session starts at 18:00 (06:00 PM), scheduled_at is 14:00 (old scheduled time)
        $this->session->update([
            'scheduled_at' => Carbon::parse('2026-09-15 14:00:00'),
            'start_at' => Carbon::parse('2026-09-15 18:00:00'),
            'end_at' => Carbon::parse('2026-09-15 19:00:00'),
        ]);

        // Current time is 16:15 (04:15 PM) -> 1h 45m before start
        $now = Carbon::parse('2026-09-15 16:15:00');
        Carbon::setTestNow($now);

        $state = $this->service->evaluateState($this->session, $this->student, $now);
        $this->assertEquals(LiveSessionState::BEFORE_JOINABLE, $state);

        // At 17:35 (05:35 PM) -> within 30 min window (25 mins before 18:00)
        $joinableTime = Carbon::parse('2026-09-15 17:35:00');
        $joinableState = $this->service->evaluateState($this->session, $this->student, $joinableTime);
        $this->assertEquals(LiveSessionState::LIVE, $joinableState);

        Carbon::setTestNow();
    }
}

