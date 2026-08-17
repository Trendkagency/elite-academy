<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseSession;
use App\Models\GradeLevel;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseTimerAndTimelineTest extends TestCase
{
    use RefreshDatabase;

    public function test_course_details_renders_countdown_timer_and_curriculum_timeline(): void
    {
        $grade = GradeLevel::create(['name' => 'Grade 12', 'slug' => 'g12']);
        $cat = Category::create(['name' => 'Science', 'slug' => 'science']);
        $subject = Subject::create(['category_id' => $cat->id, 'name' => 'Physics', 'slug' => 'physics']);

        $teacherUser = User::create(['name' => 'Dr. Lecturer', 'email' => 'lecturer@elite.edu', 'password' => bcrypt('password')]);
        $teacher = TeacherProfile::create(['user_id' => $teacherUser->id, 'slug' => 'tlecturer']);

        $course = Course::create([
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'grade_level_id' => $grade->id,
            'title' => 'Quantum Physics Masterclass',
            'slug' => 'quantum-physics-masterclass',
            'is_active' => true,
        ]);

        CourseSession::create([
            'course_id' => $course->id,
            'title' => 'Intro to Wave Functions',
            'sort_order' => 1,
            'duration_minutes' => 60,
            'is_free_demo' => true,
        ]);

        $response = $this->get('/course-details/'.$course->slug);

        $response->assertStatus(200)
            ->assertSee('Live Cohort Start Timer')
            ->assertSee('MODULE TIMELINE ROADMAP')
            ->assertSee('Module Lifetime Roadmap')
            ->assertSee('Intro to Wave Functions');
    }
}
