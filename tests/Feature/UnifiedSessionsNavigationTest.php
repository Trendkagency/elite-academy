<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Filament\Resources\CourseSessions\CourseSessionResource;
use App\Filament\Resources\LiveSessions\LiveSessionResource;
use App\Filament\Resources\LiveSessions\Pages\ListLiveSessions;
use App\Models\AdminProfile;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseSession;
use App\Models\GradeLevel;
use App\Models\LiveSession;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UnifiedSessionsNavigationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Course $course;
    protected TeacherProfile $teacher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin.sessions@elite.edu',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => AccountStatus::APPROVED,
        ]);
        AdminProfile::create(['user_id' => $this->admin->id]);

        $gradeLevel = GradeLevel::create([
            'name' => 'Secondary 1',
            'slug' => 'sec-1',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $category = Category::create(['name' => 'Sciences', 'slug' => 'sciences']);
        $subject = Subject::create(['category_id' => $category->id, 'name' => 'Biology', 'slug' => 'biology']);

        $teacherUser = User::create([
            'name' => 'Biology Teacher',
            'email' => 'bio.teacher@elite.edu',
            'password' => bcrypt('password'),
            'role' => 'teacher',
            'status' => AccountStatus::APPROVED,
        ]);
        $this->teacher = TeacherProfile::create([
            'user_id' => $teacherUser->id,
            'slug' => 'bio-teacher',
        ]);

        $this->course = Course::create([
            'title' => 'Biology 101',
            'slug' => 'bio-101',
            'grade_level_id' => $gradeLevel->id,
            'subject_id' => $subject->id,
            'teacher_id' => $this->teacher->id,
            'is_published' => true,
        ]);
    }

    public function test_only_one_session_resource_registers_in_sidebar_navigation(): void
    {
        $this->assertTrue(LiveSessionResource::shouldRegisterNavigation(), 'LiveSessionResource should be visible in navigation.');
        $this->assertFalse(CourseSessionResource::shouldRegisterNavigation(), 'CourseSessionResource should be hidden from sidebar to avoid duplication.');
    }

    public function test_live_session_resource_has_unified_arabic_and_english_labels(): void
    {
        app()->setLocale('ar');
        $this->assertEquals('الحصص والبث المباشر', LiveSessionResource::getNavigationLabel());
        $this->assertEquals('الحصص والبث المباشر', LiveSessionResource::getPluralModelLabel());

        app()->setLocale('en');
        $this->assertEquals('Live Sessions & Classes', LiveSessionResource::getNavigationLabel());
        $this->assertEquals('Live Sessions & Classes', LiveSessionResource::getPluralModelLabel());
    }

    public function test_visiting_course_sessions_redirects_to_live_sessions(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get('/admin/course-sessions?activeTab=free_demo');
        $response->assertRedirect('/admin/live-sessions?activeTab=free_demo');
    }

    public function test_live_sessions_list_page_renders_with_all_tabs(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(ListLiveSessions::class)
            ->assertSuccessful()
            ->assertSee('All Sessions')
            ->assertSee('Today')
            ->assertSee('Free Demo');
    }

    public function test_creating_live_session_for_course_auto_syncs_course_session(): void
    {
        $session = LiveSession::create([
            'title' => 'Cell Structure & Organelles',
            'course_id' => $this->course->id,
            'subject_id' => $this->course->subject_id,
            'teacher_profile_id' => $this->teacher->id,
            'scheduled_at' => now()->addDays(2),
            'duration_minutes' => 60,
            'meeting_platform' => 'agora',
            'meeting_link' => 'https://meet.elite-academy.test/room-bio',
            'status' => 'scheduled',
            'is_free_demo' => false,
        ]);

        $this->assertNotNull($session->course_session_id, 'course_session_id should be automatically populated.');

        $courseSession = CourseSession::find($session->course_session_id);
        $this->assertNotNull($courseSession);
        $this->assertEquals('Cell Structure & Organelles', $courseSession->title);
        $this->assertEquals($this->course->id, $courseSession->course_id);

        // Soft deleting live session cascades to course session
        $session->delete();
        $this->assertSoftDeleted('course_sessions', ['id' => $courseSession->id]);

        // Restoring live session restores course session
        $session->restore();
        $this->assertDatabaseHas('course_sessions', ['id' => $courseSession->id, 'deleted_at' => null]);
    }
}
