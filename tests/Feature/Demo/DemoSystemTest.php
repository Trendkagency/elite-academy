<?php

namespace Tests\Feature\Demo;

use App\Enums\AccountStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class DemoSystemTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();
        Config::set('demo.enabled', true);
    }

    public function test_demo_direct_login_for_all_configured_roles(): void
    {
        $roles = [
            'admin' => '/admin',
            'teacher' => '/teacher',
            'student' => '/student',
            'parent' => '/parent',
        ];

        foreach ($roles as $role => $expectedRedirect) {
            $response = $this->post("/demo/login/{$role}");
            $response->assertStatus(302);
            $response->assertRedirect($expectedRedirect);
            $this->assertAuthenticated();
            $this->assertEquals("{$role}@demo.test", auth()->user()->email);
            auth()->logout();
        }
    }

    public function test_demo_login_endpoint_returns_404_when_demo_disabled(): void
    {
        Config::set('demo.enabled', false);

        $response = $this->post('/demo/login/admin');
        $response->assertStatus(404);
    }

    public function test_trendk_support_account_bypasses_all_gates(): void
    {
        $supportUser = User::firstOrCreate(
            ['email' => 'support@trendk.com'],
            [
                'name' => 'Trendk Support',
                'phone' => '+201000000000',
                'password' => Hash::make('password'),
                'status' => AccountStatus::APPROVED,
                'email_verified_at' => now(),
            ]
        );

        $this->actingAs($supportUser);

        // Any ability should be allowed via Gate::before
        $this->assertTrue(Gate::allows('view-any-system-resource'));
        $this->assertTrue(Gate::allows('delete-everything'));
        $this->assertTrue(Gate::allows('manage-billing'));
    }

    public function test_trendk_support_is_excluded_from_scope_and_demo_credentials(): void
    {
        $supportUser = User::firstOrCreate(
            ['email' => 'support@trendk.com'],
            [
                'name' => 'Trendk Support',
                'phone' => '+201000000000',
                'password' => Hash::make('password'),
                'status' => AccountStatus::APPROVED,
            ]
        );

        // Trendk Standard: User::excludeSupport() must not contain support@trendk.com
        $excludedList = User::excludeSupport()->pluck('email')->toArray();
        $this->assertNotContains('support@trendk.com', $excludedList);

        // Trendk Standard: Must NOT be listed in config demo accounts
        $demoAccounts = config('demo.credentials.accounts', []);
        $demoEmails = array_column($demoAccounts, 'email');
        $this->assertNotContains('support@trendk.com', $demoEmails);
    }

    public function test_config_demo_changes_immediately_reflected_in_login_view(): void
    {
        Config::set('demo.branding.app_name', 'Unique Test Demo Academy');
        Config::set('demo.login.heading.en', 'Custom Dynamic Heading Test');

        $html = view('pages.login')->render();

        $this->assertStringContainsString('Unique Test Demo Academy', $html);
        $this->assertStringContainsString('Custom Dynamic Heading Test', $html);
    }

    public function test_protected_demo_accounts_cannot_be_deleted_in_demo_mode(): void
    {
        $demoUser = User::where('email', 'admin@demo.test')->first();
        if (! $demoUser) {
            $demoUser = User::create([
                'name' => 'Admin Demo',
                'email' => 'admin@demo.test',
                'phone' => '+201000000099',
                'password' => Hash::make('Demo@12345'),
                'status' => AccountStatus::APPROVED,
            ]);
        }

        $this->expectException(HttpException::class);
        $demoUser->delete();
    }
}
