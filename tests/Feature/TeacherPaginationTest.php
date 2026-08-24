<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\TeacherProfile;
use App\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherPaginationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed grade level and teacher profile
        User::factory()->create(['status' => 'approved']);
    }

    public function test_teacher_directory_paginates_12_items_per_page(): void
    {
        $response = $this->get('/teachers');

        $response->assertStatus(200);
        $response->assertViewHas('teachers');
        $this->assertEquals(12, $response->viewData('teachers')->perPage());
    }

    public function test_teacher_directory_returns_json_on_ajax_request(): void
    {
        $response = $this->getJson('/teachers?page=1');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'total',
            'from',
            'to',
            'current_page',
            'last_page',
            'html',
            'pagination_html',
        ]);
        $this->assertTrue($response->json('success'));
        $this->assertEquals(1, $response->json('current_page'));
    }
}
