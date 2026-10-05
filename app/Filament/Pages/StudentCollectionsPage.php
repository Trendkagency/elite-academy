<?php

namespace App\Filament\Pages;

use App\Models\PackageTemplate;
use App\Models\RecurringSchedule;
use App\Models\StudentPackage;
use App\Models\User;
use App\Services\Notification\FcmNotificationService;
use App\Services\Session\RecurringScheduleService;
use BackedEnum;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;

class StudentCollectionsPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'student-collections';

    protected string $view = 'filament.pages.student-collections-page';

    #[Url(as: 'tab')]
    public string $activeTab = 'packages'; // 'packages' | 'schedules'

    #[Url(as: 'package_filter')]
    public string $packageFilter = 'all'; // 'all', 'exhausted', 'low', 'expiring'

    #[Url(as: 'schedule_filter')]
    public string $scheduleFilter = 'all'; // 'all', 'ending_soon', 'ended'

    #[Url(as: 'q')]
    public string $searchQuery = '';

    // Modals
    public bool $showRenewModal = false;
    public ?int $renewingPackageId = null;
    public int $newTotalSessions = 12;
    public ?int $newTemplateId = null;
    public ?string $newExpiresAt = null;
    public string $renewReason = 'تجديد الباقة وتحصيل الاشتراك';

    public bool $showAddCreditsModal = false;
    public ?int $addCreditsPackageId = null;
    public int $creditsToAdd = 5;

    public bool $showExtendScheduleModal = false;
    public ?int $extendingScheduleId = null;
    public string $newScheduleEndDate = '';
    public string $extendReason = 'تجديد الدورة الشهرية للجدول الدوري';

    public function mount(): void
    {
        $this->activeTab = request()->query('tab', 'packages');
        $this->packageFilter = request()->query('package_filter', 'all');
        $this->scheduleFilter = request()->query('schedule_filter', 'all');
        $this->searchQuery = request()->query('q', '');
    }

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'ar' ? 'إدارة الباقات والمالية' : 'Packages & Finance';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'متابعة التحصيلات والتجديدات' : 'Collections & Renewals';
    }

    public function getTitle(): string
    {
        return app()->getLocale() === 'ar' ? 'متابعة التحصيلات، الباقات المنتهية وتجديد الجداول الدورية' : 'Collections, Expiring Packages & Schedule Renewals';
    }

    public static function getNavigationBadge(): ?string
    {
        $pkgCount = StudentPackage::whereIn('status', ['active', 'exhausted'])
            ->where(function ($q) {
                $q->where('remaining_sessions', '<=', 3)
                  ->orWhere(function ($eq) {
                      $eq->whereNotNull('expires_at')->where('expires_at', '<=', now()->addDays(7));
                  });
            })
            ->count();

        $schCount = RecurringSchedule::where('status', 'active')
            ->whereNotNull('end_date')
            ->where('end_date', '<=', now()->addDays(14))
            ->count();

        $total = $pkgCount + $schCount;
        return $total > 0 ? (string) $total : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function setPackageFilter(string $filter): void
    {
        $this->packageFilter = $filter;
    }

    public function setScheduleFilter(string $filter): void
    {
        $this->scheduleFilter = $filter;
    }

    /**
     * Compute Top KPI Stats
     */
    public function getStatsProperty(): array
    {
        $now = now();

        $exhaustedPackages = StudentPackage::where('remaining_sessions', '<=', 0)
            ->whereIn('status', ['active', 'exhausted'])
            ->count();

        $lowBalancePackages = StudentPackage::where('remaining_sessions', '>', 0)
            ->where('remaining_sessions', '<=', 3)
            ->where('status', 'active')
            ->count();

        $expiringIn7Days = StudentPackage::where('status', 'active')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', $now->copy()->addDays(7))
            ->where('expires_at', '>=', $now)
            ->count();

        $cyclesEndingIn14Days = RecurringSchedule::where('status', 'active')
            ->whereNotNull('end_date')
            ->where('end_date', '<=', $now->copy()->addDays(14))
            ->count();

        return [
            'exhausted_packages' => $exhaustedPackages,
            'low_balance_packages' => $lowBalancePackages,
            'expiring_in_7_days' => $expiringIn7Days,
            'cycles_ending_soon' => $cyclesEndingIn14Days,
            'total_actionable' => $exhaustedPackages + $lowBalancePackages + $cyclesEndingIn14Days,
        ];
    }

    /**
     * Get Expiring / Low-Balance Packages
     */
    public function getExpiringPackagesProperty(): Collection
    {
        $now = now();
        $query = StudentPackage::query()
            ->whereIn('status', ['active', 'exhausted'])
            ->where(function ($q) use ($now) {
                $q->where('remaining_sessions', '<=', 3)
                  ->orWhere(function ($eq) use ($now) {
                      $eq->whereNotNull('expires_at')->where('expires_at', '<=', $now->copy()->addDays(7));
                  });
            })
            ->with([
                'studentUser.studentProfile.gradeLevel',
                'packageTemplate',
                'course.subject',
            ]);

        // Search query
        if (! empty($this->searchQuery)) {
            $s = trim($this->searchQuery);
            $query->whereHas('studentUser', function ($sq) use ($s) {
                $sq->where('name', 'like', "%{$s}%")
                   ->orWhere('phone', 'like', "%{$s}%")
                   ->orWhere('email', 'like', "%{$s}%");
            });
        }

        // Sub filter
        if ($this->packageFilter === 'exhausted') {
            $query->where('remaining_sessions', '<=', 0);
        } elseif ($this->packageFilter === 'low') {
            $query->where('remaining_sessions', '>', 0)->where('remaining_sessions', '<=', 3);
        } elseif ($this->packageFilter === 'expiring') {
            $query->whereNotNull('expires_at')->where('expires_at', '<=', $now->copy()->addDays(7));
        }

        $results = $query->orderBy('remaining_sessions', 'asc')
            ->orderBy('expires_at', 'asc')
            ->get();

        // Enhance with Parent contacts and clean WhatsApp Links
        return $results->map(function (StudentPackage $pkg) use ($now) {
            $student = $pkg->studentUser;
            $parent = null;
            if ($student) {
                $parent = DB::table('parent_student')
                    ->join('users', 'users.id', '=', 'parent_student.parent_user_id')
                    ->where('parent_student.student_user_id', $student->id)
                    ->select('users.name', 'users.phone', 'parent_student.relationship')
                    ->first();
            }

            // WhatsApp link helper
            $cleanStudentPhone = $this->formatWhatsAppPhone($student?->phone);
            $cleanParentPhone = $this->formatWhatsAppPhone($parent?->phone);

            $pkgName = $pkg->packageTemplate?->name ?: ($pkg->course ? $pkg->course->title : 'الباقة التعليمية');
            $rem = $pkg->remaining_sessions;

            $messageText = "مرحباً ولي أمر الطالب " . ($student?->name ?? '') . "، نود إحاطتكم بأن رصيد باقة ({$pkgName}) متبقي به {$rem} حصص فقط. نرجو التكرم بسداد الاشتراك وتجديد الباقة لمتابعة الحصص الدراسية بانتظام. أكاديمية إيليت.";

            $studentWhatsAppUrl = $cleanStudentPhone ? "https://wa.me/{$cleanStudentPhone}?text=" . urlencode($messageText) : null;
            $parentWhatsAppUrl = $cleanParentPhone ? "https://wa.me/{$cleanParentPhone}?text=" . urlencode($messageText) : null;

            // Expiry countdown text
            $expiryCountdown = null;
            $isExpired = false;
            if ($pkg->expires_at) {
                if ($pkg->expires_at->isPast()) {
                    $expiryCountdown = 'منتهية الصلاحية';
                    $isExpired = true;
                } else {
                    $expiryCountdown = 'تنتهي ' . $pkg->expires_at->diffForHumans();
                }
            }

            // Urgency level
            $urgency = 'warning';
            $urgencyLabel = 'رصيد منخفض (1-3 حصص)';
            if ($rem <= 0) {
                $urgency = 'danger';
                $urgencyLabel = 'مستحق السداد فوراً (0 حصة)';
            } elseif ($isExpired) {
                $urgency = 'danger';
                $urgencyLabel = 'الصلاحية منتهية';
            }

            return (object) [
                'record' => $pkg,
                'student' => $student,
                'parent' => $parent,
                'studentWhatsAppUrl' => $studentWhatsAppUrl,
                'parentWhatsAppUrl' => $parentWhatsAppUrl,
                'pkgName' => $pkgName,
                'remaining' => $rem,
                'total' => $pkg->total_sessions,
                'used' => $pkg->used_sessions,
                'expiryFormatted' => $pkg->expires_at ? $pkg->expires_at->format('Y-m-d') : null,
                'expiryCountdown' => $expiryCountdown,
                'urgency' => $urgency,
                'urgencyLabel' => $urgencyLabel,
            ];
        });
    }

    /**
     * Get Expiring Recurring Schedules (Ending Cycles)
     */
    public function getEndingSchedulesProperty(): Collection
    {
        $now = now();
        $query = RecurringSchedule::query()
            ->where('status', 'active')
            ->whereNotNull('end_date')
            ->where('end_date', '<=', $now->copy()->addDays(14))
            ->with([
                'studentUser.studentProfile.gradeLevel',
                'course.subject',
                'teacherProfile.user',
            ]);

        // Search query
        if (! empty($this->searchQuery)) {
            $s = trim($this->searchQuery);
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhereHas('studentUser', function ($sq) use ($s) {
                      $sq->where('name', 'like', "%{$s}%")->orWhere('phone', 'like', "%{$s}%");
                  })
                  ->orWhereHas('teacherProfile.user', function ($tq) use ($s) {
                      $tq->where('name', 'like', "%{$s}%");
                  });
            });
        }

        // Sub filter
        if ($this->scheduleFilter === 'ending_soon') {
            $query->where('end_date', '>=', $now->format('Y-m-d'));
        } elseif ($this->scheduleFilter === 'ended') {
            $query->where('end_date', '<', $now->format('Y-m-d'));
        }

        $results = $query->orderBy('end_date', 'asc')->get();

        return $results->map(function (RecurringSchedule $sch) use ($now) {
            $student = $sch->studentUser;
            $parent = null;
            if ($student) {
                $parent = DB::table('parent_student')
                    ->join('users', 'users.id', '=', 'parent_student.parent_user_id')
                    ->where('parent_student.student_user_id', $student->id)
                    ->select('users.name', 'users.phone', 'parent_student.relationship')
                    ->first();
            }

            // Remaining sessions in this recurring schedule
            $remainingSessionsCount = $sch->sessions()
                ->where('scheduled_at', '>=', $now)
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->count();

            // Total generated sessions
            $totalSessionsCount = $sch->sessions()->count();

            // Formatted Days of Week
            $dayNamesMap = [
                0 => 'الأحد', 1 => 'الاثنين', 2 => 'الثلاثاء',
                3 => 'الأربعاء', 4 => 'الخميس', 5 => 'الجمعة', 6 => 'السبت',
                'sunday' => 'الأحد', 'monday' => 'الاثنين', 'tuesday' => 'الثلاثاء',
                'wednesday' => 'الأربعاء', 'thursday' => 'الخميس', 'friday' => 'الجمعة', 'saturday' => 'السبت',
            ];
            $daysList = [];
            foreach ($sch->days_of_week ?? [] as $d) {
                $daysList[] = $dayNamesMap[$d] ?? (string) $d;
            }
            $daysFormatted = !empty($daysList) ? implode('، ', $daysList) : 'أسبوعي';

            // WhatsApp link for Schedule Cycle Extension
            $cleanStudentPhone = $this->formatWhatsAppPhone($student?->phone);
            $cleanParentPhone = $this->formatWhatsAppPhone($parent?->phone);
            $endDateFormatted = $sch->end_date ? $sch->end_date->format('Y/m/d') : '';
            $courseTitle = $sch->course?->title ?: $sch->title;

            $messageText = "مرحباً ولي أمر الطالب " . ($student?->name ?? '') . "، نود تذكيركم بأن دورة الجدول الأسبوعي لمقرر ({$courseTitle}) ستنتهي بتاريخ {$endDateFormatted} (متبقي {$remainingSessionsCount} حصص). نرجو التكرم بتأكيد الرغبة في تجديد الجدول للشهر القادم وسداد الاشتراك. أكاديمية إيليت.";

            $studentWhatsAppUrl = $cleanStudentPhone ? "https://wa.me/{$cleanStudentPhone}?text=" . urlencode($messageText) : null;
            $parentWhatsAppUrl = $cleanParentPhone ? "https://wa.me/{$cleanParentPhone}?text=" . urlencode($messageText) : null;

            // Countdown Status
            $isPast = $sch->end_date->isPast();
            if ($isPast) {
                $statusBadge = 'الدورة انتهت وتحتاج تمديداً';
                $statusColor = 'danger';
            } elseif ($sch->end_date->diffInDays($now) <= 3) {
                $statusBadge = 'متبقي ' . $sch->end_date->diffForHumans();
                $statusColor = 'danger';
            } else {
                $statusBadge = 'تنتهي ' . $sch->end_date->diffForHumans();
                $statusColor = 'warning';
            }

            return (object) [
                'record' => $sch,
                'student' => $student,
                'parent' => $parent,
                'teacher' => $sch->teacherProfile?->user,
                'courseTitle' => $courseTitle,
                'subjectName' => $sch->course?->subject?->name ?: 'عام',
                'daysFormatted' => $daysFormatted,
                'startTime' => $sch->start_time ? Carbon::parse($sch->start_time)->format('h:i A') : '—',
                'endDateFormatted' => $endDateFormatted,
                'remainingSessions' => $remainingSessionsCount,
                'totalSessions' => $totalSessionsCount,
                'studentWhatsAppUrl' => $studentWhatsAppUrl,
                'parentWhatsAppUrl' => $parentWhatsAppUrl,
                'statusBadge' => $statusBadge,
                'statusColor' => $statusColor,
            ];
        });
    }

    /**
     * Open Renew Package Modal
     */
    public function openRenewPackageModal(int $packageId): void
    {
        $pkg = StudentPackage::find($packageId);
        if (! $pkg) return;

        $this->renewingPackageId = $pkg->id;
        $this->newTotalSessions = $pkg->total_sessions ?: 12;
        $this->newTemplateId = $pkg->package_template_id;
        $this->newExpiresAt = now()->addDays(30)->format('Y-m-d\TH:i');
        $this->renewReason = 'تجديد الباقة وتحصيل الاشتراك الشهري';
        $this->showRenewModal = true;
    }

    /**
     * Submit Package Renewal
     */
    public function submitRenewPackage(): void
    {
        if (! $this->renewingPackageId) return;

        $pkg = StudentPackage::find($this->renewingPackageId);
        if (! $pkg) return;

        $newExpiry = !empty($this->newExpiresAt) ? Carbon::parse($this->newExpiresAt) : null;

        $pkg->renewPackage(
            newTotalSessions: (int) $this->newTotalSessions,
            packageTemplateId: $this->newTemplateId,
            newExpiresAt: $newExpiry,
            reason: $this->renewReason ?: 'تجديد الباقة وتحصيل الاشتراك'
        );

        $this->showRenewModal = false;

        Notification::make()
            ->title(__('Package Renewed Successfully!'))
            ->body(__('Activated :count session credits for student :name.', [
                'count' => $this->newTotalSessions,
                'name' => $pkg->studentUser?->name,
            ]))
            ->success()
            ->send();
    }

    /**
     * Open Quick Add Credits Modal
     */
    public function openAddCreditsModal(int $packageId): void
    {
        $this->addCreditsPackageId = $packageId;
        $this->creditsToAdd = 5;
        $this->showAddCreditsModal = true;
    }

    /**
     * Submit Quick Add Credits
     */
    public function submitAddCredits(): void
    {
        if (! $this->addCreditsPackageId) return;

        $pkg = StudentPackage::find($this->addCreditsPackageId);
        if (! $pkg) return;

        $n = (int) $this->creditsToAdd;
        if ($n <= 0) return;

        $pkg->increment('remaining_sessions', $n);
        $pkg->increment('total_sessions', $n);
        if ($pkg->remaining_sessions > 0 && $pkg->status === 'exhausted') {
            $pkg->update(['status' => 'active']);
        }

        $this->showAddCreditsModal = false;

        Notification::make()
            ->title(__('Added :count Sessions', ['count' => $n]))
            ->body(__('Updated balance to :remaining sessions.', ['remaining' => $pkg->fresh()->remaining_sessions]))
            ->success()
            ->send();
    }

    /**
     * Send Instant Push / In-App Payment Reminder
     */
    public function sendPaymentReminder(int $packageId, FcmNotificationService $fcmService): void
    {
        $pkg = StudentPackage::find($packageId);
        if (! $pkg || ! $pkg->studentUser) return;

        $student = $pkg->studentUser;
        $title = 'تنبيه: تجديد رصيد الباقة التعليمية';
        $body = "مرحباً {$student->name}، رصيد باقتك الحالية متبقي به ({$pkg->remaining_sessions}) حصص فقط. يرجى مراجعة إدارة الأكاديمية للتجديد وتجنب انقطاع الحصص.";

        $fcmService->sendNotification($student, 'package_reminder', $title, $body, '/student-portal');

        // Also notify parent if exists
        $parent = DB::table('parent_student')
            ->join('users', 'users.id', '=', 'parent_student.parent_user_id')
            ->where('parent_student.student_user_id', $student->id)
            ->select('users.id')
            ->first();

        if ($parent) {
            $parentUser = User::find($parent->id);
            if ($parentUser) {
                $fcmService->sendNotification($parentUser, 'package_reminder', $title, "تنبيه لولي الأمر: رصيد باقة الطالب {$student->name} أوشك على الانتهاء ({$pkg->remaining_sessions} حصص متبقية). يرجى التجديد.", '/parent-portal');
            }
        }

        Notification::make()
            ->title(__('Sent Payment Reminder!'))
            ->body(__('Instant notification sent to :name and parents.', ['name' => $student->name]))
            ->success()
            ->send();
    }

    /**
     * Open Extend Schedule Cycle Modal
     */
    public function openExtendScheduleModal(int $scheduleId): void
    {
        $sch = RecurringSchedule::find($scheduleId);
        if (! $sch) return;

        $this->extendingScheduleId = $sch->id;
        $currentEnd = $sch->end_date ?: now();
        $this->newScheduleEndDate = Carbon::parse($currentEnd)->addMonth()->format('Y-m-d');
        $this->extendReason = 'تجديد الدورة الشهرية وتوليد حصص الشهر القادم';
        $this->showExtendScheduleModal = true;
    }

    /**
     * Submit Extend Recurring Schedule Cycle
     */
    public function submitExtendSchedule(RecurringScheduleService $service): void
    {
        if (! $this->extendingScheduleId || empty($this->newScheduleEndDate)) return;

        $sch = RecurringSchedule::find($this->extendingScheduleId);
        if (! $sch) return;

        try {
            $generated = $service->extendScheduleCycle(
                schedule: $sch,
                newEndDate: $this->newScheduleEndDate,
                user: auth()->user(),
                reason: $this->extendReason
            );

            $this->showExtendScheduleModal = false;

            Notification::make()
                ->title(__('Cycle Extended Successfully!'))
                ->body(__('Extended cycle until :date. Generated :count new recurring session instances.', [
                    'date' => $this->newScheduleEndDate,
                    'count' => $generated,
                ]))
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title(__('Failed to Extend Cycle'))
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    /**
     * Clean phone number for WhatsApp
     */
    private function formatWhatsAppPhone(?string $phone): ?string
    {
        if (! $phone) return null;
        $clean = preg_replace('/[^0-9]/', '', $phone);
        if (empty($clean)) return null;

        // If local Egyptian phone 01XXXXXXXXX -> 201XXXXXXXXX
        if (str_starts_with($clean, '01') && strlen($clean) === 11) {
            $clean = '2' . $clean;
        }

        return $clean;
    }
}
