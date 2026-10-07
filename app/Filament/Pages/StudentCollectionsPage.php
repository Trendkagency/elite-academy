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
    public bool $showKpiDetailModal = false;
    public ?string $inspectedKpi = null;

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
    public ?string $extendingScheduleTitle = null;
    public ?string $extendingScheduleStudentName = null;
    public ?string $extendingScheduleCurrentEndDate = null;

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

    public function getHeading(): string
    {
        return '';
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

    public function selectKpi(string $key): void
    {
        if ($key === 'exhausted') {
            $this->activeTab = 'packages';
            $this->packageFilter = ($this->packageFilter === 'exhausted' && $this->activeTab === 'packages') ? 'all' : 'exhausted';
        } elseif ($key === 'low') {
            $this->activeTab = 'packages';
            $this->packageFilter = ($this->packageFilter === 'low' && $this->activeTab === 'packages') ? 'all' : 'low';
        } elseif ($key === 'expiring') {
            $this->activeTab = 'packages';
            $this->packageFilter = ($this->packageFilter === 'expiring' && $this->activeTab === 'packages') ? 'all' : 'expiring';
        } elseif ($key === 'ending_soon') {
            $this->activeTab = 'schedules';
            $this->scheduleFilter = ($this->scheduleFilter === 'ending_soon' && $this->activeTab === 'schedules') ? 'all' : 'ending_soon';
        }
    }

    public function clearKpiFilter(): void
    {
        if ($this->activeTab === 'packages') {
            $this->packageFilter = 'all';
        } else {
            $this->scheduleFilter = 'all';
        }
    }

    public function getActiveKpiProperty(): ?string
    {
        if ($this->activeTab === 'packages') {
            return in_array($this->packageFilter, ['exhausted', 'low', 'expiring'], true) ? $this->packageFilter : null;
        }
        if ($this->activeTab === 'schedules') {
            return in_array($this->scheduleFilter, ['ending_soon', 'ended'], true) ? $this->scheduleFilter : null;
        }
        return null;
    }

    public function inspectKpi(string $key): void
    {
        $this->inspectedKpi = $key;
        $this->showKpiDetailModal = true;
    }

    public function closeKpiModal(): void
    {
        $this->showKpiDetailModal = false;
        $this->inspectedKpi = null;
    }

    public function getKpiMetadata(?string $key): array
    {
        return match ($key) {
            'exhausted' => [
                'title' => 'الباقات المنتهية بالكامل (رصيد 0 حصص)',
                'user_friendly_title' => 'باقات استنفدت كامل الحصص (رصيد صفر)',
                'subtitle' => 'طلاب انتهت حصصهم بالكامل وتتطلب حساباتهم سداداً وتجديداً فورياً لحضور الحصص القادمة',
                'color' => 'red',
                'badge_class' => 'bg-red-500/15 text-red-500 border-red-500/30',
                'icon' => 'fa-circle-exclamation',
                'count' => $this->stats['exhausted_packages'],
                'urgency' => 'حرج / مستحق السداد فوراً',
                'criterion_pill' => 'الرصيد الحالي: 0 حصة',
                'who_are_they' => 'طلاب استهلكوا كامل رصيد حصصهم وأصبح رصيدهم (صفر). لا يمكن للطالب حضور حصص جديدة إلا بعد شحن أو تجديد الباقة.',
                'why_it_matters' => 'لتفادي استهلاك وقت المعلمين دون سداد مسبق، وحث ولي الأمر على التجديد فوراً لضمان استقرار مواعيد الطالب في الجدول.',
                'impact_pill' => 'ضمان مستحقات الأكاديمية والمدربين',
                'how_to_act' => 'تواصل فوراً مع ولي الأمر عبر الواتساب للمطالبة بتجديد الباقة، أو أرسل إشعاراً تطبيقياً، أو جدد الباقة مباشرة.',
                'action_pill' => 'مطلوب: رسالة تذكير بالسداد أو تجديد الباقة',
                'sql_condition' => "remaining_sessions <= 0 AND status IN ('active', 'exhausted')",
                'business_rule' => 'الطلاب الذين استهلكوا كامل رصيد حصصهم وأصبح رصيدهم صفراً. لا يمكن للطالب حضور أو حجز حصص إضافية بدون تجديد الباقة أو شحن رصيد.',
                'action_label' => 'تجديد الباقة أو إرسال مطالبة مالية عبر الواتساب',
                'operational_impact' => 'منع استنزاف وقت وجهد المعلمين دون تحصيل مالي مسبق وتفادي تراكم المستحقات.',
            ],
            'low' => [
                'title' => 'الباقات ذات الرصيد المنخفض (1 إلى 3 حصص)',
                'user_friendly_title' => 'باقات أوشكت على النفاد (متبقي 1 إلى 3 حصص)',
                'subtitle' => 'طلاب على وشك نفاد حصصهم ويستحسن تذكير أولياء أمورهم مبكراً قبل توقف الدروس',
                'color' => 'amber',
                'badge_class' => 'bg-amber-500/15 text-amber-500 border-amber-500/30',
                'icon' => 'fa-bell',
                'count' => $this->stats['low_balance_packages'],
                'urgency' => 'تنبيه مبكر / متابعة استباقية',
                'criterion_pill' => 'الرصيد المتبقي: 1 إلى 3 حصص',
                'who_are_they' => 'طلاب منتظمون يتبقى في رصيدهم حصة واحدة أو حصتان أو ثلاث حصص فقط من باقتهم الحالية.',
                'why_it_matters' => 'إشعار ولي الأمر مسبقاً قبل نفاد الحصص يمنحه وقتاً كافياً للسداد براحة، ويضمن استمرار دروس الطالب دون انقطاع مفاجئ.',
                'impact_pill' => 'استمرارية الدروس دون انقطاع',
                'how_to_act' => 'أرسل تذكيراً ودياً عبر الواتساب أو إشعار فوري لولي الأمر لتجديد الباقة قبل انتهاء الحصص المتبقية.',
                'action_pill' => 'مطلوب: إرسال تذكير استباقي لتجديد الباقة',
                'sql_condition' => "remaining_sessions BETWEEN 1 AND 3 AND status = 'active'",
                'business_rule' => 'تتبع الطلاب قبل وصولهم للصفر لتفادي توقف العملية التعليمية فجأة وإشعار ولي الأمر قبل نفاد الحصص بوقت كافٍ.',
                'action_label' => 'إرسال تذكير استباقي عبر الواتساب لتجديد الاشتراك',
                'operational_impact' => 'الحفاظ على استمرارية التدريس وزيادة معدل التجديد التلقائي (Retention) دون انقطاع.',
            ],
            'expiring' => [
                'title' => 'الباقات التي تنتهي صلاحيتها الزمنية خلال 7 أيام',
                'user_friendly_title' => 'باقات تنتهي صلاحيتها الزمنية هذا الأسبوع',
                'subtitle' => 'باقات أوشكت مدتها التعاقدية على الانتهاء وتتطلب استهلاك الحصص المتبقية أو تمديد الصلاحية',
                'color' => 'purple',
                'badge_class' => 'bg-purple-500/15 text-purple-500 border-purple-500/30',
                'icon' => 'fa-clock',
                'count' => $this->stats['expiring_in_7_days'],
                'urgency' => 'صلاحية زمنية / انتهاء تعاقد',
                'criterion_pill' => 'تنتهي الصلاحية: خلال 7 أيام',
                'who_are_they' => 'طلاب ستنتهي صلاحية باقتهم الزمنية خلال أقل من أسبوع حتى وإن كان لا يزال لديهم رصيد حصص.',
                'why_it_matters' => 'لتنبيه الطالب بسرعة لجدولة حصصه المتبقية قبل فوات الأوان، أو تمديد الصلاحية بالتنسيق مع الإدارة لتفادي فقدان الرصيد.',
                'impact_pill' => 'حماية حقوق الطالب ورضا أولياء الأمور',
                'how_to_act' => 'تواصل مع الطالب/ولي الأمر لتنسيق مواعيد الحصص المتبقية أو تمديد فترة صلاحية الباقة بنقرة زر.',
                'action_pill' => 'مطلوب: تذكير باستهلاك الرصيد أو تمديد الصلاحية',
                'sql_condition' => "expires_at <= NOW() + 7 DAYS AND status = 'active'",
                'business_rule' => 'تنبيه الإدارة بالباقات التي ستنتهي مدتها الصالحة خلال 7 أيام حتى لو كان بها رصيد متبقٍ، لمراجعة اشتراك الطالب أو تمديد الصلاحية.',
                'action_label' => 'تجديد الباقة أو تمديد الصلاحية وتذكير الطالب',
                'operational_impact' => 'حماية حقوق الأكاديمية وحساب تواريخ الصلاحية وتفادي الشكاوى المتعلقة بانتهاء المدة.',
            ],
            'ending_soon' => [
                'title' => 'الجداول الدورية المنتهية قريباً (خلال 14 يوماً)',
                'user_friendly_title' => 'اشتراكات شهرية أوشكت دورتها على الانتهاء',
                'subtitle' => 'جداول حصص أسبوعية منتظمة شارفت دورتها الحالية على الانتهاء وتتطلب التمديد للشهر الجديد',
                'color' => 'emerald',
                'badge_class' => 'bg-emerald-500/15 text-emerald-500 border-emerald-500/30',
                'icon' => 'fa-calendar-days',
                'count' => $this->stats['cycles_ending_soon'],
                'urgency' => 'تجديد دورة شهرية',
                'criterion_pill' => 'نهاية الدورة: خلال أسبوعين أو انتهت',
                'who_are_they' => 'مجموعات وجداول تعليمية أسبوعية متكررة قاربت دورتها المحددة على الانقضاء وتنتظر بدء دورة الشهر الجديد.',
                'why_it_matters' => 'تمديد الدورة يضمن حجز مواعيد الطلاب في تقويم المعلم للشهر القادم تلقائياً دون الحاجة لإعادة الجدولة يدوياً.',
                'impact_pill' => 'تثبيت مواعيد الدروس للشهر الجديد',
                'how_to_act' => 'اضغط على زر (تمديد وتوليد حصص الشهر الجديد) في الجدول أدناه لتمديد الدورة لمدة شهر إضافي فوراً.',
                'action_pill' => 'مطلوب: تمديد الدورة وتوليد حصص الشهر القادم',
                'sql_condition' => "end_date <= NOW() + 14 DAYS AND status = 'active'",
                'business_rule' => 'رصد مواعيد انتهاء الدورات الشهرية للجداول الدورية لتمكين الإدارة من تمديد الدورة وتوليد حصص الشهر الجديد بنقرة واحدة.',
                'action_label' => 'تمديد الدورة لشهر إضافي وتوليد الحصص التلقائية',
                'operational_impact' => 'ضمان عدم سقوط مواعيد الطلاب في التقويم الدراسي للمعلمين عند بداية الشهر الجديد.',
            ],
            default => [],
        };
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

        // Comprehensive multi-field Search query
        if (! empty($this->searchQuery)) {
            $s = trim($this->searchQuery);
            $query->where(function ($q) use ($s) {
                $q->whereHas('studentUser', function ($sq) use ($s) {
                    $sq->where('name', 'like', "%{$s}%")
                       ->orWhere('phone', 'like', "%{$s}%")
                       ->orWhere('email', 'like', "%{$s}%")
                       ->orWhereHas('studentProfile.gradeLevel', fn ($gq) => $gq->where('name', 'like', "%{$s}%"));
                })
                ->orWhereHas('packageTemplate', fn ($pq) => $pq->where('name', 'like', "%{$s}%"))
                ->orWhereHas('course', function ($cq) use ($s) {
                    $cq->where('title', 'like', "%{$s}%")
                       ->orWhereHas('subject', fn ($sub) => $sub->where('name', 'like', "%{$s}%"));
                })
                ->orWhereIn('student_user_id', function ($sub) use ($s) {
                    $sub->select('parent_student.student_user_id')
                        ->from('parent_student')
                        ->join('users as parent_users', 'parent_users.id', '=', 'parent_student.parent_user_id')
                        ->where('parent_users.name', 'like', "%{$s}%")
                        ->orWhere('parent_users.phone', 'like', "%{$s}%");
                });
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

        // Comprehensive multi-field Search query
        if (! empty($this->searchQuery)) {
            $s = trim($this->searchQuery);
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhereHas('studentUser', function ($sq) use ($s) {
                      $sq->where('name', 'like', "%{$s}%")
                         ->orWhere('phone', 'like', "%{$s}%")
                         ->orWhere('email', 'like', "%{$s}%")
                         ->orWhereHas('studentProfile.gradeLevel', fn ($gq) => $gq->where('name', 'like', "%{$s}%"));
                  })
                  ->orWhereHas('teacherProfile.user', function ($tq) use ($s) {
                      $tq->where('name', 'like', "%{$s}%")
                         ->orWhere('phone', 'like', "%{$s}%");
                  })
                  ->orWhereHas('course', function ($cq) use ($s) {
                      $cq->where('title', 'like', "%{$s}%")
                         ->orWhereHas('subject', fn ($sub) => $sub->where('name', 'like', "%{$s}%"));
                  })
                  ->orWhereIn('student_user_id', function ($sub) use ($s) {
                      $sub->select('parent_student.student_user_id')
                          ->from('parent_student')
                          ->join('users as parent_users', 'parent_users.id', '=', 'parent_student.parent_user_id')
                          ->where('parent_users.name', 'like', "%{$s}%")
                          ->orWhere('parent_users.phone', 'like', "%{$s}%");
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
        $sch = RecurringSchedule::with(['studentUser', 'course'])->find($scheduleId);
        if (! $sch) return;

        $this->extendingScheduleId = $sch->id;
        $this->extendingScheduleTitle = $sch->title ?: ($sch->course?->title ?? 'جدول دوري');
        $this->extendingScheduleStudentName = $sch->studentUser?->name ?? 'طالب مسجل';
        $this->extendingScheduleCurrentEndDate = $sch->end_date ? Carbon::parse($sch->end_date)->format('Y-m-d') : null;

        $currentEnd = $sch->end_date ? Carbon::parse($sch->end_date) : now();
        $baseDate = $currentEnd->isPast() ? now() : $currentEnd;
        $this->newScheduleEndDate = $baseDate->copy()->addMonth()->format('Y-m-d');
        $this->extendReason = 'تجديد الدورة الشهرية وتوليد حصص الشهر القادم';
        $this->showExtendScheduleModal = true;
    }

    /**
     * Submit Extend Recurring Schedule Cycle
     */
    public function submitExtendSchedule(RecurringScheduleService $service): void
    {
        if (! $this->extendingScheduleId || empty($this->newScheduleEndDate)) {
            Notification::make()->title(__('يرجى تحديد تاريخ نهاية الدورة الجديد'))->warning()->send();
            return;
        }

        $sch = RecurringSchedule::find($this->extendingScheduleId);
        if (! $sch) {
            $this->showExtendScheduleModal = false;
            return;
        }

        $user = auth()->user() ?: \Filament\Facades\Filament::auth()->user() ?: User::whereHas('adminProfile')->first();

        try {
            $generated = $service->extendScheduleCycle(
                schedule: $sch,
                newEndDate: $this->newScheduleEndDate,
                user: $user,
                reason: $this->extendReason
            );

            $this->showExtendScheduleModal = false;
            $this->extendingScheduleId = null;

            Notification::make()
                ->title(__('تم تمديد الدورة وتوليد الحصص بنجاح!'))
                ->body(__('تم تمديد الدورة حتى :date وتوليد :count حصة جديدة في جدول الطالب والمعلم.', [
                    'date' => $this->newScheduleEndDate,
                    'count' => $generated,
                ]))
                ->success()
                ->send();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $firstMsg = collect($e->errors())->flatten()->first() ?: $e->getMessage();
            Notification::make()
                ->title(__('تنبيه في تاريخ التمديد'))
                ->body($firstMsg)
                ->warning()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title(__('تعذر تمديد الدورة'))
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    /**
     * Send Instant Push / In-App Schedule Renewal Reminder
     */
    public function sendScheduleRenewalReminder(int $scheduleId, FcmNotificationService $fcmService): void
    {
        $sch = RecurringSchedule::with(['student', 'course', 'subject'])->find($scheduleId);
        if (! $sch) return;

        $student = $sch->student;
        $title = 'تنبيه: اقتراب انتهاء دورة الجدول الدراسي';
        $endDateStr = $sch->end_date ? Carbon::parse($sch->end_date)->format('Y-m-d') : '';
        $body = "مرحباً" . ($student ? " {$student->name}" : '') . "، نود تذكيركم باقتراب موعد انتهاء الدورة الشهرية الحالية للجدول الدراسي ({$sch->title}) في تاريخ {$endDateStr}. يرجى مراجعة إدارة الأكاديمية لتأكيد تجديد الاشتراك واستمرار الحصص.";

        if ($student) {
            $fcmService->sendNotification($student, 'schedule_reminder', $title, $body, '/student-portal');

            // Notify parent if exists
            $parent = DB::table('parent_student')
                ->join('users', 'users.id', '=', 'parent_student.parent_user_id')
                ->where('parent_student.student_user_id', $student->id)
                ->select('users.id')
                ->first();

            if ($parent) {
                $parentUser = User::find($parent->id);
                if ($parentUser) {
                    $fcmService->sendNotification($parentUser, 'schedule_reminder', $title, "تنبيه لولي الأمر: تقترب دورة جدول الطالب {$student->name} ({$sch->title}) من الانتهاء. يرجى مراجعة الأكاديمية لتجديد الاشتراك.", '/parent-portal');
                }
            }
        }

        Notification::make()
            ->title(__('Sent Schedule Renewal Reminder!'))
            ->body(__('Notification sent to student and parents successfully.'))
            ->success()
            ->send();
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
