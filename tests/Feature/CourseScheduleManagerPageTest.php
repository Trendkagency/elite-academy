<?php

namespace Tests\Feature;

use App\Filament\Pages\CourseScheduleManagerPage;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\GradeLevel;
use App\Models\LiveSession;
use App\Models\RecurringSchedule;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CourseScheduleManagerPageTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Course $course;
    protected TeacherProfile $teacher;
    protected User $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Schedule Super Admin',
            'email' => 'admin_schedule@elite.test',
            'password' => bcrypt('AdminPass123!'),
            'role' => 'super_admin',
            'status' => \App\Enums\AccountStatus::APPROVED,
        ]);
        \App\Models\AdminProfile::create(['user_id' => $this->admin->id]);

        $category = Category::create(['name' => 'Mathematics', 'slug' => 'mathematics']);
        $subject = Subject::create(['category_id' => $category->id, 'name' => 'Calculus', 'slug' => 'calculus']);
        $grade = GradeLevel::create(['name' => 'Grade 12', 'code' => 'G12', 'slug' => 'g12', 'sort_order' => 1]);

        $teacherUser = User::create([
            'name' => 'Prof. Calculus',
            'email' => 'prof_calc@test.com',
            'password' => bcrypt('TeacherPass123!'),
            'role' => 'teacher',
            'status' => \App\Enums\AccountStatus::APPROVED,
        ]);
        $this->teacher = TeacherProfile::create([
            'user_id' => $teacherUser->id,
            'status' => 'approved',
            'title' => 'Senior Math Instructor',
            'slug' => 'senior-math-instructor',
        ]);

        $this->course = Course::create([
            'subject_id' => $subject->id,
            'teacher_id' => $this->teacher->id,
            'grade_level_id' => $grade->id,
            'title' => 'Advanced Calculus 101',
            'slug' => 'advanced-calculus-101',
            'is_active' => true,
        ]);

        $this->student = User::create([
            'name' => 'John Student',
            'email' => 'john_student@test.com',
            'password' => bcrypt('StudentPass123!'),
            'role' => 'student',
            'status' => \App\Enums\AccountStatus::APPROVED,
        ]);
        StudentProfile::create(['user_id' => $this->student->id, 'student_code' => 'STU-100', 'grade_level_id' => $grade->id]);

        CourseEnrollment::create([
            'course_id' => $this->course->id,
            'student_user_id' => $this->student->id,
            'cohort' => 'Cohort 2026',
            'status' => 'active',
        ]);
    }

    public function test_admin_can_render_course_schedule_manager_page(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(CourseScheduleManagerPage::class)
            ->assertSuccessful()
            ->assertSee('Advanced Calculus 101')
            ->assertSee('Prof. Calculus');
    }

    public function test_admin_can_schedule_single_live_session_for_student(): void
    {
        $this->actingAs($this->admin);

        $sessionTime = now()->addDays(2)->setTime(14, 0);

        Livewire::test(CourseScheduleManagerPage::class)
            ->set('singleCourseId', $this->course->id)
            ->set('singleTeacherId', $this->teacher->id)
            ->set('singleStudentId', $this->student->id)
            ->set('singleTitle', 'Derivatives & Limits 1-on-1')
            ->set('singleScheduledAt', $sessionTime->format('Y-m-d\TH:i'))
            ->set('singleDuration', 90)
            ->set('singleMeetingPlatform', 'agora')
            ->call('createSingleSession')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('live_sessions', [
            'course_id' => $this->course->id,
            'teacher_profile_id' => $this->teacher->id,
            'student_user_id' => $this->student->id,
            'title' => 'Derivatives & Limits 1-on-1',
            'duration_minutes' => 90,
            'status' => 'scheduled',
        ]);
    }

    public function test_admin_can_generate_recurring_schedule_for_student(): void
    {
        $this->actingAs($this->admin);

        $startDate = now()->startOfWeek(Carbon::MONDAY)->format('Y-m-d');
        $endDate = now()->startOfWeek(Carbon::MONDAY)->addWeeks(2)->format('Y-m-d');

        Livewire::test(CourseScheduleManagerPage::class)
            ->set('recCourseId', $this->course->id)
            ->set('recTeacherId', $this->teacher->id)
            ->set('recStudentIds', [$this->student->id])
            ->set('recTitle', 'Bi-Weekly Calculus Routine')
            ->set('recType', 'weekly')
            ->set('recDays', [1, 3]) // Monday & Wednesday
            ->set('recStartTime', '11:00')
            ->set('recDuration', 60)
            ->set('recStartDate', $startDate)
            ->set('recEndDate', $endDate)
            ->call('submitRecurringSchedule')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('recurring_schedules', [
            'course_id' => $this->course->id,
            'student_user_id' => $this->student->id,
            'teacher_profile_id' => $this->teacher->id,
            'title' => 'Bi-Weekly Calculus Routine - Student #' . $this->student->id,
            'status' => 'active',
        ]);

        $generatedCount = LiveSession::where('course_id', $this->course->id)
            ->where('student_user_id', $this->student->id)
            ->count();

        $this->assertGreaterThan(0, $generatedCount);
    }

    public function test_admin_can_reschedule_cancel_and_update_meeting_link(): void
    {
        $this->actingAs($this->admin);

        $session = LiveSession::create([
            'course_id' => $this->course->id,
            'subject_id' => $this->course->subject_id,
            'teacher_profile_id' => $this->teacher->id,
            'student_user_id' => $this->student->id,
            'title' => 'Midterm Review',
            'scheduled_at' => now()->addDay()->setTime(10, 0),
            'duration_minutes' => 60,
            'status' => 'scheduled',
        ]);

        $newDate = now()->addDays(3)->setTime(16, 0);

        // Test Reschedule
        Livewire::test(CourseScheduleManagerPage::class)
            ->set('targetSessionId', $session->id)
            ->set('rescheduleNewDate', $newDate->format('Y-m-d\TH:i'))
            ->set('rescheduleReason', 'Student requested evening slot')
            ->call('confirmReschedule')
            ->assertHasNoErrors();

        $this->assertEquals($newDate->format('Y-m-d H:i'), $session->fresh()->scheduled_at->format('Y-m-d H:i'));

        // Test Meeting Link Update
        Livewire::test(CourseScheduleManagerPage::class)
            ->set('linkSessionId', $session->id)
            ->set('linkUrl', 'https://meet.google.com/abc-defg-hij')
            ->call('saveMeetingLink')
            ->assertHasNoErrors();

        $this->assertEquals('https://meet.google.com/abc-defg-hij', $session->fresh()->meeting_link);

        // Test Cancel
        Livewire::test(CourseScheduleManagerPage::class)
            ->set('cancelSessionId', $session->id)
            ->set('cancelReason', 'Emergency reschedule needed')
            ->call('confirmCancel')
            ->assertHasNoErrors();

        $this->assertEquals('cancelled', $session->fresh()->status);
    }

    public function test_selecting_course_dynamically_filters_enrolled_students_and_updates_recurrence_cycle(): void
    {
        $this->actingAs($this->admin);

        $otherStudent = User::create([
            'name' => 'Unenrolled Student',
            'email' => 'unenrolled@test.com',
            'password' => bcrypt('Pass123!'),
            'role' => 'student',
            'status' => \App\Enums\AccountStatus::APPROVED,
        ]);
        StudentProfile::create(['user_id' => $otherStudent->id, 'student_code' => 'STU-999']);

        $startDate = '2026-10-01';

        $component = Livewire::test(CourseScheduleManagerPage::class)
            ->call('openRecurringModal')
            ->set('recCourseId', $this->course->id)
            ->set('recStartDate', $startDate)
            ->set('recType', 'monthly');

        // Verify that recTeacherId was auto-filled from course
        $this->assertEquals($this->teacher->id, $component->get('recTeacherId'));

        // Verify that recurrence end date is automatically calculated (+1 month for monthly)
        $this->assertEquals('2026-11-01', $component->get('recEndDate'));

        // Test changing type to multi_month (+3 months)
        $component->set('recType', 'multi_month');
        $this->assertEquals('2027-01-01', $component->get('recEndDate'));

        // Verify filtered students only contains enrolled student for this course
        $filteredStudents = $component->get('filteredRecStudents');
        $this->assertTrue($filteredStudents->contains('id', $this->student->id));
        $this->assertFalse($filteredStudents->contains('id', $otherStudent->id));
    }

    public function test_teacher_can_access_course_schedule_manager_scoped_to_own_courses(): void
    {
        $teacherUser = $this->teacher->user;
        $this->actingAs($teacherUser);

        // Other teacher and course
        $otherTeacherUser = User::create([
            'name' => 'Other Instructor',
            'email' => 'other_inst@test.com',
            'password' => bcrypt('Pass123!'),
            'role' => 'teacher',
            'status' => \App\Enums\AccountStatus::APPROVED,
        ]);
        $otherTeacher = TeacherProfile::create([
            'user_id' => $otherTeacherUser->id,
            'status' => 'approved',
            'title' => 'Biology Teacher',
            'slug' => 'biology-teacher',
        ]);
        $otherCourse = Course::create([
            'subject_id' => $this->course->subject_id,
            'teacher_id' => $otherTeacher->id,
            'title' => 'Secret Biology Course',
            'slug' => 'secret-biology-course',
            'is_active' => true,
        ]);

        $component = Livewire::test(CourseScheduleManagerPage::class)
            ->assertSuccessful()
            ->assertSee('Advanced Calculus 101')
            ->assertDontSee('Secret Biology Course');

        $this->assertTrue($component->get('isTeacherOnly'));
        $this->assertEquals($this->teacher->id, $component->get('teacherProfileId'));

        // Check courses query only returns this teacher's courses
        $courses = $component->get('courses');
        $this->assertTrue($courses->contains('id', $this->course->id));
        $this->assertFalse($courses->contains('id', $otherCourse->id));
    }
}
