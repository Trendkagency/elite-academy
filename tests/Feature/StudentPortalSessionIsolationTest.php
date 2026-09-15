<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\LiveSession;
use App\Models\StudentPackage;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentPortalSessionIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_does_not_see_another_students_sessions_on_the_same_course(): void
    {
        $category = Category::create(['name' => 'Science', 'slug' => 'science-iso']);
        $subject = Subject::create(['category_id' => $category->id, 'name' => 'Physics', 'slug' => 'physics-iso']);

        $teacherUser = User::create([
            'name' => 'Dr Isolation Teacher',
            'email' => 'teacher.iso@elite.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        $teacher = TeacherProfile::create(['user_id' => $teacherUser->id, 'slug' => 'teacher-iso']);

        $course = Course::create([
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'title' => 'Shared STEM Course',
            'slug' => 'shared-stem-iso',
            'is_active' => true,
        ]);

        $owner = User::create([
            'name' => 'Malek Isolation',
            'email' => 'malek.iso@elite.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        StudentProfile::create(['user_id' => $owner->id]);

        $viewer = User::create([
            'name' => 'Manal Isolation',
            'email' => 'manal.iso@elite.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        StudentProfile::create(['user_id' => $viewer->id]);

        CourseEnrollment::create(['student_user_id' => $owner->id, 'course_id' => $course->id]);
        CourseEnrollment::create(['student_user_id' => $viewer->id, 'course_id' => $course->id]);

        StudentPackage::create([
            'student_user_id' => $viewer->id,
            'total_sessions' => 10,
            'used_sessions' => 0,
            'remaining_sessions' => 10,
            'status' => 'active',
            'activated_at' => now(),
        ]);

        LiveSession::create([
            'title' => 'Malek Isolation - Student #' . $owner->id,
            'student_user_id' => $owner->id,
            'teacher_profile_id' => $teacher->id,
            'subject_id' => $subject->id,
            'course_id' => $course->id,
            'scheduled_at' => now()->addDay(),
            'start_at' => now()->addDay(),
            'end_at' => now()->addDay()->addHour(),
            'duration_minutes' => 60,
            'status' => 'scheduled',
        ]);

        LiveSession::create([
            'title' => 'Manal Isolation Private Lesson',
            'student_user_id' => $viewer->id,
            'teacher_profile_id' => $teacher->id,
            'subject_id' => $subject->id,
            'course_id' => $course->id,
            'scheduled_at' => now()->addDays(2),
            'start_at' => now()->addDays(2),
            'end_at' => now()->addDays(2)->addHour(),
            'duration_minutes' => 60,
            'status' => 'scheduled',
        ]);

        $response = $this->actingAs($viewer)->get('/student-portal');

        $response->assertOk()
            ->assertSee('Manal Isolation')
            ->assertSee('Manal Isolation Private Lesson')
            ->assertDontSee('Malek Isolation');
    }
}
