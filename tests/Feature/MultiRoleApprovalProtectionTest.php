<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\ParentProfile;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiRoleApprovalProtectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_parent_student_registration_sets_pending_and_blocks_login_until_approved(): void
    {
        // 1. Teacher Registration
        $teacherRes = $this->postJson('/ajax/register', [
            'name' => 'Professor Smith',
            'email' => 'teacher@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'user_type' => 'teacher',
        ]);
        $teacherRes->assertStatus(201);
        $teacherUser = User::where('email', 'teacher@example.com')->first();
        $this->assertEquals(AccountStatus::PENDING, $teacherUser->status);

        // Teacher Login Attempt before approval
        $teacherLoginRes = $this->postJson('/ajax/login', [
            'email' => 'teacher@example.com',
            'password' => 'password123',
        ]);
        $teacherLoginRes->assertStatus(403);
        $this->assertFalse(auth()->check());

        // 2. Parent Registration
        $parentRes = $this->postJson('/ajax/register', [
            'name' => 'Parent Johnson',
            'email' => 'parent@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'user_type' => 'parent',
        ]);
        $parentRes->assertStatus(201);
        $parentUser = User::where('email', 'parent@example.com')->first();
        $this->assertEquals(AccountStatus::PENDING, $parentUser->status);

        // Parent Portal access before approval
        $this->actingAs($parentUser);
        $parentPortalRes = $this->get('/parent-portal');
        $parentPortalRes->assertRedirect('/login');
        $this->assertFalse(auth()->check());

        // 3. Student Registration
        $studentRes = $this->postJson('/ajax/register', [
            'name' => 'Student Alex',
            'email' => 'student@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'user_type' => 'student',
        ]);
        $studentRes->assertStatus(201);
        $studentUser = User::where('email', 'student@example.com')->first();
        $this->assertEquals(AccountStatus::PENDING, $studentUser->status);

        // Student Portal access before approval
        $this->actingAs($studentUser);
        $studentPortalRes = $this->get('/student-portal');
        $studentPortalRes->assertRedirect('/login');
        $this->assertFalse(auth()->check());

        // 4. Approve Accounts via Admin Action simulation
        $teacherUser->update(['status' => AccountStatus::APPROVED]);
        $parentUser->update(['status' => AccountStatus::APPROVED]);
        $studentUser->update(['status' => AccountStatus::APPROVED]);

        // Login after approval
        $teacherApprovedLogin = $this->postJson('/ajax/login', [
            'email' => 'teacher@example.com',
            'password' => 'password123',
        ]);
        $teacherApprovedLogin->assertStatus(200);

        $parentApprovedLogin = $this->postJson('/ajax/login', [
            'email' => 'parent@example.com',
            'password' => 'password123',
        ]);
        $parentApprovedLogin->assertStatus(200);

        $studentApprovedLogin = $this->postJson('/ajax/login', [
            'email' => 'student@example.com',
            'password' => 'password123',
        ]);
        $studentApprovedLogin->assertStatus(200);
    }
}
