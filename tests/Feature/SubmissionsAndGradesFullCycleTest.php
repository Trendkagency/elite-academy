<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Enums\SubmissionStatus;
use App\Models\Assignment;
use App\Models\AssignmentQuestion;
use App\Models\AssignmentQuestionOption;
use App\Models\AssignmentSecurityAudit;
use App\Models\AssignmentSubmission;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseSession;
use App\Models\CourseSessionProgress;
use App\Models\GradeLevel;
use App\Models\LiveSession;
use App\Models\ParentProfile;
use App\Models\StudentPackage;
use App\Models\StudentProfile;
use App\Models\StudentSession;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SubmissionsAndGradesFullCycleTest extends TestCase
{
    use RefreshDatabase;

    protected User $teacherUser;
    protected TeacherProfile $teacherProfile;
    protected User $studentUser;
    protected StudentProfile $studentProfile;
    protected User $parentUser;
    protected ParentProfile $parentProfile;
    protected Course $course;
    protected CourseSession $session1;
    protected CourseSession $session2;
    protected LiveSession $liveSession;
    protected CourseEnrollment $enrollment;

    protected function setUp(): void
    {
        parent::setUp();

        $grade = GradeLevel::create(['name' => 'Grade 12', 'slug' => 'grade-12']);
        $cat = Category::create(['name' => 'Science', 'slug' => 'science']);
        $subject = Subject::create(['category_id' => $cat->id, 'name' => 'Physics', 'slug' => 'physics']);

        // Teacher
        $this->teacherUser = User::create([
            'name' => 'Dr. Ahmed Zewail',
            'email' => 'zewail@elite.edu',
            'password' => bcrypt('secret123'),
            'role' => 'teacher',
            'status' => AccountStatus::APPROVED,
        ]);
        $this->teacherProfile = TeacherProfile::create([
            'user_id' => $this->teacherUser->id,
            'slug' => 'ahmed-zewail',
            'bio' => 'Senior Physics Educator',
        ]);

        // Student
        $this->studentUser = User::create([
            'name' => 'Omar Student',
            'email' => 'omar@elite.edu',
            'password' => bcrypt('secret123'),
            'role' => 'student',
            'status' => AccountStatus::APPROVED,
        ]);
        $this->studentProfile = StudentProfile::create([
            'user_id' => $this->studentUser->id,
            'student_code' => 'STU-1001',
            'grade_level_id' => $grade->id,
        ]);

        // Student Active Package
        StudentPackage::create([
            'student_user_id' => $this->studentUser->id,
            'status' => 'active',
            'remaining_sessions' => 10,
            'total_sessions' => 10,
            'expires_at' => now()->addMonth(),
        ]);

        // Parent
        $this->parentUser = User::create([
            'name' => 'Parent User',
            'email' => 'parent@elite.edu',
            'password' => bcrypt('secret123'),
            'role' => 'parent',
            'status' => AccountStatus::APPROVED,
        ]);
        $this->parentProfile = ParentProfile::create([
            'user_id' => $this->parentUser->id,
            'phone' => '01012345678',
        ]);
        DB::table('parent_student')->insert([
            'parent_user_id' => $this->parentUser->id,
            'student_user_id' => $this->studentUser->id,
            'relationship' => 'father',
            'is_primary' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Course & Sessions
        $this->course = Course::create([
            'subject_id' => $subject->id,
            'teacher_id' => $this->teacherProfile->id,
            'grade_level_id' => $grade->id,
            'title' => 'Advanced Physics & Electromagnetism',
            'slug' => 'advanced-physics',
            'is_active' => true,
        ]);

        $this->session1 = CourseSession::create([
            'course_id' => $this->course->id,
            'title' => 'Session 1: Coulomb Law & Electric Fields',
            'sort_order' => 1,
            'duration_minutes' => 60,
        ]);

        $this->session2 = CourseSession::create([
            'course_id' => $this->course->id,
            'title' => 'Session 2: Gauss Law & Flux',
            'sort_order' => 2,
            'duration_minutes' => 60,
        ]);

        $this->liveSession = LiveSession::create([
            'course_id' => $this->course->id,
            'teacher_profile_id' => $this->teacherProfile->id,
            'subject_id' => $subject->id,
            'title' => 'Live Interactive Stream 1',
            'scheduled_at' => now()->addDays(2),
            'duration_minutes' => 90,
            'status' => 'scheduled',
        ]);

        $this->enrollment = CourseEnrollment::create([
            'student_user_id' => $this->studentUser->id,
            'course_id' => $this->course->id,
            'status' => 'active',
            'enrolled_at' => now(),
        ]);
    }

    public function test_full_submissions_and_grades_lifecycle_end_to_end(): void
    {
        // ── 1. Teacher Creates and Publishes Assignment ───────────────────────
        $assignment = Assignment::create([
            'course_id' => $this->course->id,
            'course_session_id' => $this->session1->id,
            'live_session_id' => $this->liveSession->id,
            'teacher_profile_id' => $this->teacherProfile->id,
            'title' => 'Coulomb Force & Field MSQ Exam',
            'description' => 'Solve all electrical charge questions accurately.',
            'duration_minutes' => 30,
            'passing_score' => 70.0,
            'passing_grade' => 70.0,
            'status' => 'published',
            'is_mandatory' => true,
        ]);

        // Q1: Single choice (Points: 5)
        $q1 = AssignmentQuestion::create([
            'assignment_id' => $assignment->id,
            'question_text' => 'What is the SI unit of electric charge?',
            'question_type' => 'text',
            'points' => 5,
            'sort_order' => 1,
            'is_multiple_choice' => false,
        ]);
        $q1OptA = AssignmentQuestionOption::create(['question_id' => $q1->id, 'option_text' => 'Coulomb (C)', 'is_correct' => true, 'explanation' => 'Coulomb is the standard SI unit.']);
        $q1OptB = AssignmentQuestionOption::create(['question_id' => $q1->id, 'option_text' => 'Volt (V)', 'is_correct' => false]);
        $q1OptC = AssignmentQuestionOption::create(['question_id' => $q1->id, 'option_text' => 'Ampere (A)', 'is_correct' => false]);

        // Q2: Multiple Choice (Points: 5)
        $q2 = AssignmentQuestion::create([
            'assignment_id' => $assignment->id,
            'question_text' => 'Which of the following particles have a negative charge?',
            'question_type' => 'text',
            'points' => 5,
            'sort_order' => 2,
            'is_multiple_choice' => true,
        ]);
        $q2OptA = AssignmentQuestionOption::create(['question_id' => $q2->id, 'option_text' => 'Electron', 'is_correct' => true]);
        $q2OptB = AssignmentQuestionOption::create(['question_id' => $q2->id, 'option_text' => 'Proton', 'is_correct' => false]);
        $q2OptC = AssignmentQuestionOption::create(['question_id' => $q2->id, 'option_text' => 'Antiproton', 'is_correct' => true]);

        // ── 2. Student Loads Assignment Details (Safe Schema Check) ───────────
        $this->actingAs($this->studentUser);

        $detailsRes = $this->getJson("/ajax/assignments/{$assignment->id}/details");
        $detailsRes->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('assignment.title', 'Coulomb Force & Field MSQ Exam')
            ->assertJsonPath('assignment.passing_score', 70);

        // Verify correct answer keys are NOT leaked to student
        $responseData = $detailsRes->json();
        foreach ($responseData['assignment']['questions'] as $q) {
            foreach ($q['options'] as $opt) {
                $this->assertArrayNotHasKey('is_correct', $opt, 'Security leak: is_correct must not be exposed to student before submission');
            }
        }

        // ── 3. Student Auto-Saves Draft Answers & Step Navigation ─────────────
        $draftRes = $this->postJson('/ajax/assignments/save-answer', [
            'assignment_id' => $assignment->id,
            'question_id' => $q1->id,
            'selected_option_ids' => [$q1OptA->id],
        ]);
        $draftRes->assertStatus(200)->assertJsonPath('success', true);

        $stepRes = $this->postJson('/ajax/assignments/update-step', [
            'assignment_id' => $assignment->id,
            'current_step_index' => 1,
        ]);
        $stepRes->assertStatus(200)->assertJsonPath('success', true)->assertJsonPath('current_step_index', 1);

        // ── 4. Student Submits Complete Answers ───────────────────────────────
        $submitRes = $this->postJson('/ajax/assignments/submit', [
            'assignment_id' => $assignment->id,
            'answers' => [
                $q1->id => [$q1OptA->id], // Correct (+5 pts)
                $q2->id => [$q2OptA->id, $q2OptC->id], // Correct (+5 pts)
            ],
        ]);

        $submitRes->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('is_passed', true)
            ->assertJsonPath('score', 10)
            ->assertJsonPath('total_points', 10)
            ->assertJsonPath('percentage', 100);

        $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('student_user_id', $this->studentUser->id)
            ->first();

        $this->assertNotNull($submission);
        $this->assertEquals(SubmissionStatus::COMPLETED, $submission->status);
        $this->assertEquals(100.0, (float) $submission->percentage);
        $this->assertEquals(100.0, (float) $submission->grade);
        $this->assertTrue($submission->isPassed());

        // Verify StudentSession state updated
        $studentSession = StudentSession::where('student_user_id', $this->studentUser->id)
            ->where('live_session_id', $this->liveSession->id)
            ->first();
        $this->assertNotNull($studentSession);
        $this->assertEquals('passed', $studentSession->assignment_status);
        $this->assertEquals('completed', $studentSession->session_status);

        // Verify Sequential Session Unlocking: Next session (session 2) must be unlocked!
        $session2Progress = CourseSessionProgress::where('course_enrollment_id', $this->enrollment->id)
            ->where('course_session_id', $this->session2->id)
            ->first();
        $this->assertNotNull($session2Progress);
        $this->assertEquals('unlocked', $session2Progress->status->value);

        // ── 5. Duplicate Submission Attempt Must Be Prevented ────────────────
        $duplicateRes = $this->postJson('/ajax/assignments/submit', [
            'assignment_id' => $assignment->id,
            'answers' => [
                $q1->id => [$q1OptB->id],
            ],
        ]);
        $duplicateRes->assertStatus(422);

        // ── 6. Teacher Reviews Submission & Auto-Correction Breakdown ─────────
        $this->actingAs($this->teacherUser);

        $reviewDetailsRes = $this->getJson("/ajax/teacher/submissions/{$submission->id}/review-details");
        $reviewDetailsRes->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('submission.id', $submission->id);

        $reviewData = $reviewDetailsRes->json();
        $this->assertCount(2, $reviewData['questions']);
        $this->assertTrue($reviewData['questions'][0]['is_correct']);
        $this->assertEquals(5.0, (float) $reviewData['questions'][0]['points_earned']);

        // ── 7. Teacher Adjusts Grade & Adds Pedagogical Notes ─────────────────
        $regradeRes = $this->postJson("/ajax/teacher/submissions/{$submission->id}/review", [
            'score' => 95.0,
            'evaluation_notes' => 'Exceptional theoretical understanding and accurate derivations!',
        ]);

        $regradeRes->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('is_passed', true);
        $this->assertEquals(95.0, (float) $regradeRes->json('score'));

        $freshSubmission = $submission->fresh();
        $this->assertEquals(95.0, (float) $freshSubmission->grade);
        $this->assertEquals(95.0, (float) $freshSubmission->score);
        $this->assertEquals(95.0, (float) $freshSubmission->percentage);
        $this->assertEquals(SubmissionStatus::REVIEWED, $freshSubmission->status);
        $this->assertStringContainsString('Exceptional theoretical understanding', $freshSubmission->evaluation_notes);

        // ── 8. Parent Portal Reflected Grades & Performance Monitoring ───────
        $this->actingAs($this->parentUser);

        $parentProgressRes = $this->getJson("/ajax/parent/student/{$this->studentUser->id}/progress");
        $parentProgressRes->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('student.name', 'Omar Student');

        $parentData = $parentProgressRes->json();
        $this->assertGreaterThanOrEqual(90.0, (float) $parentData['average_grade']);
        $this->assertGreaterThanOrEqual(1, (int) $parentData['submissions_count']);
        $this->assertCount(1, $parentData['submissions']);
        $this->assertEquals(95.0, (float) $parentData['submissions'][0]['grade_num']);
    }

    public function test_failing_submission_behavior_and_teacher_override_unlock(): void
    {
        $assignment = Assignment::create([
            'course_id' => $this->course->id,
            'course_session_id' => $this->session1->id,
            'live_session_id' => $this->liveSession->id,
            'teacher_profile_id' => $this->teacherProfile->id,
            'title' => 'Rigorous Magnetic Induction Quiz',
            'duration_minutes' => 20,
            'passing_score' => 75.0,
            'status' => 'published',
        ]);

        $q = AssignmentQuestion::create([
            'assignment_id' => $assignment->id,
            'question_text' => 'State Faradays Law of Induction.',
            'question_type' => 'text',
            'points' => 10,
            'sort_order' => 1,
        ]);
        $optRight = AssignmentQuestionOption::create(['question_id' => $q->id, 'option_text' => 'EMF is negative rate of change of magnetic flux', 'is_correct' => true]);
        $optWrong = AssignmentQuestionOption::create(['question_id' => $q->id, 'option_text' => 'Flux remains strictly constant', 'is_correct' => false]);

        // Student submits WRONG answer (0% earned, below 75% pass mark)
        $this->actingAs($this->studentUser);
        $submitRes = $this->postJson('/ajax/assignments/submit', [
            'assignment_id' => $assignment->id,
            'answers' => [
                $q->id => [$optWrong->id],
            ],
        ]);

        $submitRes->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('is_passed', false)
            ->assertJsonPath('percentage', 0);

        $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('student_user_id', $this->studentUser->id)
            ->first();

        $this->assertFalse($submission->isPassed());

        // Next session must NOT be unlocked
        $session2Progress = CourseSessionProgress::where('course_enrollment_id', $this->enrollment->id)
            ->where('course_session_id', $this->session2->id)
            ->first();
        $this->assertNull($session2Progress, 'Session 2 must remain locked when student fails assignment');

        // Teacher overrides grade with passing score (80%) after oral re-examination
        $this->actingAs($this->teacherUser);
        $reviewRes = $this->postJson("/ajax/teacher/submissions/{$submission->id}/review", [
            'score' => 80.0,
            'evaluation_notes' => 'Passed after oral clarification and re-answering.',
        ]);

        $reviewRes->assertStatus(200)->assertJsonPath('is_passed', true);
        $this->assertTrue($submission->fresh()->isPassed());

        // Now next session must be unlocked!
        $session2ProgressAfter = CourseSessionProgress::where('course_enrollment_id', $this->enrollment->id)
            ->where('course_session_id', $this->session2->id)
            ->first();
        $this->assertNotNull($session2ProgressAfter, 'Session 2 must be unlocked once teacher awards passing score');
        $this->assertEquals('unlocked', $session2ProgressAfter->status->value);
    }

    public function test_anti_cheating_telemetry_audit_logging(): void
    {
        $assignment = Assignment::create([
            'course_id' => $this->course->id,
            'teacher_profile_id' => $this->teacherProfile->id,
            'title' => 'Telemetry Monitored Examination',
            'status' => 'published',
        ]);

        $this->actingAs($this->studentUser);

        $telemetryRes = $this->postJson("/ajax/assignments/{$assignment->id}/security-audit", [
            'event_type' => 'TAB_SWITCH',
            'metadata' => [
                'blurred_at' => now()->toIso8601String(),
                'switch_count' => 1,
            ],
        ]);

        $telemetryRes->assertStatus(200)
            ->assertJsonPath('success', true);

        $audit = AssignmentSecurityAudit::where('assignment_id', $assignment->id)
            ->where('student_user_id', $this->studentUser->id)
            ->first();

        $this->assertNotNull($audit);
        $this->assertEquals('TAB_SWITCH', $audit->event_type);
        $this->assertEquals(2, $audit->risk_score);
    }
}
