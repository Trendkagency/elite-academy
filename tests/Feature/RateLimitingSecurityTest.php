<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class RateLimitingSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_rate_limiter_blocks_excessive_brute_force_attempts(): void
    {
        $email = 'bruteforce@test.com';

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/ajax/login', [
                'email' => $email,
                'password' => 'wrongpassword',
            ]);
        }

        // 6th Attempt MUST return HTTP 429 Too Many Requests
        $response = $this->postJson('/ajax/login', [
            'email' => $email,
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(429);
        $response->assertJsonPath('success', false);
        $this->assertArrayHasKey('retry_after', $response->json());
    }

    public function test_register_rate_limiter_blocks_excessive_account_creations(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/ajax/register', [
                'name' => 'Spam Bot ' . $i,
                'email' => "bot{$i}@spam.com",
                'phone' => '0100000000' . $i,
                'password' => 'password123',
                'user_type' => 'student',
            ]);
        }

        $response = $this->postJson('/ajax/register', [
            'name' => 'Spam Bot 6',
            'email' => 'bot6@spam.com',
            'phone' => '01000000006',
            'password' => 'password123',
            'user_type' => 'student',
        ]);

        $response->assertStatus(429);
        $response->assertJsonPath('success', false);
    }

    public function test_contact_form_rate_limiter_blocks_spam_submissions(): void
    {
        for ($i = 0; $i < 3; $i++) {
            $this->postJson('/ajax/contact/submit', [
                'full_name' => 'Spammer ' . $i,
                'email' => "spam{$i}@test.com",
                'phone' => '0100000000' . $i,
                'message' => 'Test message spam ' . $i,
            ]);
        }

        $response = $this->postJson('/ajax/contact/submit', [
            'full_name' => 'Spammer 4',
            'email' => 'spam4@test.com',
            'phone' => '01000000004',
            'message' => 'Test message spam 4',
        ]);

        $response->assertStatus(429);
        $response->assertJsonPath('success', false);
    }

    public function test_custom_429_error_blade_renders_properly(): void
    {
        $view = $this->view('errors.429', ['retryAfter' => 45]);
        $view->assertSee('HTTP 429');
        $view->assertSee('Too Many Requests');
        $view->assertSee('45');
    }
}
