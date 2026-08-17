<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubjectsAboutContactIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_subjects_index_renders_active_subjects(): void
    {
        $category = Category::create(['name' => 'Secondary Science', 'slug' => 'sec-science', 'sort_order' => 1]);

        $subject = Subject::create([
            'category_id' => $category->id,
            'name' => 'Advanced Theoretical Physics',
            'slug' => 'adv-physics',
            'description' => 'Thermodynamics and Quantum Mechanics.',
            'is_active' => true,
        ]);

        $response = $this->get('/subjects');

        $response->assertStatus(200)
            ->assertSee('Advanced Theoretical Physics')
            ->assertSee('Secondary Science');
    }

    public function test_subject_details_page_renders_subject_info(): void
    {
        $category = Category::create(['name' => 'Secondary Math', 'slug' => 'sec-math', 'sort_order' => 1]);

        $subject = Subject::create([
            'category_id' => $category->id,
            'name' => 'Calculus & Geometry',
            'slug' => 'calculus-geometry',
            'description' => 'Derivatives and coordinate geometry.',
            'is_active' => true,
        ]);

        $response = $this->get("/subject-details/{$subject->slug}");

        $response->assertStatus(200)
            ->assertSee('Calculus & Geometry')
            ->assertSee('Derivatives and coordinate geometry');
    }

    public function test_contact_form_ajax_submission_stores_message(): void
    {
        $payload = [
            'full_name' => 'Test Parent User',
            'email' => 'parent.test@elite.edu',
            'phone' => '+201009988776',
            'subject' => 'Grade 11 Physics Enrollment Inquiry',
            'message' => 'Hello, I would like to inquire about the physics lab requirements for my son.',
        ];

        $response = $this->postJson('/ajax/contact/submit', $payload);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'parent.test@elite.edu',
            'subject' => 'Grade 11 Physics Enrollment Inquiry',
            'status' => 'new',
        ]);
    }

    public function test_about_page_renders_successfully(): void
    {
        $response = $this->get('/about');

        $response->assertStatus(200)
            ->assertSee('About');
    }
}
