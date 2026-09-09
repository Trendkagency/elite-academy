<?php

namespace App\Filament\Pages;

use App\Models\AssignmentSubmission;
use App\Models\CourseEnrollment;
use App\Models\ExceptionRequest;
use App\Models\LiveSession;
use App\Models\PackageTransaction;
use App\Models\StudentEducationalNote;
use App\Models\StudentPackage;
use App\Models\User;
use Filament\Pages\Page;
use Livewire\Attributes\Url;

class StudentReportsPage extends Page
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.student-reports-page';

    #[Url(as: 'student_id')]
    public ?int $selectedStudentId = null;

    public string $activeTab = 'overview';

    public ?string $searchQuery = '';

    public static function getNavigationGroup(): ?string
    {
        return __('Reports & Analytics');
    }

    public static function getNavigationLabel(): string
    {
        return __('Student Reports');
    }

    public function getTitle(): string
    {
        return __('Student Comprehensive Reports & Academic History');
    }

    public function mount(): void
    {
        if (! $this->selectedStudentId) {
            $firstStudent = User::roleStudent()->first();
            $this->selectedStudentId = $firstStudent?->id;
        }
    }

    public function selectStudent(int $id): void
    {
        $this->selectedStudentId = $id;
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function getStudentsListProperty()
    {
        $query = User::roleStudent()
            ->with(['studentProfile.gradeLevel']);

        if ($this->searchQuery) {
            $s = trim($this->searchQuery);
            $query->where(function ($q) use ($s) {
                $q->where('name', 'LIKE', "%{$s}%")
                    ->orWhere('phone', 'LIKE', "%{$s}%")
                    ->orWhere('email', 'LIKE', "%{$s}%");
            });
        }

        return $query->orderBy('name')->get();
    }

    public function getSelectedStudentProperty(): ?User
    {
        if (! $this->selectedStudentId) {
            return null;
        }

        return User::where('id', $this->selectedStudentId)
            ->with([
                'studentProfile.gradeLevel',
                'studentProfile.subjects',
                'parents',
            ])
            ->first();
    }

    public function getStudentMetricsProperty(): array
    {
        $student = $this->selectedStudent;
        if (! $student) {
            return [
                'enrolled_courses_count' => 0,
                'total_packages_count' => 0,
                'total_sessions_balance' => 0,
                'used_sessions_balance' => 0,
                'remaining_sessions_balance' => 0,
                'total_live_sessions' => 0,
                'attended_sessions' => 0,
                'attendance_rate' => 0,
                'total_submissions' => 0,
                'graded_submissions' => 0,
                'average_grade' => 0,
                'notes_count' => 0,
                'exceptions_count' => 0,
            ];
        }

        $enrollments = CourseEnrollment::where('student_user_id', $student->id)->get();
        $packages = StudentPackage::where('student_user_id', $student->id)->get();
        $liveSessions = LiveSession::where('student_user_id', $student->id)->get();
        $submissions = AssignmentSubmission::where('student_user_id', $student->id)->get();
        $notes = StudentEducationalNote::where('student_user_id', $student->id)->get();
        $exceptions = ExceptionRequest::where('student_user_id', $student->id)->get();

        $totalBalance = $packages->sum('total_sessions');
        $usedBalance = $packages->sum('used_sessions');
        $remainingBalance = $packages->sum('remaining_sessions');

        $totalLive = $liveSessions->count();
        $attendedLive = $liveSessions->filter(fn ($s) => in_array($s->attendance_status, ['present', 'attended'], true) || $s->status === 'completed')->count();
        $attendanceRate = $totalLive > 0 ? round(($attendedLive / $totalLive) * 100) : 0;

        $gradedSubmissions = $submissions->whereNotNull('grade');
        $avgGrade = $gradedSubmissions->count() > 0 ? round($gradedSubmissions->avg('grade'), 1) : 0;

        return [
            'enrolled_courses_count' => $enrollments->count(),
            'total_packages_count' => $packages->count(),
            'total_sessions_balance' => $totalBalance,
            'used_sessions_balance' => $usedBalance,
            'remaining_sessions_balance' => $remainingBalance,
            'total_live_sessions' => $totalLive,
            'attended_sessions' => $attendedLive,
            'attendance_rate' => $attendanceRate,
            'total_submissions' => $submissions->count(),
            'graded_submissions' => $gradedSubmissions->count(),
            'average_grade' => $avgGrade,
            'notes_count' => $notes->count(),
            'exceptions_count' => $exceptions->count(),
        ];
    }

    public function getEnrollmentsProperty()
    {
        if (! $this->selectedStudentId) {
            return collect();
        }

        return CourseEnrollment::where('student_user_id', $this->selectedStudentId)
            ->with(['course.subject', 'course.teacher.user', 'sessionProgress'])
            ->latest('enrolled_at')
            ->get();
    }

    public function getPackagesProperty()
    {
        if (! $this->selectedStudentId) {
            return collect();
        }

        return StudentPackage::where('student_user_id', $this->selectedStudentId)
            ->with(['packageTemplate', 'transactions'])
            ->latest('activated_at')
            ->get();
    }

    public function getLiveSessionsProperty()
    {
        if (! $this->selectedStudentId) {
            return collect();
        }

        return LiveSession::where('student_user_id', $this->selectedStudentId)
            ->with(['course.subject', 'teacherProfile.user'])
            ->orderBy('scheduled_at', 'desc')
            ->get();
    }

    public function getSubmissionsProperty()
    {
        if (! $this->selectedStudentId) {
            return collect();
        }

        return AssignmentSubmission::where('student_user_id', $this->selectedStudentId)
            ->with(['assignment.course', 'assignment.courseSession'])
            ->latest('submitted_at')
            ->get();
    }

    public function getNotesProperty()
    {
        if (! $this->selectedStudentId) {
            return collect();
        }

        return StudentEducationalNote::where('student_user_id', $this->selectedStudentId)
            ->with('teacherProfile.user')
            ->latest()
            ->get();
    }

    public function getExceptionsProperty()
    {
        if (! $this->selectedStudentId) {
            return collect();
        }

        return ExceptionRequest::where('student_user_id', $this->selectedStudentId)
            ->with(['liveSession', 'course'])
            ->latest()
            ->get();
    }
}
