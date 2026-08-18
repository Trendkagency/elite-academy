<?php

namespace Tests\Feature;

use App\Models\GradeLevel;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StudentProfileTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;
    protected GradeLevel $gradeLevel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->gradeLevel = GradeLevel::create([
            'name' => 'Grade 12 STEM',
            'slug' => 'grade-12-stem',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $this->student = User::factory()->create([
            'name' => 'Ahmed Student Profile Test',
            'email' => 'student.profile@elite.edu',
            'phone' => '+201099887766',
            'password' => Hash::make('Secret123!'),
        ]);

        StudentProfile::create([
            'user_id' => $this->student->id,
            'grade_level_id' => $this->gradeLevel->id,
            'school_name' => 'STEM Academy Cairo',
        ]);
    }

    public function test_authenticated_student_can_view_profile_page(): void
    {
        $response = $this->actingAs($this->student)
            ->get(route('student.profile'));

        $response->assertStatus(200);
        $response->assertSee('Ahmed Student Profile Test');
        $response->assertSee('student.profile@elite.edu');
        $response->assertSee('STEM Academy Cairo');
    }

    public function test_unauthenticated_user_redirected_from_profile_page(): void
    {
        $response = $this->get(route('student.profile'));

        $response->assertRedirect(route('login'));
    }

    public function test_student_can_update_profile_details(): void
    {
        $response = $this->actingAs($this->student)
            ->post(route('student.profile.update'), [
                'name' => 'Ahmed Updated Name',
                'phone' => '+201122334455',
                'grade_level_id' => $this->gradeLevel->id,
                'school_name' => 'New STEM High School',
                'date_of_birth' => '2008-05-15',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $this->student->id,
            'name' => 'Ahmed Updated Name',
            'phone' => '+201122334455',
        ]);

        $this->assertDatabaseHas('student_profiles', [
            'user_id' => $this->student->id,
            'school_name' => 'New STEM High School',
        ]);
    }

    public function test_student_can_upload_avatar(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('avatar.jpg', 400, 400);

        $response = $this->actingAs($this->student)
            ->post(route('student.profile.update'), [
                'name' => 'Ahmed Student Profile Test',
                'avatar' => $file,
            ]);

        $response->assertRedirect();

        $profile = StudentProfile::where('user_id', $this->student->id)->first();
        $this->assertNotNull($profile->avatar);
        Storage::disk('public')->assertExists($profile->avatar);
    }

    public function test_student_can_update_password(): void
    {
        $response = $this->actingAs($this->student)
            ->post(route('student.profile.password'), [
                'current_password' => 'Secret123!',
                'password' => 'NewSecret456!',
                'password_confirmation' => 'NewSecret456!',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->student->refresh();
        $this->assertTrue(Hash::check('NewSecret456!', $this->student->password));
    }

    public function test_password_update_fails_with_invalid_current_password(): void
    {
        $response = $this->actingAs($this->student)
            ->post(route('student.profile.password'), [
                'current_password' => 'WrongPassword',
                'password' => 'NewSecret456!',
                'password_confirmation' => 'NewSecret456!',
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('current_password');
    }
}
