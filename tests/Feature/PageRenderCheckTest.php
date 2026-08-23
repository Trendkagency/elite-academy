<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\ParentProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageRenderCheckTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_system_pages_render_without_white_screen(): void
    {
        $parentUser = User::create([
            'name' => 'Test Parent',
            'email' => 'parentcheck@test.com',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        ParentProfile::create(['user_id' => $parentUser->id]);

        $routes = [
            '/',
            '/about',
            '/contact',
            '/blog',
            '/parent-portal',
            '/parent-portal#section-children',
            '/parent-portal#section-sessions',
            '/parent-portal#section-attendance',
            '/parent-portal#section-assignments',
            '/courses',
            '/subjects',
            '/teachers',
        ];

        foreach ($routes as $url) {
            $response = $this->actingAs($parentUser)->get($url);
            $response->assertStatus(200);
            $this->assertGreaterThan(500, strlen($response->getContent()), "URL {$url} returned empty content.");
        }

        $teacherUser = User::create([
            'name' => 'Test Teacher Render',
            'email' => 'teachercheck@test.com',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        \App\Models\TeacherProfile::create(['user_id' => $teacherUser->id, 'title' => 'Dr.', 'slug' => 'teachercheck']);
        $teacherResponse = $this->actingAs($teacherUser)->get('/teacher-portal');
        $teacherResponse->assertStatus(200);

        $studentUser = User::create([
            'name' => 'Test Student Render',
            'email' => 'studentcheck@test.com',
            'phone' => '01011112222',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        \App\Models\StudentProfile::create(['user_id' => $studentUser->id]);
        $studentResponse = $this->actingAs($studentUser)->get('/student-portal');
        $studentResponse->assertStatus(200);
    }

    public function test_i18n_json_translation_files_have_valid_syntax(): void
    {
        $arPath = base_path('lang/ar.json');
        $enPath = base_path('lang/en.json');

        $this->assertFileExists($arPath);
        $this->assertFileExists($enPath);

        $arData = json_decode(file_get_contents($arPath), true);
        $this->assertNotNull($arData, 'lang/ar.json contains a JSON syntax error: ' . json_last_error_msg());
        $this->assertIsArray($arData);

        $enData = json_decode(file_get_contents($enPath), true);
        $this->assertNotNull($enData, 'lang/en.json contains a JSON syntax error: ' . json_last_error_msg());
        $this->assertIsArray($enData);
    }

    public function test_issue_02_bfcache_motion_engine_page_exit_opacity_protection(): void
    {
        $scrollRevealPath = public_path('js/scroll-reveal.js');
        $this->assertFileExists($scrollRevealPath);

        $jsContent = file_get_contents($scrollRevealPath);
        $this->assertStringContainsString('pageshow', $jsContent);
        $this->assertStringContainsString("document.body.classList.remove('page-exit')", $jsContent);

        $cssPath = public_path('dist/output.css');
        if (file_exists($cssPath)) {
            $cssContent = file_get_contents($cssPath);
            $this->assertStringNotContainsString('body.page-exit { opacity: 0 !important;', $cssContent);
        }
    }

    public function test_issue_03_parent_portal_has_static_section_anchors(): void
    {
        $viewPath = resource_path('views/pages/parent-portal.blade.php');
        $this->assertFileExists($viewPath);

        $viewContent = file_get_contents($viewPath);
        $this->assertStringContainsString('id="section-children"', $viewContent);
        $this->assertStringContainsString('id="section-attendance"', $viewContent);
        $this->assertStringContainsString('id="section-sessions"', $viewContent);
        $this->assertStringContainsString('id="section-assignments"', $viewContent);
    }

    public function test_issue_04_student_registration_requires_mandatory_phone(): void
    {
        $response = $this->postJson('/ajax/register', [
            'name' => 'Test Student Require Phone',
            'email' => 'phonecheck@student.com',
            'password' => 'password123',
            'user_type' => 'student',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['phone']);
    }

    public function test_issue_05_teacher_portal_api_omits_student_phone(): void
    {
        $teacherUser = User::create(['name' => 'Teacher Phone Check', 'email' => 'tphone@elite.edu', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        $teacherProfile = \App\Models\TeacherProfile::create(['user_id' => $teacherUser->id, 'title' => 'Dr.', 'slug' => 'tphone']);

        $studentUser = User::create(['name' => 'Student Phone Mask', 'email' => 'smask@student.com', 'phone' => '01099998888', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        \App\Models\StudentProfile::create(['user_id' => $studentUser->id]);

        $response = $this->actingAs($teacherUser)->getJson("/ajax/teacher/students/{$studentUser->id}/details");
        $response->assertStatus(200);

        $json = $response->json();
        $this->assertArrayNotHasKey('phone', $json['student']);
        $response->assertDontSee('01099998888');
    }
}
