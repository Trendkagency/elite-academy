<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeparateLoginAndRegisterPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_and_register_are_separate_pages(): void
    {
        // 1. Check Login Page
        $loginRes = $this->get('/login');
        $loginRes->assertStatus(200)
            ->assertSee(route('register'));

        // 2. Check Register Page
        $registerRes = $this->get('/register');
        $registerRes->assertStatus(200)
            ->assertSee(route('login'));

        // 3. Test Registration Action (Status starts PENDING)
        $regData = [
            'name' => 'New Learner',
            'email' => 'new.learner@elite.edu',
            'phone' => '01012345678',
            'password' => 'password123',
            'user_type' => 'student',
        ];
        $regResponse = $this->postJson('/ajax/register', $regData);
        $regResponse->assertStatus(201)
            ->assertJsonPath('success', true);

        $user = User::where('email', 'new.learner@elite.edu')->first();
        $this->assertNotNull($user);
        $this->assertEquals(AccountStatus::PENDING, $user->status);

        // 4. Attempt Login Before Approval -> Must fail with HTTP 403 Forbidden
        $failedLogin = $this->postJson('/ajax/login', [
            'email' => 'new.learner@elite.edu',
            'password' => 'password123',
        ]);
        $failedLogin->assertStatus(403);

        // 5. Attempt Dashboard Access Before Approval -> Must redirect to login
        $this->actingAs($user);
        $dashboardResponse = $this->get('/student-portal');
        $dashboardResponse->assertRedirect('/login');

        // 6. Admin Approves Account
        $user->update(['status' => AccountStatus::APPROVED]);
        $this->post('/logout');

        // 7. Login After Admin Approval -> Must succeed with HTTP 200
        $loginResponse = $this->postJson('/ajax/login', [
            'email' => 'new.learner@elite.edu',
            'password' => 'password123',
        ]);
        $loginResponse->assertStatus(200)
            ->assertJsonPath('success', true);
    }
}
