<?php

namespace App\Filament\Pages;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\ExceptionRequest;
use App\Models\LiveSession;
use App\Models\StudentEducationalNote;
use App\Models\TeacherProfile;
use App\Models\User;
use Filament\Pages\Page;
use Livewire\Attributes\Url;

class TeacherReportsPage extends Page
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.teacher-reports-page';

    #[Url(as: 'teacher_id')]
    public ?int $selectedTeacherId = null;

    public string $activeTab = 'overview';

    public ?string $searchQuery = '';

    public static function getNavigationGroup(): ?string
    {
        return __('Reports & Analytics');
    }

    public static function getNavigationLabel(): string
    {
        return __('Teacher Reports');
    }

    public function getTitle(): string
    {
        return __('Teacher Comprehensive Reports & Faculty Analytics');
    }

    public function mount(): void
    {
        if (! $this->selectedTeacherId) {
            $firstTeacher = TeacherProfile::with('user')->first();
            $this->selectedTeacherId = $firstTeacher?->id;
        }
    }

    public function selectTeacher(int $id): void
    {
        $this->selectedTeacherId = $id;
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function getTeachersListProperty()
    {
        $query = TeacherProfile::with(['user', 'courses']);

        if ($this->searchQuery) {
            $s = trim($this->searchQuery);
            $query->where(function ($q) use ($s) {
                $q->where('title', 'LIKE', "%{$s}%")
                    ->orWhere('specialization', 'LIKE', "%{$s}%")
                    ->orWhereHas('user', function ($uQuery) use ($s) {
                        $uQuery->where('name', 'LIKE', "%{$s}%")
                            ->orWhere('email', 'LIKE', "%{$s}%")
                            ->orWhere('phone', 'LIKE', "%{$s}%");
                    });
            });
        }

        return $query->get();
    }

    public function getSelectedTeacherProperty(): ?TeacherProfile
    {
        if (! $this->selectedTeacherId) {
            return null;
        }

        return TeacherProfile::where('id', $this->selectedTeacherId)
            ->with(['user', 'courses.subject', 'courses.gradeLevel'])
            ->first();
    }

    public function getTeacherMetricsProperty(): array
    {
        $teacher = $this->selectedTeacher;
        if (! $teacher) {
            return [
                'courses_count' => 0,
                'total_students_enrolled' => 0,
                'live_sessions_count' => 0,
                'completed_sessions_count' => 0,
                'assignments_count' => 0,
                'graded_submissions_count' => 0,
                'average_rating' => 0,
                'reviews_count' => 0,
                'notes_authored_count' => 0,
                'exceptions_handled_count' => 0,
                'total_hours_taught' => 0,
            ];
        }

        $courseIds = $teacher->courses->pluck('id')->toArray();
        $coursesCount = count($courseIds);

        $enrollments = CourseEnrollment::whereIn('course_id', $courseIds)->get();
        $uniqueStudentsCount = $enrollments->pluck('student_user_id')->unique()->count();

        $liveSessions = LiveSession::where('teacher_profile_id', $teacher->id)->get();
        $completedSessions = $liveSessions->filter(fn ($s) => $s->status === 'completed' || in_array($s->attendance_status, ['present', 'attended'], true));
        $totalDurationMinutes = $completedSessions->sum('duration_minutes');
        $totalHours = round($totalDurationMinutes / 60, 1);

        $assignments = Assignment::whereIn('course_id', $courseIds)->get();
        $assignmentIds = $assignments->pluck('id')->toArray();
        $submissions = AssignmentSubmission::whereIn('assignment_id', $assignmentIds)->get();
        $gradedSubmissions = $submissions->whereNotNull('grade');

        $notesAuthored = StudentEducationalNote::where('teacher_profile_id', $teacher->id)->get();
        $exceptions = ExceptionRequest::whereIn('course_id', $courseIds)
            ->orWhereIn('live_session_id', $liveSessions->pluck('id'))
            ->get();

        return [
            'courses_count' => $coursesCount,
            'total_students_enrolled' => $uniqueStudentsCount,
            'live_sessions_count' => $liveSessions->count(),
            'completed_sessions_count' => $completedSessions->count(),
            'assignments_count' => $assignments->count(),
            'graded_submissions_count' => $gradedSubmissions->count(),
            'average_rating' => $teacher->rating_avg ?: 5.0,
            'reviews_count' => $teacher->reviews_count ?: 0,
            'notes_authored_count' => $notesAuthored->count(),
            'exceptions_handled_count' => $exceptions->count(),
            'total_hours_taught' => $totalHours,
        ];
    }

    public function getCoursesProperty()
    {
        if (! $this->selectedTeacher) {
            return collect();
        }

        return Course::where('teacher_id', $this->selectedTeacher->id)
            ->with(['subject', 'gradeLevel', 'enrollments', 'sessions'])
            ->latest()
            ->get();
    }

    public function getLiveSessionsProperty()
    {
        if (! $this->selectedTeacherId) {
            return collect();
        }

        return LiveSession::where('teacher_profile_id', $this->selectedTeacherId)
            ->with(['course.subject', 'studentUser.studentProfile'])
            ->orderBy('scheduled_at', 'desc')
            ->get();
    }

    public function getEnrolledStudentsProperty()
    {
        if (! $this->selectedTeacher) {
            return collect();
        }

        $courseIds = $this->selectedTeacher->courses->pluck('id')->toArray();

        return CourseEnrollment::whereIn('course_id', $courseIds)
            ->with(['studentUser.studentProfile.gradeLevel', 'course.subject'])
            ->latest('enrolled_at')
            ->get();
    }

    public function getAssignmentsProperty()
    {
        if (! $this->selectedTeacher) {
            return collect();
        }

        $courseIds = $this->selectedTeacher->courses->pluck('id')->toArray();

        return Assignment::whereIn('course_id', $courseIds)
            ->with(['course.subject', 'submissions'])
            ->latest()
            ->get();
    }

    public function getNotesProperty()
    {
        if (! $this->selectedTeacher) {
            return collect();
        }

        return StudentEducationalNote::where('teacher_profile_id', $this->selectedTeacher->id)
            ->with('studentUser.studentProfile')
            ->latest()
            ->get();
    }

    public function getExceptionsProperty()
    {
        if (! $this->selectedTeacher) {
            return collect();
        }

        $courseIds = $this->selectedTeacher->courses->pluck('id')->toArray();
        $sessionIds = LiveSession::where('teacher_profile_id', $this->selectedTeacherId)->pluck('id')->toArray();

        return ExceptionRequest::where(function ($q) use ($courseIds, $sessionIds) {
            $q->whereIn('course_id', $courseIds)
                ->orWhereIn('live_session_id', $sessionIds);
        })
            ->with(['studentUser.studentProfile', 'course', 'liveSession'])
            ->latest()
            ->get();
    }
}
