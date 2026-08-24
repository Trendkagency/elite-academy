<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\LiveSession;
use App\Models\MeetingAttendance;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MeetingAttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;
    protected LiveSession $session;

    protected function setUp(): void
    {
        parent::setUp();

        $this->student = User::factory()->create(['name' => 'Attendance Test Student', 'status' => 'approved']);
        StudentProfile::create(['user_id' => $this->student->id]);

        $teacherUser = User::factory()->create(['name' => 'Dr. Teacher']);
        $teacherProfile = TeacherProfile::create(['user_id' => $teacherUser->id, 'slug' => 'dr-teacher']);

        $category = Category::create(['name' => 'Science', 'slug' => 'science']);
        $subject = Subject::create(['category_id' => $category->id, 'name' => 'Chemistry', 'slug' => 'chemistry']);
        $course = Course::create([
            'subject_id' => $subject->id,
            'teacher_id' => $teacherProfile->id,
            'title' => 'Organic Chemistry',
            'slug' => 'organic-chemistry',
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

    public function test_joining_meeting_creates_attendance_record(): void
    {
        Carbon::setTestNow('2026-08-24 10:05:00');

        $response = $this->actingAs($this->student)
            ->postJson(route('ajax.meeting.join', ['id' => $this->session->id]));

        $response->assertStatus(200);

        $this->assertDatabaseHas('meeting_attendances', [
            'live_session_id' => $this->session->id,
            'student_user_id' => $this->student->id,
            'status' => 'joined',
        ]);

        Carbon::setTestNow();
    }

    public function test_heartbeat_updates_attendance_presence_and_duration(): void
    {
        Carbon::setTestNow('2026-08-24 10:05:00');

        $joinResponse = $this->actingAs($this->student)
            ->postJson(route('ajax.meeting.join', ['id' => $this->session->id]));

        $accessToken = $joinResponse->json('access_token');
        $expiresAt = $joinResponse->json('expires_at');

        // Advance time by 30 seconds
        Carbon::setTestNow('2026-08-24 10:05:30');

        $hbResponse = $this->actingAs($this->student)
            ->postJson(route('ajax.meeting.heartbeat', ['id' => $this->session->id]), [
                'access_token' => $accessToken,
                'expires_at' => $expiresAt,
            ]);

        $hbResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status' => 'active',
            ]);

        $this->assertDatabaseHas('meeting_attendances', [
            'live_session_id' => $this->session->id,
            'student_user_id' => $this->student->id,
            'status' => 'active',
        ]);

        Carbon::setTestNow();
    }

    public function test_invalid_heartbeat_token_is_rejected(): void
    {
        Carbon::setTestNow('2026-08-24 10:05:00');

        $response = $this->actingAs($this->student)
            ->postJson(route('ajax.meeting.heartbeat', ['id' => $this->session->id]), [
                'access_token' => 'invalid_forged_token',
                'expires_at' => now()->addMinutes(5)->timestamp,
            ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
            ]);

        Carbon::setTestNow();
    }

    public function test_leaving_meeting_updates_attendance_status(): void
    {
        Carbon::setTestNow('2026-08-24 10:05:00');

        $this->actingAs($this->student)
            ->postJson(route('ajax.meeting.join', ['id' => $this->session->id]));

        Carbon::setTestNow('2026-08-24 10:30:00');

        $response = $this->actingAs($this->student)
            ->postJson(route('ajax.meeting.leave', ['id' => $this->session->id]));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('meeting_attendances', [
            'live_session_id' => $this->session->id,
            'student_user_id' => $this->student->id,
            'status' => 'left',
        ]);

        Carbon::setTestNow();
    }
}
