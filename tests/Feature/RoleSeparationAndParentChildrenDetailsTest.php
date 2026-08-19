<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\GradeLevel;
use App\Models\ParentProfile;
use App\Models\StudentPackage;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleSeparationAndParentChildrenDetailsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_roles_are_separated_and_parent_profile_displays_linked_children_details(): void
    {
        // 1. Create Users for each role
        $admin = User::factory()->create(['name' => 'System Admin', 'status' => AccountStatus::APPROVED]);
        \App\Models\AdminProfile::create(['user_id' => $admin->id]);
        $teacherUser = User::factory()->create(['name' => 'Dr. Teacher', 'status' => AccountStatus::APPROVED]);
        $teacher = TeacherProfile::create(['user_id' => $teacherUser->id, 'slug' => 'dr-teacher']);

        $parentUser = User::factory()->create(['name' => 'Khaled Parent', 'status' => AccountStatus::APPROVED]);
        $parent = ParentProfile::create(['user_id' => $parentUser->id]);

        $grade = GradeLevel::create(['name' => 'Grade 10', 'slug' => 'grade-10']);
        $studentUser1 = User::factory()->create(['name' => 'Child Student 1', 'status' => AccountStatus::APPROVED]);
        $studentProfile1 = StudentProfile::create(['user_id' => $studentUser1->id, 'grade_level_id' => $grade->id, 'school_name' => 'International Academy']);

        $studentUser2 = User::factory()->create(['name' => 'Child Student 2', 'status' => AccountStatus::PENDING]);
        $studentProfile2 = StudentProfile::create(['user_id' => $studentUser2->id, 'grade_level_id' => $grade->id, 'school_name' => 'National School']);

        // Link children to parent
        $parent->students()->attach([$studentUser1->id, $studentUser2->id]);

        StudentPackage::create([
            'student_user_id' => $studentUser1->id,
            'total_sessions' => 12,
            'remaining_sessions' => 8,
            'status' => 'active',
        ]);

        // Verify count of users per role query
        $this->assertEquals(1, User::whereHas('teacherProfile')->count());
        $this->assertEquals(1, User::whereHas('parentProfile')->count());
        $this->assertEquals(2, User::whereHas('studentProfile')->count());

        // Verify parent linked children details relation
        $linkedChildren = $parent->fresh()->students;
        $this->assertCount(2, $linkedChildren);
        $this->assertTrue($linkedChildren->contains($studentUser1));
        $this->assertTrue($linkedChildren->contains($studentUser2));
    }
}
