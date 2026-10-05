<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\GradeLevel;
use App\Models\LiveSession;
use App\Models\PackageTemplate;
use App\Models\StudentPackage;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentDashboardTodaySessionsAndCalendarTest extends TestCase
{
    use RefreshDatabase;

    private User $student;
    private User $teacher;
    private LiveSession $todaySession;
    private StudentPackage $package;

    protected function setUp(): void
    {
        parent::setUp();

        $grade = GradeLevel::create(['name' => 'الثانوية العامة', 'slug' => 'secondary']);
        $cat = Category::create(['name' => 'الرياضيات', 'slug' => 'math']);
        $subject = Subject::create(['category_id' => $cat->id, 'name' => 'الجبر والهندسة', 'slug' => 'algebra']);

        $this->teacher = User::create([
            'name' => 'د. سمير إبراهيم',
            'email' => 'samir@elite.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        $teacherProfile = TeacherProfile::create([
            'user_id' => $this->teacher->id,
            'slug' => 'samir-ibrahim',
        ]);

        $this->student = User::create([
            'name' => 'يوسف أحمد',
            'email' => 'youssef@elite.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        StudentProfile::create([
            'user_id' => $this->student->id,
            'grade_level_id' => $grade->id,
            'school_name' => 'Elite STEM',
        ]);

        $pkgTemplate = PackageTemplate::create([
            'name' => 'باقة التميز STEM',
            'sessions_count' => 12,
            'price' => 1200,
        ]);

        $this->package = StudentPackage::create([
            'student_user_id' => $this->student->id,
            'package_template_id' => $pkgTemplate->id,
            'remaining_sessions' => 10,
            'used_sessions' => 2,
            'total_sessions' => 12,
            'status' => 'active',
            'expires_at' => now()->addMonths(3),
        ]);

        // Today's live session
        $this->todaySession = LiveSession::create([
            'student_user_id' => $this->student->id,
            'teacher_profile_id' => $teacherProfile->id,
            'subject_id' => $subject->id,
            'title' => 'مراجعة المصفوفات والمحددات',
            'scheduled_at' => now()->addMinutes(30),
            'meeting_link' => 'https://meet.google.com/test-meeting-room',
        ]);
    }

    public function test_student_dashboard_displays_today_sessions_and_package_details(): void
    {
        $response = $this->actingAs($this->student)->get(route('student-portal'));

        $response->assertStatus(200);

        // Assert Student Welcome
        $response->assertSee('يوسف أحمد');

        // Assert Today's Session section & details
        $response->assertSee('مراجعة المصفوفات والمحددات');
        $response->assertSee('د. سمير إبراهيم');
        $response->assertSee('الجبر والهندسة');

        // Assert Package Details Card
        $response->assertSee('باقة التميز STEM');
        $response->assertSee('#PKG-' . $this->package->id);
        $response->assertSee('10');
        $response->assertSee('12');
        $response->assertSee('2');

        // Assert Calendar Section
        $response->assertSee('id="studentCalendarSection"', false);
        $response->assertSee('id="calMonthYearTitle"', false);
        $response->assertSee('id="calDaysGrid"', false);
        $response->assertSee('id="calSessionsContainer"', false);
        $response->assertSee('id="calPill_today"', false);
        $response->assertSee('id="calPill_upcoming"', false);
        $response->assertSee('id="calPill_history"', false);
        $response->assertSee('id="calPill_all"', false);
    }

    public function test_student_dashboard_english_locale_shows_package_and_sessions(): void
    {
        $response = $this->actingAs($this->student)
            ->withSession(['locale' => 'en'])
            ->get(route('student-portal'));

        $response->assertStatus(200);
        $response->assertSee('10 Sessions Remaining');
        $response->assertSee('studentCalendarSection');
    }
}
