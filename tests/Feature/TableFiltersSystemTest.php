<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\AdminProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class TableFiltersSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('demo.enabled', true);

        $this->adminUser = User::create([
            'name' => 'Demo Administrator',
            'email' => 'admin@trendk.com',
            'password' => bcrypt('Demo@12345'),
            'status' => AccountStatus::APPROVED,
        ]);
        AdminProfile::create(['user_id' => $this->adminUser->id]);
    }

    public function test_users_resource_page_loads_with_smart_glass_filters(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/users');
        $response->assertSuccessful();
    }

    public function test_student_packages_page_loads_with_filters(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/student-packages');
        $response->assertSuccessful();
    }

    public function test_live_sessions_page_loads_with_filters(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/live-sessions');
        $response->assertSuccessful();
    }
}
