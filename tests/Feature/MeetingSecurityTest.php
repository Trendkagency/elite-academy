<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\LiveSession;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MeetingSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;
    protected User $attackerStudent;
    protected LiveSession $session;

    protected function setUp(): void
    {
        parent::setUp();

        $this->student = User::factory()->create(['name' => 'Victim Student', 'status' => 'approved']);
        StudentProfile::create(['user_id' => $this->student->id]);

        $this->attackerStudent = User::factory()->create(['name' => 'Attacker Student', 'status' => 'approved']);
        StudentProfile::create(['user_id' => $this->attackerStudent->id]);

        $teacherUser = User::factory()->create(['name' => 'Dr. Teacher']);
        $teacherProfile = TeacherProfile::create(['user_id' => $teacherUser->id, 'slug' => 'dr-teacher']);

        $category = Category::create(['name' => 'Science', 'slug' => 'science']);
        $subject = Subject::create(['category_id' => $category->id, 'name' => 'Biology', 'slug' => 'biology']);
        $course = Course::create([
            'subject_id' => $subject->id,
            'teacher_id' => $teacherProfile->id,
            'title' => 'Cellular Biology',
            'slug' => 'cellular-biology',
            'is_active' => true,
            'has_free_demo' => true,
        ]);

        CourseEnrollment::create(['student_user_id' => $this->student->id, 'course_id' => $course->id]);

        $scheduledAt = Carbon::parse('2026-08-24 10:00:00');
        $this->session = LiveSession::create([
            'student_user_id' => $this->student->id,
            'teacher_profile_id' => $teacherProfile->id,
            'subject_id' => $subject->id,
            'course_id' => $course->id,
            'scheduled_at' => $scheduledAt,
            'start_at' => $scheduledAt,
            'end_at' => $scheduledAt->copy()->addMinutes(60),
            'duration_minutes' => 60,
            'meeting_link' => 'https://meet.google.com/test-live-stream',
            'status' => 'scheduled',
            'is_free_demo' => true,
        ]);
    }

    public function test_security_event_logging_endpoint(): void
    {
        Carbon::setTestNow('2026-08-24 10:10:00');

        $response = $this->actingAs($this->student)
            ->postJson(route('ajax.meeting.security-event', ['id' => $this->session->id]), [
                'event_type' => 'TAB_HIDDEN',
                'metadata' => ['window_focused' => false],
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('meeting_security_events', [
            'live_session_id' => $this->session->id,
            'user_id' => $this->student->id,
            'event_type' => 'TAB_HIDDEN',
        ]);

        Carbon::setTestNow();
    }

    public function test_idor_attack_attempt_logs_access_denied_security_event(): void
    {
        Carbon::setTestNow('2026-08-24 10:10:00');

        $response = $this->actingAs($this->attackerStudent)
            ->postJson(route('ajax.meeting.join', ['id' => $this->session->id]));

        $response->assertStatus(403);

        $this->assertDatabaseHas('meeting_security_events', [
            'live_session_id' => $this->session->id,
            'user_id' => $this->attackerStudent->id,
            'event_type' => 'MEETING_ACCESS_DENIED',
        ]);

        Carbon::setTestNow();
    }
}
