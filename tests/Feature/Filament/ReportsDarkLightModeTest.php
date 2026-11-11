<?php

namespace Tests\Feature\Filament;

use App\Enums\AccountStatus;
use App\Models\AdminProfile;
use App\Models\Category;
use App\Models\Course;
use App\Models\ParentProfile;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ReportsDarkLightModeTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::create([
            'name' => 'Reports Admin',
            'email' => 'admin.reports@elite.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        AdminProfile::create(['user_id' => $this->adminUser->id]);
    }

    public function test_teacher_reports_page_renders_dual_theme_course_cards_without_broken_inline_css(): void
    {
        $category = Category::create(['name' => 'Science', 'slug' => 'science']);
        $subject = Subject::create(['name' => 'Physics', 'slug' => 'physics', 'category_id' => $category->id]);

        $teacherUser = User::create([
            'name' => 'Dr. Ahmed Mahmoud',
            'email' => 'dr.ahmed@elite.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        $teacher = TeacherProfile::create([
            'user_id' => $teacherUser->id,
            'slug' => 'dr-ahmed',
            'title' => 'Senior Physics Professor',
            'specialization' => 'Physics',
        ]);

        Course::create([
            'title' => 'Electromagnetism & Modern Physics',
            'slug' => 'electro-modern-physics',
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->adminUser)->get('/admin/teacher-reports-page');

        $response->assertStatus(200)
            ->assertSee('Electromagnetism & Modern Physics')
            ->assertSee('--rpt-bg-surface')
            ->assertSee('html.dark .rpt-wrapper')
            ->assertSee('rpt-course-card')
            ->assertSee('rpt-course-metric-box')
            ->assertDontSee('dark:color:white')
            ->assertDontSee('dark:bg-slate-950/60');
    }

    public function test_student_reports_page_renders_with_dual_theme_support(): void
    {
        $studentUser = User::create([
            'name' => 'Ahmed Khaled',
            'email' => 'ahmed.student@elite.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        StudentProfile::create(['user_id' => $studentUser->id]);

        $response = $this->actingAs($this->adminUser)->get('/admin/student-reports-page');

        $response->assertStatus(200)
            ->assertSee('Ahmed Khaled')
            ->assertSee('html.dark .rpt-item-card')
            ->assertSee('rpt-pill-neutral');
    }

    public function test_children_details_component_renders_with_dual_theme_variables(): void
    {
        $parentUser = User::create([
            'name' => 'Parent User',
            'email' => 'parent.ui@elite.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        $parent = ParentProfile::create(['user_id' => $parentUser->id]);

        $studentUser = User::create([
            'name' => 'Child Student',
            'email' => 'child.ui@elite.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        StudentProfile::create(['user_id' => $studentUser->id]);

        DB::table('parent_student')->insert([
            'parent_user_id' => $parentUser->id,
            'student_user_id' => $studentUser->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $html = view('filament.resources.parent-profile.children-details', [
            'getRecord' => fn () => $parent->fresh(),
        ])->render();

        $this->assertStringContainsString('--ch-bg-card', $html);
        $this->assertStringContainsString('html.dark .linked-children-container', $html);
        $this->assertStringContainsString('Child Student', $html);
        $this->assertStringNotContainsString('class="text-white font-extrabold"', $html);
    }
}
