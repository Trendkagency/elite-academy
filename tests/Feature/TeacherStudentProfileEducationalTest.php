<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\LiveSession;
use App\Models\StudentEducationalNote;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherStudentProfileEducationalTest extends TestCase
{
    use RefreshDatabase;

    protected User $teacherUser;
    protected TeacherProfile $teacherProfile;
    protected User $teacherUser2;
    protected TeacherProfile $teacherProfile2;

    protected User $assignedStudentUser;
    protected StudentProfile $assignedStudentProfile;
    protected User $unassignedStudentUser;
    protected StudentProfile $unassignedStudentProfile;

    protected Course $course;

    protected function setUp(): void
    {
        parent::setUp();

        $category = Category::create(['name' => 'Sciences', 'slug' => 'sciences', 'sort_order' => 1]);
        $subject = Subject::create(['category_id' => $category->id, 'name' => 'Physics', 'slug' => 'physics', 'is_active' => true]);

        // 1. Teacher 1
        $this->teacherUser = User::create([
            'name' => 'Prof. Richard Feynman',
            'email' => 'feynman@elite-academy.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        $this->teacherProfile = TeacherProfile::create([
            'user_id' => $this->teacherUser->id,
            'slug' => 'richard-feynman',
            'title' => 'Professor of Physics',
            'specialization' => 'Theoretical Physics',
            'years_experience' => 15,
            'rating_avg' => 4.95,
            'students_count' => 1200,
        ]);

        // 2. Teacher 2 (for IDOR isolation test)
        $this->teacherUser2 = User::create([
            'name' => 'Prof. Albert Einstein',
            'email' => 'einstein@elite-academy.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        $this->teacherProfile2 = TeacherProfile::create([
            'user_id' => $this->teacherUser2->id,
            'slug' => 'albert-einstein',
            'title' => 'Professor of Relativity',
            'specialization' => 'Astrophysics',
            'years_experience' => 20,
            'rating_avg' => 5.0,
            'students_count' => 800,
        ]);

        // 3. Course owned by Teacher 1
        $this->course = Course::create([
            'subject_id' => $subject->id,
            'teacher_id' => $this->teacherProfile->id,
            'title' => 'Quantum Mechanics 101',
            'slug' => 'qm-101',
            'sessions_count' => 8,
            'is_active' => true,
        ]);

        // 4. Student 1 (Enrolled in Teacher 1 course)
        $this->assignedStudentUser = User::create([
            'name' => 'Student Marie Curie',
            'email' => 'marie@curie.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        $this->assignedStudentProfile = StudentProfile::create([
            'user_id' => $this->assignedStudentUser->id,
            'school_name' => 'Sorbonne Academy',
        ]);
        CourseEnrollment::create([
            'course_id' => $this->course->id,
            'student_user_id' => $this->assignedStudentUser->id,
            'status' => 'active',
            'enrolled_at' => now(),
        ]);

        // 5. Student 2 (NOT enrolled in Teacher 1 course)
        $this->unassignedStudentUser = User::create([
            'name' => 'Student Niels Bohr',
            'email' => 'bohr@copenhagen.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        $this->unassignedStudentProfile = StudentProfile::create([
            'user_id' => $this->unassignedStudentUser->id,
            'school_name' => 'Copenhagen High',
        ]);
    }

    public function test_teacher_can_view_my_students_roster_in_portal(): void
    {
        $response = $this->actingAs($this->teacherUser)->get('/teacher-portal?tab=students');

        $response->assertStatus(200)
            ->assertSee('Student Marie Curie')
            ->assertSee('Sorbonne Academy')
            ->assertSee('Quantum Mechanics 101');
    }

    public function test_teacher_can_fetch_assigned_student_educational_details(): void
    {
        // Add sample assignment & submission
        $assignment = Assignment::create([
            'course_id' => $this->course->id,
            'teacher_profile_id' => $this->teacherProfile->id,
            'title' => 'Midterm Problem Set',
            'passing_score' => 70,
            'due_at' => now()->addDays(3),
        ]);

        AssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'student_user_id' => $this->assignedStudentUser->id,
            'score' => 92.5,
            'status' => 'reviewed',
            'submitted_at' => now(),
            'evaluation_notes' => 'Excellent analytical derivation!',
        ]);

        // Add sample live session
        LiveSession::create([
            'teacher_profile_id' => $this->teacherProfile->id,
            'student_user_id' => $this->assignedStudentUser->id,
            'course_id' => $this->course->id,
            'title' => 'Quantum State Vectors Lab',
            'scheduled_at' => now()->subDay(),
            'attendance_status' => 'present',
            'duration_minutes' => 60,
        ]);

        $response = $this->actingAs($this->teacherUser)
            ->getJson("/ajax/teacher/students/{$this->assignedStudentUser->id}/details");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'student' => [
                    'id' => $this->assignedStudentUser->id,
                    'name' => 'Student Marie Curie',
                    'school' => 'Sorbonne Academy',
                ],
            ])
            ->assertJsonPath('metrics.attendance_rate', 100)
            ->assertJsonPath('metrics.avg_score', 92.5)
            ->assertJsonPath('courses.0.title', 'Quantum Mechanics 101')
            ->assertJsonPath('submissions.0.assignment_title', 'Midterm Problem Set')
            ->assertJsonPath('submissions.0.score', 92.5);
    }

    public function test_idor_protection_teacher_cannot_access_unassigned_student_details(): void
    {
        $response = $this->actingAs($this->teacherUser)
            ->getJson("/ajax/teacher/students/{$this->unassignedStudentUser->id}/details");

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_teacher_can_add_educational_note_for_assigned_student(): void
    {
        $payload = [
            'category' => 'academic',
            'note' => 'Student demonstrates exceptional mastery of quantum harmonic oscillators.',
        ];

        $response = $this->actingAs($this->teacherUser)
            ->postJson("/ajax/teacher/students/{$this->assignedStudentUser->id}/notes", $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'note' => [
                    'category' => 'academic',
                    'note' => 'Student demonstrates exceptional mastery of quantum harmonic oscillators.',
                ],
            ]);

        $this->assertDatabaseHas('student_educational_notes', [
            'teacher_profile_id' => $this->teacherProfile->id,
            'student_user_id' => $this->assignedStudentUser->id,
            'category' => 'academic',
        ]);
    }

    public function test_idor_protection_teacher_cannot_add_note_for_unassigned_student(): void
    {
        $payload = [
            'category' => 'academic',
            'note' => 'Trying to add note to unassigned student',
        ];

        $response = $this->actingAs($this->teacherUser)
            ->postJson("/ajax/teacher/students/{$this->unassignedStudentUser->id}/notes", $payload);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
            ]);

        $this->assertDatabaseMissing('student_educational_notes', [
            'student_user_id' => $this->unassignedStudentUser->id,
        ]);
    }

    public function test_direct_student_profile_url_redirects_for_assigned_student(): void
    {
        $response = $this->actingAs($this->teacherUser)
            ->get("/teacher/students/{$this->assignedStudentUser->id}");

        $response->assertRedirect("/teacher-portal?tab=students&student={$this->assignedStudentUser->id}");
    }

    public function test_direct_student_profile_url_aborts_403_for_unassigned_student(): void
    {
        $response = $this->actingAs($this->teacherUser)
            ->get("/teacher/students/{$this->unassignedStudentUser->id}");

        $response->assertStatus(403);
    }

    public function test_teacher_can_fetch_session_attendance_roster_for_own_course(): void
    {
        $session = LiveSession::create([
            'teacher_profile_id' => $this->teacherProfile->id,
            'course_id' => $this->course->id,
            'title' => 'Quantum Cohort Lecture #2',
            'scheduled_at' => now()->addDay(),
            'duration_minutes' => 60,
        ]);

        $response = $this->actingAs($this->teacherUser)
            ->getJson("/ajax/teacher/sessions/{$session->id}/attendance-roster");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'session' => [
                    'id' => $session->id,
                    'title' => 'Quantum Cohort Lecture #2',
                ],
            ])
            ->assertJsonPath('students.0.id', $this->assignedStudentUser->id)
            ->assertJsonPath('students.0.name', 'Student Marie Curie')
            ->assertJsonPath('students.0.status', 'present');
    }

    public function test_teacher_cannot_fetch_attendance_roster_for_other_teacher_session(): void
    {
        $otherSession = LiveSession::create([
            'teacher_profile_id' => $this->teacherProfile2->id,
            'title' => 'Relativity Lecture',
            'scheduled_at' => now()->addDay(),
        ]);

        $response = $this->actingAs($this->teacherUser)
            ->getJson("/ajax/teacher/sessions/{$otherSession->id}/attendance-roster");

        $response->assertStatus(403)
            ->assertJson(['success' => false]);
    }

    public function test_phone_number_security_blocks_teacher_from_sending_phone_number_in_notes(): void
    {
        $testNumbers = [
            'Call me on +201099475854 for questions',
            'Contact 00201099475854 anytime',
            'My WhatsApp is 01099475854',
            'Number is 0 1 0 9 9 4 7 5 8 5 4',
            'تواصل معي على الرقم ٠١٠٩٩٤٧٥٨٥٤',
            'Direct line: +966 50 123 4567',
        ];

        foreach ($testNumbers as $invalidNote) {
            $response = $this->actingAs($this->teacherUser)
                ->postJson("/ajax/teacher/students/{$this->assignedStudentUser->id}/notes", [
                    'category' => 'general',
                    'note' => $invalidNote,
                ]);

            $response->assertStatus(422)
                ->assertJson([
                    'success' => false,
                ]);
        }
    }

    public function test_student_portal_renders_teacher_pedagogical_notes(): void
    {
        \App\Models\StudentEducationalNote::create([
            'teacher_profile_id' => $this->teacherProfile->id,
            'student_user_id' => $this->assignedStudentUser->id,
            'category' => 'academic',
            'note' => 'Great performance in quantum thermodynamics!',
        ]);

        $response = $this->actingAs($this->assignedStudentUser)
            ->get('/student-portal');

        $response->assertStatus(200);
        $response->assertSee('Great performance in quantum thermodynamics!');
    }

    public function test_fcm_notification_is_generated_when_teacher_adds_educational_note(): void
    {
        $response = $this->actingAs($this->teacherUser)
            ->postJson("/ajax/teacher/students/{$this->assignedStudentUser->id}/notes", [
                'category' => 'homework',
                'note' => 'Excellent homework analysis and clear problem-solving steps.',
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $this->assignedStudentUser->id,
            'type' => 'TEACHER_NOTE_ADDED',
        ]);
    }
}
