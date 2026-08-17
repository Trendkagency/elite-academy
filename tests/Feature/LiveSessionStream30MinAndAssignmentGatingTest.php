<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseSession;
use App\Models\ExceptionRequest;
use App\Models\GradeLevel;
use App\Models\LiveSession;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LiveSessionStream30MinAndAssignmentGatingTest extends TestCase
{
    use RefreshDatabase;

    public function test_live_session_gated_by_30_mins_and_assignment_or_exception(): void
    {
        $grade = GradeLevel::create(['name' => 'Grade 12', 'slug' => 'g12', 'sort_order' => 1]);
        $student = User::create(['name' => 'Gated Student', 'email' => 'gated.student@elite.edu', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        $teacher = User::create(['name' => 'Dr. Teacher', 'email' => 'dr.teacher@elite.edu', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        $teacherProf = TeacherProfile::create(['user_id' => $teacher->id, 'slug' => 'dr-teacher']);
        $cat = Category::create(['name' => 'Sciences', 'slug' => 'sciences']);
        $subject = Subject::create(['name' => 'Physics', 'slug' => 'physics', 'category_id' => $cat->id]);

        $course = Course::create(['title' => 'Physics 101', 'slug' => 'physics-101', 'subject_id' => $subject->id, 'teacher_id' => $teacherProf->id, 'grade_level_id' => $grade->id]);
        $enrollment = CourseEnrollment::create(['student_user_id' => $student->id, 'course_id' => $course->id, 'status' => 'active']);

        $session1 = CourseSession::create(['course_id' => $course->id, 'sort_order' => 1, 'title' => 'Session 1', 'duration_minutes' => 60]);
        $assignment1 = Assignment::create(['course_session_id' => $session1->id, 'title' => 'Assignment 1', 'passing_grade' => 70, 'status' => 'published']);

        // Case 1: Live session 2 hours away -> Time Gated (>30 mins)
        $liveSessionFuture = LiveSession::create([
            'student_user_id' => $student->id,
            'teacher_profile_id' => $teacherProf->id,
            'subject_id' => $subject->id,
            'course_id' => $course->id,
            'scheduled_at' => now()->addHours(2),
            'meeting_link' => 'https://meet.google.com/test-link',
        ]);

        $res1 = $liveSessionFuture->canStudentAccessStream($student);
        $this->assertFalse($res1['can_access']);
        $this->assertEquals('time_gated', $res1['reason']);

        // Case 2: Live session 15 mins away (<=30 mins), BUT assignment not submitted -> Assignment Required
        $liveSessionSoon = LiveSession::create([
            'student_user_id' => $student->id,
            'teacher_profile_id' => $teacherProf->id,
            'subject_id' => $subject->id,
            'course_id' => $course->id,
            'scheduled_at' => now()->addMinutes(15),
            'meeting_link' => 'https://meet.google.com/test-link-soon',
        ]);

        $res2 = $liveSessionSoon->canStudentAccessStream($student);
        $this->assertFalse($res2['can_access']);
        $this->assertEquals('assignment_required', $res2['reason']);

        // Case 3: Student submits assignment -> Unlocked!
        AssignmentSubmission::create([
            'assignment_id' => $assignment1->id,
            'student_user_id' => $student->id,
            'course_enrollment_id' => $enrollment->id,
            'status' => 'completed',
            'grade' => 90,
        ]);

        $res3 = $liveSessionSoon->canStudentAccessStream($student);
        $this->assertTrue($res3['can_access']);
        $this->assertEquals('https://meet.google.com/test-link-soon', $res3['meeting_link']);

        // Case 4: Another student without assignment submission BUT with approved Exception Request -> Unlocked!
        $student2 = User::create(['name' => 'Exception Student', 'email' => 'exception.student@elite.edu', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        $enrollment2 = CourseEnrollment::create(['student_user_id' => $student2->id, 'course_id' => $course->id, 'status' => 'active']);

        $res4Before = $liveSessionSoon->canStudentAccessStream($student2);
        $this->assertFalse($res4Before['can_access']);

        ExceptionRequest::create([
            'student_user_id' => $student2->id,
            'course_id' => $course->id,
            'scope' => 'course',
            'reason' => 'Technical issue',
            'status' => 'approved',
        ]);

        $res4After = $liveSessionSoon->canStudentAccessStream($student2);
        $this->assertTrue($res4After['can_access']);
    }
}
