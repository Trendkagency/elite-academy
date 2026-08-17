<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\GradeLevel;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoursesMenuAndPaginationTest extends TestCase
{
    use RefreshDatabase;

    public function test_navbar_contains_courses_link_and_courses_page_is_paginated(): void
    {
        $grade = GradeLevel::create(['name' => 'Grade 12', 'slug' => 'g12']);
        $cat = Category::create(['name' => 'Science', 'slug' => 'science']);
        $subject = Subject::create(['category_id' => $cat->id, 'name' => 'Physics', 'slug' => 'physics']);
        $teacherUser = User::create(['name' => 'Dr. Teacher', 'email' => 't.nav@elite.edu', 'password' => bcrypt('password')]);
        $teacher = TeacherProfile::create(['user_id' => $teacherUser->id, 'slug' => 'tnav']);

        // Create 8 courses to trigger pagination (limit is 6 per page)
        for ($i = 1; $i <= 8; $i++) {
            Course::create([
                'subject_id' => $subject->id,
                'teacher_id' => $teacher->id,
                'grade_level_id' => $grade->id,
                'title' => "Paginated Course {$i}",
                'slug' => "paginated-course-{$i}",
                'is_active' => true,
            ]);
        }

        // Test Navbar contains Courses
        $homePage = $this->get('/');
        $homePage->assertStatus(200)
            ->assertSee('Courses');

        // Test Courses Page contains pagination (6 items per page)
        $coursesPage = $this->get('/courses');
        $coursesPage->assertStatus(200)
            ->assertSee('Paginated Course 1')
            ->assertSee('Paginated Course 6')
            ->assertDontSee('Paginated Course 7'); // Page 1 should not have item 7

        // Test Page 2
        $page2 = $this->get('/courses?page=2');
        $page2->assertStatus(200)
            ->assertSee('Paginated Course 7')
            ->assertSee('Paginated Course 8');
    }
}
