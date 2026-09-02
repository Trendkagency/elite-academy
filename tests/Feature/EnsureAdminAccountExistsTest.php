<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\AdminProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EnsureAdminAccountExistsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_accounts_are_auto_created_when_accessing_login_or_admin_page(): void
    {
        // Ensure no user exists initially
        $this->assertEquals(0, User::count());

        // 1. Visit /login (web portal login)
        $response = $this->get('/login');
        $response->assertStatus(200);

        // Verify both admin accounts exist and are approved
        $this->assertDatabaseHas('users', [
            'email' => 'admin@elite.edu',
            'status' => AccountStatus::APPROVED->value,
        ]);
        $this->assertDatabaseHas('users', [
            'email' => 'admin@elite-academy.com',
            'status' => AccountStatus::APPROVED->value,
        ]);

        $adminEdu = User::where('email', 'admin@elite.edu')->first();
        $this->assertNotNull($adminEdu);
        $this->assertTrue(Hash::check('password', $adminEdu->password));
        $this->assertTrue($adminEdu->isAdmin());
        $this->assertEquals(\App\Enums\Role::ADMIN->value, $adminEdu->getRoleName());
        $this->assertDatabaseHas('admin_profiles', ['user_id' => $adminEdu->id]);

        // 2. Can login via AJAX with admin@elite.edu
        $loginResponse = $this->postJson('/ajax/login', [
            'email' => 'admin@elite.edu',
            'password' => 'password',
        ]);
        $loginResponse->assertStatus(200);
        $loginResponse->assertJson(['success' => true]);
    }

    public function test_admin_accounts_created_when_accessing_filament_admin_page(): void
    {
        $this->assertEquals(0, User::count());

        $response = $this->get('/admin/login');
        $response->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'email' => 'admin@elite.edu',
            'status' => AccountStatus::APPROVED->value,
        ]);
    }
}
