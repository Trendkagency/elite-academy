<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\LiveSession;
use App\Models\TeacherProfile;
use App\Models\User;
use App\Models\UserNotification;
use App\Services\Session\SessionReminderService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EscalatingSessionRemindersTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $teacherUser;
    protected TeacherProfile $teacherProfile;
    protected User $studentUser;
    protected Course $course;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'email' => 'admin@elite-academy.com',
        ]);
        \App\Models\AdminProfile::create([
            'user_id' => $this->adminUser->id,
        ]);

        $this->teacherUser = User::factory()->create([
            'email' => 'teacher_reminder@elite.test',
        ]);
        $this->teacherProfile = TeacherProfile::create([
            'user_id' => $this->teacherUser->id,
            'bio' => 'Physics Teacher',
            'slug' => 'teacher-reminder-' . uniqid(),
        ]);

        $this->studentUser = User::factory()->create([
            'email' => 'student_reminder@elite.test',
        ]);

        $category = \App\Models\Category::create(['name' => 'Sciences', 'slug' => 'sciences-' . uniqid(), 'sort_order' => 1]);
        $subject = \App\Models\Subject::create(['category_id' => $category->id, 'name' => 'Physics', 'slug' => 'physics-' . uniqid(), 'is_active' => true]);

        $this->course = Course::create([
            'category_id' => $category->id,
            'subject_id' => $subject->id,
            'teacher_id' => $this->teacherProfile->id,
            'title' => 'Advanced Mechanics',
            'slug' => 'adv-mechanics-' . uniqid(),
            'is_active' => true,
        ]);

        CourseEnrollment::create([
            'course_id' => $this->course->id,
            'student_user_id' => $this->studentUser->id,
            'status' => 'active',
        ]);
    }

    public function test_escalating_reminders_trigger_multi_tier_schedule_for_student_teacher_and_admin(): void
    {
        // 1. Session tomorrow (>24h): 36 hours away
        $session36h = LiveSession::create([
            'title' => 'Mechanics Lab 36h',
            'teacher_profile_id' => $this->teacherProfile->id,
            'student_user_id' => $this->studentUser->id,
            'course_id' => $this->course->id,
            'scheduled_at' => Carbon::now()->addHours(36),
            'start_at' => Carbon::now()->addHours(36),
            'duration_minutes' => 60,
            'status' => 'scheduled',
            'reminders_sent' => [],
        ]);

        // 2. Session on same day: 5 hours away
        $session5h = LiveSession::create([
            'title' => 'Mechanics Lab 5h',
            'teacher_profile_id' => $this->teacherProfile->id,
            'student_user_id' => $this->studentUser->id,
            'course_id' => $this->course->id,
            'scheduled_at' => Carbon::now()->addHours(5),
            'start_at' => Carbon::now()->addHours(5),
            'duration_minutes' => 60,
            'status' => 'scheduled',
            'reminders_sent' => [],
        ]);

        // 3. Approaching session: 45 minutes away
        $session45m = LiveSession::create([
            'title' => 'Mechanics Lab 45m',
            'teacher_profile_id' => $this->teacherProfile->id,
            'student_user_id' => $this->studentUser->id,
            'course_id' => $this->course->id,
            'scheduled_at' => Carbon::now()->addMinutes(45),
            'start_at' => Carbon::now()->addMinutes(45),
            'duration_minutes' => 60,
            'status' => 'scheduled',
            'reminders_sent' => [],
        ]);

        // 4. Final countdown: 5 minutes away
        $session5m = LiveSession::create([
            'title' => 'Mechanics Lab 5m',
            'teacher_profile_id' => $this->teacherProfile->id,
            'student_user_id' => $this->studentUser->id,
            'course_id' => $this->course->id,
            'scheduled_at' => Carbon::now()->addMinutes(5),
            'start_at' => Carbon::now()->addMinutes(5),
            'duration_minutes' => 60,
            'status' => 'scheduled',
            'reminders_sent' => [],
        ]);

        $service = app(SessionReminderService::class);
        $count = $service->processDueReminders();

        $this->assertEquals(4, $count);

        $session36h->refresh();
        $session5h->refresh();
        $session45m->refresh();
        $session5m->refresh();

        $this->assertContains('36h', $session36h->reminders_sent);
        $this->assertContains('5h', $session5h->reminders_sent);
        $this->assertContains('45m', $session45m->reminders_sent);
        $this->assertContains('5m', $session5m->reminders_sent);

        // Verify Student received notifications
        $studentNotifs = UserNotification::where('user_id', $this->studentUser->id)->get();
        $this->assertGreaterThanOrEqual(4, $studentNotifs->count());

        // Verify Teacher received notifications
        $teacherNotifs = UserNotification::where('user_id', $this->teacherUser->id)->get();
        $this->assertGreaterThanOrEqual(4, $teacherNotifs->count());

        // Verify Admin received notifications
        $adminNotifs = UserNotification::where('user_id', $this->adminUser->id)->get();
        $this->assertGreaterThanOrEqual(4, $adminNotifs->count());

        // Idempotency: rerunning must dispatch 0
        $secondCount = $service->processDueReminders();
        $this->assertEquals(0, $secondCount);
    }

    public function test_session_started_updates_lifecycle_and_notifies(): void
    {
        $sessionLive = LiveSession::create([
            'title' => 'Live Quantum Stream',
            'teacher_profile_id' => $this->teacherProfile->id,
            'student_user_id' => $this->studentUser->id,
            'course_id' => $this->course->id,
            'scheduled_at' => Carbon::now(),
            'start_at' => Carbon::now(),
            'duration_minutes' => 60,
            'status' => 'scheduled',
            'reminders_sent' => [],
        ]);

        $service = app(SessionReminderService::class);
        $count = $service->processDueReminders();

        $this->assertEquals(1, $count);

        $sessionLive->refresh();
        $this->assertContains('started', $sessionLive->reminders_sent);
        $this->assertEquals('ready', $sessionLive->lifecycle_state);
        $this->assertEquals('link_visible', $sessionLive->status);
    }

    public function test_artisan_commands_run_smoothly(): void
    {
        $exitCode1 = $this->artisan('notifications:upcoming-sessions');
        $exitCode1->assertExitCode(0);

        $exitCode2 = $this->artisan('sessions:send-reminders');
        $exitCode2->assertExitCode(0);
    }
}
