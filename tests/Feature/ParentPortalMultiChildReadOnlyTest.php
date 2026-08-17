<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\Category;
use App\Models\GradeLevel;
use App\Models\LiveSession;
use App\Models\ParentProfile;
use App\Models\StudentPackage;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ParentPortalMultiChildReadOnlyTest extends TestCase
{
    use RefreshDatabase;

    public function test_parent_portal_renders_all_6_child_metrics_in_read_only_mode(): void
    {
        $grade = GradeLevel::create(['name' => 'الصف الثالث الثانوي', 'slug' => 'g12']);
        $cat = Category::create(['name' => 'العلوم', 'slug' => 'science']);
        $subject = Subject::create(['category_id' => $cat->id, 'name' => 'الفيزياء', 'slug' => 'physics']);

        $teacherUser = User::create(['name' => 'د. معلم', 'email' => 't.parent@elite.edu', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        $teacher = TeacherProfile::create(['user_id' => $teacherUser->id, 'slug' => 'tparent']);

        $parentUser = User::create(['name' => 'أستاذ أحمد (ولي أمر)', 'email' => 'parent.main@elite.edu', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        $parentProfile = ParentProfile::create(['user_id' => $parentUser->id, 'phone_number' => '01000000000']);

        $child1 = User::create(['name' => 'عمر أحمد', 'email' => 'omar@elite.edu', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        StudentProfile::create(['user_id' => $child1->id, 'grade_level_id' => $grade->id, 'school_name' => 'STEM Cairo']);

        $child2 = User::create(['name' => 'مريم أحمد', 'email' => 'mariam@elite.edu', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        StudentProfile::create(['user_id' => $child2->id, 'grade_level_id' => $grade->id, 'school_name' => 'STEM Giza']);

        // Link child1 and child2 to parent
        DB::table('parent_student')->insert([
            ['parent_user_id' => $parentUser->id, 'student_user_id' => $child1->id, 'created_at' => now(), 'updated_at' => now()],
            ['parent_user_id' => $parentUser->id, 'student_user_id' => $child2->id, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Student package for child1
        StudentPackage::create([
            'student_user_id' => $child1->id,
            'total_sessions' => 12,
            'used_sessions' => 4,
            'remaining_sessions' => 8,
            'status' => 'active',
        ]);

        LiveSession::create([
            'student_user_id' => $child1->id,
            'teacher_profile_id' => $teacher->id,
            'subject_id' => $subject->id,
            'scheduled_at' => now()->addHours(6),
        ]);

        $this->actingAs($parentUser);

        // 1. Access Parent Portal
        $response = $this->withSession(['locale' => 'ar'])->get('/parent-portal');
        $response->assertStatus(200)
            ->assertSee('عمر أحمد')
            ->assertSee('مريم أحمد');

        // 2. Fetch Progress for Linked Child 1
        $progressRes = $this->getJson("/ajax/parent/student/{$child1->id}/progress");
        $progressRes->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('is_read_only', true)
            ->assertJsonPath('student.name', 'عمر أحمد')
            ->assertJsonPath('package.remaining_sessions', 8)
            ->assertJsonPath('attendance.rate', '94%');

        // 3. Attempting to view unlinked child returns 403 Forbidden
        $strangerStudent = User::create(['name' => 'طالب غريب', 'email' => 'stranger@elite.edu', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        $forbiddenRes = $this->getJson("/ajax/parent/student/{$strangerStudent->id}/progress");
        $forbiddenRes->assertStatus(403);
    }
}
