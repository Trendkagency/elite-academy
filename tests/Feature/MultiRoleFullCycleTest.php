<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\AdminProfile;
use App\Models\Assignment;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseSession;
use App\Models\GradeLevel;
use App\Models\ParentProfile;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MultiRoleFullCycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_multi_role_cycle_admin_teacher_student_parent(): void
    {
        $gradeLevel = GradeLevel::create(['name' => 'Grade 10', 'slug' => 'g10']);
        $category = Category::create(['name' => 'Science', 'slug' => 'science']);
        $subject = Subject::create(['category_id' => $category->id, 'name' => 'Physics', 'slug' => 'physics']);

        // 1. Create Admin User
        $adminUser = User::create(['name' => 'Admin User', 'email' => 'admin@elite.com', 'password' => bcrypt('secret'), 'status' => AccountStatus::APPROVED]);
        AdminProfile::create(['user_id' => $adminUser->id]);
        $this->assertTrue($adminUser->isAdmin());

        // 2. Create Teacher User
        $teacherUser = User::create(['name' => 'Teacher User', 'email' => 'teacher@elite.com', 'password' => bcrypt('secret'), 'status' => AccountStatus::APPROVED]);
        $teacherProfile = TeacherProfile::create(['user_id' => $teacherUser->id, 'slug' => 'teacher-user']);
        $this->assertTrue($teacherUser->isTeacher());

        // 3. Create Parent User
        $parentUser = User::create(['name' => 'Parent User', 'email' => 'parent@elite.com', 'password' => bcrypt('secret'), 'status' => AccountStatus::APPROVED]);
        ParentProfile::create(['user_id' => $parentUser->id]);
        $this->assertTrue($parentUser->isParent());

        // 4. Create Student User linked to Parent
        $studentUser = User::create(['name' => 'Student User', 'email' => 'student@elite.com', 'password' => bcrypt('secret'), 'status' => AccountStatus::APPROVED]);
        StudentProfile::create([
            'user_id' => $studentUser->id,
            'grade_level_id' => $gradeLevel->id,
        ]);
        $this->assertTrue($studentUser->isStudent());

        DB::table('parent_student')->insert([
            'parent_user_id' => $parentUser->id,
            'student_user_id' => $studentUser->id,
            'relationship' => 'father',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 5. Course Creation
        $course = Course::create([
            'subject_id' => $subject->id,
            'teacher_id' => $teacherProfile->id,
            'grade_level_id' => $gradeLevel->id,
            'title' => 'Advanced Physics 101',
            'slug' => 'adv-physics-101',
            'is_active' => true,
        ]);

        $session1 = CourseSession::create(['course_id' => $course->id, 'title' => 'Session 1: Motion', 'sort_order' => 1]);
        $assignment1 = Assignment::create(['course_session_id' => $session1->id, 'title' => 'HW 1: Kinematics', 'passing_grade' => 70]);

        // 6. Student Enrolls in Course via Ajax
        $this->actingAs($studentUser);
        $enrollResponse = $this->postJson("/ajax/courses/{$course->id}/enroll");
        $enrollResponse->assertStatus(201)->assertJsonPath('success', true);

        // 7. Student Submits Assignment
        $submissionResponse = $this->postJson('/ajax/assignments/submit', [
            'course_id' => $course->id,
            'assignment_id' => $assignment1->id,
            'content' => 'Calculated velocity = 25 m/s',
        ]);
        $submissionResponse->assertStatus(201)->assertJsonPath('success', true);
        $submissionId = $submissionResponse->json('submission_id');

        // 8. Teacher Grades Submission via Ajax
        $this->actingAs($teacherUser);
        $gradeResponse = $this->postJson("/ajax/submissions/{$submissionId}/grade", [
            'grade' => 95,
            'teacher_notes' => 'Excellent work and steps!',
        ]);
        $gradeResponse->assertStatus(200)->assertJsonPath('success', true);

        // 9. Parent Tracks Linked Student Progress via Ajax
        $this->actingAs($parentUser);
        $parentProgressResponse = $this->getJson("/ajax/parent/student/{$studentUser->id}/progress");
        $parentProgressResponse->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('enrollments_count', 1);
    }
}
