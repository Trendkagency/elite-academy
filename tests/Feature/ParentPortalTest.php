<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseEnrollment;
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

class ParentPortalTest extends TestCase
{
    use RefreshDatabase;

    protected User $parentUser;
    protected User $studentUser;
    protected User $unlinkedStudentUser;

    protected function setUp(): void
    {
        parent::setUp();

        $category = Category::create(['name' => 'Sciences', 'slug' => 'sciences', 'sort_order' => 1]);
        $subject = Subject::create(['category_id' => $category->id, 'name' => 'Physics', 'slug' => 'physics', 'is_active' => true]);

        $teacherUser = User::create(['name' => 'Teacher Dr. Ahmed', 'email' => 'teacher@elite.edu', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        $teacherProfile = TeacherProfile::create(['user_id' => $teacherUser->id, 'title' => 'Dr.', 'slug' => 'dr-ahmed']);

        $course = Course::create(['subject_id' => $subject->id, 'teacher_id' => $teacherProfile->id, 'title' => 'Physics Secondary Track', 'slug' => 'physics-track', 'is_active' => true]);

        // Student 1 (Linked)
        $this->studentUser = User::create(['name' => 'Student Youssef', 'email' => 'youssef@student.com', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        StudentProfile::create(['user_id' => $this->studentUser->id, 'school_name' => 'STEM Cairo']);
        CourseEnrollment::create(['student_user_id' => $this->studentUser->id, 'course_id' => $course->id, 'status' => 'active']);

        // Student 2 (Unlinked)
        $this->unlinkedStudentUser = User::create(['name' => 'Student Kareem', 'email' => 'kareem@student.com', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        StudentProfile::create(['user_id' => $this->unlinkedStudentUser->id, 'school_name' => 'STEM Alexandria']);

        // Parent
        $this->parentUser = User::create(['name' => 'Parent Mr. Mahmoud', 'email' => 'parent@family.com', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        $parentProfile = ParentProfile::create(['user_id' => $this->parentUser->id]);

        // Link Parent -> Student 1
        DB::table('parent_student')->insert([
            'parent_user_id' => $this->parentUser->id,
            'student_user_id' => $this->studentUser->id,
        ]);

        // Create Package, Session & Submission for Student 1
        StudentPackage::create([
            'student_user_id' => $this->studentUser->id,
            'total_sessions' => 12,
            'used_sessions' => 4,
            'remaining_sessions' => 8,
            'status' => 'active',
        ]);

        LiveSession::create([
            'teacher_profile_id' => $teacherProfile->id,
            'course_id' => $course->id,
            'title' => 'Physics Live Stream #3',
            'scheduled_at' => now()->addDay(),
            'status' => 'scheduled',
        ]);

        $assignment = Assignment::create([
            'course_id' => $course->id,
            'teacher_profile_id' => $teacherProfile->id,
            'title' => 'Kirchhoff Laws Assignment',
            'due_at' => now()->addDays(2),
            'status' => 'published',
        ]);

        AssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'student_user_id' => $this->studentUser->id,
            'submitted_at' => now(),
            'grade' => 95,
            'status' => \App\Enums\SubmissionStatus::REVIEWED,
        ]);
    }

    public function test_parent_portal_view_renders_linked_children(): void
    {
        $response = $this->actingAs($this->parentUser)->get('/parent-portal');

        $response->assertStatus(200);
        $response->assertSee('Parent Portal');
        $response->assertSee('Student Youssef');
    }

    public function test_parent_can_fetch_all_6_required_metrics_for_linked_child(): void
    {
        $response = $this->actingAs($this->parentUser)
            ->getJson("/ajax/parent/student/{$this->studentUser->id}/progress");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'is_read_only' => true,
            'student' => [
                'name' => 'Student Youssef',
            ],
            'package' => [
                'remaining_sessions' => 8,
                'total_sessions' => 12,
                'used_sessions' => 4,
            ],
            'attendance' => [
                'rate' => '93%',
            ],
        ]);

        $json = $response->json();
        $this->assertNotEmpty($json['upcoming_sessions']);
        $this->assertNotEmpty($json['submissions']);
        $this->assertNotEmpty($json['notifications']);
    }

    public function test_parent_cannot_fetch_unlinked_student_progress_idor_protection(): void
    {
        $response = $this->actingAs($this->parentUser)
            ->getJson("/ajax/parent/student/{$this->unlinkedStudentUser->id}/progress");

        $response->assertStatus(403);
        $response->assertJson(['success' => false]);
    }

    public function test_parent_can_link_new_child_using_phone_number_or_email(): void
    {
        // Unlinked Student 2 has email 'kareem@student.com'
        $response = $this->actingAs($this->parentUser)
            ->postJson('/ajax/parent/link-child', [
                'phone_or_email' => 'kareem@student.com',
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verify parent can now fetch progress for Kareem
        $responseProgress = $this->actingAs($this->parentUser)
            ->getJson("/ajax/parent/student/{$this->unlinkedStudentUser->id}/progress");

        $responseProgress->assertStatus(200);
        $responseProgress->assertJson(['success' => true]);
    }
}
