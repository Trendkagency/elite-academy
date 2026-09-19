<?php

namespace Tests\Feature;

use App\Filament\Resources\Courses\CourseResource;
use App\Filament\Resources\Courses\RelationManagers\EnrollmentsRelationManager;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\GradeLevel;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CourseEnrollmentsFilamentTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Course $course;
    protected User $student1;
    protected User $student2;
    protected User $student3;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Super Administrator',
            'email' => 'admin_enroll@elite.test',
            'password' => bcrypt('AdminPass123!'),
            'role' => 'super_admin',
            'status' => \App\Enums\AccountStatus::APPROVED,
        ]);
        \App\Models\AdminProfile::create(['user_id' => $this->admin->id]);

        $category = Category::create(['name' => 'Sciences', 'slug' => 'sciences']);
        $subject = Subject::create(['category_id' => $category->id, 'name' => 'Physics', 'slug' => 'physics']);
        $grade = GradeLevel::create(['name' => 'Secondary 3', 'code' => 'SEC3', 'slug' => 'sec3', 'sort_order' => 1]);

        $teacherUser = User::create([
            'name' => 'Teacher One',
            'email' => 'teacher@test.com',
            'password' => bcrypt('TeacherPass123!'),
            'role' => 'teacher',
            'status' => \App\Enums\AccountStatus::APPROVED,
        ]);
        $teacher = TeacherProfile::create([
            'user_id' => $teacherUser->id,
            'status' => 'approved',
            'title' => 'Physics Teacher',
            'slug' => 'physics-teacher',
        ]);

        $this->course = Course::create([
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'grade_level_id' => $grade->id,
            'title' => 'Physics Advanced Level',
            'slug' => 'physics-advanced-level',
            'is_active' => true,
        ]);

        $this->student1 = User::create([
            'name' => 'Student One',
            'email' => 'stu1@test.com',
            'password' => bcrypt('StuPass123!'),
            'role' => 'student',
            'status' => \App\Enums\AccountStatus::APPROVED,
        ]);
        StudentProfile::create(['user_id' => $this->student1->id, 'student_code' => 'STU-001', 'grade_level_id' => $grade->id]);

        $this->student2 = User::create([
            'name' => 'Student Two',
            'email' => 'stu2@test.com',
            'password' => bcrypt('StuPass123!'),
            'role' => 'student',
            'status' => \App\Enums\AccountStatus::APPROVED,
        ]);
        StudentProfile::create(['user_id' => $this->student2->id, 'student_code' => 'STU-002', 'grade_level_id' => $grade->id]);

        $this->student3 = User::create([
            'name' => 'Student Three',
            'email' => 'stu3@test.com',
            'password' => bcrypt('StuPass123!'),
            'role' => 'student',
            'status' => \App\Enums\AccountStatus::APPROVED,
        ]);
        StudentProfile::create(['user_id' => $this->student3->id, 'student_code' => 'STU-003', 'grade_level_id' => $grade->id]);
    }

    public function test_course_resource_contains_enrollments_relation_manager(): void
    {
        $relations = CourseResource::getRelations();
        $this->assertContains(EnrollmentsRelationManager::class, $relations);
    }

    public function test_admin_can_render_enrollments_relation_manager_for_course(): void
    {
        CourseEnrollment::create([
            'course_id' => $this->course->id,
            'student_user_id' => $this->student1->id,
            'cohort' => 'Cohort Alpha',
            'status' => 'active',
        ]);

        $this->actingAs($this->admin);

        Livewire::test(EnrollmentsRelationManager::class, [
            'ownerRecord' => $this->course,
            'pageClass' => CourseResource\Pages\EditCourse::class,
        ])
            ->call('loadTable')
            ->assertSuccessful()
            ->assertSee('Student One')
            ->assertSee('Cohort Alpha');
    }

    public function test_admin_can_enroll_multiple_students_simultaneously(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(EnrollmentsRelationManager::class, [
            'ownerRecord' => $this->course,
            'pageClass' => CourseResource\Pages\EditCourse::class,
        ])
            ->callTableAction('enroll_multiple_students', data: [
                'student_user_ids' => [$this->student1->id, $this->student2->id, $this->student3->id],
                'cohort' => 'Cohort Omega 2026',
                'status' => 'active',
                'enrolled_at' => now()->toDateTimeString(),
            ])
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas('course_enrollments', [
            'course_id' => $this->course->id,
            'student_user_id' => $this->student1->id,
            'cohort' => 'Cohort Omega 2026',
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('course_enrollments', [
            'course_id' => $this->course->id,
            'student_user_id' => $this->student2->id,
            'cohort' => 'Cohort Omega 2026',
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('course_enrollments', [
            'course_id' => $this->course->id,
            'student_user_id' => $this->student3->id,
            'cohort' => 'Cohort Omega 2026',
            'status' => 'active',
        ]);

        $this->assertCount(3, $this->course->fresh()->enrollments);
        $this->assertCount(3, $this->course->fresh()->students);
    }
}
