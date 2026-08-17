<?php

namespace Tests\Feature;

use App\Actions\Submission\GradeSubmissionAction;
use App\Enums\AccountStatus;
use App\Enums\SessionProgressStatus;
use App\Enums\SubmissionStatus;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseSession;
use App\Models\GradeLevel;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SequentialSessionUnlockingTest extends TestCase
{
    use RefreshDatabase;

    public function test_passing_grade_unlocks_next_session_automatically(): void
    {
        // 1. Setup Domain Entities
        $grade = GradeLevel::create(['name' => 'High School', 'slug' => 'hs']);
        $studentUser = User::create([
            'name' => 'John Student',
            'email' => 'john@test.com',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);

        $teacherUser = User::create([
            'name' => 'Jane Teacher',
            'email' => 'jane@test.com',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        $teacherProfile = TeacherProfile::create([
            'user_id' => $teacherUser->id,
            'slug' => 'jane-teacher',
            'title' => 'Physics Teacher',
        ]);

        $category = Category::create(['name' => 'Science', 'slug' => 'science']);
        $subject = Subject::create(['category_id' => $category->id, 'name' => 'Physics', 'slug' => 'physics']);
        $course = Course::create([
            'subject_id' => $subject->id,
            'teacher_id' => $teacherProfile->id,
            'grade_level_id' => $grade->id,
            'title' => 'Physics 101',
            'slug' => 'physics-101',
            'is_active' => true,
        ]);

        $enrollment = CourseEnrollment::create([
            'student_user_id' => $studentUser->id,
            'course_id' => $course->id,
            'status' => 'active',
        ]);

        $session1 = CourseSession::create([
            'course_id' => $course->id,
            'title' => 'Session 1',
            'sort_order' => 1,
        ]);
        $session2 = CourseSession::create([
            'course_id' => $course->id,
            'title' => 'Session 2',
            'sort_order' => 2,
        ]);

        $assignment1 = Assignment::create([
            'course_session_id' => $session1->id,
            'title' => 'Assignment 1',
        ]);

        $submission = AssignmentSubmission::create([
            'assignment_id' => $assignment1->id,
            'student_user_id' => $studentUser->id,
            'course_enrollment_id' => $enrollment->id,
            'status' => SubmissionStatus::SUBMITTED,
        ]);

        // Verify Session 2 is currently NOT unlocked for student
        $this->assertDatabaseMissing('course_session_progress', [
            'course_enrollment_id' => $enrollment->id,
            'course_session_id' => $session2->id,
            'status' => SessionProgressStatus::UNLOCKED->value,
        ]);

        // 2. Grade Submission with Passing Grade (85 >= 50)
        /** @var GradeSubmissionAction $action */
        $action = app(GradeSubmissionAction::class);
        $gradedSubmission = $action->execute($submission, 85, 'Great work!');

        // 3. Assertions
        $this->assertEquals(SubmissionStatus::COMPLETED, $gradedSubmission->status);
        $this->assertEquals(85, $gradedSubmission->grade);

        // Verify Session 2 is NOW unlocked in database!
        $this->assertDatabaseHas('course_session_progress', [
            'course_enrollment_id' => $enrollment->id,
            'course_session_id' => $session2->id,
            'status' => SessionProgressStatus::UNLOCKED->value,
        ]);
    }
}
