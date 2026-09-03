<?php

namespace Tests\Feature;

use App\Models\GradeLevel;
use App\Models\ParentProfile;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class FullSystemLocalizationAuditTest extends TestCase
{
    use RefreshDatabase;

    protected User $studentUser;
    protected User $teacherUser;
    protected User $parentUser;
    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $gradeLevel = GradeLevel::firstOrCreate(
            ['code' => 'G10'],
            ['name' => 'Grade 10', 'slug' => 'grade-10', 'sort_order' => 1]
        );

        $this->studentUser = User::create([
            'name' => 'Student Localization Test',
            'email' => 'student_loc@elite.test',
            'password' => bcrypt('password'),
            'status' => \App\Enums\AccountStatus::APPROVED,
        ]);
        StudentProfile::create([
            'user_id' => $this->studentUser->id,
            'grade_level_id' => $gradeLevel->id,
            'school_name' => 'Cairo International School',
        ]);

        $this->teacherUser = User::create([
            'name' => 'Teacher Localization Test',
            'email' => 'teacher_loc@elite.test',
            'password' => bcrypt('password'),
            'status' => \App\Enums\AccountStatus::APPROVED,
        ]);
        TeacherProfile::create([
            'user_id' => $this->teacherUser->id,
            'slug' => 'teacher-loc-test',
            'bio' => 'Physics Teacher',
        ]);

        $this->parentUser = User::create([
            'name' => 'Parent Localization Test',
            'email' => 'parent_loc@elite.test',
            'password' => bcrypt('password'),
            'status' => \App\Enums\AccountStatus::APPROVED,
        ]);
        ParentProfile::create([
            'user_id' => $this->parentUser->id,
        ]);

        $this->adminUser = User::create([
            'name' => 'Admin Localization Test',
            'email' => 'admin_loc@elite.test',
            'password' => bcrypt('password'),
            'status' => \App\Enums\AccountStatus::APPROVED,
        ]);
    }

    public function test_language_switcher_persists_session_and_cookie(): void
    {
        $response = $this->get(route('lang.switch', ['locale' => 'en']));
        $response->assertRedirect();
        $response->assertSessionHas('locale', 'en');
        $response->assertCookie('elite_locale', 'en');

        $responseAr = $this->get(route('lang.switch', ['locale' => 'ar']));
        $responseAr->assertRedirect();
        $responseAr->assertSessionHas('locale', 'ar');
        $responseAr->assertCookie('elite_locale', 'ar');
    }

    public function test_public_pages_render_in_arabic_with_rtl(): void
    {
        $pages = ['/', '/about', '/contact', '/faq', '/blog'];

        foreach ($pages as $url) {
            $response = $this->withSession(['locale' => 'ar'])->get($url);
            $response->assertStatus(200);
            $response->assertSee('dir="rtl"', false);
            $response->assertSee('lang="ar"', false);
        }
    }

    public function test_public_pages_render_in_english_with_ltr(): void
    {
        $pages = ['/', '/about', '/contact', '/faq', '/blog'];

        foreach ($pages as $url) {
            $response = $this->withSession(['locale' => 'en'])->get($url);
            $response->assertStatus(200);
            $response->assertSee('dir="ltr"', false);
            $response->assertSee('lang="en"', false);
        }
    }

    public function test_auth_pages_render_in_both_languages(): void
    {
        // Login Page
        $this->withSession(['locale' => 'ar'])->get('/login')
            ->assertStatus(200)
            ->assertSee('dir="rtl"', false);

        $this->withSession(['locale' => 'en'])->get('/login')
            ->assertStatus(200)
            ->assertSee('dir="ltr"', false);

        // Register Page
        $this->withSession(['locale' => 'ar'])->get('/register')
            ->assertStatus(200)
            ->assertSee('dir="rtl"', false);

        $this->withSession(['locale' => 'en'])->get('/register')
            ->assertStatus(200)
            ->assertSee('dir="ltr"', false);
    }

    public function test_student_portal_renders_in_both_locales(): void
    {
        $this->actingAs($this->studentUser)
            ->withSession(['locale' => 'ar'])
            ->get('/student-portal')
            ->assertStatus(200)
            ->assertSee('dir="rtl"', false);

        $this->actingAs($this->studentUser)
            ->withSession(['locale' => 'en'])
            ->get('/student-portal')
            ->assertStatus(200)
            ->assertSee('dir="ltr"', false);
    }

    public function test_teacher_portal_renders_in_both_locales(): void
    {
        $this->actingAs($this->teacherUser)
            ->withSession(['locale' => 'ar'])
            ->get('/teacher-portal')
            ->assertStatus(200)
            ->assertSee('dir="rtl"', false);

        $this->actingAs($this->teacherUser)
            ->withSession(['locale' => 'en'])
            ->get('/teacher-portal')
            ->assertStatus(200)
            ->assertSee('dir="ltr"', false);
    }

    public function test_parent_portal_renders_in_both_locales(): void
    {
        $this->actingAs($this->parentUser)
            ->withSession(['locale' => 'ar'])
            ->get('/parent-portal')
            ->assertStatus(200)
            ->assertSee('dir="rtl"', false);

        $this->actingAs($this->parentUser)
            ->withSession(['locale' => 'en'])
            ->get('/parent-portal')
            ->assertStatus(200)
            ->assertSee('dir="ltr"', false);
    }

    public function test_validation_messages_are_localized_in_arabic_and_english(): void
    {
        // Arabic Validation
        App::setLocale('ar');
        $validatorAr = Validator::make([], [
            'name' => 'required',
            'email' => 'required|email',
        ]);
        $this->assertTrue($validatorAr->fails());
        $errorsAr = $validatorAr->errors();
        $this->assertStringContainsString('الاسم الكامل', $errorsAr->first('name'));
        $this->assertStringContainsString('مطلوب', $errorsAr->first('name'));

        // English Validation
        App::setLocale('en');
        $validatorEn = Validator::make([], [
            'name' => 'required',
            'email' => 'required|email',
        ]);
        $this->assertTrue($validatorEn->fails());
        $errorsEn = $validatorEn->errors();
        $this->assertStringContainsString('Full Name', $errorsEn->first('name'));
        $this->assertStringContainsString('required', $errorsEn->first('name'));
    }

    public function test_error_pages_render_without_errors(): void
    {
        $locales = ['ar', 'en'];

        foreach ($locales as $loc) {
            App::setLocale($loc);
            
            $view403 = view('errors.403', ['exception' => new \Exception('Forbidden')])->render();
            $this->assertNotEmpty($view403);

            $view404 = view('errors.404', ['exception' => new \Exception('Not Found')])->render();
            $this->assertNotEmpty($view404);

            $view419 = view('errors.419', ['exception' => new \Exception('Page Expired')])->render();
            $this->assertNotEmpty($view419);

            $view429 = view('errors.429', ['exception' => new \Exception('Too Many Requests')])->render();
            $this->assertNotEmpty($view429);

            $view500 = view('errors.500', ['exception' => new \Exception('Server Error')])->render();
            $this->assertNotEmpty($view500);

            $view503 = view('errors.503', ['exception' => new \Exception('Service Unavailable')])->render();
            $this->assertNotEmpty($view503);
        }
    }
}
