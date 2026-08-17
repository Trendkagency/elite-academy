<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_via_api(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test Student',
            'email' => 'student_api@test.com',
            'password' => 'password123',
            'user_type' => 'student',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['message', 'user' => ['id', 'name', 'email'], 'access_token']);

        $this->assertDatabaseHas('users', ['email' => 'student_api@test.com']);
        $this->assertDatabaseHas('student_profiles', ['user_id' => $response->json('user.id')]);
    }

    public function test_user_can_login_via_api(): void
    {
        $user = User::create([
            'name' => 'Login User',
            'email' => 'login_api@test.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'login_api@test.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'user', 'access_token']);
    }
}
