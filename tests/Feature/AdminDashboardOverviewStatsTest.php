<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Filament\Widgets\AdminOverviewStatsWidget;
use App\Models\Category;
use App\Models\Course;
use App\Models\ExceptionRequest;
use App\Models\LiveSession;
use App\Models\StudentPackage;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardOverviewStatsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_overview_stats_widget_computes_all_12_points(): void
    {
        // 1. Create Students
        $activeStudent = User::factory()->create(['status' => AccountStatus::APPROVED]);
        \App\Models\StudentProfile::create(['user_id' => $activeStudent->id]);

        $pendingStudent = User::factory()->create(['status' => AccountStatus::PENDING]);
        \App\Models\StudentProfile::create(['user_id' => $pendingStudent->id]);

        // 2. Create Parent
        $parentUser = User::factory()->create(['status' => AccountStatus::APPROVED]);
        \App\Models\ParentProfile::create(['user_id' => $parentUser->id]);

        // 3. Create Teacher & Subject
        $teacherUser = User::factory()->create(['status' => AccountStatus::APPROVED]);
        $teacher = TeacherProfile::create(['user_id' => $teacherUser->id, 'slug' => 'stat-teacher']);
        $cat = Category::create(['name' => 'Stat Cat', 'slug' => 'stat-cat']);
        $sub = Subject::create(['category_id' => $cat->id, 'name' => 'Stat Sub', 'slug' => 'stat-sub']);
        $course = Course::create(['title' => 'Stat Course', 'slug' => 'stat-course', 'teacher_id' => $teacher->id, 'subject_id' => $sub->id]);

        // 4. Create Sessions
        LiveSession::create([
            'student_user_id' => $activeStudent->id,
            'teacher_profile_id' => $teacher->id,
            'subject_id' => $sub->id,
            'course_id' => $course->id,
            'scheduled_at' => now(),
            'start_at' => now(),
            'end_at' => now()->addHour(),
            'status' => 'completed',
        ]);

        LiveSession::create([
            'student_user_id' => $activeStudent->id,
            'teacher_profile_id' => $teacher->id,
            'subject_id' => $sub->id,
            'course_id' => $course->id,
            'scheduled_at' => now()->addDays(2),
            'status' => 'scheduled',
        ]);

        LiveSession::create([
            'student_user_id' => $activeStudent->id,
            'teacher_profile_id' => $teacher->id,
            'subject_id' => $sub->id,
            'course_id' => $course->id,
            'scheduled_at' => now()->addDays(3),
            'status' => 'cancelled_by_teacher',
        ]);

        // 5. Create Packages & Exceptions
        StudentPackage::create([
            'student_user_id' => $activeStudent->id,
            'total_sessions' => 10,
            'remaining_sessions' => 10,
            'status' => 'active',
        ]);

        StudentPackage::create([
            'student_user_id' => $pendingStudent->id,
            'total_sessions' => 5,
            'remaining_sessions' => 5,
            'status' => 'pending',
        ]);

        ExceptionRequest::create([
            'student_user_id' => $activeStudent->id,
            'reason' => 'Absence excuse',
            'status' => 'pending',
        ]);

        // Verify Widget instantiates and retrieves stats array of length 12
        $widget = new class extends AdminOverviewStatsWidget {
            public function testGetStats(): array
            {
                return $this->getStats();
            }
        };

        $stats = $widget->testGetStats();
        $this->assertCount(12, $stats);
    }
}
