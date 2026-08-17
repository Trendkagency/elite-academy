<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
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

class HomeworkAndExamSubmissionsFullIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_homework_submission_and_admin_grading_flow(): void
    {
        $grade = GradeLevel::create(['name' => 'Grade 12', 'slug' => 'g12']);
        $cat = Category::create(['name' => 'Science', 'slug' => 'science']);
        $subject = Subject::create(['category_id' => $cat->id, 'name' => 'Physics', 'slug' => 'physics']);

        $teacherUser = User::create(['name' => 'Dr. Teacher', 'email' => 't.hw@elite.edu', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        $teacher = TeacherProfile::create(['user_id' => $teacherUser->id, 'slug' => 'thw']);

        $student = User::create(['name' => 'Student Homework', 'email' => 'student.hw@elite.edu', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);

        $course = Course::create([
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'grade_level_id' => $grade->id,
            'title' => 'Physics Electromagnetism',
            'slug' => 'physics-electromagnetism',
            'is_active' => true,
        ]);

        $session = CourseSession::create([
            'course_id' => $course->id,
            'title' => 'Session 1: Circuits',
            'sort_order' => 1,
            'duration_minutes' => 60,
        ]);

        $assignment = Assignment::create([
            'course_session_id' => $session->id,
            'title' => 'Kirchhoff Homework 1',
            'passing_grade' => 70,
            'is_published' => true,
        ]);

        $enrollment = CourseEnrollment::create([
            'student_user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'active',
            'enrolled_at' => now(),
        ]);

        $this->actingAs($student);

        // 1. Student Submits Assignment
        $submitRes = $this->postJson('/ajax/assignments/submit', [
            'assignment_id' => $assignment->id,
            'course_id' => $course->id,
            'content' => 'Solution details for Kirchhoff networks calculation.',
        ]);

        $submitRes->assertStatus(201)
            ->assertJsonPath('success', true);

        $submission = AssignmentSubmission::where('student_user_id', $student->id)
            ->where('assignment_id', $assignment->id)
            ->first();

        $this->assertNotNull($submission);
        $this->assertEquals(SubmissionStatus::SUBMITTED, $submission->status);

        // 2. Admin / Teacher Grades Submission
        $submission->update([
            'grade' => 95,
            'status' => SubmissionStatus::COMPLETED->value,
            'teacher_notes' => 'Excellent work and precise steps!',
            'reviewed_at' => now(),
        ]);

        $this->assertEquals(95, $submission->fresh()->grade);
        $this->assertEquals(SubmissionStatus::COMPLETED->value, $submission->fresh()->status->value);
    }
}
