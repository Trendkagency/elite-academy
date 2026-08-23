<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\AdminProfile;
use App\Models\ParentProfile;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleBasedAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected User $studentUser;
    protected User $teacherUser;
    protected User $parentUser;
    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Student User
        $this->studentUser = User::create([
            'name' => 'Student Alice',
            'email' => 'alice@student.com',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        StudentProfile::create(['user_id' => $this->studentUser->id, 'school_name' => 'High School A']);

        // 2. Teacher User
        $this->teacherUser = User::create([
            'name' => 'Teacher Bob',
            'email' => 'bob@teacher.com',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        TeacherProfile::create(['user_id' => $this->teacherUser->id, 'title' => 'Dr.', 'slug' => 'teacher-bob']);

        // 3. Parent User
        $this->parentUser = User::create([
            'name' => 'Parent Charlie',
            'email' => 'charlie@parent.com',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        ParentProfile::create(['user_id' => $this->parentUser->id]);

        // 4. Admin User
        $this->adminUser = User::create([
            'name' => 'Admin Dave',
            'email' => 'admin@elite-academy.com',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        AdminProfile::create(['user_id' => $this->adminUser->id]);
    }

    public function test_student_cannot_access_teacher_portal_or_parent_portal(): void
    {
        $response1 = $this->actingAs($this->studentUser)->get('/teacher-portal');
        $response1->assertStatus(403);

        $response2 = $this->actingAs($this->studentUser)->get('/parent-portal');
        $response2->assertStatus(403);
    }

    public function test_teacher_cannot_access_student_portal_or_parent_portal(): void
    {
        $response1 = $this->actingAs($this->teacherUser)->get('/student-portal');
        $response1->assertStatus(403);

        $response2 = $this->actingAs($this->teacherUser)->get('/parent-portal');
        $response2->assertStatus(403);
    }

    public function test_parent_cannot_access_teacher_portal_or_student_portal(): void
    {
        $response1 = $this->actingAs($this->parentUser)->get('/teacher-portal');
        $response1->assertStatus(403);

        $response2 = $this->actingAs($this->parentUser)->get('/student-portal');
        $response2->assertStatus(403);
    }

    public function test_student_can_access_student_portal(): void
    {
        $response = $this->actingAs($this->studentUser)->get('/student-portal');
        $response->assertStatus(200);
    }

    public function test_teacher_can_access_teacher_portal(): void
    {
        $response = $this->actingAs($this->teacherUser)->get('/teacher-portal');
        $response->assertStatus(200);
    }

    public function test_parent_can_access_parent_portal(): void
    {
        $response = $this->actingAs($this->parentUser)->get('/parent-portal');
        $response->assertStatus(200);
    }

    public function test_admin_can_access_all_portals(): void
    {
        $this->actingAs($this->adminUser)->get('/teacher-portal')->assertStatus(200);
        $this->actingAs($this->adminUser)->get('/student-portal')->assertStatus(200);
        $this->actingAs($this->adminUser)->get('/parent-portal')->assertStatus(200);
    }

    public function test_teacher_is_redirected_from_public_catalog_to_teacher_portal(): void
    {
        $this->actingAs($this->teacherUser)->get('/courses')->assertRedirect(route('teacher-portal'));
        $this->actingAs($this->teacherUser)->get('/subjects')->assertRedirect(route('teacher-portal'));
        $this->actingAs($this->teacherUser)->get('/teachers')->assertRedirect(route('teacher-portal'));
    }
}
