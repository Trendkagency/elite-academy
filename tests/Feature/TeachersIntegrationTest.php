<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeachersIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_teachers_page_renders_dynamic_teachers_catalog(): void
    {
        $user = User::create([
            'name' => 'Prof. Tarek Fouad',
            'email' => 'tarek@elite.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);

        TeacherProfile::create([
            'user_id' => $user->id,
            'slug' => 'prof-tarek-fouad',
            'title' => 'Professor of Chemistry',
            'specialization' => 'Chemistry',
            'bio' => 'Experienced professor with 20 years experience.',
            'years_experience' => 20,
            'rating_avg' => 4.9,
            'students_count' => 2000,
            'is_featured' => true,
            'is_public' => true,
        ]);

        $response = $this->get('/teachers');
        $response->assertStatus(200)
            ->assertSee('Prof. Tarek Fouad')
            ->assertSee('Professor of Chemistry');
    }

    public function test_legacy_instructors_url_redirects_to_teachers(): void
    {
        $response = $this->get('/instructors');
        $response->assertRedirect('/teachers');
    }

    public function test_teacher_profile_page_renders_successfully(): void
    {
        $user = User::create([
            'name' => 'Dr. Mona El-Sayed',
            'email' => 'mona@elite.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);

        $teacher = TeacherProfile::create([
            'user_id' => $user->id,
            'slug' => 'dr-mona-el-sayed',
            'title' => 'Biology Chair',
            'specialization' => 'Biology',
            'bio' => 'Top biology instructor in Cairo.',
            'years_experience' => 12,
            'rating_avg' => 4.8,
            'students_count' => 1500,
            'is_featured' => true,
            'is_public' => true,
        ]);

        $response = $this->get('/teacher-profile/'.$teacher->slug);
        $response->assertStatus(200)
            ->assertSee('Dr. Mona El-Sayed')
            ->assertSee('Biology Chair');
    }
}
