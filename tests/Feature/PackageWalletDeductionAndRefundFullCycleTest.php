<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\PackageTemplate;
use App\Models\PackageTransaction;
use App\Models\StudentPackage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PackageWalletDeductionAndRefundFullCycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_package_wallet_deduction_exhaustion_and_refund_cycle(): void
    {
        $student = User::create([
            'name' => 'Wallet Student',
            'email' => 'student.wallet@elite.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);

        $package = StudentPackage::create([
            'student_user_id' => $student->id,
            'total_sessions' => 1,
            'used_sessions' => 0,
            'remaining_sessions' => 1,
            'status' => 'active',
            'activated_at' => now(),
        ]);

        // 1. Deduct session -> Remaining balance becomes 0 & package status becomes 'exhausted'
        $deductSuccess = $package->deductSession(null, 'Live Session Attendance');
        $this->assertTrue($deductSuccess);
        $this->assertEquals(0, $package->fresh()->remaining_sessions);
        $this->assertEquals(1, $package->fresh()->used_sessions);
        $this->assertEquals('exhausted', $package->fresh()->status);

        // Attempting to deduct again on exhausted package fails
        $this->assertFalse($package->fresh()->deductSession(null, 'Attempt on exhausted package'));

        // 2. Refund session -> Remaining balance becomes 1 & package status reactivates to 'active'
        $refundSuccess = $package->fresh()->refundSession(null, 'Teacher Cancelled Session Refund');
        $this->assertTrue($refundSuccess);
        $this->assertEquals(1, $package->fresh()->remaining_sessions);
        $this->assertEquals(0, $package->fresh()->used_sessions);
        $this->assertEquals('active', $package->fresh()->status);

        // 3. Verify transaction logs recorded in database
        $this->assertDatabaseHas('package_transactions', [
            'student_package_id' => $package->id,
            'type' => 'session_deduct',
            'balance_after' => 0,
        ]);

        $this->assertDatabaseHas('package_transactions', [
            'student_package_id' => $package->id,
            'type' => 'session_refund',
            'balance_after' => 1,
        ]);
    }
}
