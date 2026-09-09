<?php

namespace Tests\Feature;

use App\Filament\Pages\StudentReportsPage;
use App\Filament\Pages\TeacherReportsPage;
use App\Models\AdminProfile;
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
use Livewire\Livewire;
use Tests\TestCase;

class FilamentReportingPagesTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $studentUser;
    protected User $teacherUser;
    protected TeacherProfile $teacherProfile;
    protected StudentProfile $studentProfile;
    protected Course $course;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Admin
        $this->adminUser = User::factory()->create([
            'email' => 'admin@elite-academy.com',
            'status' => 'approved',
        ]);
        AdminProfile::create(['user_id' => $this->adminUser->id]);

        // 2. Category, Grade & Subject
        $category = \App\Models\Category::create(['name' => 'Sciences', 'slug' => 'sciences']);
        $grade = GradeLevel::create(['name' => 'Grade 12', 'slug' => 'grade-12', 'sort_order' => 1]);
        $subject = Subject::create(['category_id' => $category->id, 'name' => 'Physics', 'slug' => 'physics']);

        // 3. Teacher
        $this->teacherUser = User::factory()->create([
            'name' => 'Dr. Ahmed',
            'email' => 'teacher@elite.edu',
            'status' => 'approved',
        ]);
        $this->teacherProfile = TeacherProfile::create([
            'user_id' => $this->teacherUser->id,
            'slug' => 'dr-ahmed',
            'title' => 'Senior Physics Lecturer',
            'specialization' => 'Physics',
            'years_experience' => 10,
            'rating_avg' => 4.9,
            'reviews_count' => 25,
        ]);

        // 4. Student
        $this->studentUser = User::factory()->create([
            'name' => 'Mohamed Ali',
            'email' => 'student@elite.edu',
            'status' => 'approved',
        ]);
        $this->studentProfile = StudentProfile::create([
            'user_id' => $this->studentUser->id,
            'grade_level_id' => $grade->id,
            'school_name' => 'Cairo STEM High School',
        ]);

        // 5. Course & Enrollment
        $this->course = Course::create([
            'title' => 'Advanced Physics',
            'slug' => 'advanced-physics',
            'subject_id' => $subject->id,
            'teacher_id' => $this->teacherProfile->id,
            'grade_level_id' => $grade->id,
            'sessions_count' => 8,
            'is_active' => true,
        ]);

        CourseEnrollment::create([
            'student_user_id' => $this->studentUser->id,
            'course_id' => $this->course->id,
            'status' => 'active',
            'progress_percent' => 50,
            'enrolled_at' => now(),
        ]);

        // 6. Package
        $pkgTpl = PackageTemplate::create([
            'name' => 'Standard Package (8 Sessions)',
            'sessions_count' => 8,
            'price' => 500,
            'is_active' => true,
        ]);

        StudentPackage::create([
            'student_user_id' => $this->studentUser->id,
            'package_template_id' => $pkgTpl->id,
            'total_sessions' => 8,
            'used_sessions' => 4,
            'remaining_sessions' => 4,
            'status' => 'active',
            'activated_at' => now(),
        ]);

        // 7. Live Session
        LiveSession::create([
            'title' => 'Mechanics Live Session',
            'student_user_id' => $this->studentUser->id,
            'teacher_profile_id' => $this->teacherProfile->id,
            'course_id' => $this->course->id,
            'subject_id' => $subject->id,
            'scheduled_at' => now(),
            'duration_minutes' => 60,
            'status' => 'completed',
            'attendance_status' => 'present',
        ]);
    }

    public function test_student_reports_page_renders_successfully(): void
    {
        $this->actingAs($this->adminUser);

        Livewire::test(StudentReportsPage::class)
            ->assertSuccessful()
            ->assertSee('Mohamed Ali')
            ->assertSee('Cairo STEM High School')
            ->assertSee('Enrolled Courses');
    }

    public function test_student_reports_page_switches_tabs_and_calculates_metrics(): void
    {
        $this->actingAs($this->adminUser);

        Livewire::test(StudentReportsPage::class)
            ->call('selectStudent', $this->studentUser->id)
            ->call('setTab', 'packages')
            ->assertSuccessful()
            ->assertSee('Standard Package (8 Sessions)')
            ->call('setTab', 'sessions')
            ->assertSuccessful()
            ->assertSee('Mechanics Live Session');
    }

    public function test_teacher_reports_page_renders_successfully(): void
    {
        $this->actingAs($this->adminUser);

        Livewire::test(TeacherReportsPage::class)
            ->assertSuccessful()
            ->assertSee('Dr. Ahmed')
            ->assertSee('Senior Physics Lecturer')
            ->assertSee('Advanced Physics');
    }

    public function test_teacher_reports_page_switches_tabs_and_calculates_metrics(): void
    {
        $this->actingAs($this->adminUser);

        Livewire::test(TeacherReportsPage::class)
            ->call('selectTeacher', $this->teacherProfile->id)
            ->call('setTab', 'students')
            ->assertSuccessful()
            ->assertSee('Mohamed Ali')
            ->call('setTab', 'sessions')
            ->assertSuccessful()
            ->assertSee('Mechanics Live Session');
    }

    public function test_reporting_pages_render_in_arabic_locale(): void
    {
        app()->setLocale('ar');
        $this->actingAs($this->adminUser);

        Livewire::test(StudentReportsPage::class)
            ->assertSuccessful()
            ->assertSee(__('Enrolled Courses & Academic Progress'));

        Livewire::test(TeacherReportsPage::class)
            ->assertSuccessful()
            ->assertSee(__('Assigned Courses & Curriculum Delivery'));
    }
}
