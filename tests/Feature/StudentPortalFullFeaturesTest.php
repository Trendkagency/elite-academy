<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\Category;
use App\Models\GradeLevel;
use App\Models\LiveSession;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentPortalFullFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_portal_renders_all_required_student_metrics_and_actions(): void
    {
        $grade = GradeLevel::create(['name' => 'الصف الثالث الثانوي', 'slug' => 'g12']);
        $cat = Category::create(['name' => 'العلوم', 'slug' => 'science']);
        $subject = Subject::create(['category_id' => $cat->id, 'name' => 'الفيزياء', 'slug' => 'physics']);

        $teacherUser = User::create(['name' => 'د. أحمد محمود', 'email' => 't.sp@elite.edu', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        $teacher = TeacherProfile::create(['user_id' => $teacherUser->id, 'slug' => 'tsp']);

        $student = User::create(['name' => 'طالب متميز', 'email' => 'student.full@elite.edu', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        StudentProfile::create(['user_id' => $student->id, 'grade_level_id' => $grade->id, 'school_name' => 'STEM Cairo']);

        $liveSession = LiveSession::create([
            'student_user_id' => $student->id,
            'teacher_profile_id' => $teacher->id,
            'subject_id' => $subject->id,
            'scheduled_at' => now()->addHours(5),
            'meeting_link' => 'https://meet.google.com/abc-defg-hij',
        ]);

        $this->actingAs($student);

        // Access Student Portal
        $response = $this->get('/student-portal');

        $response->assertStatus(200)
            ->assertSee('طالب متميز')
            ->assertSee('د. أحمد محمود')
            ->assertSee('https://meet.google.com/abc-defg-hij')
            ->assertSee(__('app.portal.submit_excuse'))
            ->assertSee(__('app.portal.submit_exception'));

        // Submit Absence Excuse Action
        $excuseRes = $this->postJson('/ajax/exceptions/submit', [
            'live_session_id' => $liveSession->id,
            'reason' => 'ظرف صحي طارئ ومستند طبي مرفق للتأكيد',
        ]);

        $excuseRes->assertStatus(201)
            ->assertJsonPath('success', true);
    }
}
