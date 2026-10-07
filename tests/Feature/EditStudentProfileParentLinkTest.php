<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Filament\Resources\StudentProfiles\Pages\EditStudentProfile;
use App\Models\AdminProfile;
use App\Models\GradeLevel;
use App\Models\ParentProfile;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class EditStudentProfileParentLinkTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $studentUser;
    protected StudentProfile $studentProfile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin.test@elite.edu',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => AccountStatus::APPROVED,
        ]);
        AdminProfile::create(['user_id' => $this->admin->id]);

        $this->studentUser = User::create([
            'name' => 'Student User',
            'email' => 'student.test@elite.edu',
            'phone' => '+201011112222',
            'password' => bcrypt('password'),
            'role' => 'student',
            'status' => AccountStatus::APPROVED,
        ]);

        $gradeLevel = GradeLevel::create([
            'name' => 'Grade 10',
            'slug' => 'grade-10',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $this->studentProfile = StudentProfile::create([
            'user_id' => $this->studentUser->id,
            'grade_level_id' => $gradeLevel->id,
        ]);
    }

    public function test_can_create_and_link_parent_without_column_not_found_query_exception(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(EditStudentProfile::class, ['record' => $this->studentProfile->id])
            ->callAction('createAndLinkParent', [
                'name' => 'Father of Student',
                'email' => 'ziadm0176@gmail.com',
                'phone' => '+201099887766',
                'password' => 'Parent@123456',
                'relationship' => 'father',
                'is_primary' => true,
            ])
            ->assertHasNoActionErrors();

        $this->assertDatabaseHas('users', [
            'email' => 'ziadm0176@gmail.com',
            'name' => 'Father of Student',
        ]);

        $parentUser = User::where('email', 'ziadm0176@gmail.com')->first();
        $this->assertNotNull($parentUser);

        $this->assertDatabaseHas('parent_profiles', [
            'user_id' => $parentUser->id,
        ]);

        $this->assertDatabaseHas('parent_student', [
            'parent_user_id' => $parentUser->id,
            'student_user_id' => $this->studentUser->id,
            'relationship' => 'father',
        ]);
    }

    public function test_linking_existing_user_email_links_parent_profile_and_student_without_error(): void
    {
        $this->actingAs($this->admin);

        // Create an existing user who is a parent
        $existingParent = User::create([
            'name' => 'Existing Parent',
            'email' => 'parent.existing@elite.edu',
            'phone' => '+201022334455',
            'password' => bcrypt('secret123'),
            'status' => AccountStatus::APPROVED,
        ]);

        Livewire::test(EditStudentProfile::class, ['record' => $this->studentProfile->id])
            ->callAction('createAndLinkParent', [
                'name' => 'Existing Parent Updated',
                'email' => 'parent.existing@elite.edu',
                'phone' => '+201022334455',
                'password' => 'Parent@123456',
                'relationship' => 'mother',
                'is_primary' => true,
            ])
            ->assertHasNoActionErrors();

        $this->assertDatabaseHas('parent_profiles', [
            'user_id' => $existingParent->id,
        ]);

        $this->assertDatabaseHas('parent_student', [
            'parent_user_id' => $existingParent->id,
            'student_user_id' => $this->studentUser->id,
            'relationship' => 'mother',
            'is_primary' => 1,
        ]);
    }
}
