<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\AdminProfile;
use App\Models\Article;
use App\Models\Category;
use App\Models\Course;
use App\Models\GradeLevel;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryCrudAndIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::create([
            'name' => 'System Admin',
            'email' => 'admin.test@elite.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        AdminProfile::create(['user_id' => $this->adminUser->id]);
    }

    public function test_category_can_be_created_updated_and_retrieved(): void
    {
        $category = Category::create([
            'name' => 'Computer Science & AI',
            'slug' => 'cs-ai',
            'color_theme' => '#2563EB',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Computer Science & AI',
            'slug' => 'cs-ai',
            'color_theme' => '#2563EB',
            'sort_order' => 1,
            'is_active' => 1,
        ]);

        $category->update([
            'name' => 'AI & Robotics',
            'color_theme' => '#0D9488',
        ]);

        $this->assertEquals('AI & Robotics', $category->fresh()->name);
        $this->assertEquals('#0D9488', $category->fresh()->color_theme);
    }

    public function test_category_relationships_and_helper_methods(): void
    {
        $category = Category::create([
            'name' => 'Natural Sciences',
            'slug' => 'natural-sciences',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $grade = GradeLevel::create(['name' => 'Grade 12', 'slug' => 'grade-12', 'sort_order' => 1]);

        $subject1 = Subject::create([
            'category_id' => $category->id,
            'name' => 'Physics',
            'slug' => 'physics',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $subject2 = Subject::create([
            'category_id' => $category->id,
            'name' => 'Chemistry',
            'slug' => 'chemistry',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        $teacherUser = User::create([
            'name' => 'Prof. Newton',
            'email' => 'newton@elite.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        $teacher = TeacherProfile::create(['user_id' => $teacherUser->id, 'slug' => 'newton']);

        $course = Course::create([
            'subject_id' => $subject1->id,
            'grade_level_id' => $grade->id,
            'teacher_id' => $teacher->id,
            'title' => 'Quantum Mechanics 101',
            'slug' => 'quantum-101',
            'is_active' => true,
        ]);

        $this->assertCount(2, $category->subjects);
        $this->assertCount(1, $category->courses);
        $this->assertEquals(2, $category->getActiveSubjectsCount());
        $this->assertEquals(1, $category->getActiveCoursesCount());
        $this->assertEquals('Natural Sciences', $category->getLocalizedName());
    }

    public function test_category_scopes_work_correctly(): void
    {
        Category::create(['name' => 'Active Cat 2', 'slug' => 'cat-2', 'sort_order' => 2, 'is_active' => true]);
        Category::create(['name' => 'Active Cat 1', 'slug' => 'cat-1', 'sort_order' => 1, 'is_active' => true]);
        Category::create(['name' => 'Inactive Cat', 'slug' => 'cat-3', 'sort_order' => 3, 'is_active' => false]);

        $activeCategories = Category::active()->ordered()->get();
        $this->assertCount(2, $activeCategories);
        $this->assertEquals('Active Cat 1', $activeCategories->first()->name);
        $this->assertEquals('Active Cat 2', $activeCategories->last()->name);
    }

    public function test_admin_can_access_filament_categories_endpoints(): void
    {
        $category = Category::create([
            'name' => 'Languages',
            'slug' => 'languages',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $responseIndex = $this->actingAs($this->adminUser)->get('/admin/categories');
        $responseIndex->assertStatus(200);

        $responseCreate = $this->actingAs($this->adminUser)->get('/admin/categories/create');
        $responseCreate->assertStatus(200);

        $responseEdit = $this->actingAs($this->adminUser)->get("/admin/categories/{$category->id}/edit");
        $responseEdit->assertStatus(200);
    }

    public function test_subject_belongs_to_created_category(): void
    {
        $category = Category::create([
            'name' => 'Mathematics',
            'slug' => 'math',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $subject = Subject::create([
            'category_id' => $category->id,
            'name' => 'Calculus',
            'slug' => 'calculus',
            'is_active' => true,
        ]);

        $this->assertEquals($category->id, $subject->category->id);
        $this->assertEquals('Mathematics', $subject->category->name);
    }

    public function test_blog_and_articles_dynamically_integrate_with_categories(): void
    {
        Category::create(['name' => 'Artificial Intelligence', 'slug' => 'ai', 'is_active' => true]);

        Article::create([
            'author_user_id' => $this->adminUser->id,
            'title' => 'The Future of AI in Education',
            'slug' => 'future-of-ai',
            'category' => 'Artificial Intelligence',
            'content' => 'Full article content here...',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $response = $this->get('/blog?category=Artificial+Intelligence');
        $response->assertStatus(200);
        $response->assertSee('The Future of AI in Education');
    }
}
