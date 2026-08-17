<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_blog_index_displays_only_published_articles(): void
    {
        $admin = User::create(['name' => 'Admin Author', 'email' => 'author@elite.com', 'password' => bcrypt('secret'), 'status' => AccountStatus::APPROVED]);

        $publishedArticle = Article::create([
            'author_user_id' => $admin->id,
            'title' => 'Published Article Title',
            'slug' => 'published-article-title',
            'category' => 'Programming',
            'excerpt' => 'Published article summary excerpt.',
            'content' => 'Full article body content text here.',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $draftArticle = Article::create([
            'author_user_id' => $admin->id,
            'title' => 'Closed Draft Article Title',
            'slug' => 'closed-draft-article-title',
            'category' => 'Programming',
            'excerpt' => 'Draft summary excerpt.',
            'content' => 'Hidden body text.',
            'is_published' => false, // Closed / Draft by Admin
            'published_at' => null,
        ]);

        $response = $this->get('/blog');

        $response->assertStatus(200)
            ->assertSee('Published Article Title')
            ->assertDontSee('Closed Draft Article Title');
    }

    public function test_blog_category_filtering_works_correctly(): void
    {
        $admin = User::create(['name' => 'Admin Author', 'email' => 'author2@elite.com', 'password' => bcrypt('secret'), 'status' => AccountStatus::APPROVED]);

        Article::create([
            'author_user_id' => $admin->id,
            'title' => 'Python Masterclass Article',
            'slug' => 'python-masterclass-article',
            'category' => 'Programming',
            'excerpt' => 'Python details.',
            'content' => 'Python content.',
            'is_published' => true,
            'published_at' => now(),
        ]);

        Article::create([
            'author_user_id' => $admin->id,
            'title' => 'Exam Prep Guide',
            'slug' => 'exam-prep-guide',
            'category' => 'Study Tips',
            'excerpt' => 'Exam details.',
            'content' => 'Exam content.',
            'is_published' => true,
            'published_at' => now(),
        ]);

        // Filter by 'Programming'
        $response = $this->get('/blog?category=Programming');

        $response->assertStatus(200)
            ->assertSee('Python Masterclass Article')
            ->assertDontSee('Exam Prep Guide');
    }

    public function test_blog_details_page_renders_article_content(): void
    {
        $admin = User::create(['name' => 'Admin Author', 'email' => 'author3@elite.com', 'password' => bcrypt('secret'), 'status' => AccountStatus::APPROVED]);

        $article = Article::create([
            'author_user_id' => $admin->id,
            'title' => 'Deep Neural Networks in 2026',
            'slug' => 'deep-neural-networks-2026',
            'category' => 'AI & Tech',
            'excerpt' => 'Neural network architecture breakthroughs.',
            'content' => 'Detailed explanation of transformer attention mechanisms.',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $response = $this->get("/blog-details/{$article->slug}");

        $response->assertStatus(200)
            ->assertSee('Deep Neural Networks in 2026')
            ->assertSee('Detailed explanation of transformer attention mechanisms')
            ->assertSee('AI & Tech');
    }
}
