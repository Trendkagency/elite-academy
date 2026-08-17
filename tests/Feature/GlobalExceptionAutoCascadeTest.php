<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\GradeLevel;
use App\Models\LiveSession;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GlobalExceptionAutoCascadeTest extends TestCase
{
    use RefreshDatabase;

    public function test_global_exception_automatically_generates_exceptions_for_all_enrolled_courses(): void
    {
        $grade = GradeLevel::create(['name' => 'Grade 12', 'slug' => 'g12']);
        $cat = Category::create(['name' => 'Science', 'slug' => 'science']);
        $subject = Subject::create(['category_id' => $cat->id, 'name' => 'Physics', 'slug' => 'physics']);

        $teacherUser = User::create(['name' => 'Teacher Cascade', 'email' => 't.cascade@elite.edu', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        $teacher = TeacherProfile::create(['user_id' => $teacherUser->id, 'slug' => 'tcascade']);

        $student = User::create(['name' => 'Global Student', 'email' => 'student.cascade@elite.edu', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);

        // Create 3 Courses and Enroll Student
        $c1 = Course::create(['subject_id' => $subject->id, 'teacher_id' => $teacher->id, 'grade_level_id' => $grade->id, 'title' => 'Course 1', 'slug' => 'c1', 'is_active' => true]);
        $c2 = Course::create(['subject_id' => $subject->id, 'teacher_id' => $teacher->id, 'grade_level_id' => $grade->id, 'title' => 'Course 2', 'slug' => 'c2', 'is_active' => true]);
        $c3 = Course::create(['subject_id' => $subject->id, 'teacher_id' => $teacher->id, 'grade_level_id' => $grade->id, 'title' => 'Course 3', 'slug' => 'c3', 'is_active' => true]);

        CourseEnrollment::create(['student_user_id' => $student->id, 'course_id' => $c1->id, 'status' => 'active', 'enrolled_at' => now()]);
        CourseEnrollment::create(['student_user_id' => $student->id, 'course_id' => $c2->id, 'status' => 'active', 'enrolled_at' => now()]);
        CourseEnrollment::create(['student_user_id' => $student->id, 'course_id' => $c3->id, 'status' => 'active', 'enrolled_at' => now()]);

        $liveSession = LiveSession::create([
            'student_user_id' => $student->id,
            'teacher_profile_id' => $teacher->id,
            'subject_id' => $subject->id,
            'scheduled_at' => now()->addHours(5),
        ]);

        $this->actingAs($student);

        // Submit Global Exception Request via API
        $response = $this->postJson('/ajax/exceptions/submit', [
            'live_session_id' => $liveSession->id,
            'scope' => 'global',
            'is_global' => true,
            'reason' => 'Global System Emergency Excuse covering all enrolled courses.',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true);

        // Verify exception records generated for all 3 courses
        $this->assertDatabaseHas('exception_requests', [
            'student_user_id' => $student->id,
            'course_id' => $c1->id,
            'is_global' => true,
        ]);

        $this->assertDatabaseHas('exception_requests', [
            'student_user_id' => $student->id,
            'course_id' => $c2->id,
            'is_global' => true,
        ]);

        $this->assertDatabaseHas('exception_requests', [
            'student_user_id' => $student->id,
            'course_id' => $c3->id,
            'is_global' => true,
        ]);
    }
}
