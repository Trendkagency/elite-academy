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

    public function test_admin_account_is_auto_created_when_accessing_admin_login_page_if_not_exists(): void
    {
        // Ensure no admin exists
        $this->assertEquals(0, User::count());
        $this->assertEquals(0, AdminProfile::count());

        // Request the Filament Admin login page
        $response = $this->get('/admin/login');

        $response->assertStatus(200);

        // Verify admin user is created
        $this->assertDatabaseHas('users', [
            'email' => 'admin@elite-academy.com',
            'status' => AccountStatus::APPROVED->value,
        ]);

        $admin = User::where('email', 'admin@elite-academy.com')->first();
        $this->assertNotNull($admin);
        $this->assertTrue(Hash::check('password', $admin->password));
        $this->assertTrue($admin->isAdmin());
        $this->assertDatabaseHas('admin_profiles', [
            'user_id' => $admin->id,
        ]);
    }

    public function test_existing_admin_account_is_not_duplicated(): void
    {
        // Create an existing admin user
        $admin = User::create([
            'name' => 'Existing Admin',
            'email' => 'admin@elite-academy.com',
            'phone' => '+201111111111',
            'password' => bcrypt('secret123'),
            'status' => AccountStatus::APPROVED,
            'email_verified_at' => now(),
        ]);
        AdminProfile::create(['user_id' => $admin->id]);

        $this->assertEquals(1, User::count());

        // Access admin login
        $response = $this->get('/admin/login');
        $response->assertStatus(200);

        // Count should still be 1, password remains unchanged
        $this->assertEquals(1, User::count());
        $this->assertTrue(Hash::check('secret123', $admin->fresh()->password));
    }
}
