<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Course;
use App\Models\GradeLevel;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_published_courses(): void
    {
        $grade = GradeLevel::create(['name' => 'G12', 'slug' => 'g12']);
        $teacherUser = User::create(['name' => 'Teacher', 'email' => 't@test.com', 'password' => bcrypt('p')]);
        $teacher = TeacherProfile::create(['user_id' => $teacherUser->id, 'slug' => 't-test']);
        $category = Category::create(['name' => 'Cat', 'slug' => 'cat']);
        $subject = Subject::create(['category_id' => $category->id, 'name' => 'Sub', 'slug' => 'sub']);

        Course::create([
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'grade_level_id' => $grade->id,
            'title' => 'Sample Course',
            'slug' => 'sample-course',
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/courses');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Sample Course');
    }
}
