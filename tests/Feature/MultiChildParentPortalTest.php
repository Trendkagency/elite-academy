<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\ParentProfile;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiChildParentPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_parent_can_view_only_their_linked_children_and_is_denied_access_to_others(): void
    {
        // 1. Create Parent Account
        $parentUser = User::create([
            'name' => 'Khaled Mohamed (Parent)',
            'email' => 'parent.multi@elite.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        $parentProfile = ParentProfile::create(['user_id' => $parentUser->id]);

        // 2. Create 3 Students: Ahmed (Son 1), Mariam (Daughter 2), Omar (Unlinked Student)
        $ahmed = User::create(['name' => 'Ahmed Khaled', 'email' => 'ahmed.k@elite.edu', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        StudentProfile::create(['user_id' => $ahmed->id, 'school_name' => 'STEM Cairo']);

        $mariam = User::create(['name' => 'Mariam Khaled', 'email' => 'mariam.k@elite.edu', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        StudentProfile::create(['user_id' => $mariam->id, 'school_name' => 'STEM Cairo']);

        $otherStudent = User::create(['name' => 'Omar Hassan (Other Student)', 'email' => 'omar.other@elite.edu', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        StudentProfile::create(['user_id' => $otherStudent->id, 'school_name' => 'Other School']);

        // 3. Link Ahmed and Mariam to Parent
        $parentProfile->students()->attach([$ahmed->id, $mariam->id]);

        // 4. Authenticate as Parent
        $this->actingAs($parentUser);

        // Parent Portal Index page should show Ahmed and Mariam
        $response = $this->get('/parent-portal');
        $response->assertStatus(200)
            ->assertSee('Ahmed Khaled')
            ->assertSee('Mariam Khaled')
            ->assertDontSee('Omar Hassan (Other Student)');

        // Parent CAN access Ahmed's progress
        $ahmedProgress = $this->getJson("/ajax/parent/student/{$ahmed->id}/progress");
        $ahmedProgress->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('student.name', 'Ahmed Khaled');

        // Parent CAN access Mariam's progress
        $mariamProgress = $this->getJson("/ajax/parent/student/{$mariam->id}/progress");
        $mariamProgress->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('student.name', 'Mariam Khaled');

        // Parent CANNOT access Omar's progress (Strict 403 Forbidden)
        $unauthorizedProgress = $this->getJson("/ajax/parent/student/{$otherStudent->id}/progress");
        $unauthorizedProgress->assertStatus(403)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Unauthorized Access: You can only view performance data for your own linked children.');
    }
}
