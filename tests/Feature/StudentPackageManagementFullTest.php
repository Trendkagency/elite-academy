<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\PackageTemplate;
use App\Models\PackageTransaction;
use App\Models\StudentPackage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentPackageManagementFullTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_package_assignment_deduction_and_portal_display(): void
    {
        $student = User::create([
            'name' => 'Package Learner',
            'email' => 'student.pkg@elite.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        \App\Models\StudentProfile::create(['user_id' => $student->id]);

        $template = PackageTemplate::create([
            'name' => 'Monthly Pro 12 Sessions',
            'sessions_count' => 12,
            'price' => 150.00,
            'is_active' => true,
        ]);

        $package = StudentPackage::create([
            'student_user_id' => $student->id,
            'package_template_id' => $template->id,
            'total_sessions' => 12,
            'used_sessions' => 2,
            'remaining_sessions' => 10,
            'status' => 'active',
            'activated_at' => now(),
        ]);

        // 1. Verify deduction logic
        $success = $package->deductSession(null, 'Attended Physics Live Stream');
        $this->assertTrue($success);
        $this->assertEquals(9, $package->fresh()->remaining_sessions);
        $this->assertEquals(3, $package->fresh()->used_sessions);

        // Verify transaction logged
        $this->assertDatabaseHas('package_transactions', [
            'student_package_id' => $package->id,
            'type' => 'session_deduct',
            'sessions_delta' => -1,
            'balance_after' => 9,
        ]);

        // 2. Student Portal view shows active package remaining sessions
        $this->actingAs($student);
        $response = $this->withSession(['locale' => 'en'])->get('/student-portal');

        $response->assertStatus(200)
            ->assertSee('9 Sessions Remaining')
            ->assertSee('Monthly Pro 12 Sessions');
    }
}
