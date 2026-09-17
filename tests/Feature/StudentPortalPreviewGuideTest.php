<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\GradeLevel;
use App\Models\PackageTemplate;
use App\Models\StudentPackage;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentPortalPreviewGuideTest extends TestCase
{
    use RefreshDatabase;

    private User $studentUser;
    private User $teacherUser;
    private Course $course;

    protected function setUp(): void
    {
        parent::setUp();

        $grade = GradeLevel::create(['name' => 'الثانوية العامة', 'slug' => 'thanaweya-guide']);
        $cat = Category::create(['name' => 'العلوم', 'slug' => 'sciences-guide']);
        $subject = Subject::create(['category_id' => $cat->id, 'name' => 'الكيمياء', 'slug' => 'chemistry-guide']);

        $this->teacherUser = User::create([
            'name' => 'أ. حسام فوزي',
            'email' => 'teacher.guide@elite.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        $teacherProfile = TeacherProfile::create([
            'user_id' => $this->teacherUser->id,
            'slug' => 'teacher-guide',
        ]);

        $this->studentUser = User::create([
            'name' => 'طالب النخبة',
            'email' => 'student.guide@elite.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        StudentProfile::create([
            'user_id' => $this->studentUser->id,
            'grade_level_id' => $grade->id,
        ]);

        $template = PackageTemplate::create([
            'name' => 'باقة التفوق',
            'sessions_count' => 12,
            'price' => 600,
        ]);
        StudentPackage::create([
            'student_user_id' => $this->studentUser->id,
            'package_template_id' => $template->id,
            'remaining_sessions' => 10,
            'total_sessions' => 12,
            'status' => 'active',
        ]);

        $this->course = Course::create([
            'teacher_id' => $teacherProfile->id,
            'subject_id' => $subject->id,
            'grade_level_id' => $grade->id,
            'title' => 'الكيمياء العضوية المتقدمة',
            'slug' => 'organic-chem-guide',
            'is_active' => true,
        ]);

        CourseEnrollment::create([
            'course_id' => $this->course->id,
            'student_user_id' => $this->studentUser->id,
            'status' => 'active',
            'enrolled_at' => now(),
        ]);
    }

    public function test_student_portal_renders_preview_guide_modal_and_hero_trigger(): void
    {
        $response = $this->actingAs($this->studentUser)->get(route('student-portal'));

        $response->assertStatus(200);

        // Assert modal exists
        $response->assertSee('id="studentPreviewGuideModal"', false);
        $response->assertSee('openStudentPreviewGuide', false);

        // Assert Hero feature button
        $response->assertSee('openStudentPreviewGuide(\'overview\')', false);

        // Assert all 8 section tabs exist
        $response->assertSee('id="stu-guide-tab-sessions"', false);
        $response->assertSee('id="stu-guide-tab-courses"', false);
        $response->assertSee('id="stu-guide-tab-assignments"', false);
        $response->assertSee('id="stu-guide-tab-submissions"', false);
        $response->assertSee('id="stu-guide-tab-packages"', false);
        $response->assertSee('id="stu-guide-tab-exceptions"', false);
        $response->assertSee('id="stu-guide-tab-notifications"', false);
        $response->assertSee('id="stu-guide-tab-overview"', false);

        // Assert all 8 section content panes exist
        $response->assertSee('id="stu-guide-content-sessions"', false);
        $response->assertSee('id="stu-guide-content-courses"', false);
        $response->assertSee('id="stu-guide-content-assignments"', false);
        $response->assertSee('id="stu-guide-content-submissions"', false);
        $response->assertSee('id="stu-guide-content-packages"', false);
        $response->assertSee('id="stu-guide-content-exceptions"', false);
        $response->assertSee('id="stu-guide-content-notifications"', false);
        $response->assertSee('id="stu-guide-content-overview"', false);

        // Assert interactive simulator elements are present
        $response->assertSee('id="demoJoinBtn"', false);
        $response->assertSee('id="demoMicToggle"', false);
        $response->assertSee('id="demoCamToggle"', false);
        $response->assertSee('id="demoCourseProgressBar"', false);
        $response->assertSee('id="demoQuizOptions"', false);
        $response->assertSee('id="demoCreditCounter"', false);
        $response->assertSee('id="demoNotifContainer"', false);
    }

    public function test_student_portal_sections_contain_individual_preview_triggers(): void
    {
        $response = $this->actingAs($this->studentUser)->get(route('student-portal'));

        $response->assertStatus(200);

        // Assert section-level triggers are present in rendered output
        $response->assertSee('openStudentPreviewGuide(\'sessions\')', false);
        $response->assertSee('openStudentPreviewGuide(\'courses\')', false);
        $response->assertSee('openStudentPreviewGuide(\'assignments\')', false);
        $response->assertSee('openStudentPreviewGuide(\'submissions\')', false);
        $response->assertSee('openStudentPreviewGuide(\'exceptions\')', false);
        $response->assertSee('openStudentPreviewGuide(\'packages\')', false);
        $response->assertSee('openStudentPreviewGuide(\'notifications\')', false);
    }
}
