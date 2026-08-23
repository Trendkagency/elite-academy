<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\Assignment;
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

class CourseVsGlobalExceptionLogicTest extends TestCase
{
    use RefreshDatabase;

    public function test_course_specific_and_global_exception_approval_logic(): void
    {
        $grade = GradeLevel::create(['name' => 'Grade 12', 'slug' => 'g12']);
        $cat = Category::create(['name' => 'Science', 'slug' => 'science']);
        $subject = Subject::create(['category_id' => $cat->id, 'name' => 'Physics', 'slug' => 'physics']);

        $teacherUser = User::create(['name' => 'Teacher', 'email' => 't.scope@elite.edu', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        $teacher = TeacherProfile::create(['user_id' => $teacherUser->id, 'slug' => 'tscope']);

        $student = User::create(['name' => 'Student Exception', 'email' => 'student.scope@elite.edu', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        \App\Models\StudentProfile::create(['user_id' => $student->id]);

        // Course A
        $courseA = Course::create([
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'grade_level_id' => $grade->id,
            'title' => 'Course A Physics',
            'slug' => 'course-a-physics',
            'is_active' => true,
        ]);
        $sessionA1 = CourseSession::create(['course_id' => $courseA->id, 'title' => 'Session A1', 'sort_order' => 1]);
        $sessionA2 = CourseSession::create(['course_id' => $courseA->id, 'title' => 'Session A2', 'sort_order' => 2]);
        Assignment::create(['course_session_id' => $sessionA1->id, 'title' => 'Homework A1', 'passing_grade' => 70, 'is_published' => true]);

        // Course B
        $courseB = Course::create([
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'grade_level_id' => $grade->id,
            'title' => 'Course B Chemistry',
            'slug' => 'course-b-chemistry',
            'is_active' => true,
        ]);
        $sessionB1 = CourseSession::create(['course_id' => $courseB->id, 'title' => 'Session B1', 'sort_order' => 1]);
        $sessionB2 = CourseSession::create(['course_id' => $courseB->id, 'title' => 'Session B2', 'sort_order' => 2]);
        Assignment::create(['course_session_id' => $sessionB1->id, 'title' => 'Homework B1', 'passing_grade' => 70, 'is_published' => true]);

        CourseEnrollment::create(['student_user_id' => $student->id, 'course_id' => $courseA->id, 'status' => 'active', 'enrolled_at' => now()]);
        CourseEnrollment::create(['student_user_id' => $student->id, 'course_id' => $courseB->id, 'status' => 'active', 'enrolled_at' => now()]);

        $liveSession = LiveSession::create([
            'student_user_id' => $student->id,
            'teacher_profile_id' => $teacher->id,
            'subject_id' => $subject->id,
            'scheduled_at' => now(),
        ]);

        $this->actingAs($student);

        // Initially, student CANNOT access Session A2 without Homework A1
        $resA2Init = $this->getJson("/ajax/sessions/{$sessionA2->id}/access");
        $resA2Init->assertStatus(403);

        // 1. Create Course-Specific Approved Exception for Course A
        $excCourseA = ExceptionRequest::create([
            'student_user_id' => $student->id,
            'live_session_id' => $liveSession->id,
            'course_id' => $courseA->id,
            'is_global' => false,
            'scope' => 'course',
            'reason' => 'Single Course Exemption Granted for Course A',
            'status' => 'approved',
        ]);

        // Student can now access Session A2
        $resA2After = $this->getJson("/ajax/sessions/{$sessionA2->id}/access");
        $resA2After->assertStatus(200)->assertJsonPath('can_access', true);

        // Student STILL CANNOT access Session B2 (Course B exception not granted)
        $resB2 = $this->getJson("/ajax/sessions/{$sessionB2->id}/access");
        $resB2->assertStatus(403);

        // 2. Grant Global Exception
        $excGlobal = ExceptionRequest::create([
            'student_user_id' => $student->id,
            'live_session_id' => $liveSession->id,
            'is_global' => true,
            'scope' => 'global',
            'reason' => 'Global Exception Granted for All Courses',
            'status' => 'approved',
        ]);

        // Student can now access Session B2 as well
        $resB2AfterGlobal = $this->getJson("/ajax/sessions/{$sessionB2->id}/access");
        $resB2AfterGlobal->assertStatus(200)->assertJsonPath('can_access', true);
    }
}
