<?php

namespace App\Console\Commands;

use App\Enums\AccountStatus;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseSession;
use App\Models\LiveSession;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use App\Services\Notification\FcmNotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreateTestLiveSessionCommand extends Command
{
    protected $signature = 'demo:create-live-session {--minutes=28 : Minutes in the future to schedule live session start time} {--time= : Target start time string e.g. 13:15}';
    protected $description = 'Create a test course starting in N minutes or at a target time (e.g. 1:15 PM) for testing free trial enrollment and 30-min window logic';

    public function handle()
    {
        $timeOpt = $this->option('time');
        if ($timeOpt) {
            $scheduledAt = Carbon::today()->setTimeFromTimeString($timeOpt);
            $minutes = now()->diffInMinutes($scheduledAt, false);
        } else {
            $minutes = (int) $this->option('minutes');
            $scheduledAt = Carbon::now()->addMinutes($minutes);
        }

        $this->info("🚀 Creating Test Course with Live Session scheduled for {$scheduledAt->format('H:i:s Y-m-d')}...");

        // 1. Get or Create Category & Subject
        $category = Category::firstOrCreate(
            ['name' => 'Science / العلوم'],
            ['slug' => 'science-cat', 'description' => 'Science category']
        );

        $subject = Subject::firstOrCreate(
            ['name' => 'Physics / الفيزياء'],
            ['slug' => 'physics-test', 'description' => 'Test Physics Subject', 'category_id' => $category->id]
        );

        // 2. Get or Create Teacher User & Profile
        $teacherUser = User::firstOrCreate(
            ['email' => 'teacher.test@elite.edu'],
            [
                'name' => 'Dr. Ahmed Physics Teacher',
                'password' => bcrypt('password'),
                'role' => 'teacher',
                'status' => AccountStatus::APPROVED,
                'phone' => '+201011112222',
            ]
        );

        $teacherProfile = TeacherProfile::firstOrCreate(
            ['user_id' => $teacherUser->id],
            ['title' => 'Dr. Ahmed Physics', 'slug' => 'dr-ahmed-physics', 'bio' => 'Senior Physics Professor', 'subject_id' => $subject->id]
        );

        // 3. Create Demo Course with has_free_demo = true
        $slug = 'physics-115pm-' . Str::random(5);
        $course = Course::create([
            'subject_id' => $subject->id,
            'teacher_id' => $teacherProfile->id,
            'title' => 'Physics 101 — Live Session at 1:15 PM (' . $scheduledAt->format('H:i') . ')',
            'slug' => $slug,
            'description' => 'Test course scheduled to start at ' . $scheduledAt->format('1:15 PM') . '. Enrolling allows attending the 1st live session for FREE!',
            'has_free_demo' => true,
            'is_active' => true,
            'sessions_count' => 12,
            'session_duration_minutes' => 60,
        ]);

        // 4. Create Curriculum Session 1
        CourseSession::create([
            'course_id' => $course->id,
            'title' => 'Session 1: Live Interactive Free Trial at 1:15 PM',
            'description' => 'First free live trial session. Interactive attendance with teacher.',
            'sort_order' => 1,
            'duration_minutes' => 60,
            'is_free_demo' => true,
        ]);

        // 5. Create Student User & Profile without an active package
        $studentUser = User::firstOrCreate(
            ['email' => 'student.free.test@elite.edu'],
            [
                'name' => 'Trial Student Learner',
                'password' => bcrypt('password'),
                'role' => 'student',
                'status' => AccountStatus::APPROVED,
                'phone' => '+201099887766',
            ]
        );

        $studentProfile = StudentProfile::firstOrCreate(
            ['user_id' => $studentUser->id],
            ['school_name' => 'Elite Test School', 'has_used_free_session' => false]
        );

        // 6. Create Live Session scheduled at 1:15 PM
        $liveSession = LiveSession::create([
            'student_user_id' => $studentUser->id,
            'teacher_profile_id' => $teacherProfile->id,
            'subject_id' => $subject->id,
            'course_id' => $course->id,
            'scheduled_at' => $scheduledAt,
            'start_at' => $scheduledAt,
            'end_at' => $scheduledAt->copy()->addMinutes(60),
            'duration_minutes' => 60,
            'meeting_link' => 'https://meet.google.com/elite-test-115pm-session',
            'meeting_platform' => 'google_meet',
            'status' => 'link_visible',
            'is_free_demo' => true,
        ]);

        // 7. Dispatch Notification Reminders
        $notificationService = app(FcmNotificationService::class);
        $remindersCount = $notificationService->sendUpcomingSessionReminders();

        $courseUrl = route('course-details', ['slug' => $course->slug]);

        $this->info("==================================================");
        $this->info("✅ COURSE & LIVE SESSION CREATED FOR 1:15 PM");
        $this->info("==================================================");
        $this->info("📚 Course Title: {$course->title}");
        $this->info("🆔 Course ID: {$course->id}");
        $this->info("🌐 Course Public URL: {$courseUrl}");
        $this->info("⏰ Live Session Scheduled: {$scheduledAt->format('H:i:s Y-m-d')}");
        $this->info("🔔 Notifications Dispatched: {$remindersCount} notification(s) sent");
        $this->info("🔑 Login Credentials: student.free.test@elite.edu / password");

        // 8. Test Live Session Access Service Evaluation
        $accessResult = app(\App\Services\Session\LiveSessionService::class)->getStreamAccess($liveSession, $studentUser);

        $this->line("--------------------------------------------------");
        $this->info("🧪 Live Session Access State Evaluation:");
        $this->line(json_encode($accessResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return 0;
    }
}
