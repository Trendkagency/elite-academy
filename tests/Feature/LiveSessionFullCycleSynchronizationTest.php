<?php

namespace Tests\Feature;

use App\Models\AdminProfile;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\LiveSession;
use App\Models\Package;
use App\Models\RecurringSchedule;
use App\Models\StudentPackage;
use App\Models\StudentProfile;
use App\Models\StudentSession;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use App\Services\Session\SessionAttendanceDeductionService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LiveSessionFullCycleSynchronizationTest extends TestCase
{
    use RefreshDatabase;

    protected function createSubject(string $name, string $slug): Subject
    {
        $category = \App\Models\Category::firstOrCreate(['slug' => 'general'], ['name' => 'General']);
        return Subject::create([
            'category_id' => $category->id,
            'name' => $name,
            'slug' => $slug,
        ]);
    }

    public function test_rescheduling_session_synchronizes_start_and_end_at_and_resets_reminders(): void
    {
        $teacher = User::factory()->create();
        $teacherProfile = TeacherProfile::create([
            'user_id' => $teacher->id,
            'slug' => 'teacher-' . $teacher->id,
        ]);
        $subject = $this->createSubject('Physics', 'phy101');

        $originalTime = Carbon::now()->addHours(2);
        $session = LiveSession::create([
            'title' => 'Physics Lecture 1',
            'teacher_profile_id' => $teacherProfile->id,
            'subject_id' => $subject->id,
            'scheduled_at' => $originalTime,
            'duration_minutes' => 60,
            'reminders_sent' => ['2h', '24h'],
            'reminder_sent_at' => Carbon::now()->subMinutes(10),
            'status' => 'scheduled',
        ]);

        $this->assertEquals($originalTime->toDateTimeString(), $session->start_at->toDateTimeString());
        $this->assertEquals($originalTime->copy()->addMinutes(60)->toDateTimeString(), $session->end_at->toDateTimeString());

        // Reschedule to tomorrow at 16:00
        $newTime = Carbon::now()->addDays(2)->setHour(16)->setMinute(0)->setSecond(0);
        $session->update([
            'scheduled_at' => $newTime,
            'duration_minutes' => 90,
        ]);

        $session->refresh();

        $this->assertEquals($newTime->toDateTimeString(), $session->scheduled_at->toDateTimeString());
        $this->assertEquals($newTime->toDateTimeString(), $session->start_at->toDateTimeString());
        $this->assertEquals($newTime->copy()->addMinutes(90)->toDateTimeString(), $session->end_at->toDateTimeString());
        $this->assertEquals($newTime->toDateTimeString(), $session->effective_start_at->toDateTimeString());
        $this->assertEquals($newTime->copy()->addMinutes(90)->toDateTimeString(), $session->effective_end_at->toDateTimeString());

        // Reminders must be reset for future trigger
        $this->assertEquals([], $session->reminders_sent);
        $this->assertNull($session->reminder_sent_at);
    }

    public function test_rescheduling_session_updates_associated_assignments_due_date(): void
    {
        $teacher = User::factory()->create();
        $teacherProfile = TeacherProfile::create([
            'user_id' => $teacher->id,
            'slug' => 'teacher-' . $teacher->id,
        ]);
        $subject = $this->createSubject('Chemistry', 'chm101');

        $sessionStart = Carbon::now()->addDays(3)->setHour(10)->setMinute(0)->setSecond(0);
        $session = LiveSession::create([
            'title' => 'Organic Chemistry Intro',
            'teacher_profile_id' => $teacherProfile->id,
            'subject_id' => $subject->id,
            'scheduled_at' => $sessionStart,
            'duration_minutes' => 60,
            'status' => 'scheduled',
        ]);

        $assignment = Assignment::create([
            'title' => 'Pre-session Homework',
            'live_session_id' => $session->id,
            'due_at' => $sessionStart->copy()->subDay(),
            'status' => 'published',
        ]);

        $this->assertEquals($sessionStart->copy()->subDay()->toDateTimeString(), $assignment->due_at->toDateTimeString());

        // Move session 2 days further
        $rescheduledStart = Carbon::now()->addDays(5)->setHour(14)->setMinute(0)->setSecond(0);
        $session->update([
            'scheduled_at' => $rescheduledStart,
        ]);

        $assignment->refresh();
        $this->assertEquals($rescheduledStart->copy()->subDay()->toDateTimeString(), $assignment->due_at->toDateTimeString());
    }

    public function test_changing_session_student_synchronizes_student_sessions_and_visibility(): void
    {
        $student1 = User::factory()->create();
        StudentProfile::create(['user_id' => $student1->id]);

        $student2 = User::factory()->create();
        StudentProfile::create(['user_id' => $student2->id]);

        $teacher = User::factory()->create();
        $teacherProfile = TeacherProfile::create([
            'user_id' => $teacher->id,
            'slug' => 'teacher-' . $teacher->id,
        ]);
        $subject = $this->createSubject('Math', 'mth101');

        $session = LiveSession::create([
            'title' => 'Calculus Private Coaching',
            'teacher_profile_id' => $teacherProfile->id,
            'student_user_id' => $student1->id,
            'subject_id' => $subject->id,
            'scheduled_at' => Carbon::now()->addDay(),
            'duration_minutes' => 60,
            'status' => 'scheduled',
        ]);

        // StudentSession record created for student1
        $this->assertDatabaseHas('student_sessions', [
            'live_session_id' => $session->id,
            'student_user_id' => $student1->id,
        ]);
        $this->assertDatabaseMissing('student_sessions', [
            'live_session_id' => $session->id,
            'student_user_id' => $student2->id,
        ]);

        $this->assertEquals(1, LiveSession::visibleToStudent($student1->id)->count());
        $this->assertEquals(0, LiveSession::visibleToStudent($student2->id)->count());

        // Reassign session to student2
        $session->update([
            'student_user_id' => $student2->id,
        ]);

        $this->assertDatabaseMissing('student_sessions', [
            'live_session_id' => $session->id,
            'student_user_id' => $student1->id,
        ]);
        $this->assertDatabaseHas('student_sessions', [
            'live_session_id' => $session->id,
            'student_user_id' => $student2->id,
        ]);

        $this->assertEquals(0, LiveSession::visibleToStudent($student1->id)->count());
        $this->assertEquals(1, LiveSession::visibleToStudent($student2->id)->count());
    }

    public function test_cancelling_session_synchronizes_status_lifecycle_and_auto_refunds_package_deductions(): void
    {
        $student = User::factory()->create();
        StudentProfile::create(['user_id' => $student->id]);

        $teacher = User::factory()->create();
        $teacherProfile = TeacherProfile::create([
            'user_id' => $teacher->id,
            'slug' => 'teacher-' . $teacher->id,
        ]);
        $subject = $this->createSubject('Biology', 'bio101');

        $studentPackage = StudentPackage::create([
            'student_user_id' => $student->id,
            'total_sessions' => 10,
            'used_sessions' => 0,
            'remaining_sessions' => 10,
            'status' => 'active',
        ]);

        $session = LiveSession::create([
            'title' => 'Biology Session 1',
            'teacher_profile_id' => $teacherProfile->id,
            'student_user_id' => $student->id,
            'subject_id' => $subject->id,
            'scheduled_at' => Carbon::now()->addHour(),
            'duration_minutes' => 60,
            'status' => 'scheduled',
        ]);

        // Teacher starts session, deducting 1 credit
        $deductionService = app(SessionAttendanceDeductionService::class);
        $result = $deductionService->processTeacherSessionStart($session, $teacher);
        $this->assertEquals(1, $result['deducted_count']);

        $studentPackage->refresh();
        $this->assertEquals(9, $studentPackage->remaining_sessions);

        // Cancel the session
        $session->update([
            'status' => 'cancelled',
            'cancellation_reason' => 'Emergency cancellation',
        ]);

        $session->refresh();
        $this->assertEquals('cancelled', $session->status);
        $this->assertEquals('cancelled', $session->lifecycle_state);
        $this->assertNotNull($session->cancelled_at);

        // Student package credit must be automatically refunded
        $studentPackage->refresh();
        $this->assertEquals(10, $studentPackage->remaining_sessions);

        // Student session record status must be cancelled
        $this->assertDatabaseHas('student_sessions', [
            'live_session_id' => $session->id,
            'student_user_id' => $student->id,
            'session_status' => 'cancelled',
        ]);
    }

    public function test_updating_recurring_schedule_propagates_to_future_uncompleted_sessions(): void
    {
        $teacher1 = User::factory()->create();
        $teacherProfile1 = TeacherProfile::create([
            'user_id' => $teacher1->id,
            'slug' => 'teacher-1-' . $teacher1->id,
        ]);

        $teacher2 = User::factory()->create();
        $teacherProfile2 = TeacherProfile::create([
            'user_id' => $teacher2->id,
            'slug' => 'teacher-2-' . $teacher2->id,
        ]);

        $subject = $this->createSubject('History', 'his101');

        $course = Course::create([
            'subject_id' => $subject->id,
            'teacher_id' => $teacherProfile1->id,
            'title' => 'History 101',
            'slug' => 'history-101',
            'is_active' => true,
        ]);

        $schedule = RecurringSchedule::create([
            'title' => 'History Cohort A',
            'course_id' => $course->id,
            'teacher_profile_id' => $teacherProfile1->id,
            'start_time' => '10:00',
            'duration_minutes' => 60,
            'meeting_link' => 'https://meet.jit.si/cohort-a',
            'meeting_platform' => 'agora',
            'status' => 'active',
            'recurrence_type' => 'weekly',
            'start_date' => Carbon::now()->startOfDay(),
            'end_date' => Carbon::now()->addMonths(1),
        ]);

        $futureSession1 = LiveSession::create([
            'title' => 'History Cohort A (1)',
            'recurring_schedule_id' => $schedule->id,
            'teacher_profile_id' => $teacherProfile1->id,
            'subject_id' => $subject->id,
            'scheduled_at' => Carbon::now()->addDays(2),
            'duration_minutes' => 60,
            'meeting_link' => 'https://meet.jit.si/cohort-a',
            'meeting_platform' => 'agora',
            'status' => 'scheduled',
            'is_override' => false,
        ]);

        $futureSession2 = LiveSession::create([
            'title' => 'History Cohort A (2)',
            'recurring_schedule_id' => $schedule->id,
            'teacher_profile_id' => $teacherProfile1->id,
            'subject_id' => $subject->id,
            'scheduled_at' => Carbon::now()->addDays(5),
            'duration_minutes' => 60,
            'meeting_link' => 'https://meet.jit.si/cohort-a',
            'meeting_platform' => 'agora',
            'status' => 'scheduled',
            'is_override' => false,
        ]);

        // Update the recurring schedule with new teacher, link, and duration
        $schedule->update([
            'teacher_profile_id' => $teacherProfile2->id,
            'meeting_link' => 'https://meet.google.com/new-link',
            'duration_minutes' => 90,
        ]);

        $futureSession1->refresh();
        $futureSession2->refresh();

        $this->assertEquals($teacherProfile2->id, $futureSession1->teacher_profile_id);
        $this->assertEquals('https://meet.google.com/new-link', $futureSession1->meeting_link);
        $this->assertEquals(90, $futureSession1->duration_minutes);

        $this->assertEquals($teacherProfile2->id, $futureSession2->teacher_profile_id);
        $this->assertEquals('https://meet.google.com/new-link', $futureSession2->meeting_link);
        $this->assertEquals(90, $futureSession2->duration_minutes);
    }

    public function test_course_schedule_manager_reschedule_modal_updates_all_lifecycle_properties(): void
    {
        $admin = User::factory()->create();
        AdminProfile::create(['user_id' => $admin->id]);

        $teacher = User::factory()->create();
        $teacherProfile = TeacherProfile::create([
            'user_id' => $teacher->id,
            'slug' => 'teacher-' . $teacher->id,
        ]);
        $subject = $this->createSubject('Algebra', 'alg101');

        $originalTime = Carbon::now()->addDay()->setHour(10)->setMinute(0)->setSecond(0);
        $session = LiveSession::create([
            'title' => 'Algebra 101 Session',
            'teacher_profile_id' => $teacherProfile->id,
            'subject_id' => $subject->id,
            'scheduled_at' => $originalTime,
            'duration_minutes' => 60,
            'status' => 'scheduled',
            'reminders_sent' => ['24h'],
        ]);

        $newTime = Carbon::now()->addDays(4)->setHour(14)->setMinute(30)->setSecond(0);

        Livewire::actingAs($admin)
            ->test(\App\Filament\Pages\CourseScheduleManagerPage::class)
            ->set('targetSessionId', $session->id)
            ->set('rescheduleNewDate', $newTime->format('Y-m-d H:i:s'))
            ->set('rescheduleReason', 'Teacher request')
            ->call('confirmReschedule')
            ->assertHasNoErrors();

        $session->refresh();

        $this->assertEquals($newTime->toDateTimeString(), $session->scheduled_at->toDateTimeString());
        $this->assertEquals($newTime->toDateTimeString(), $session->start_at->toDateTimeString());
        $this->assertEquals($newTime->copy()->addMinutes(60)->toDateTimeString(), $session->end_at->toDateTimeString());
        $this->assertEquals('rescheduled', $session->status);
        $this->assertEquals('rescheduled', $session->lifecycle_state);
        $this->assertTrue((bool) $session->is_override);
        $this->assertEquals([], $session->reminders_sent);
        $this->assertNull($session->reminder_sent_at);
    }
}
