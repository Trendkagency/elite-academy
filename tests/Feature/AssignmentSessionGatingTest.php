<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Enums\SessionProgressStatus;
use App\Enums\SubmissionStatus;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseSession;
use App\Models\CourseSessionProgress;
use App\Models\GradeLevel;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssignmentSessionGatingTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_cannot_access_next_session_if_previous_assignment_not_submitted(): void
    {
        $grade = GradeLevel::create(['name' => 'Grade 12', 'slug' => 'g12']);
        $cat = Category::create(['name' => 'Science', 'slug' => 'science']);
        $subject = Subject::create(['category_id' => $cat->id, 'name' => 'Physics', 'slug' => 'physics']);

        $teacherUser = User::create([
            'name' => 'Teacher User',
            'email' => 'teacher.gate@elite.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        $teacher = TeacherProfile::create(['user_id' => $teacherUser->id, 'slug' => 'tgate']);

        $course = Course::create([
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'grade_level_id' => $grade->id,
            'title' => 'Gated Course',
            'slug' => 'gated-course',
            'is_active' => true,
        ]);

        $session1 = CourseSession::create([
            'course_id' => $course->id,
            'title' => 'Session 1',
            'sort_order' => 1,
            'duration_minutes' => 60,
            'is_free_demo' => false,
        ]);

        $session2 = CourseSession::create([
            'course_id' => $course->id,
            'title' => 'Session 2',
            'sort_order' => 2,
            'duration_minutes' => 60,
            'is_free_demo' => false,
        ]);

        // Assignment for Session 1
        $assignment = Assignment::create([
            'course_session_id' => $session1->id,
            'title' => 'Session 1 Homework',
            'passing_grade' => 70,
            'status' => 'published',
        ]);

        $student = User::create([
            'name' => 'Student Gate User',
            'email' => 'student.gate@elite.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        \App\Models\StudentProfile::create(['user_id' => $student->id]);

        $enrollment = CourseEnrollment::create([
            'student_user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'active',
        ]);

        CourseSessionProgress::create([
            'course_enrollment_id' => $enrollment->id,
            'course_session_id' => $session2->id,
            'status' => SessionProgressStatus::UNLOCKED,
        ]);

        $this->actingAs($student);

        // Session 2 should be DENIED because Session 1 assignment is not submitted
        $response = $this->getJson("/ajax/sessions/{$session2->id}/access");
        $response->assertStatus(403)
            ->assertJson(['can_access' => false]);

        // Submit Session 1 assignment
        AssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'student_user_id' => $student->id,
            'course_enrollment_id' => $enrollment->id,
            'status' => SubmissionStatus::COMPLETED->value,
            'grade' => 90,
        ]);

        // Session 2 should now be ACCESSIBLE
        $response2 = $this->getJson("/ajax/sessions/{$session2->id}/access");
        $response2->assertStatus(200)
            ->assertJson(['can_access' => true]);
    }
}
