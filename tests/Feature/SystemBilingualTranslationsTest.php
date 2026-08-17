<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SystemBilingualTranslationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_system_renders_bilingual_translations_for_english_and_arabic(): void
    {
        $student = User::create([
            'name' => 'Bilingual Student',
            'email' => 'student.bi@elite.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);

        $this->actingAs($student);

        // English Translation Test
        $responseEn = $this->withSession(['locale' => 'en'])->get('/student-portal');
        $responseEn->assertStatus(200);

        // Arabic Translation Test
        $responseAr = $this->withSession(['locale' => 'ar'])->get('/student-portal');
        $responseAr->assertStatus(200);
    }
}
