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

        // 3. Test Registration Action
        $regData = [
            'name' => 'New Learner',
            'email' => 'new.learner@elite.edu',
            'password' => 'password123',
            'user_type' => 'student',
        ];
        $regResponse = $this->postJson('/ajax/register', $regData);
        $regResponse->assertStatus(201)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('users', ['email' => 'new.learner@elite.edu']);

        // Logout
        $this->post('/logout');

        // 4. Test Login Action
        $loginResponse = $this->postJson('/ajax/login', [
            'email' => 'new.learner@elite.edu',
            'password' => 'password123',
        ]);
        $loginResponse->assertStatus(200)
            ->assertJsonPath('success', true);
    }
}
