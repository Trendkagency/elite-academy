<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\GradeLevel;
use App\Models\LiveSession;
use App\Models\MeetingAttendance;
use App\Models\RecurringSchedule;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LiveTeachingScheduleAndFilamentControlTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $teacherUser;
    protected TeacherProfile $teacherProfile;
    protected User $teacherBUser;
    protected TeacherProfile $teacherProfileB;
    protected User $studentUser;
    protected Course $course;
    protected Subject $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $gradeLevel = GradeLevel::create(['name' => 'Secondary', 'slug' => 'sec', 'sort_order' => 1]);
        $category = Category::create(['name' => 'Sciences', 'slug' => 'sci']);
        $this->subject = Subject::create(['name' => 'Physics', 'slug' => 'phys', 'category_id' => $category->id]);

        $this->adminUser = User::create([
            'name' => 'Super Administrator',
            'email' => 'admin@elite.edu',
            'password' => bcrypt('AdminPass123!'),
            'role' => 'super_admin',
            'status' => \App\Enums\AccountStatus::APPROVED,
        ]);
        \App\Models\AdminProfile::create(['user_id' => $this->adminUser->id]);

        $this->teacherUser = User::create([
            'name' => 'Dr. Hazem Physics',
            'email' => 'hazem@elite.edu',
            'password' => bcrypt('Password123!'),
            'role' => 'teacher',
            'status' => \App\Enums\AccountStatus::APPROVED,
        ]);

        $this->teacherProfile = TeacherProfile::create([
            'user_id' => $this->teacherUser->id,
            'title' => 'Senior Physics Professor',
            'slug' => 'hazem-physics',
        ]);

        $this->teacherBUser = User::create([
            'name' => 'Dr. Sarah Chemistry',
            'email' => 'sarah@elite.edu',
            'password' => bcrypt('Password123!'),
            'role' => 'teacher',
            'status' => \App\Enums\AccountStatus::APPROVED,
        ]);

        $this->teacherProfileB = TeacherProfile::create([
            'user_id' => $this->teacherBUser->id,
            'title' => 'Chemistry Lead',
            'slug' => 'sarah-chem',
        ]);

        $this->studentUser = User::create([
            'name' => 'Amr Student',
            'email' => 'amr@student.elite.edu',
            'password' => bcrypt('Password123!'),
            'role' => 'student',
            'status' => \App\Enums\AccountStatus::APPROVED,
        ]);

        $this->course = Course::create([
            'teacher_id' => $this->teacherProfile->id,
            'subject_id' => $this->subject->id,
            'grade_level_id' => $gradeLevel->id,
            'title' => 'Complete Quantum Mechanics 2026',
            'slug' => 'quantum-mechanics-2026',
            'is_active' => true,
        ]);
    }

    /**
     * 1. Teacher Portal: Preview recurring schedule dates & validate conflicts
     */
    public function test_teacher_can_preview_recurring_schedule_with_no_conflicts(): void
    {
        $this->actingAs($this->teacherUser);

        $response = $this->postJson(route('ajax.teacher.recurring.preview'), [
            'course_id' => $this->course->id,
            'student_user_id' => $this->studentUser->id,
            'start_date' => Carbon::now()->startOfWeek()->format('Y-m-d'),
            'end_date' => Carbon::now()->startOfWeek()->addWeeks(3)->format('Y-m-d'),
            'start_time' => '11:00',
            'duration_minutes' => 60,
            'recurrence_type' => 'weekly',
            'days_of_week' => [6, 0], // Sat & Sun
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('has_conflicts', false);

        $this->assertGreaterThanOrEqual(6, $response->json('total_sessions'));
    }

    /**
     * 2. Teacher Portal: Create recurring schedule and batch generate live sessions
     */
    public function test_teacher_can_create_recurring_schedule_and_generate_sessions(): void
    {
        $this->actingAs($this->teacherUser);

        $response = $this->postJson(route('ajax.teacher.recurring.create'), [
            'title' => 'Weekly Elite Physics Cohort',
            'course_id' => $this->course->id,
            'student_user_id' => $this->studentUser->id,
            'start_date' => Carbon::now()->startOfWeek()->format('Y-m-d'),
            'end_date' => Carbon::now()->startOfWeek()->addWeeks(4)->format('Y-m-d'),
            'start_time' => '10:00',
            'duration_minutes' => 60,
            'recurrence_type' => 'weekly',
            'days_of_week' => [6, 0],
            'meeting_platform' => 'agora',
            'meeting_link' => 'https://zoom.us/j/9988776655',
        ]);

        $response->assertCreated()
            ->assertJsonPath('success', true);

        $schedule = RecurringSchedule::where('course_id', $this->course->id)->first();
        $this->assertNotNull($schedule);
        $this->assertEquals('Weekly Elite Physics Cohort', $schedule->title);
        $this->assertEquals('agora', $schedule->meeting_platform);

        $sessions = LiveSession::where('recurring_schedule_id', $schedule->id)->get();
        $this->assertGreaterThanOrEqual(8, $sessions->count());

        foreach ($sessions as $session) {
            $this->assertEquals($this->teacherProfile->id, $session->teacher_profile_id);
            $this->assertEquals('agora', $session->meeting_platform);
            $this->assertEquals('scheduled', $session->status);
        }
    }

    /**
     * 3. Teacher Portal: Schedule a single standalone session
     */
    public function test_teacher_can_schedule_single_live_session(): void
    {
        $this->actingAs($this->teacherUser);

        $scheduledAt = Carbon::now()->addDays(2)->setTime(14, 0)->format('Y-m-d\TH:i');

        $response = $this->postJson(route('ajax.teacher.sessions.create'), [
            'title' => 'Thermodynamics Problem Solving Live',
            'course_id' => $this->course->id,
            'scheduled_at' => $scheduledAt,
            'duration_minutes' => 90,
            'meeting_platform' => 'zoom',
            'meeting_link' => 'https://zoom.us/j/1122334455',
            'is_free_demo' => false,
        ]);

        $response->assertCreated()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('live_sessions', [
            'title' => 'Thermodynamics Problem Solving Live',
            'teacher_profile_id' => $this->teacherProfile->id,
            'meeting_platform' => 'zoom',
            'duration_minutes' => 90,
        ]);
    }

    /**
     * 4. Teacher Portal: Update meeting broadcast link
     */
    public function test_teacher_can_update_meeting_link(): void
    {
        $this->actingAs($this->teacherUser);

        $session = LiveSession::create([
            'title' => 'Live Lab Experiment',
            'teacher_profile_id' => $this->teacherProfile->id,
            'course_id' => $this->course->id,
            'subject_id' => $this->subject->id,
            'scheduled_at' => Carbon::now()->addHours(2),
            'start_at' => Carbon::now()->addHours(2),
            'end_at' => Carbon::now()->addHours(3),
            'duration_minutes' => 60,
            'status' => 'scheduled',
            'meeting_platform' => 'agora',
        ]);

        $newLink = 'https://meet.google.com/xyz-abcd-efg';

        $response = $this->postJson(route('ajax.teacher.sessions.link', ['id' => $session->id]), [
            'meeting_link' => $newLink,
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true);

        $session->refresh();
        $this->assertEquals($newLink, $session->meeting_link);
        $this->assertEquals('link_visible', $session->status);
    }

    /**
     * 5. Teacher Portal: Override single session from recurring schedule
     */
    public function test_teacher_can_override_single_recurring_session(): void
    {
        $this->actingAs($this->teacherUser);

        $schedule = RecurringSchedule::create([
            'teacher_profile_id' => $this->teacherProfile->id,
            'course_id' => $this->course->id,
            'title' => 'Saturday Physics Series',
            'recurrence_type' => 'weekly',
            'days_of_week' => [6],
            'start_time' => '10:00:00',
            'start_date' => Carbon::now()->toDateString(),
            'end_date' => Carbon::now()->addWeeks(4)->toDateString(),
            'duration_minutes' => 60,
            'status' => 'active',
            'meeting_platform' => 'agora',
        ]);

        $session = LiveSession::create([
            'title' => 'Saturday Physics Series (1)',
            'teacher_profile_id' => $this->teacherProfile->id,
            'course_id' => $this->course->id,
            'subject_id' => $this->subject->id,
            'recurring_schedule_id' => $schedule->id,
            'scheduled_at' => Carbon::now()->addDay()->setTime(10, 0),
            'start_at' => Carbon::now()->addDay()->setTime(10, 0),
            'end_at' => Carbon::now()->addDay()->setTime(11, 0),
            'duration_minutes' => 60,
            'status' => 'scheduled',
            'meeting_platform' => 'agora',
            'is_override' => false,
        ]);

        $newScheduledAt = Carbon::now()->addDay()->setTime(16, 30)->format('Y-m-d\TH:i');

        $response = $this->postJson(route('ajax.teacher.sessions.override', ['id' => $session->id]), [
            'scope' => 'this_only',
            'title' => 'Special Review: Modern Physics Q&A',
            'scheduled_at' => $newScheduledAt,
            'duration_minutes' => 75,
            'reason' => 'Extended review by student request',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true);

        $session->refresh();
        $this->assertTrue((bool) $session->is_override);
        $this->assertEquals('Special Review: Modern Physics Q&A', $session->title);
        $this->assertEquals(75, $session->duration_minutes);
        $this->assertEquals('Extended review by student request', $session->override_reason);
    }

    /**
     * 6. Teacher Portal: Reschedule session
     */
    public function test_teacher_can_reschedule_session(): void
    {
        $this->actingAs($this->teacherUser);

        $session = LiveSession::create([
            'title' => 'Acoustics Class',
            'teacher_profile_id' => $this->teacherProfile->id,
            'course_id' => $this->course->id,
            'subject_id' => $this->subject->id,
            'scheduled_at' => Carbon::now()->addDay(),
            'duration_minutes' => 60,
            'status' => 'scheduled',
            'meeting_platform' => 'agora',
        ]);

        $rescheduledDate = Carbon::now()->addDays(3)->setTime(18, 0)->format('Y-m-d\TH:i');

        $response = $this->postJson(route('ajax.teacher.sessions.reschedule', ['id' => $session->id]), [
            'scheduled_at' => $rescheduledDate,
            'reason' => 'Rescheduled due to national holiday',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true);

        $session->refresh();
        $this->assertEquals('rescheduled', $session->lifecycle_state);
        $this->assertTrue((bool) $session->is_override);
    }

    /**
     * 7. Teacher Portal: Cancel session
     */
    public function test_teacher_can_cancel_session(): void
    {
        $this->actingAs($this->teacherUser);

        $session = LiveSession::create([
            'title' => 'Nuclear Physics Seminar',
            'teacher_profile_id' => $this->teacherProfile->id,
            'course_id' => $this->course->id,
            'subject_id' => $this->subject->id,
            'scheduled_at' => Carbon::now()->addDays(2),
            'duration_minutes' => 60,
            'status' => 'scheduled',
            'meeting_platform' => 'agora',
        ]);

        $response = $this->postJson(route('ajax.teacher.sessions.cancel', ['id' => $session->id]), [
            'reason' => 'Emergency lab maintenance',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true);

        $session->refresh();
        $this->assertEquals('cancelled_by_teacher', $session->status);
    }

    /**
     * 8. Teacher Portal: Mark attendance
     */
    public function test_teacher_can_mark_attendance_for_session(): void
    {
        $this->actingAs($this->teacherUser);

        $session = LiveSession::create([
            'title' => 'Optics Interactive Live',
            'teacher_profile_id' => $this->teacherProfile->id,
            'course_id' => $this->course->id,
            'subject_id' => $this->subject->id,
            'scheduled_at' => Carbon::now()->subHour(),
            'duration_minutes' => 60,
            'status' => 'completed',
            'meeting_platform' => 'agora',
        ]);

        $response = $this->postJson(route('ajax.teacher.attendance.mark', ['sessionId' => $session->id]), [
            'attendance' => [
                [
                    'student_user_id' => $this->studentUser->id,
                    'status' => 'present',
                ],
            ],
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('student_sessions', [
            'live_session_id' => $session->id,
            'student_user_id' => $this->studentUser->id,
            'attendance_status' => 'present',
        ]);
    }

    /**
     * 9. Security / IDOR: Teacher A cannot modify Teacher B's session
     */
    public function test_teacher_cannot_modify_another_teachers_session(): void
    {
        $this->actingAs($this->teacherBUser);

        $sessionA = LiveSession::create([
            'title' => 'Dr Hazem Secret Class',
            'teacher_profile_id' => $this->teacherProfile->id,
            'course_id' => $this->course->id,
            'subject_id' => $this->subject->id,
            'scheduled_at' => Carbon::now()->addDay(),
            'duration_minutes' => 60,
            'status' => 'scheduled',
            'meeting_platform' => 'agora',
        ]);

        $response = $this->postJson(route('ajax.teacher.sessions.link', ['id' => $sessionA->id]), [
            'meeting_link' => 'https://hacked.com',
        ]);

        $response->assertForbidden();
    }

    /**
     * 10. Admin Filament Control: LiveSession and RecurringSchedule resources exist and can be queried
     */
    public function test_admin_can_access_filament_live_session_and_recurring_schedule_resources(): void
    {
        $this->actingAs($this->adminUser);

        // Check LiveSessionResource model mapping
        $this->assertEquals(LiveSession::class, \App\Filament\Resources\LiveSessions\LiveSessionResource::getModel());
        $this->assertEquals(RecurringSchedule::class, \App\Filament\Resources\RecurringSchedules\RecurringScheduleResource::getModel());

        // Verify navigation labels
        $this->assertNotEmpty(\App\Filament\Resources\LiveSessions\LiveSessionResource::getNavigationLabel());
        $this->assertNotEmpty(\App\Filament\Resources\RecurringSchedules\RecurringScheduleResource::getNavigationLabel());

        // Create a live session and ensure Filament table query fetches it
        $session = LiveSession::create([
            'title' => 'Filament Controlled Session',
            'teacher_profile_id' => $this->teacherProfile->id,
            'course_id' => $this->course->id,
            'subject_id' => $this->subject->id,
            'scheduled_at' => Carbon::now()->addDay(),
            'duration_minutes' => 60,
            'status' => 'scheduled',
            'meeting_platform' => 'agora',
        ]);

        $this->assertDatabaseHas('live_sessions', ['id' => $session->id, 'title' => 'Filament Controlled Session']);

        // Test Livewire/Filament table rendering for live sessions
        $responseLive = $this->get(route('filament.admin.resources.live-sessions.index'));
        $responseLive->assertOk();

        // Create recurring schedule and test rendering for recurring schedules index
        RecurringSchedule::create([
            'teacher_profile_id' => $this->teacherProfile->id,
            'course_id' => $this->course->id,
            'title' => 'Filament Weekly Cohort',
            'recurrence_type' => 'weekly',
            'days_of_week' => [6, 0],
            'start_time' => '10:00:00',
            'start_date' => Carbon::now()->toDateString(),
            'end_date' => Carbon::now()->addMonths(2)->toDateString(),
            'duration_minutes' => 60,
            'status' => 'active',
            'meeting_platform' => 'agora',
        ]);

        $responseRecurring = $this->get(route('filament.admin.resources.recurring-schedules.index'));
        $responseRecurring->assertOk();

        // Test create form rendering (verifies student_user_id query without unknown column 'role')
        $responseLiveCreate = $this->get(route('filament.admin.resources.live-sessions.create'));
        $responseLiveCreate->assertOk();

        $responseRecurringCreate = $this->get(route('filament.admin.resources.recurring-schedules.create'));
        $responseRecurringCreate->assertOk();
    }
}
