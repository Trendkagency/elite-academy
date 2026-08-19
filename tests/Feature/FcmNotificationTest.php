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

    public function test_all_9_student_flow_notifications(): void
    {
        // 1. Account approval notification
        $student = User::factory()->create(['status' => 'pending']);
        $student->update(['status' => 'approved']);
        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $student->id,
            'type' => 'ACCOUNT_APPROVED',
        ]);

        $teacherUser = User::factory()->create();
        $teacher = \App\Models\TeacherProfile::create(['user_id' => $teacherUser->id, 'slug' => 'math-teacher', 'bio' => 'Math Teacher']);
        $category = \App\Models\Category::create(['name' => 'Math', 'slug' => 'math']);
        $subject = \App\Models\Subject::create(['category_id' => $category->id, 'name' => 'Algebra', 'slug' => 'algebra']);
        $course = Course::create(['title' => 'Algebra 101', 'slug' => 'algebra-101', 'teacher_id' => $teacher->id, 'subject_id' => $subject->id]);

        $liveSession = LiveSession::create([
            'student_user_id' => $student->id,
            'teacher_profile_id' => $teacher->id,
            'subject_id' => $subject->id,
            'course_id' => $course->id,
            'scheduled_at' => now()->addMinutes(30),
            'start_at' => now()->addMinutes(30),
            'end_at' => now()->addMinutes(90),
            'status' => 'scheduled',
        ]);

        // 2. Upcoming session notification
        $service = new FcmNotificationService();
        $count = $service->sendUpcomingSessionReminders();
        $this->assertEquals(1, $count);
        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $student->id,
            'type' => 'SESSION_UPCOMING',
        ]);

        // 3. Session opened notification
        $liveSession->update(['status' => 'in_progress']);
        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $student->id,
            'type' => 'SESSION_OPENED',
        ]);

        // 4. Assignment added notification
        $assignment = Assignment::create([
            'course_id' => $course->id,
            'live_session_id' => $liveSession->id,
            'title' => 'Algebra Worksheet 1',
            'status' => 'published',
        ]);
        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $student->id,
            'type' => 'ASSIGNMENT_ADDED',
        ]);

        // 5. Assignment overdue notification
        $service->notifyAssignmentOverdue($assignment, $student);
        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $student->id,
            'type' => 'ASSIGNMENT_DEADLINE_REMINDER',
        ]);

        // 6. Session closed notification
        $liveSession->update(['status' => 'completed']);
        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $student->id,
            'type' => 'SESSION_CLOSED',
        ]);

        // 7. Exception request accepted and rejected notifications
        $exception = ExceptionRequest::create([
            'student_user_id' => $student->id,
            'live_session_id' => $liveSession->id,
            'scope' => 'course',
            'reason' => 'Sick leave',
            'status' => 'pending',
        ]);
        $exception->update(['status' => 'rejected', 'reason' => 'No medical report']);
        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $student->id,
            'type' => 'EXCEPTION_REJECTED',
        ]);

        $exception->update(['status' => 'approved']);
        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $student->id,
            'type' => 'EXCEPTION_APPROVED',
        ]);

        // 8. Session cancelled notification
        $liveSession2 = LiveSession::create([
            'student_user_id' => $student->id,
            'teacher_profile_id' => $teacher->id,
            'subject_id' => $subject->id,
            'course_id' => $course->id,
            'scheduled_at' => now()->addDays(2),
            'status' => 'scheduled',
        ]);
        $liveSession2->update(['status' => 'cancelled_by_teacher', 'cancellation_reason' => 'Teacher emergency']);
        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $student->id,
            'type' => 'SESSION_CANCELLED',
        ]);

        // 9. Session rescheduled notification
        $liveSession3 = LiveSession::create([
            'student_user_id' => $student->id,
            'teacher_profile_id' => $teacher->id,
            'subject_id' => $subject->id,
            'course_id' => $course->id,
            'scheduled_at' => now()->addDays(3),
            'status' => 'scheduled',
        ]);
        $liveSession3->update(['scheduled_at' => now()->addDays(4)]);
        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $student->id,
            'type' => 'SESSION_RESCHEDULED',
        ]);
    }

    public function test_all_5_teacher_flow_notifications(): void
    {
        $student = User::factory()->create(['name' => 'Student Learner']);
        $teacherUser = User::factory()->create(['name' => 'Dr. Teacher']);
        $teacher = \App\Models\TeacherProfile::create(['user_id' => $teacherUser->id, 'slug' => 'dr-teacher', 'bio' => 'Physics Teacher']);
        $category = \App\Models\Category::create(['name' => 'Science', 'slug' => 'science-teacher']);
        $subject = \App\Models\Subject::create(['category_id' => $category->id, 'name' => 'Physics', 'slug' => 'physics-teacher']);
        $course = Course::create(['title' => 'Physics Advanced', 'slug' => 'physics-adv', 'teacher_id' => $teacher->id, 'subject_id' => $subject->id]);

        // 1. Session assigned & opened notifications for Teacher
        $liveSession = LiveSession::create([
            'student_user_id' => $student->id,
            'teacher_profile_id' => $teacher->id,
            'subject_id' => $subject->id,
            'course_id' => $course->id,
            'scheduled_at' => now()->addMinutes(30),
            'status' => 'scheduled',
        ]);

        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $teacherUser->id,
            'type' => 'TEACHER_SESSION_ASSIGNED',
        ]);

        $liveSession->update(['status' => 'in_progress']);
        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $teacherUser->id,
            'type' => 'TEACHER_SESSION_OPENED',
        ]);

        // 2. Homework submission notification for Teacher
        $assignment = Assignment::create([
            'course_id' => $course->id,
            'live_session_id' => $liveSession->id,
            'title' => 'Lab Worksheet 1',
            'status' => 'published',
        ]);

        $enrollment = \App\Models\CourseEnrollment::create([
            'student_user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'active',
            'enrolled_at' => now(),
        ]);

        \App\Models\AssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'course_enrollment_id' => $enrollment->id,
            'live_session_id' => $liveSession->id,
            'student_user_id' => $student->id,
            'status' => 'submitted',
        ]);

        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $teacherUser->id,
            'type' => 'TEACHER_ASSIGNMENT_SUBMITTED',
        ]);

        // 3. Exception request notification for Teacher
        ExceptionRequest::create([
            'student_user_id' => $student->id,
            'live_session_id' => $liveSession->id,
            'scope' => 'course',
            'reason' => 'Family emergency',
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $teacherUser->id,
            'type' => 'TEACHER_EXCEPTION_REQUESTED',
        ]);

        // 4. Student absence notification for Teacher
        $liveSession->update(['attendance_status' => 'absent']);

        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $teacherUser->id,
            'type' => 'TEACHER_STUDENT_ABSENT',
        ]);

        // 5. Session changes (rescheduled and cancelled) for Teacher
        $liveSession2 = LiveSession::create([
            'student_user_id' => $student->id,
            'teacher_profile_id' => $teacher->id,
            'subject_id' => $subject->id,
            'course_id' => $course->id,
            'scheduled_at' => now()->addDays(1),
            'status' => 'scheduled',
        ]);

        $liveSession2->update(['scheduled_at' => now()->addDays(2)]);
        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $teacherUser->id,
            'type' => 'TEACHER_SESSION_RESCHEDULED',
        ]);

        $liveSession2->update(['status' => 'cancelled_by_teacher', 'cancellation_reason' => 'Schedule conflict']);
        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $teacherUser->id,
            'type' => 'TEACHER_SESSION_CANCELLED',
        ]);
    }
}
