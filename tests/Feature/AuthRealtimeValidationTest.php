<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\GradeLevel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthRealtimeValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_realtime_check_email_exists_for_login(): void
    {
        // 1. Check non-existent email
        $res = $this->postJson(route('ajax.validate.email-exists'), [
            'email' => 'unknown.student@elite.edu',
        ]);
        $res->assertStatus(200)
            ->assertJson([
                'valid'  => true,
                'exists' => false,
            ]);

        // 2. Create user and check existent email
        User::create([
            'name'     => 'Existing Student',
            'email'    => 'existing.student@elite.edu',
            'password' => bcrypt('password123'),
            'status'   => AccountStatus::APPROVED,
        ]);

        $res2 = $this->postJson(route('ajax.validate.email-exists'), [
            'email' => 'existing.student@elite.edu',
        ]);
        $res2->assertStatus(200)
            ->assertJson([
                'valid'  => true,
                'exists' => true,
            ]);

        // 3. Check invalid email format
        $res3 = $this->postJson(route('ajax.validate.email-exists'), [
            'email' => 'invalid-email-format',
        ]);
        $res3->assertStatus(422)
            ->assertJson([
                'valid' => false,
            ]);
    }

    public function test_realtime_check_email_available_for_register(): void
    {
        User::create([
            'name'     => 'Registered User',
            'email'    => 'registered@elite.edu',
            'password' => bcrypt('password123'),
            'status'   => AccountStatus::APPROVED,
        ]);

        // Taken email
        $resTaken = $this->postJson(route('ajax.validate.email-available'), [
            'email' => 'registered@elite.edu',
        ]);
        $resTaken->assertStatus(200)
            ->assertJson([
                'valid'     => true,
                'available' => false,
            ]);

        // Available email
        $resAvail = $this->postJson(route('ajax.validate.email-available'), [
            'email' => 'brandnew@elite.edu',
        ]);
        $resAvail->assertStatus(200)
            ->assertJson([
                'valid'     => true,
                'available' => true,
            ]);
    }

    public function test_realtime_check_phone_available_for_register(): void
    {
        User::create([
            'name'     => 'Registered User',
            'email'    => 'registered2@elite.edu',
            'phone'    => '01099887766',
            'password' => bcrypt('password123'),
            'status'   => AccountStatus::APPROVED,
        ]);

        // Taken phone
        $resTaken = $this->postJson(route('ajax.validate.phone-available'), [
            'phone' => '01099887766',
        ]);
        $resTaken->assertStatus(200)
            ->assertJson([
                'valid'     => true,
                'available' => false,
            ]);

        // Available phone
        $resAvail = $this->postJson(route('ajax.validate.phone-available'), [
            'phone' => '01122334455',
        ]);
        $resAvail->assertStatus(200)
            ->assertJson([
                'valid'     => true,
                'available' => true,
            ]);
    }

    public function test_login_returns_email_error_when_email_does_not_exist(): void
    {
        $response = $this->postJson(route('ajax.login'), [
            'email'    => 'nonexistent@elite.edu',
            'password' => 'anyPassword123',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'field'   => 'email',
            ]);
    }

    public function test_login_returns_password_error_when_password_is_incorrect(): void
    {
        User::create([
            'name'     => 'Valid User',
            'email'    => 'valid.user@elite.edu',
            'password' => bcrypt('correctPassword123'),
            'status'   => AccountStatus::APPROVED,
        ]);

        $response = $this->postJson(route('ajax.login'), [
            'email'    => 'valid.user@elite.edu',
            'password' => 'wrongPassword999',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'field'   => 'password',
            ]);
    }

    public function test_login_succeeds_with_correct_credentials(): void
    {
        User::create([
            'name'     => 'Valid User',
            'email'    => 'valid.user@elite.edu',
            'password' => bcrypt('correctPassword123'),
            'status'   => AccountStatus::APPROVED,
        ]);

        $response = $this->postJson(route('ajax.login'), [
            'email'    => 'valid.user@elite.edu',
            'password' => 'correctPassword123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_register_fails_when_email_already_exists(): void
    {
        User::create([
            'name'     => 'Existing User',
            'email'    => 'taken@elite.edu',
            'phone'    => '01011112222',
            'password' => bcrypt('password123'),
            'status'   => AccountStatus::APPROVED,
        ]);

        $grade = GradeLevel::create(['name' => 'Grade 10', 'slug' => 'grade-10', 'sort_order' => 1]);

        $res = $this->postJson(route('ajax.register'), [
            'name'           => 'Another User',
            'email'          => 'taken@elite.edu',
            'phone'          => '01033334444',
            'password'       => 'password123',
            'user_type'      => 'student',
            'grade_level_id' => $grade->id,
        ]);

        $res->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }
}
