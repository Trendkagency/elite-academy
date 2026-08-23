<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentPortalAuthProtectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_accessing_student_portal(): void
    {
        // 1. Guest request -> Should redirect to /login
        $guestResponse = $this->get('/student-portal');
        $guestResponse->assertRedirect('/login');

        // 2. Authenticated Student request -> Access granted
        $student = User::create([
            'name' => 'Portal Student',
            'email' => 'student.portal.auth@elite.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        \App\Models\StudentProfile::create(['user_id' => $student->id]);

        $this->actingAs($student);

        $authResponse = $this->get('/student-portal');
        $authResponse->assertStatus(200)
            ->assertSee('Portal Student');
    }
}
