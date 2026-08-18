<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\Category;
use App\Models\Course;
use App\Models\GradeLevel;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrolledCoursesVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_cannot_re_enroll_in_already_enrolled_course(): void
    {
        $grade = GradeLevel::create(['name' => 'Grade 11', 'slug' => 'g11']);
        $cat = Category::create(['name' => 'Tech', 'slug' => 'tech']);
        $subject = Subject::create(['category_id' => $cat->id, 'name' => 'Coding', 'slug' => 'coding']);

        $teacherUser = User::create(['name' => 'Teacher', 'email' => 't.reenroll@elite.edu', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        $teacher = TeacherProfile::create(['user_id' => $teacherUser->id, 'slug' => 'treenroll']);

        $course = Course::create([
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'grade_level_id' => $grade->id,
            'title' => 'Python Fundamentals',
            'slug' => 'python-fundamentals',
            'is_active' => true,
        ]);

        $student = User::create(['name' => 'Reenroll Student', 'email' => 'student.reenroll@elite.edu', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        \App\Models\StudentPackage::create(['student_user_id' => $student->id, 'total_sessions' => 12, 'used_sessions' => 0, 'remaining_sessions' => 12, 'status' => 'active', 'activated_at' => now()]);
        $this->actingAs($student);

        // 1st enrollment request
        $res1 = $this->postJson("/ajax/courses/{$course->id}/enroll");
        $res1->assertStatus(201)
            ->assertJsonPath('success', true);

        // 2nd enrollment request (attempting duplicate enrollment)
        $res2 = $this->postJson("/ajax/courses/{$course->id}/enroll");
        $res2->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('already_enrolled', true);

        // Catalog UI should indicate already enrolled
        $coursesPage = $this->get('/courses');
        $coursesPage->assertStatus(200)
            ->assertSee('Go to Portal');

        // Course details UI should show Already Enrolled button
        $detailsPage = $this->get('/course-details/'.$course->slug);
        $detailsPage->assertStatus(200)
            ->assertSee('Enrolled Course');
    }
}
