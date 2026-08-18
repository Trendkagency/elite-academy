<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\ExceptionRequest;
use App\Models\LiveSession;
use App\Models\User;
use App\Models\UserNotification;
use App\Services\Notification\FcmNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FcmNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_register_fcm_token(): void
    {
        $student = User::factory()->create();

        $response = $this->actingAs($student)->postJson(route('ajax.notifications.token'), [
            'token' => 'test-fcm-device-token-12345',
            'device_type' => 'web',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('fcm_tokens', [
            'user_id' => $student->id,
            'token' => 'test-fcm-device-token-12345',
        ]);
    }

    public function test_assignment_deadline_reminders_sent_24h_before_session(): void
    {
        $student = User::factory()->create();
        $teacherUser = User::factory()->create();
        $teacher = \App\Models\TeacherProfile::create(['user_id' => $teacherUser->id, 'slug' => 'physics-teacher', 'bio' => 'Physics Teacher']);
        $category = \App\Models\Category::create(['name' => 'Sciences', 'slug' => 'sciences']);
        $subject = \App\Models\Subject::create(['category_id' => $category->id, 'name' => 'Physics', 'slug' => 'physics']);
        $course = Course::create(['title' => 'Physics 101', 'slug' => 'physics-101', 'teacher_id' => $teacher->id, 'subject_id' => $subject->id]);

        $liveSession = LiveSession::create([
            'student_user_id' => $student->id,
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
            'title' => 'Physics Lesson 1',
            'session_number' => 1,
        ]);

        $assignment = Assignment::create([
            'course_id' => $course->id,
            'course_session_id' => $courseSession->id,
            'live_session_id' => $liveSession->id,
            'title' => 'Pre-Lesson Physics Homework',
            'status' => 'published',
            'duration_minutes' => 30,
        ]);

        $service = new FcmNotificationService();
        $sentCount = $service->sendAssignmentDeadlineReminders();

        $this->assertEquals(1, $sentCount);
        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $student->id,
            'type' => 'ASSIGNMENT_DEADLINE_REMINDER',
        ]);
    }

    public function test_admin_approval_triggers_notification(): void
    {
        $student = User::factory()->create();

        $exception = ExceptionRequest::create([
            'student_user_id' => $student->id,
            'scope' => 'global',
            'is_global' => true,
            'reason' => 'Technical illness excuse',
            'status' => 'pending',
        ]);

        // Admin approves exception
        $exception->update(['status' => 'approved']);

        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $student->id,
            'type' => 'ADMIN_APPROVAL_ALERT',
        ]);
    }

    public function test_student_can_trigger_30s_test_push(): void
    {
        $student = User::factory()->create();

        $response = $this->actingAs($student)->postJson(route('ajax.notifications.test-push'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'delay_seconds' => 30,
        ]);

        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $student->id,
            'type' => 'TEST_NOTIFICATION',
        ]);
    }

    public function test_admin_can_broadcast_custom_fcm_notification_to_target_audience(): void
    {
        $studentUser = User::factory()->create();
        \App\Models\StudentProfile::create(['user_id' => $studentUser->id, 'school_name' => 'Cairo STEM']);

        $teacherUser = User::factory()->create();
        \App\Models\TeacherProfile::create(['user_id' => $teacherUser->id, 'slug' => 'teacher-1', 'bio' => 'Teacher']);

        $service = new FcmNotificationService();
        $count = $service->broadcastNotification('students', '📢 Exam Update', 'Exam starts tomorrow at 9 AM.');

        $this->assertEquals(1, $count);
        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $studentUser->id,
            'type' => 'BROADCAST_ALERT',
            'title' => '📢 Exam Update',
        ]);
        $this->assertDatabaseMissing('user_notifications', [
            'user_id' => $teacherUser->id,
            'title' => '📢 Exam Update',
        ]);
    }
}
