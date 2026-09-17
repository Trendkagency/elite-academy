<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\LiveSession;
use App\Models\MeetingProvider;
use App\Models\SessionMeeting;
use App\Models\StudentPackage;
use App\Models\StudentProfile;
use App\Models\StudentSession;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentAssignedSessionLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected User $teacherUser;
    protected TeacherProfile $teacherProfile;
    protected Course $coursePhysics;
    protected Course $courseMath;
    protected Course $courseBio;
    protected User $studentAhmed;
    protected User $studentSarah;
    protected User $studentOmar;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Teacher Setup
        $this->teacherUser = User::create([
            'name' => 'Dr. Tareq Physics',
            'email' => 'dr.tareq@elite-academy.edu',
            'password' => bcrypt('secret123'),
            'role' => 'teacher',
            'status' => AccountStatus::APPROVED,
        ]);
        $this->teacherProfile = TeacherProfile::create([
            'user_id' => $this->teacherUser->id,
            'slug' => 'dr-tareq-physics',
        ]);

        // 2. Categories & Subjects
        $category = Category::create(['name' => 'STEM', 'slug' => 'stem']);
        $subPhysics = Subject::create(['category_id' => $category->id, 'name' => 'Physics', 'slug' => 'physics']);
        $subMath = Subject::create(['category_id' => $category->id, 'name' => 'Mathematics', 'slug' => 'math']);
        $subBio = Subject::create(['category_id' => $category->id, 'name' => 'Biology', 'slug' => 'bio']);

        // 3. Courses taught by Dr. Tareq
        $this->coursePhysics = Course::create([
            'subject_id' => $subPhysics->id,
            'teacher_id' => $this->teacherProfile->id,
            'title' => 'Physics STEM Advanced',
            'slug' => 'physics-stem-adv',
            'is_active' => true,
            'has_free_demo' => true,
        ]);

        $this->courseMath = Course::create([
            'subject_id' => $subMath->id,
            'teacher_id' => $this->teacherProfile->id,
            'title' => 'Calculus & Pure Math',
            'slug' => 'calculus-pure-math',
            'is_active' => true,
            'has_free_demo' => true,
        ]);

        $this->courseBio = Course::create([
            'subject_id' => $subBio->id,
            'teacher_id' => $this->teacherProfile->id,
            'title' => 'Molecular Biology',
            'slug' => 'molecular-biology',
            'is_active' => true,
            'has_free_demo' => true,
        ]);

        // 4. Students
        $this->studentAhmed = User::create([
            'name' => 'Ahmed Mohamed',
            'email' => 'ahmed.m@elite-student.edu',
            'password' => bcrypt('secret123'),
            'role' => 'student',
            'status' => AccountStatus::APPROVED,
        ]);
        StudentProfile::create(['user_id' => $this->studentAhmed->id]);

        $this->studentSarah = User::create([
            'name' => 'Sarah Khalil',
            'email' => 'sarah.k@elite-student.edu',
            'password' => bcrypt('secret123'),
            'role' => 'student',
            'status' => AccountStatus::APPROVED,
        ]);
        StudentProfile::create(['user_id' => $this->studentSarah->id]);

        $this->studentOmar = User::create([
            'name' => 'Omar Hassan',
            'email' => 'omar.h@elite-student.edu',
            'password' => bcrypt('secret123'),
            'role' => 'student',
            'status' => AccountStatus::APPROVED,
        ]);
        StudentProfile::create(['user_id' => $this->studentOmar->id]);

        // Active packages for students
        foreach ([$this->studentAhmed, $this->studentSarah, $this->studentOmar] as $st) {
            StudentPackage::create([
                'student_user_id' => $st->id,
                'total_sessions' => 20,
                'used_sessions' => 0,
                'remaining_sessions' => 20,
                'status' => 'active',
                'activated_at' => now(),
            ]);
        }

        // 5. Enrollments:
        // Ahmed is enrolled in Physics and Math
        CourseEnrollment::create(['student_user_id' => $this->studentAhmed->id, 'course_id' => $this->coursePhysics->id]);
        CourseEnrollment::create(['student_user_id' => $this->studentAhmed->id, 'course_id' => $this->courseMath->id]);

        // Sarah is enrolled in Physics and Biology
        CourseEnrollment::create(['student_user_id' => $this->studentSarah->id, 'course_id' => $this->coursePhysics->id]);
        CourseEnrollment::create(['student_user_id' => $this->studentSarah->id, 'course_id' => $this->courseBio->id]);

        // Omar is enrolled in Physics only
        CourseEnrollment::create(['student_user_id' => $this->studentOmar->id, 'course_id' => $this->coursePhysics->id]);
    }

    /**
     * Test 1: Dynamic Student Course API returns ONLY courses enrolled by the selected student.
     */
    public function test_dynamic_course_api_filters_courses_by_selected_student(): void
    {
        // 1. Ahmed's courses
        $resAhmed = $this->actingAs($this->teacherUser)
            ->getJson("/ajax/teacher/students/{$this->studentAhmed->id}/courses");

        $resAhmed->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonCount(2, 'courses')
            ->assertJsonFragment(['id' => $this->coursePhysics->id, 'title' => 'Physics STEM Advanced'])
            ->assertJsonFragment(['id' => $this->courseMath->id, 'title' => 'Calculus & Pure Math'])
            ->assertJsonMissing(['id' => $this->courseBio->id]);

        // 2. Sarah's courses
        $resSarah = $this->actingAs($this->teacherUser)
            ->getJson("/ajax/teacher/students/{$this->studentSarah->id}/courses");

        $resSarah->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonCount(2, 'courses')
            ->assertJsonFragment(['id' => $this->coursePhysics->id, 'title' => 'Physics STEM Advanced'])
            ->assertJsonFragment(['id' => $this->courseBio->id, 'title' => 'Molecular Biology'])
            ->assertJsonMissing(['id' => $this->courseMath->id]);

        // 3. Omar's courses (only Physics)
        $resOmar = $this->actingAs($this->teacherUser)
            ->getJson("/ajax/teacher/students/{$this->studentOmar->id}/courses");

        $resOmar->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonCount(1, 'courses')
            ->assertJsonFragment(['id' => $this->coursePhysics->id]);
    }

    /**
     * Test 2: Server-side validation rejects creating a 1:1 session if student is NOT enrolled in the selected course.
     */
    public function test_backend_rejects_session_if_student_not_enrolled_in_selected_course(): void
    {
        // Ahmed is NOT enrolled in Biology. Attempting to create Ahmed + Bio must be rejected with 422.
        $payload = [
            'student_user_id' => $this->studentAhmed->id,
            'course_id' => $this->courseBio->id,
            'title' => 'Ahmed Private Bio Lesson',
            'scheduled_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'duration_minutes' => 60,
        ];

        $response = $this->actingAs($this->teacherUser)
            ->postJson(route('ajax.teacher.sessions.create'), $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['course_id']);

        $this->assertDatabaseMissing('live_sessions', [
            'title' => 'Ahmed Private Bio Lesson',
        ]);
    }

    /**
     * Test 3: Creating a valid 1:1 session assigns the student to live_sessions and student_sessions.
     */
    public function test_creating_session_correctly_assigns_selected_student(): void
    {
        $payload = [
            'student_user_id' => $this->studentAhmed->id,
            'course_id' => $this->coursePhysics->id,
            'title' => 'Ahmed Private Physics Mastery',
            'scheduled_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'duration_minutes' => 90,
            'meeting_link' => 'https://meet.google.com/xyz-test',
        ];

        $response = $this->actingAs($this->teacherUser)
            ->postJson(route('ajax.teacher.sessions.create'), $payload);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $createdSession = LiveSession::where('title', 'Ahmed Private Physics Mastery')->first();
        $this->assertNotNull($createdSession);
        $this->assertEquals($this->studentAhmed->id, $createdSession->student_user_id);
        $this->assertEquals($this->coursePhysics->id, $createdSession->course_id);

        // Verify student_sessions record is synchronized with the same student
        $studentSession = StudentSession::where('live_session_id', $createdSession->id)->first();
        $this->assertNotNull($studentSession);
        $this->assertEquals($this->studentAhmed->id, $studentSession->student_user_id);
        $this->assertEquals('scheduled', $studentSession->session_status);
    }

    /**
     * Test 4: Visibility Isolation: Only the assigned student can see the 1-to-1 session.
     * Other students enrolled in the same course CANNOT see it.
     */
    public function test_only_assigned_student_can_see_private_session_in_student_portal(): void
    {
        // Create 1-to-1 session for Ahmed in Physics course (shared with Sarah & Omar)
        $session = LiveSession::create([
            'title' => 'Ahmed Private Physics 1-on-1',
            'student_user_id' => $this->studentAhmed->id,
            'teacher_profile_id' => $this->teacherProfile->id,
            'subject_id' => $this->coursePhysics->subject_id,
            'course_id' => $this->coursePhysics->id,
            'scheduled_at' => now()->addHours(2),
            'start_at' => now()->addHours(2),
            'end_at' => now()->addHours(3),
            'duration_minutes' => 60,
            'status' => 'scheduled',
        ]);

        // Ahmed views portal -> SEES the session
        $resAhmed = $this->actingAs($this->studentAhmed)->get('/student-portal?tab=sessions');
        $resAhmed->assertOk()
            ->assertSee('Ahmed Private Physics 1-on-1');

        // Sarah (enrolled in Physics) views portal -> DOES NOT see Ahmed's session
        $resSarah = $this->actingAs($this->studentSarah)->get('/student-portal?tab=sessions');
        $resSarah->assertOk()
            ->assertDontSee('Ahmed Private Physics 1-on-1');

        // Omar (enrolled in Physics) views portal -> DOES NOT see Ahmed's session
        $resOmar = $this->actingAs($this->studentOmar)->get('/student-portal?tab=sessions');
        $resOmar->assertOk()
            ->assertDontSee('Ahmed Private Physics 1-on-1');
    }

    /**
     * Test 5: Join Security: Sarah attempting to join Ahmed's 1-to-1 session gets 403 Forbidden.
     */
    public function test_unassigned_student_cannot_join_private_session_via_endpoint_or_view(): void
    {
        $scheduledAt = Carbon::parse('2026-09-17 10:00:00');
        $session = LiveSession::create([
            'title' => 'Ahmed Private Physics Gated Session',
            'student_user_id' => $this->studentAhmed->id,
            'teacher_profile_id' => $this->teacherProfile->id,
            'subject_id' => $this->coursePhysics->subject_id,
            'course_id' => $this->coursePhysics->id,
            'scheduled_at' => $scheduledAt,
            'start_at' => $scheduledAt,
            'end_at' => $scheduledAt->copy()->addMinutes(60),
            'duration_minutes' => 60,
            'status' => 'scheduled',
            'meeting_link' => 'https://meet.google.com/test-gate',
        ]);

        $provider = MeetingProvider::create([
            'name' => 'Google Meet',
            'slug' => 'google_meet',
            'is_active' => true,
            'supports_embedding' => true,
        ]);

        SessionMeeting::create([
            'live_session_id' => $session->id,
            'meeting_provider_id' => $provider->id,
            'provider_slug' => 'google_meet',
            'join_url' => 'https://meet.google.com/test-gate',
            'status' => 'active',
        ]);

        Carbon::setTestNow('2026-09-17 10:05:00');

        // Sarah attempts to join via Meeting Access AJAX endpoint -> 403 Forbidden
        $responseJoin = $this->actingAs($this->studentSarah)
            ->postJson(route('ajax.meeting.join', ['id' => $session->id]));
        $responseJoin->assertStatus(403);

        // Sarah attempts to access Meeting Blade Page directly -> 403 Forbidden
        $responseView = $this->actingAs($this->studentSarah)
            ->get(route('student.meeting.show', ['id' => $session->id]));
        $responseView->assertStatus(403);

        Carbon::setTestNow();
    }

    /**
     * Test 6: Attendance Roster Scoping:
     * For a 1-to-1 session, the roster returns ONLY the assigned student, not all course students.
     */
    public function test_attendance_roster_returns_only_assigned_student_for_1_to_1_session(): void
    {
        $session = LiveSession::create([
            'title' => 'Ahmed Private Session for Roster Test',
            'student_user_id' => $this->studentAhmed->id,
            'teacher_profile_id' => $this->teacherProfile->id,
            'subject_id' => $this->coursePhysics->subject_id,
            'course_id' => $this->coursePhysics->id,
            'scheduled_at' => now()->addHour(),
            'start_at' => now()->addHour(),
            'end_at' => now()->addHours(2),
            'duration_minutes' => 60,
            'status' => 'scheduled',
        ]);

        $response = $this->actingAs($this->teacherUser)
            ->getJson("/ajax/teacher/sessions/{$session->id}/attendance-roster");

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonCount(1, 'students')
            ->assertJsonFragment(['id' => $this->studentAhmed->id, 'name' => 'Ahmed Mohamed'])
            ->assertJsonMissing(['id' => $this->studentSarah->id])
            ->assertJsonMissing(['id' => $this->studentOmar->id]);
    }

    /**
     * Test 7: Group Session Compatibility:
     * When student_user_id is NULL, all enrolled students see it and appear in the roster.
     */
    public function test_group_sessions_remain_visible_to_all_enrolled_students(): void
    {
        // Create Group session in Physics (Ahmed, Sarah, Omar are all enrolled)
        $groupSession = LiveSession::create([
            'title' => 'Physics Cohort General Workshop',
            'student_user_id' => null, // Group session
            'teacher_profile_id' => $this->teacherProfile->id,
            'subject_id' => $this->coursePhysics->subject_id,
            'course_id' => $this->coursePhysics->id,
            'scheduled_at' => now()->addHours(3),
            'start_at' => now()->addHours(3),
            'end_at' => now()->addHours(4),
            'duration_minutes' => 60,
            'status' => 'scheduled',
        ]);

        // Both Ahmed and Sarah see the group session
        $this->actingAs($this->studentAhmed)
            ->get('/student-portal?tab=sessions')
            ->assertOk()
            ->assertSee('Physics Cohort General Workshop');

        $this->actingAs($this->studentSarah)
            ->get('/student-portal?tab=sessions')
            ->assertOk()
            ->assertSee('Physics Cohort General Workshop');

        // Group attendance roster contains all 3 enrolled students
        $resRoster = $this->actingAs($this->teacherUser)
            ->getJson("/ajax/teacher/sessions/{$groupSession->id}/attendance-roster");

        $resRoster->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonCount(3, 'students')
            ->assertJsonFragment(['id' => $this->studentAhmed->id])
            ->assertJsonFragment(['id' => $this->studentSarah->id])
            ->assertJsonFragment(['id' => $this->studentOmar->id]);
    }
}
