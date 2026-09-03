<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\GradeLevel;
use App\Models\LiveSession;
use App\Models\RecurringSchedule;
use App\Models\SessionAuditLog;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use App\Services\Session\RecurringScheduleService;
use App\Services\Session\SessionReminderService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherRecurringSessionAndEducationProfileTest extends TestCase
{
    use RefreshDatabase;

    protected User $teacherUser;
    protected TeacherProfile $teacherProfile;
    protected User $studentUser;
    protected StudentProfile $studentProfile;
    protected Course $course;

    protected function setUp(): void
    {
        parent::setUp();

        $gradeLevel = GradeLevel::create(['name' => 'Secondary Stage', 'slug' => 'secondary', 'sort_order' => 1]);
        $category = \App\Models\Category::create(['name' => 'Sciences', 'slug' => 'sciences']);
        $subject = Subject::create(['name' => 'Physics', 'slug' => 'physics', 'category_id' => $category->id]);

        $this->teacherUser = User::create([
            'name' => 'Dr. Ahmed Mahmoud',
            'email' => 'ahmed.mahmoud@elite.edu',
            'password' => bcrypt('Password123!'),
            'role' => 'teacher',
            'status' => \App\Enums\AccountStatus::APPROVED,
        ]);

        $this->teacherProfile = TeacherProfile::create([
            'user_id' => $this->teacherUser->id,
            'slug' => 'dr-ahmed-mahmoud',
            'title' => 'Senior Physics Professor',
            'specialization' => 'Theoretical & Quantum Physics',
            'years_experience' => 15,
            'rating_avg' => 4.9,
            'students_count' => 120,
        ]);

        $this->studentUser = User::create([
            'name' => 'Kareem Tarek',
            'email' => 'kareem.tarek@student.elite.edu',
            'password' => bcrypt('Password123!'),
            'role' => 'student',
            'status' => \App\Enums\AccountStatus::APPROVED,
        ]);

        $this->studentProfile = StudentProfile::create([
            'user_id' => $this->studentUser->id,
            'grade_level_id' => $gradeLevel->id,
            'school_name' => 'Cairo International School',
        ]);

        $this->course = Course::create([
            'teacher_id' => $this->teacherProfile->id,
            'subject_id' => $subject->id,
            'grade_level_id' => $gradeLevel->id,
            'title' => 'Advanced Electromagnetism & Modern Physics',
            'slug' => 'advanced-electromagnetism',
            'is_active' => true,
        ]);

        \App\Models\CourseEnrollment::create([
            'student_user_id' => $this->studentUser->id,
            'course_id' => $this->course->id,
            'status' => 'active',
        ]);
    }

    public function test_teacher_can_preview_recurring_schedule_dates_without_saving(): void
    {
        $this->actingAs($this->teacherUser);

        $response = $this->postJson(route('ajax.teacher.recurring.preview'), [
            'course_id' => $this->course->id,
            'student_user_id' => $this->studentUser->id,
            'start_date' => Carbon::now()->startOfWeek()->format('Y-m-d'),
            'end_date' => Carbon::now()->startOfWeek()->addWeeks(4)->format('Y-m-d'),
            'start_time' => '10:00',
            'duration_minutes' => 60,
            'recurrence_type' => 'weekly',
            'days_of_week' => [6, 0], // Saturday and Sunday
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['success', 'total_sessions', 'has_conflicts', 'dates']);

        $this->assertGreaterThanOrEqual(8, $response->json('total_sessions'));
        $this->assertFalse($response->json('has_conflicts'));
    }

    public function test_teacher_can_create_recurring_schedule_and_generate_instances(): void
    {
        $this->actingAs($this->teacherUser);

        $startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
        $endDate = Carbon::now()->startOfWeek()->addMonths(2)->format('Y-m-d');

        $response = $this->postJson(route('ajax.teacher.recurring.create'), [
            'title' => 'Weekly Quantum Physics Masterclass',
            'course_id' => $this->course->id,
            'student_user_id' => $this->studentUser->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'start_time' => '14:00',
            'duration_minutes' => 90,
            'recurrence_type' => 'weekly',
            'days_of_week' => [6, 0], // Sat and Sun
            'meeting_link' => 'https://zoom.us/j/123456789',
        ]);

        $response->assertCreated()
            ->assertJsonPath('success', true);

        $schedule = RecurringSchedule::first();
        $this->assertNotNull($schedule);
        $this->assertEquals('Weekly Quantum Physics Masterclass', $schedule->title);
        $this->assertEquals(90, $schedule->duration_minutes);

        $generatedSessions = LiveSession::where('recurring_schedule_id', $schedule->id)->get();
        $this->assertGreaterThan(10, $generatedSessions->count());

        // Verify audit log
        $this->assertDatabaseHas('session_audit_logs', [
            'recurring_schedule_id' => $schedule->id,
            'action' => 'created',
        ]);
    }

    public function test_detects_conflict_when_creating_overlapping_session(): void
    {
        $this->actingAs($this->teacherUser);

        $scheduledAt = Carbon::now()->addDays(2)->setHour(10)->setMinute(0);

        LiveSession::create([
            'title' => 'Existing Morning Session',
            'teacher_profile_id' => $this->teacherProfile->id,
            'course_id' => $this->course->id,
            'scheduled_at' => $scheduledAt,
            'start_at' => $scheduledAt,
            'end_at' => $scheduledAt->copy()->addMinutes(60),
            'status' => 'scheduled',
        ]);

        $service = app(RecurringScheduleService::class);
        $conflicts = $service->detectConflicts(
            $this->teacherProfile->id,
            null,
            $scheduledAt->copy()->addMinutes(15),
            $scheduledAt->copy()->addMinutes(75)
        );

        $this->assertNotEmpty($conflicts);
        $this->assertEquals('teacher_overlap', $conflicts[0]['type']);
    }

    public function test_session_override_scope_this_only(): void
    {
        $this->actingAs($this->teacherUser);

        $schedule = RecurringSchedule::create([
            'teacher_profile_id' => $this->teacherProfile->id,
            'course_id' => $this->course->id,
            'title' => 'Standard Series',
            'recurrence_type' => 'weekly',
            'start_time' => '10:00:00',
            'duration_minutes' => 60,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonths(1),
        ]);

        $session1 = LiveSession::create([
            'title' => 'Series Session 1',
            'teacher_profile_id' => $this->teacherProfile->id,
            'course_id' => $this->course->id,
            'recurring_schedule_id' => $schedule->id,
            'scheduled_at' => Carbon::now()->addDays(2)->setHour(10),
            'start_at' => Carbon::now()->addDays(2)->setHour(10),
            'end_at' => Carbon::now()->addDays(2)->setHour(11),
            'duration_minutes' => 60,
            'is_override' => false,
        ]);

        $session2 = LiveSession::create([
            'title' => 'Series Session 2',
            'teacher_profile_id' => $this->teacherProfile->id,
            'course_id' => $this->course->id,
            'recurring_schedule_id' => $schedule->id,
            'scheduled_at' => Carbon::now()->addDays(9)->setHour(10),
            'start_at' => Carbon::now()->addDays(9)->setHour(10),
            'end_at' => Carbon::now()->addDays(9)->setHour(11),
            'duration_minutes' => 60,
            'is_override' => false,
        ]);

        $newTime = Carbon::now()->addDays(2)->setHour(14)->format('Y-m-d\TH:i');

        $response = $this->postJson(route('ajax.teacher.sessions.override', ['id' => $session1->id]), [
            'scope' => 'this_only',
            'title' => 'Rescheduled Exam Prep Override',
            'scheduled_at' => $newTime,
            'duration_minutes' => 90,
            'reason' => 'Student requested afternoon time',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true);

        $session1->refresh();
        $session2->refresh();

        $this->assertTrue($session1->is_override);
        $this->assertEquals('Rescheduled Exam Prep Override', $session1->title);
        $this->assertEquals(90, $session1->duration_minutes);

        // Session 2 remains untouched
        $this->assertFalse($session2->is_override);
        $this->assertEquals('Series Session 2', $session2->title);
        $this->assertEquals(60, $session2->duration_minutes);
    }

    public function test_session_reminder_pipeline_dispatches_idempotently(): void
    {
        $session24h = LiveSession::create([
            'title' => 'Tomorrow Afternoon Quantum Lab',
            'teacher_profile_id' => $this->teacherProfile->id,
            'student_user_id' => $this->studentUser->id,
            'course_id' => $this->course->id,
            'scheduled_at' => Carbon::now()->addHours(24),
            'start_at' => Carbon::now()->addHours(24),
            'duration_minutes' => 60,
            'status' => 'scheduled',
            'reminders_sent' => [],
        ]);

        $session15m = LiveSession::create([
            'title' => 'Starting Soon Interactive Lab',
            'teacher_profile_id' => $this->teacherProfile->id,
            'student_user_id' => $this->studentUser->id,
            'course_id' => $this->course->id,
            'scheduled_at' => Carbon::now()->addMinutes(15),
            'start_at' => Carbon::now()->addMinutes(15),
            'duration_minutes' => 60,
            'status' => 'scheduled',
            'reminders_sent' => [],
        ]);

        $service = app(SessionReminderService::class);
        $firstRunCount = $service->processDueReminders();

        $this->assertEquals(2, $firstRunCount);

        $session24h->refresh();
        $session15m->refresh();

        $this->assertContains('24h', $session24h->reminders_sent);
        $this->assertContains('15m', $session15m->reminders_sent);

        // Second run must be strictly idempotent (0 duplicate notifications)
        $secondRunCount = $service->processDueReminders();
        $this->assertEquals(0, $secondRunCount);
    }

    public function test_student_educational_note_rejects_phone_numbers(): void
    {
        $this->actingAs($this->teacherUser);

        $response = $this->postJson(route('ajax.teacher.students.notes.create', ['studentUserId' => $this->studentUser->id]), [
            'note' => 'Please contact me at 01099475854 for extra physics revision sheets.',
            'category' => 'academic',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false);

        $this->assertDatabaseMissing('student_educational_notes', [
            'student_user_id' => $this->studentUser->id,
        ]);
    }

    public function test_student_educational_note_saves_and_notifies_when_clean(): void
    {
        $this->actingAs($this->teacherUser);

        $response = $this->postJson(route('ajax.teacher.students.notes.create', ['studentUserId' => $this->studentUser->id]), [
            'note' => 'Excellent comprehension of Faraday\'s Law and magnetic induction during today\'s problem solving session.',
            'category' => 'academic',
        ]);

        $response->assertCreated()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('student_educational_notes', [
            'student_user_id' => $this->studentUser->id,
            'teacher_profile_id' => $this->teacherProfile->id,
        ]);
    }
}
