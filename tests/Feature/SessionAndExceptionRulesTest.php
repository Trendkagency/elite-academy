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
use App\Models\LiveSession;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SessionAndExceptionRulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_cannot_submit_excuse_less_than_2_hours_before_session(): void
    {
        $category = Category::create(['name' => 'Cat', 'slug' => 'cat']);
        $subject = Subject::create(['category_id' => $category->id, 'name' => 'Sub', 'slug' => 'sub']);
        $studentUser = User::create(['name' => 'Student', 'email' => 's1@test.com', 'password' => bcrypt('p'), 'status' => AccountStatus::APPROVED]);
        $teacherUser = User::create(['name' => 'Teacher', 'email' => 't1@test.com', 'password' => bcrypt('p'), 'status' => AccountStatus::APPROVED]);
        $teacherProfile = TeacherProfile::create(['user_id' => $teacherUser->id, 'slug' => 't1']);

        // Live Session scheduled in 1 hour (less than 2 hours cutoff)
        $liveSession = LiveSession::create([
            'student_user_id' => $studentUser->id,
            'teacher_profile_id' => $teacherProfile->id,
            'subject_id' => $subject->id,
            'scheduled_at' => now()->addHour(),
        ]);

        $this->actingAs($studentUser);

        $response = $this->postJson('/ajax/exceptions/submit', [
            'live_session_id' => $liveSession->id,
            'reason' => 'Feeling unwell today and cannot attend.',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false);

        $this->assertDatabaseMissing('exception_requests', [
            'student_user_id' => $studentUser->id,
            'live_session_id' => $liveSession->id,
        ]);
    }

    public function test_student_can_submit_excuse_more_than_2_hours_before_session(): void
    {
        $category = Category::create(['name' => 'Cat', 'slug' => 'cat']);
        $subject = Subject::create(['category_id' => $category->id, 'name' => 'Sub', 'slug' => 'sub']);
        $studentUser = User::create(['name' => 'Student', 'email' => 's2@test.com', 'password' => bcrypt('p'), 'status' => AccountStatus::APPROVED]);
        $teacherUser = User::create(['name' => 'Teacher', 'email' => 't2@test.com', 'password' => bcrypt('p'), 'status' => AccountStatus::APPROVED]);
        $teacherProfile = TeacherProfile::create(['user_id' => $teacherUser->id, 'slug' => 't2']);

        // Live Session scheduled in 5 hours (greater than 2 hours cutoff)
        $liveSession = LiveSession::create([
            'student_user_id' => $studentUser->id,
            'teacher_profile_id' => $teacherProfile->id,
            'subject_id' => $subject->id,
            'scheduled_at' => now()->addHours(5),
        ]);

        $this->actingAs($studentUser);

        $response = $this->postJson('/ajax/exceptions/submit', [
            'live_session_id' => $liveSession->id,
            'reason' => 'Family event scheduled ahead of time.',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('exception_requests', [
            'student_user_id' => $studentUser->id,
            'live_session_id' => $liveSession->id,
        ]);
    }

    public function test_student_cannot_start_session_if_previous_assignment_not_completed(): void
    {
        $grade = GradeLevel::create(['name' => 'HS', 'slug' => 'hs']);
        $studentUser = User::create(['name' => 'Student', 'email' => 's3@test.com', 'password' => bcrypt('p'), 'status' => AccountStatus::APPROVED]);
        $teacherUser = User::create(['name' => 'Teacher', 'email' => 't3@test.com', 'password' => bcrypt('p'), 'status' => AccountStatus::APPROVED]);
        $teacherProfile = TeacherProfile::create(['user_id' => $teacherUser->id, 'slug' => 't3']);

        $category = Category::create(['name' => 'Cat', 'slug' => 'cat']);
        $subject = Subject::create(['category_id' => $category->id, 'name' => 'Sub', 'slug' => 'sub']);
        $course = Course::create([
            'subject_id' => $subject->id,
            'teacher_id' => $teacherProfile->id,
            'grade_level_id' => $grade->id,
            'title' => 'Course 1',
            'slug' => 'c1',
            'is_active' => true,
        ]);

        $enrollment = CourseEnrollment::create(['student_user_id' => $studentUser->id, 'course_id' => $course->id, 'status' => 'active']);

        $session1 = CourseSession::create(['course_id' => $course->id, 'title' => 'Session 1', 'sort_order' => 1]);
        $session2 = CourseSession::create(['course_id' => $course->id, 'title' => 'Session 2', 'sort_order' => 2]);
        $assignment1 = Assignment::create(['course_session_id' => $session1->id, 'title' => 'HW 1', 'passing_grade' => 70]);

        CourseSessionProgress::create(['course_enrollment_id' => $enrollment->id, 'course_session_id' => $session1->id, 'status' => SessionProgressStatus::COMPLETED]);
        CourseSessionProgress::create(['course_enrollment_id' => $enrollment->id, 'course_session_id' => $session2->id, 'status' => SessionProgressStatus::UNLOCKED]);

        $this->actingAs($studentUser);

        // Attempting to access Session 2 WITHOUT completing Session 1 Homework
        $response = $this->getJson("/ajax/sessions/{$session2->id}/access");

        $response->assertStatus(403)
            ->assertJsonPath('can_access', false);
    }

    public function test_student_can_start_session_after_completing_previous_assignment(): void
    {
        $grade = GradeLevel::create(['name' => 'HS', 'slug' => 'hs']);
        $studentUser = User::create(['name' => 'Student', 'email' => 's4@test.com', 'password' => bcrypt('p'), 'status' => AccountStatus::APPROVED]);
        $teacherUser = User::create(['name' => 'Teacher', 'email' => 't4@test.com', 'password' => bcrypt('p'), 'status' => AccountStatus::APPROVED]);
        $teacherProfile = TeacherProfile::create(['user_id' => $teacherUser->id, 'slug' => 't4']);

        $category = Category::create(['name' => 'Cat', 'slug' => 'cat']);
        $subject = Subject::create(['category_id' => $category->id, 'name' => 'Sub', 'slug' => 'sub']);
        $course = Course::create([
            'subject_id' => $subject->id,
            'teacher_id' => $teacherProfile->id,
            'grade_level_id' => $grade->id,
            'title' => 'Course 2',
            'slug' => 'c2',
            'is_active' => true,
        ]);

        $enrollment = CourseEnrollment::create(['student_user_id' => $studentUser->id, 'course_id' => $course->id, 'status' => 'active']);

        $session1 = CourseSession::create(['course_id' => $course->id, 'title' => 'Session 1', 'sort_order' => 1]);
        $session2 = CourseSession::create(['course_id' => $course->id, 'title' => 'Session 2', 'sort_order' => 2]);
        $assignment1 = Assignment::create(['course_session_id' => $session1->id, 'title' => 'HW 1', 'passing_grade' => 70]);

        CourseSessionProgress::create(['course_enrollment_id' => $enrollment->id, 'course_session_id' => $session1->id, 'status' => SessionProgressStatus::COMPLETED]);
        CourseSessionProgress::create(['course_enrollment_id' => $enrollment->id, 'course_session_id' => $session2->id, 'status' => SessionProgressStatus::UNLOCKED]);

        // Student completes Assignment 1
        AssignmentSubmission::create([
            'assignment_id' => $assignment1->id,
            'student_user_id' => $studentUser->id,
            'course_enrollment_id' => $enrollment->id,
            'status' => SubmissionStatus::COMPLETED->value,
            'grade' => 90,
        ]);

        $this->actingAs($studentUser);

        // Attempting to access Session 2 AFTER completing Session 1 Homework
        $response = $this->getJson("/ajax/sessions/{$session2->id}/access");

        $response->assertStatus(200)
            ->assertJsonPath('can_access', true);
    }
}
