<?php

namespace Tests\Feature;

use App\Models\AdminProfile;
use App\Models\ParentProfile;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_user_can_access_filament_admin_panel(): void
    {
        $adminUser = User::factory()->create([
            'email' => 'admin@elite-academy.com',
            'status' => 'approved',
        ]);
        AdminProfile::create(['user_id' => $adminUser->id]);

        $response = $this->actingAs($adminUser)->get('/admin');
        $response->assertStatus(200);
    }

    public function test_student_user_is_blocked_from_filament_admin_panel(): void
    {
        $studentUser = User::factory()->create([
            'email' => 'student@elite-academy.com',
            'status' => 'approved',
        ]);
        StudentProfile::create(['user_id' => $studentUser->id]);

        $response = $this->actingAs($studentUser)->get('/admin');
        $response->assertStatus(403);
    }

    public function test_teacher_user_is_blocked_from_filament_admin_panel(): void
    {
        $teacherUser = User::factory()->create([
            'email' => 'teacher@elite-academy.com',
            'status' => 'approved',
        ]);
        TeacherProfile::create([
            'user_id' => $teacherUser->id,
            'slug' => 'teacher-slug',
        ]);

        $response = $this->actingAs($teacherUser)->get('/admin');
        $response->assertStatus(403);
    }

    public function test_parent_user_is_blocked_from_filament_admin_panel(): void
    {
        $parentUser = User::factory()->create([
            'email' => 'parent@elite-academy.com',
            'status' => 'approved',
        ]);
        ParentProfile::create(['user_id' => $parentUser->id]);

        $response = $this->actingAs($parentUser)->get('/admin');
        $response->assertStatus(403);
    }
}
