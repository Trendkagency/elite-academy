<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\GradeLevel;
use App\Models\LiveSession;
use App\Models\StudentPackage;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentRealtimeSessionsFeedLifecycleTest extends TestCase
{
    use RefreshDatabase;

    private User $studentUser;
    private User $teacherUser;
    private TeacherProfile $teacherProfile;
    private Course $course;

    protected function setUp(): void
    {
        parent::setUp();

        $grade = GradeLevel::create(['name' => 'الثانوية العامة', 'slug' => 'thanaweya-realtime']);
        $cat = Category::create(['name' => 'العلوم', 'slug' => 'sciences-realtime']);
        $subject = Subject::create(['category_id' => $cat->id, 'name' => 'الفيزياء', 'slug' => 'physics-realtime']);

        $this->teacherUser = User::create([
            'name' => 'د. أحمد محمود',
            'email' => 'teacher.rt@elite.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        $this->teacherProfile = TeacherProfile::create([
            'user_id' => $this->teacherUser->id,
            'slug' => 'prof-ahmed-rt',
        ]);

        $this->studentUser = User::create([
            'name' => 'طالب تجريبي',
            'email' => 'student.rt@elite.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        StudentProfile::create([
            'user_id' => $this->studentUser->id,
            'grade_level_id' => $grade->id,
        ]);

        $template = \App\Models\PackageTemplate::create([
            'name' => 'باقة قياسية',
            'sessions_count' => 10,
            'price' => 500,
        ]);
        StudentPackage::create([
            'student_user_id' => $this->studentUser->id,
            'package_template_id' => $template->id,
            'remaining_sessions' => 8,
            'total_sessions' => 10,
            'status' => 'active',
        ]);

        $this->course = Course::create([
            'teacher_id' => $this->teacherProfile->id,
            'subject_id' => $subject->id,
            'grade_level_id' => $grade->id,
            'title' => 'الفيزياء الكهربية المتقدمة',
            'slug' => 'adv-physics-rt',
            'is_active' => true,
        ]);

        CourseEnrollment::create([
            'student_user_id' => $this->studentUser->id,
            'course_id' => $this->course->id,
            'status' => 'active',
        ]);
    }

    public function test_unauthenticated_user_cannot_access_realtime_feed(): void
    {
        $response = $this->getJson(route('ajax.student.sessions.feed'));
        $response->assertStatus(401);
    }

    public function test_student_portal_renders_realtime_indicators_and_sessions(): void
    {
        $session = LiveSession::create([
            'title' => 'مراجعة الديناميكا والموجات',
            'teacher_profile_id' => $this->teacherProfile->id,
            'course_id' => $this->course->id,
            'student_user_id' => $this->studentUser->id,
            'scheduled_at' => now()->addMinutes(15),
            'start_at' => now()->addMinutes(15),
            'end_at' => now()->addMinutes(75),
            'duration_minutes' => 60,
            'status' => 'scheduled',
        ]);

        $this->actingAs($this->studentUser);

        $response = $this->get('/student-portal?tab=sessions');
        $response->assertStatus(200);
        $response->assertSee('sessionRealtimeStatus');
        $response->assertSee('مراجعة الديناميكا والموجات');
        $response->assertSee('sessionPane_soon');
        $response->assertSee('sessionPane_upcoming');
        $response->assertSee('sessionPane_history');
    }

    public function test_realtime_feed_lifecycle_on_session_create_update_and_delete(): void
    {
        $this->actingAs($this->studentUser);

        // 1. Initial Feed fetch: has_changes should be true and hash returned
        $feed1 = $this->getJson(route('ajax.student.sessions.feed'));
        $feed1->assertStatus(200)
              ->assertJsonPath('success', true)
              ->assertJsonPath('has_changes', true)
              ->assertJsonStructure(['hash', 'counts' => ['soon', 'upcoming', 'history', 'live'], 'panes' => ['soon', 'upcoming', 'history']]);

        $hash1 = $feed1->json('hash');
        $this->assertNotEmpty($hash1);

        // 2. Immediate second call with identical hash: should return has_changes = false
        $feed2 = $this->getJson(route('ajax.student.sessions.feed', ['hash' => $hash1]));
        $feed2->assertStatus(200)
              ->assertJsonPath('success', true)
              ->assertJsonPath('has_changes', false)
              ->assertJsonPath('hash', $hash1);

        // 3. Teacher creates a new 1:1 session for this student
        $this->actingAs($this->teacherUser);
        $createRes = $this->postJson(route('ajax.teacher.sessions.create'), [
            'title' => 'حصة تفاعلية فردية - كهرومغناطيسية',
            'course_id' => $this->course->id,
            'student_user_id' => $this->studentUser->id,
            'scheduled_at' => now()->addHours(2)->toDateTimeString(),
            'duration_minutes' => 60,
            'meeting_link' => 'https://meet.google.com/rt-test-123',
        ]);
        $createRes->assertStatus(201);
        $newSessionId = $createRes->json('session_id');
        $this->assertNotNull($newSessionId);

        // 4. Student queries feed with the old hash: MUST detect changes!
        $this->actingAs($this->studentUser);
        $feed3 = $this->getJson(route('ajax.student.sessions.feed', ['hash' => $hash1]));
        $feed3->assertStatus(200)
              ->assertJsonPath('success', true)
              ->assertJsonPath('has_changes', true);

        $hash2 = $feed3->json('hash');
        $this->assertNotEquals($hash1, $hash2, 'Hash must change when new session is created.');
        $this->assertStringContainsString('حصة تفاعلية فردية - كهرومغناطيسية', $feed3->json('panes.soon'));

        // 5. Teacher updates the session title and link
        $this->actingAs($this->teacherUser);
        $updateRes = $this->postJson(route('ajax.teacher.sessions.update', ['id' => $newSessionId]), [
            'title' => 'حصة معدلة - فيزياء نووية متقدمة',
            'course_id' => $this->course->id,
            'student_user_id' => $this->studentUser->id,
            'scheduled_at' => now()->addHours(3)->toDateTimeString(),
            'duration_minutes' => 90,
            'meeting_link' => 'https://meet.google.com/rt-updated-456',
        ]);
        $updateRes->assertStatus(200);

        // 6. Student queries feed with hash2: MUST detect update!
        $this->actingAs($this->studentUser);
        $feed4 = $this->getJson(route('ajax.student.sessions.feed', ['hash' => $hash2]));
        $feed4->assertStatus(200)
              ->assertJsonPath('success', true)
              ->assertJsonPath('has_changes', true);

        $hash3 = $feed4->json('hash');
        $this->assertNotEquals($hash2, $hash3, 'Hash must change when session is updated.');
        $this->assertStringContainsString('حصة معدلة - فيزياء نووية متقدمة', $feed4->json('panes.soon'));

        // 7. Teacher deletes the session
        $this->actingAs($this->teacherUser);
        $deleteRes = $this->deleteJson(route('ajax.teacher.sessions.delete', ['id' => $newSessionId]));
        $deleteRes->assertStatus(200);

        // 8. Student queries feed with hash3: MUST detect deletion!
        $this->actingAs($this->studentUser);
        $feed5 = $this->getJson(route('ajax.student.sessions.feed', ['hash' => $hash3]));
        $feed5->assertStatus(200)
              ->assertJsonPath('success', true)
              ->assertJsonPath('has_changes', true);

        $hash4 = $feed5->json('hash');
        $this->assertNotEquals($hash3, $hash4, 'Hash must change when session is deleted.');
        $this->assertStringNotContainsString('حصة معدلة - فيزياء نووية متقدمة', $feed5->json('panes.soon'));
    }
}
