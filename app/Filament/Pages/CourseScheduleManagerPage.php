<?php

namespace App\Filament\Pages;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\LiveSession;
use App\Models\RecurringSchedule;
use App\Models\TeacherProfile;
use App\Models\User;
use App\Services\Notification\FcmNotificationService;
use App\Services\Session\RecurringScheduleService;
use BackedEnum;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;

class CourseScheduleManagerPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.course-schedule-manager-page';

    // Filters
    #[Url(as: 'student_id')]
    public ?int $selectedStudentId = null;

    #[Url(as: 'teacher_id')]
    public ?int $selectedTeacherId = null;

    #[Url(as: 'course_id')]
    public ?int $selectedCourseId = null;

    #[Url(as: 'status')]
    public string $selectedStatus = 'all';

    public string $searchQuery = '';

    public string $activeTab = 'sessions'; // 'sessions', 'recurring', 'timetable'

    public ?string $dateFrom = null;

    public ?string $dateTo = null;

    // Create Single Session Modal State
    public bool $showCreateSessionModal = false;
    public ?int $singleStudentId = null;
    public ?int $singleTeacherId = null;
    public ?int $singleCourseId = null;
    public string $singleTitle = '';
    public string $singleScheduledAt = '';
    public int $singleDuration = 60;
    public ?string $singleMeetingLink = null;
    public string $singleMeetingPlatform = 'agora';
    public bool $singleIsFreeDemo = false;

    public bool $isSubmitting = false;

    // Create Recurring Schedule Modal State
    public bool $showRecurringModal = false;
    public array $recStudentIds = [];
    public string $recStudentSearch = '';
    public ?int $recTeacherId = null;
    public ?int $recCourseId = null;
    public string $recTitle = '';
    public string $recType = 'weekly';
    public array $recDays = [6, 1]; // Default Saturday & Monday
    public string $recStartTime = '10:00';
    public int $recDuration = 60;
    public string $recStartDate = '';
    public string $recEndDate = '';
    public ?string $recMeetingLink = null;
    public string $recMeetingPlatform = 'agora';
    public array $recPreviewList = [];
    public ?string $recConflictWarning = null;

    // Inline Modals State
    public bool $showRescheduleModal = false;
    public ?int $targetSessionId = null;
    public string $rescheduleNewDate = '';
    public string $rescheduleReason = '';

    public bool $showCancelModal = false;
    public ?int $cancelSessionId = null;
    public string $cancelReason = '';

    public bool $showLinkModal = false;
    public ?int $linkSessionId = null;
    public string $linkUrl = '';

    public static function getNavigationGroup(): ?string
    {
        return __('Academic Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('Student & Course Schedules');
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        if (method_exists($user, 'isAdmin') && ($user->isAdmin() || $user->isSuperAdmin())) {
            return true;
        }

        if ($user->teacherProfile !== null) {
            return true;
        }

        if (method_exists($user, 'hasRole') && ($user->hasRole('admin') || $user->hasRole('super_admin') || $user->hasRole('teacher'))) {
            return true;
        }

        return false;
    }

    public function getIsTeacherOnlyProperty(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        $isAdmin = (method_exists($user, 'isAdmin') && ($user->isAdmin() || $user->isSuperAdmin()));
        return $user->teacherProfile !== null && ! $isAdmin;
    }

    public function getTeacherProfileIdProperty(): ?int
    {
        return auth()->user()?->teacherProfile?->id;
    }

    public function mount(): void
    {
        if ($this->isTeacherOnly && $this->teacherProfileId) {
            $this->selectedTeacherId = $this->teacherProfileId;
            $this->singleTeacherId = $this->teacherProfileId;
            $this->recTeacherId = $this->teacherProfileId;
        }

        $this->singleScheduledAt = now()->addDay()->setTime(10, 0)->format('Y-m-d\TH:i');
        $this->recStartDate = now()->format('Y-m-d');
        $this->recEndDate = now()->addMonths(3)->format('Y-m-d');
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function selectStudent(?int $id): void
    {
        $this->selectedStudentId = $id;
    }

    public function selectTeacher(?int $id): void
    {
        if (! $this->isTeacherOnly) {
            $this->selectedTeacherId = $id;
        }
    }

    public function selectCourse(?int $id): void
    {
        $this->selectedCourseId = $id;
    }

    public function resetFilters(): void
    {
        $this->selectedStudentId = null;
        $this->selectedTeacherId = $this->isTeacherOnly ? $this->teacherProfileId : null;
        $this->selectedCourseId = null;
        $this->selectedStatus = 'all';
        $this->searchQuery = '';
        $this->dateFrom = null;
        $this->dateTo = null;
    }

    // Dynamic Lists for Filters & Form Selectors
    public function getStudentsProperty(): Collection
    {
        $query = User::query();

        if ($this->isTeacherOnly && $this->teacherProfileId) {
            $teacherCourseIds = Course::where('teacher_id', $this->teacherProfileId)->pluck('id')->all();
            $enrolledStudentIds = CourseEnrollment::whereIn('course_id', $teacherCourseIds)
                ->pluck('student_user_id')
                ->filter()
                ->all();
            $directSessionStudentIds = LiveSession::where('teacher_profile_id', $this->teacherProfileId)
                ->whereNotNull('student_user_id')
                ->pluck('student_user_id')
                ->filter()
                ->all();
            $teacherStudentIds = array_values(array_unique(array_merge($enrolledStudentIds, $directSessionStudentIds)));

            if (! empty($teacherStudentIds)) {
                $query->whereIn('id', $teacherStudentIds);
            } else {
                return collect();
            }
        } elseif (method_exists(User::class, 'scopeRoleStudent')) {
            $query->roleStudent()->orWhereHas('studentProfile');
        } else {
            $query->whereHas('studentProfile');
        }

        return $query
            ->with('studentProfile.gradeLevel')
            ->orderBy('name')
            ->get();
    }

    public function getTeachersProperty(): Collection
    {
        $query = TeacherProfile::query()->with('user');

        if ($this->isTeacherOnly && $this->teacherProfileId) {
            $query->where('id', $this->teacherProfileId);
        }

        return $query->orderBy('id')->get();
    }

    public function getCoursesProperty(): Collection
    {
        $query = Course::query()->with(['subject', 'teacher.user', 'gradeLevel'])->where('is_active', true);

        if ($this->isTeacherOnly && $this->teacherProfileId) {
            $query->where('teacher_id', $this->teacherProfileId);
        } elseif ($this->selectedTeacherId) {
            $query->where('teacher_id', $this->selectedTeacherId);
        }

        return $query->orderBy('title')->get();
    }

    // Sessions Query for the Table
    public function getSessionsListProperty(): Collection
    {
        $query = LiveSession::query()
            ->with([
                'course.subject',
                'course.teacher.user',
                'studentUser.studentProfile.gradeLevel',
                'recurringSchedule',
            ]);

        if ($this->selectedStudentId) {
            $query->where(function ($q) {
                $q->where('student_user_id', $this->selectedStudentId)
                    ->orWhere(function ($sq) {
                        $sq->whereNull('student_user_id')
                            ->whereHas('course.enrollments', function ($eq) {
                                $eq->where('student_user_id', $this->selectedStudentId);
                            });
                    });
            });
        }

        $effectiveTeacherId = ($this->isTeacherOnly && $this->teacherProfileId) ? $this->teacherProfileId : $this->selectedTeacherId;
        if ($effectiveTeacherId) {
            $query->where(function ($tq) use ($effectiveTeacherId) {
                $tq->where('teacher_profile_id', $effectiveTeacherId)
                   ->orWhereHas('course', fn ($cq) => $cq->where('teacher_id', $effectiveTeacherId));
            });
        }

        if ($this->selectedCourseId) {
            $query->where('course_id', $this->selectedCourseId);
        }

        if ($this->selectedStatus !== 'all') {
            if ($this->selectedStatus === 'cancelled') {
                $query->whereIn('status', ['cancelled', 'cancelled_by_teacher']);
            } else {
                $query->where('status', $this->selectedStatus);
            }
        }

        if ($this->dateFrom) {
            $query->whereDate('scheduled_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('scheduled_at', '<=', $this->dateTo);
        }

        if (! empty($this->searchQuery)) {
            $s = trim($this->searchQuery);
            $query->where(function ($q) use ($s) {
                $q->where('title', 'LIKE', "%{$s}%")
                    ->orWhereHas('studentUser', fn ($sq) => $sq->where('name', 'LIKE', "%{$s}%")->orWhere('email', 'LIKE', "%{$s}%"))
                    ->orWhereHas('course', fn ($cq) => $cq->where('title', 'LIKE', "%{$s}%"));
            });
        }

        return $query->orderBy('scheduled_at', 'desc')->take(150)->get();
    }

    // Recurring Schedules Query
    public function getRecurringSchedulesListProperty(): Collection
    {
        $query = RecurringSchedule::query()
            ->with([
                'course.subject',
                'teacherProfile.user',
                'studentUser.studentProfile',
            ])
            ->withCount('liveSessions');

        if ($this->selectedStudentId) {
            $query->where('student_user_id', $this->selectedStudentId);
        }

        $effectiveTeacherId = ($this->isTeacherOnly && $this->teacherProfileId) ? $this->teacherProfileId : $this->selectedTeacherId;
        if ($effectiveTeacherId) {
            $query->where('teacher_profile_id', $effectiveTeacherId);
        }

        if ($this->selectedCourseId) {
            $query->where('course_id', $this->selectedCourseId);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    // Metrics Summary
    public function getMetricsProperty(): array
    {
        $effectiveTeacherId = ($this->isTeacherOnly && $this->teacherProfileId) ? $this->teacherProfileId : $this->selectedTeacherId;

        $sessionsQ = LiveSession::query();
        $upcomingQ = LiveSession::where('scheduled_at', '>=', now())->whereNotIn('status', ['cancelled', 'cancelled_by_teacher']);
        $recurringQ = RecurringSchedule::where('status', 'active');
        $enrollmentsQ = CourseEnrollment::where('status', 'active');

        if ($effectiveTeacherId) {
            $sessionsQ->where(function ($sq) use ($effectiveTeacherId) {
                $sq->where('teacher_profile_id', $effectiveTeacherId)
                   ->orWhereHas('course', fn ($cq) => $cq->where('teacher_id', $effectiveTeacherId));
            });
            $upcomingQ->where(function ($sq) use ($effectiveTeacherId) {
                $sq->where('teacher_profile_id', $effectiveTeacherId)
                   ->orWhereHas('course', fn ($cq) => $cq->where('teacher_id', $effectiveTeacherId));
            });
            $recurringQ->where('teacher_profile_id', $effectiveTeacherId);
            $enrollmentsQ->whereHas('course', fn ($cq) => $cq->where('teacher_id', $effectiveTeacherId));
        }

        return [
            'total_sessions' => $sessionsQ->count(),
            'upcoming' => $upcomingQ->count(),
            'recurring_rules' => $recurringQ->count(),
            'active_enrollments' => $enrollmentsQ->count(),
        ];
    }

    // --- Modal Openers ---
    public function openCreateSessionModal(): void
    {
        $this->singleCourseId = $this->selectedCourseId;
        $this->singleTeacherId = $this->selectedTeacherId;
        $this->singleStudentId = $this->selectedStudentId;
        $this->singleTitle = '';
        $this->singleScheduledAt = now()->addDay()->setTime(10, 0)->format('Y-m-d\TH:i');
        $this->singleDuration = 60;
        $this->singleMeetingLink = '';
        $this->singleMeetingPlatform = 'agora';
        $this->singleIsFreeDemo = false;

        if ($this->singleCourseId) {
            $this->updatedSingleCourseId($this->singleCourseId);
        }

        $this->showCreateSessionModal = true;
    }

    public function closeCreateSessionModal(): void
    {
        $this->showCreateSessionModal = false;
    }

    public function updatedSingleCourseId(?int $courseId): void
    {
        if ($courseId) {
            $course = Course::find($courseId);
            if ($course) {
                if ($course->teacher_id) {
                    $this->singleTeacherId = $course->teacher_id;
                }
                if (empty($this->singleTitle)) {
                    $this->singleTitle = $course->title . ' - ' . __('Live Session');
                }
            }

            // Verify if currently selected student is enrolled in this course
            if ($this->singleStudentId) {
                $isEnrolled = CourseEnrollment::where('course_id', $courseId)
                    ->where('student_user_id', $this->singleStudentId)
                    ->where('status', 'active')
                    ->exists();
                if (! $isEnrolled) {
                    $this->singleStudentId = null;
                }
            }
        }
    }

    public function getSingleModalStudentsProperty(): Collection
    {
        if ($this->singleCourseId) {
            $enrolledIds = CourseEnrollment::where('course_id', $this->singleCourseId)
                ->where('status', 'active')
                ->pluck('student_user_id')
                ->filter()
                ->all();

            if (! empty($enrolledIds)) {
                return User::whereIn('id', $enrolledIds)
                    ->with('studentProfile.gradeLevel')
                    ->orderBy('name')
                    ->get();
            }
        }

        return $this->students;
    }

    public function createSingleSession(): void
    {
        if ($this->isSubmitting) {
            return;
        }

        $this->isSubmitting = true;

        try {
            $this->validate([
                'singleCourseId' => 'required|exists:courses,id',
                'singleTitle' => 'required|string|max:255',
                'singleScheduledAt' => 'required|date',
                'singleDuration' => 'required|numeric|min:15|max:300',
                'singleStudentId' => 'nullable|exists:users,id',
                'singleMeetingLink' => 'nullable|url|max:500',
            ], [
                'singleCourseId.required' => __('Please select a course.'),
                'singleTitle.required' => __('Please enter a session title.'),
                'singleScheduledAt.required' => __('Please select a valid scheduled date & time.'),
                'singleDuration.min' => __('Session duration must be at least 15 minutes.'),
                'singleDuration.max' => __('Session duration cannot exceed 300 minutes.'),
            ]);

            $course = Course::findOrFail($this->singleCourseId);
            $teacherId = $this->singleTeacherId ?? $course->teacher_id;

            $session = LiveSession::create([
                'course_id' => $course->id,
                'subject_id' => $course->subject_id,
                'teacher_profile_id' => $teacherId,
                'student_user_id' => $this->singleStudentId ?: null,
                'title' => trim($this->singleTitle),
                'scheduled_at' => Carbon::parse($this->singleScheduledAt),
                'duration_minutes' => $this->singleDuration,
                'meeting_link' => $this->singleMeetingLink ? trim($this->singleMeetingLink) : null,
                'meeting_platform' => $this->singleMeetingPlatform ?: 'agora',
                'status' => 'scheduled',
                'is_free_demo' => $this->singleIsFreeDemo,
            ]);

            $this->showCreateSessionModal = false;

            Notification::make()
                ->title(__('Live Session Scheduled Successfully'))
                ->body(__("Session ':title' has been created and assigned.", ['title' => $session->title]))
                ->success()
                ->send();
        } finally {
            $this->isSubmitting = false;
        }
    }

    public function updatedRecCourseId(?int $courseId): void
    {
        if ($courseId) {
            $course = Course::find($courseId);
            if ($course) {
                if ($course->teacher_id) {
                    $this->recTeacherId = $course->teacher_id;
                }
                if (empty($this->recTitle)) {
                    $this->recTitle = $course->title . ' - ' . __('Schedule');
                }
            }

            // Sync selected students with course active enrollments
            $enrolledIds = CourseEnrollment::where('course_id', $courseId)
                ->where('status', 'active')
                ->pluck('student_user_id')
                ->filter()
                ->all();

            if (! empty($enrolledIds)) {
                $this->recStudentIds = array_values(array_intersect($this->recStudentIds, $enrolledIds));
            }
        }

        $this->recalculateRecurrenceCycle();
    }

    public function updatedRecType(string $type): void
    {
        $this->recalculateRecurrenceCycle();
    }

    public function updatedRecStartDate(string $date): void
    {
        $this->recalculateRecurrenceCycle();
    }

    public function recalculateRecurrenceCycle(): void
    {
        if (empty($this->recStartDate)) {
            $this->recStartDate = now()->format('Y-m-d');
        }

        try {
            $start = Carbon::parse($this->recStartDate);
        } catch (\Throwable $e) {
            $start = now();
        }

        // Automatic cycle duration based on recurrence pattern
        switch ($this->recType) {
            case 'weekly':
                $this->recEndDate = $start->copy()->addWeeks(4)->format('Y-m-d');
                break;
            case 'monthly':
                $this->recEndDate = $start->copy()->addMonth()->format('Y-m-d');
                break;
            case 'multi_month':
                $this->recEndDate = $start->copy()->addMonths(3)->format('Y-m-d');
                break;
            case 'yearly':
                $this->recEndDate = $start->copy()->addMonths(9)->format('Y-m-d');
                break;
            default:
                $this->recEndDate = $start->copy()->addMonth()->format('Y-m-d');
                break;
        }

        // Ensure start date's day of week is selected in recDays if recDays is empty
        $dayOfWeek = $start->dayOfWeek; // 0 (Sun) to 6 (Sat)
        if (empty($this->recDays)) {
            $this->recDays = [$dayOfWeek];
        }
    }

    public function getFilteredRecStudentsProperty(): Collection
    {
        if ($this->recCourseId) {
            $enrolledIds = CourseEnrollment::where('course_id', $this->recCourseId)
                ->where('status', 'active')
                ->pluck('student_user_id')
                ->filter()
                ->all();

            if (! empty($enrolledIds)) {
                $baseList = User::whereIn('id', $enrolledIds)
                    ->with('studentProfile.gradeLevel')
                    ->orderBy('name')
                    ->get();
            } else {
                $baseList = $this->students;
            }
        } else {
            $baseList = $this->students;
        }

        if (trim($this->recStudentSearch) !== '') {
            $term = mb_strtolower(trim($this->recStudentSearch));
            return $baseList->filter(function ($stu) use ($term) {
                $name = mb_strtolower($stu->name ?? '');
                $code = mb_strtolower((string) ($stu->studentProfile?->student_code ?? ''));
                $id = (string) $stu->id;
                return str_contains($name, $term) || str_contains($code, $term) || str_contains($id, $term);
            });
        }

        return $baseList;
    }

    public function toggleStudentSelection(int $studentId): void
    {
        if (in_array($studentId, $this->recStudentIds)) {
            $this->recStudentIds = array_values(array_diff($this->recStudentIds, [$studentId]));
        } else {
            $this->recStudentIds[] = $studentId;
        }
    }

    public function removeSelectedStudent(int $studentId): void
    {
        $this->recStudentIds = array_values(array_diff($this->recStudentIds, [$studentId]));
    }

    public function selectAllRecStudents(): void
    {
        $this->recStudentIds = $this->filteredRecStudents->pluck('id')->all();
    }

    public function clearAllRecStudents(): void
    {
        $this->recStudentIds = [];
    }

    // --- Recurring Schedule Modal & Logic ---
    public function openRecurringModal(): void
    {
        $this->recStudentSearch = '';
        $this->recTeacherId = $this->selectedTeacherId;
        $this->recCourseId = $this->selectedCourseId;
        $this->recTitle = '';
        $this->recType = 'weekly';
        $this->recDays = [6, 1];
        $this->recStartTime = '10:00';
        $this->recDuration = 60;
        $this->recStartDate = now()->format('Y-m-d');
        $this->recEndDate = now()->addMonths(3)->format('Y-m-d');
        $this->recMeetingLink = '';
        $this->recMeetingPlatform = 'agora';
        $this->recPreviewList = [];
        $this->recConflictWarning = null;

        if ($this->recCourseId) {
            $this->updatedRecCourseId($this->recCourseId);
        } else {
            $this->recStudentIds = $this->selectedStudentId ? [$this->selectedStudentId] : [];
            $this->recalculateRecurrenceCycle();
        }

        $this->showRecurringModal = true;
    }

    public function closeRecurringModal(): void
    {
        $this->showRecurringModal = false;
    }

    public function previewRecurringSchedule(RecurringScheduleService $service): void
    {
        $this->validate([
            'recCourseId' => 'required|exists:courses,id',
            'recStartDate' => 'required|date',
            'recEndDate' => 'required|date|after_or_equal:recStartDate',
            'recStartTime' => 'required',
            'recDays' => 'required|array|min:1',
        ], [
            'recCourseId.required' => __('Please select a course.'),
            'recStartDate.required' => __('Please select a valid start date.'),
            'recEndDate.required' => __('Please select a valid end date.'),
            'recEndDate.after_or_equal' => __('The end date must be equal to or after the start date.'),
            'recStartTime.required' => __('Please select a start time.'),
            'recDays.min' => __('Please select at least one day of the week for recurrence.'),
        ]);

        $course = Course::findOrFail($this->recCourseId);
        $teacherId = $this->recTeacherId ?? $course->teacher_id;

        $previewData = $service->previewDates([
            'course_id' => $course->id,
            'teacher_profile_id' => $teacherId,
            'student_user_id' => ! empty($this->recStudentIds) ? $this->recStudentIds[0] : null,
            'start_date' => $this->recStartDate,
            'end_date' => $this->recEndDate,
            'start_time' => $this->recStartTime,
            'duration_minutes' => $this->recDuration,
            'recurrence_type' => $this->recType,
            'days_of_week' => $this->recDays,
        ]);

        $hasConflict = false;
        $preview = [];
        foreach (array_slice($previewData, 0, 15) as $item) {
            if ($item['has_conflict']) {
                $hasConflict = true;
            }
            $preview[] = [
                'date' => $item['date'],
                'day' => $item['day_name'],
                'time' => $item['start_time'] . ' - ' . $item['end_time'],
                'has_conflict' => $item['has_conflict'],
                'conflict_reason' => ! empty($item['conflict_details'][0]['message']) ? $item['conflict_details'][0]['message'] : null,
            ];
        }

        $this->recPreviewList = $preview;
        $this->recConflictWarning = $hasConflict ? __('Warning: One or more selected slots conflict with existing teacher/student commitments.') : null;
    }

    public function submitRecurringSchedule(RecurringScheduleService $service): void
    {
        if ($this->isSubmitting) {
            return;
        }

        $this->isSubmitting = true;

        try {
            $this->validate([
                'recCourseId' => 'required|exists:courses,id',
                'recTitle' => 'required|string|max:255',
                'recStartDate' => 'required|date',
                'recEndDate' => 'required|date|after_or_equal:recStartDate',
                'recStartTime' => 'required',
                'recDuration' => 'required|numeric|min:15|max:300',
                'recDays' => 'required|array|min:1',
                'recMeetingLink' => 'nullable|url|max:500',
            ], [
                'recCourseId.required' => __('Please select a course.'),
                'recTitle.required' => __('Please enter a schedule title.'),
                'recStartDate.required' => __('Please select a valid start date.'),
                'recEndDate.required' => __('Please select a valid end date.'),
                'recEndDate.after_or_equal' => __('The end date must be equal to or after the start date.'),
                'recStartTime.required' => __('Please select a start time.'),
                'recDays.min' => __('Please select at least one day of the week for recurrence.'),
            ]);

            $course = Course::findOrFail($this->recCourseId);
            $teacherId = $this->recTeacherId ?? $course->teacher_id;
            $studentIds = ! empty($this->recStudentIds) ? $this->recStudentIds : [null];

            $totalGenerated = 0;

            foreach ($studentIds as $stuId) {
                $studentTitle = $stuId ? (' - Student #' . $stuId) : '';
                $schedule = $service->createSchedule([
                    'course_id' => $course->id,
                    'teacher_profile_id' => $teacherId,
                    'student_user_id' => $stuId ?: null,
                    'title' => trim($this->recTitle) . $studentTitle,
                    'recurrence_type' => $this->recType,
                    'days_of_week' => $this->recDays,
                    'start_time' => $this->recStartTime,
                    'duration_minutes' => $this->recDuration,
                    'start_date' => $this->recStartDate,
                    'end_date' => $this->recEndDate,
                    'meeting_link' => $this->recMeetingLink ? trim($this->recMeetingLink) : null,
                    'meeting_platform' => $this->recMeetingPlatform ?: 'agora',
                ], auth()->user());

                $totalGenerated += $schedule->liveSessions()->count();
            }

            $this->showRecurringModal = false;

            Notification::make()
                ->title(__('Recurring Schedule Created & Populated'))
                ->body(__("Successfully created recurring rule and generated :count live sessions.", ['count' => $totalGenerated]))
                ->success()
                ->send();
        } finally {
            $this->isSubmitting = false;
        }
    }

    // --- Quick Inline Session Actions ---
    public function openReschedule(int $sessionId): void
    {
        $session = LiveSession::findOrFail($sessionId);
        $this->targetSessionId = $session->id;
        $this->rescheduleNewDate = $session->scheduled_at->format('Y-m-d\TH:i');
        $this->rescheduleReason = '';
        $this->showRescheduleModal = true;
    }

    public function confirmReschedule(): void
    {
        $this->validate([
            'rescheduleNewDate' => 'required|date',
        ]);

        $session = LiveSession::findOrFail($this->targetSessionId);
        $oldTime = $session->scheduled_at->format('Y-m-d H:i');
        $newTime = Carbon::parse($this->rescheduleNewDate);

        $session->update([
            'scheduled_at' => $newTime,
            'is_override' => true,
            'override_reason' => $this->rescheduleReason ?: "Rescheduled by admin from {$oldTime} to " . $newTime->format('Y-m-d H:i'),
        ]);

        $this->showRescheduleModal = false;

        Notification::make()
            ->title(__('Session Rescheduled Successfully'))
            ->success()
            ->send();
    }

    public function openCancelModal(int $sessionId): void
    {
        $this->cancelSessionId = $sessionId;
        $this->cancelReason = '';
        $this->showCancelModal = true;
    }

    public function confirmCancel(): void
    {
        $this->validate([
            'cancelReason' => 'required|string|max:255',
        ]);

        $session = LiveSession::findOrFail($this->cancelSessionId);
        $user = auth()->user();
        if ($user) {
            app(\App\Services\Session\RecurringScheduleService::class)->cancelSession($session, trim($this->cancelReason), $user);
            $session->update([
                'status' => 'cancelled',
                'lifecycle_state' => 'cancelled',
            ]);
        } else {
            $session->update([
                'status' => 'cancelled',
                'lifecycle_state' => 'cancelled',
                'cancellation_reason' => trim($this->cancelReason),
            ]);
        }

        $this->showCancelModal = false;

        Notification::make()
            ->title(__('Session Cancelled'))
            ->body(__('Enrolled learners and teachers will be alerted.'))
            ->warning()
            ->send();
    }

    public function deleteSession(int $sessionId): void
    {
        $session = LiveSession::findOrFail($sessionId);
        $session->delete();

        Notification::make()
            ->title(__('Session Deleted'))
            ->body(__('Session has been deleted successfully.'))
            ->success()
            ->send();
    }

    public function openLinkModal(int $sessionId): void
    {
        $session = LiveSession::findOrFail($sessionId);
        $this->linkSessionId = $session->id;
        $this->linkUrl = $session->meeting_link ?? '';
        $this->showLinkModal = true;
    }

    public function saveMeetingLink(): void
    {
        $this->validate([
            'linkUrl' => 'required|url',
        ]);

        $session = LiveSession::findOrFail($this->linkSessionId);
        $session->update([
            'meeting_link' => trim($this->linkUrl),
        ]);

        $this->showLinkModal = false;

        Notification::make()
            ->title(__('Live Stream URL Updated'))
            ->success()
            ->send();
    }

    public function markAttendance(int $sessionId, string $status): void
    {
        $session = LiveSession::findOrFail($sessionId);
        $session->update([
            'attendance_status' => $status,
            'status' => $status === 'present' ? 'completed' : $session->status,
        ]);

        Notification::make()
            ->title(__('Attendance Recorded: ') . ucfirst($status))
            ->success()
            ->send();
    }

    public function deleteRecurringRule(int $scheduleId): void
    {
        $schedule = RecurringSchedule::findOrFail($scheduleId);
        // Cancel all non-started future sessions
        LiveSession::where('recurring_schedule_id', $schedule->id)
            ->where('scheduled_at', '>', now())
            ->where('status', 'scheduled')
            ->delete();

        $schedule->delete();

        Notification::make()
            ->title(__('Recurring Schedule Deleted'))
            ->body(__('Future pending sessions generated by this template were removed.'))
            ->success()
            ->send();
    }
}
