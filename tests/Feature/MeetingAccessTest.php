<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\LiveSession;
use App\Models\MeetingProvider;
use App\Models\SessionMeeting;
use App\Models\StudentProfile;

use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MeetingAccessTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;
    protected User $otherStudent;
    protected LiveSession $session;

    protected function setUp(): void
    {
        parent::setUp();

        $this->student = User::factory()->create(['name' => 'Enrolled Student', 'status' => 'approved']);
        StudentProfile::create(['user_id' => $this->student->id]);

        $this->otherStudent = User::factory()->create(['name' => 'Unauthorized Student', 'status' => 'approved']);
        StudentProfile::create(['user_id' => $this->otherStudent->id]);

        $teacherUser = User::factory()->create(['name' => 'Dr. Teacher']);
        $teacherProfile = TeacherProfile::create(['user_id' => $teacherUser->id, 'slug' => 'dr-teacher']);

        $category = Category::create(['name' => 'Science', 'slug' => 'science']);
        $subject = Subject::create(['category_id' => $category->id, 'name' => 'Physics', 'slug' => 'physics']);
        $course = Course::create([
            'subject_id' => $subject->id,
            'teacher_id' => $teacherProfile->id,
            'title' => 'Advanced Physics',
            'slug' => 'advanced-physics',
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

        $provider = MeetingProvider::create([
            'name' => 'Google Meet',
            'slug' => 'google_meet',
            'is_active' => true,
            'supports_embedding' => true,
        ]);

        SessionMeeting::create([
            'live_session_id' => $this->session->id,
            'meeting_provider_id' => $provider->id,
            'provider_slug' => 'google_meet',
            'join_url' => 'https://meet.google.com/test-live-stream',
            'status' => 'active',
        ]);
    }

    public function test_unauthenticated_user_cannot_access_meeting_endpoint(): void
    {
        $response = $this->postJson(route('ajax.meeting.join', ['id' => $this->session->id]));
        $response->assertStatus(401);
    }

    public function test_unauthorized_student_cannot_join_another_students_session(): void
    {
        Carbon::setTestNow('2026-08-24 10:15:00');

        $response = $this->actingAs($this->otherStudent)
            ->postJson(route('ajax.meeting.join', ['id' => $this->session->id]));

        $response->assertStatus(403);

        Carbon::setTestNow();
    }

    public function test_authorized_student_can_join_live_session_during_valid_window(): void
    {
        // Current time: 10:15 AM (Session starts at 10:00 AM)
        Carbon::setTestNow('2026-08-24 10:15:00');

        $response = $this->actingAs($this->student)
            ->postJson(route('ajax.meeting.join', ['id' => $this->session->id]));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'can_access' => true,
                'provider' => 'google_meet',
            ])
            ->assertJsonStructure([
                'access_token',
                'expires_at',
                'watermark' => ['student_name', 'student_id', 'session_id'],
            ]);

        // Ensure raw secrets are not returned in JSON response
        $this->assertArrayNotHasKey('sdk_secret', $response->json());
        $this->assertArrayNotHasKey('api_secret', $response->json());

        Carbon::setTestNow();
    }

    public function test_meeting_join_locked_before_30_minute_window(): void
    {
        // 08:30 AM (1.5 hours before 10:00 AM session)
        Carbon::setTestNow('2026-08-24 08:30:00');

        $response = $this->actingAs($this->student)
            ->postJson(route('ajax.meeting.join', ['id' => $this->session->id]));

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'can_access' => false,
            ]);

        Carbon::setTestNow();
    }

    public function test_meeting_join_denied_after_session_ended(): void
    {
        // 11:30 AM (After 11:00 AM end time)
        Carbon::setTestNow('2026-08-24 11:30:00');

        $response = $this->actingAs($this->student)
            ->postJson(route('ajax.meeting.join', ['id' => $this->session->id]));

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'can_access' => false,
            ]);

        Carbon::setTestNow();
    }
}
