<?php

namespace App\Filament\Resources\RecurringSchedules\Schemas;

use App\Models\Course;
use App\Models\PackageTemplate;
use App\Models\StudentPackage;
use App\Models\TeacherProfile;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class RecurringScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        $isAr = app()->getLocale() === 'ar';

        return $schema
            ->columns(12)
            ->components([
                // ── 1. BASIC COHORT INFORMATION ──────────────────────────────
                Section::make($isAr ? 'البيانات الأساسية للجدول الأسبوعي' : 'Basic Cohort Schedule Information')
                    ->icon('heroicon-o-academic-cap')
                    ->iconColor('teal')
                    ->description($isAr ? 'تحديد مسمى الجدول، المعلم المسؤول، الكورس وحالة الجدول' : 'Cohort title, assigned teacher, course and active status')
                    ->columnSpan([
                        'default' => 12,
                        'xl' => 6,
                    ])
                    ->columns(2)
                    ->components([
                        TextInput::make('title')
                            ->label($isAr ? 'عنوان الجدول الأسبوعي' : 'Cohort Schedule Title')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Weekly Cohort Schedule')
                            ->columnSpanFull(),

                        Select::make('teacher_profile_id')
                            ->label($isAr ? 'المعلم المسؤول' : 'Assigned Teacher')
                            ->options(fn () => TeacherProfile::with('user')->get()->mapWithKeys(fn ($tp) => [
                                $tp->id => ($tp->user?->name ?: 'Teacher #' . $tp->id) . ' (' . ($tp->title ?: 'Faculty') . ')',
                            ]))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(1),

                        Select::make('course_id')
                            ->label($isAr ? 'الكورس المرتبط' : 'Associated Course')
                            ->relationship('course', 'title')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn ($state, $set, $get) => self::syncAutoEndDateAndQuota($set, $get))
                            ->columnSpan(1),

                        Select::make('student_user_id')
                            ->label($isAr ? 'طالب فردي حصة خاصة (اختياري)' : '1-on-1 Student (Optional)')
                            ->options(function () {
                                $query = User::query();
                                if (method_exists(User::class, 'scopeRoleStudent')) {
                                    $query->roleStudent()->orWhereHas('studentProfile');
                                } else {
                                    $query->whereHas('studentProfile');
                                }
                                return $query->pluck('name', 'id');
                            })
                            ->searchable()
                            ->nullable()
                            ->live()
                            ->afterStateUpdated(fn ($state, $set, $get) => self::syncAutoEndDateAndQuota($set, $get))
                            ->helperText($isAr ? 'اتركه فارغاً ليتاح لجميع طلاب المجموعة العامة بالمقرر' : 'Leave empty for course-wide public cohort')
                            ->columnSpan(1),

                        Placeholder::make('student_package_quota_status')
                            ->label($isAr ? 'حالة باقة الطالب والرصيد المتاح' : 'Student Package & Quota Balance')
                            ->columnSpanFull()
                            ->visible(fn ($get) => ! empty($get('student_user_id')))
                            ->content(function ($get) use ($isAr) {
                                $studentId = $get('student_user_id');
                                $courseId = $get('course_id');
                                if (! $studentId) return null;

                                $service = app(\App\Services\Session\RecurringScheduleService::class);
                                $res = $service->calculateEndDateAndQuota([
                                    'course_id' => $courseId,
                                    'student_user_id' => $studentId,
                                    'start_date' => $get('start_date') ?: now()->toDateString(),
                                    'recurrence_type' => $get('recurrence_type') ?: 'weekly',
                                    'days_of_week' => (array) ($get('days_of_week') ?? []),
                                ]);

                                $quota = $res['student_quota'];
                                if (! $quota) return null;

                                if ($get('auto_renew_package')) {
                                    $renewCount = (int) ($get('renew_sessions_count') ?: $quota['required_sessions']);
                                    return new HtmlString('
                                        <div class="rec-quota-card rec-quota-card-auto-provision">
                                            <div style="display: flex; align-items: center; gap: 0.85rem;">
                                                <div class="rec-quota-icon-badge" style="background: rgba(139, 92, 246, 0.2); color: #c4b5fd; border: 1.5px solid rgba(139, 92, 246, 0.4);">
                                                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                                                </div>
                                                <div style="flex: 1;">
                                                    <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                                        <span style="font-weight: 800; font-size: 0.875rem; color: #ffffff;">' . ($isAr ? 'تم تفعيل التجديد التلقائي للباقة مع هذا الجدول' : 'Package Auto-Renew / Provisioning Active') . '</span>
                                                        <span style="background: rgba(139, 92, 246, 0.25); color: #ddd6fe; border: 1px solid rgba(139, 92, 246, 0.4); padding: 0.15rem 0.6rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 700;">⚡ ' . ($isAr ? 'جاهز للحفظ' : 'Ready to Save') . '</span>
                                                    </div>
                                                    <div style="color: #ede9fe; font-size: 0.75rem; margin-top: 0.25rem; line-height: 1.4;">' . ($isAr ? "سيتم تفعيل أو تجديد باقة الطالب تلقائياً برصيد ({$renewCount} حصة) فور حفظ هذا الجدول، ولن يتم حظر إنشاء الحصص." : "Package will be automatically provisioned/renewed with {$renewCount} sessions on save.") . '</div>
                                                </div>
                                            </div>
                                            <div class="rec-chip-group">
                                                <span class="rec-chip" style="background: rgba(139, 92, 246, 0.25); border-color: rgba(139, 92, 246, 0.5); color: #ede9fe;">' . ($isAr ? 'رصيد الباقة الجديد: ' : 'New Balance: ') . '<strong style="color: #c4b5fd;">' . $renewCount . '</strong> ' . ($isAr ? 'حصة' : 'sessions') . '</span>
                                                <span class="rec-chip">' . ($isAr ? 'المطلوب للجدول: ' : 'Required: ') . '<strong style="color: #22d3ee;">' . $quota['required_sessions'] . '</strong></span>
                                            </div>
                                        </div>
                                    ');
                                }

                                if (! empty($quota['status']) && $quota['status'] === 'enrolled_direct') {
                                    return new HtmlString('
                                        <div class="rec-quota-card rec-quota-card-enrolled">
                                            <div style="display: flex; align-items: center; gap: 0.85rem;">
                                                <div class="rec-quota-icon-badge rec-quota-enrolled-icon">
                                                    <i class="fa-solid fa-graduation-cap"></i>
                                                </div>
                                                <div style="flex: 1;">
                                                    <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                                        <span style="font-weight: 800; font-size: 0.875rem; color: #ffffff;">' . ($isAr ? 'تسجيل معتمد في المقرر (مباشر)' : 'Active Course Direct Enrollment') . '</span>
                                                        <span style="background: rgba(6, 182, 212, 0.25); color: #a5f3fc; border: 1px solid rgba(6, 182, 212, 0.4); padding: 0.15rem 0.6rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 700;">🟢 ' . ($isAr ? 'مسموح بالجدولة' : 'Allowed') . '</span>
                                                    </div>
                                                    <div style="color: #cffafe; font-size: 0.75rem; margin-top: 0.25rem;">' . ($isAr ? 'الطالب مسجل رسمياً في المقرر بدون باقة حصص مقيدة. يمكن جدولة الحصص مباشرة.' : 'Student is officially enrolled in this course without package restriction.') . '</div>
                                                </div>
                                            </div>
                                        </div>
                                    ');
                                }

                                if ($quota['has_package']) {
                                    if ($quota['can_create']) {
                                        $remainingAfter = max(0, $quota['remaining_sessions'] - $quota['required_sessions']);
                                        return new HtmlString('
                                            <div class="rec-quota-card rec-quota-card-sufficient">
                                                <div style="display: flex; align-items: center; gap: 0.85rem;">
                                                    <div class="rec-quota-icon-badge rec-quota-sufficient-icon">
                                                        <i class="fa-solid fa-circle-check"></i>
                                                    </div>
                                                    <div>
                                                        <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                                            <span style="font-weight: 800; font-size: 0.875rem; color: #ffffff;">' . ($isAr ? 'باقة الطالب نشطة ورصيد الحصص كافٍ' : 'Student Package Active & Sufficient') . '</span>
                                                            <span style="background: rgba(16, 185, 129, 0.25); color: #6ee7b7; border: 1px solid rgba(16, 185, 129, 0.4); padding: 0.15rem 0.6rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 700;">' . e($quota['package_name']) . '</span>
                                                        </div>
                                                        <div style="color: #a7f3d0; font-size: 0.75rem; margin-top: 0.25rem;">' . ($isAr ? 'رصيد الطالب يغطي كامل حصص هذا الجدول بنجاح.' : 'Package balance covers full schedule requirements.') . '</div>
                                                    </div>
                                                </div>
                                                <div class="rec-chip-group">
                                                    <span class="rec-chip">' . ($isAr ? 'المتاح: ' : 'Available: ') . '<strong style="color: #34d399;">' . $quota['remaining_sessions'] . '</strong> ' . ($isAr ? 'من ' : 'of ') . $quota['total_sessions'] . '</span>
                                                    <span class="rec-chip">' . ($isAr ? 'المطلوب: ' : 'Required: ') . '<strong style="color: #22d3ee;">' . $quota['required_sessions'] . '</strong></span>
                                                    <span class="rec-chip" style="background: rgba(16, 185, 129, 0.2); border-color: rgba(16, 185, 129, 0.4);">' . ($isAr ? 'المتبقي بعد الخصم: ' : 'Remaining: ') . '<strong style="color: #6ee7b7;">' . $remainingAfter . '</strong></span>
                                                </div>
                                            </div>
                                        ');
                                    } else {
                                        $deficit = $quota['required_sessions'] - $quota['remaining_sessions'];
                                        return new HtmlString('
                                            <div class="rec-quota-card rec-quota-card-insufficient">
                                                <div style="display: flex; align-items: center; gap: 0.85rem;">
                                                    <div class="rec-quota-icon-badge rec-quota-insufficient-icon">
                                                        <i class="fa-solid fa-triangle-exclamation"></i>
                                                    </div>
                                                    <div>
                                                        <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                                            <span style="font-weight: 800; font-size: 0.875rem; color: #ffffff;">' . ($isAr ? 'عجز في رصيد باقة الطالب (الجدولة موقوفة)' : 'Insufficient Balance (Schedule Blocked)') . '</span>
                                                            <span style="background: rgba(244, 63, 94, 0.25); color: #fecdd3; border: 1px solid rgba(244, 63, 94, 0.4); padding: 0.15rem 0.6rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 700;">' . e($quota['package_name']) . '</span>
                                                        </div>
                                                        <div style="color: #fecdd3; font-size: 0.75rem; margin-top: 0.25rem;">' . ($isAr ? 'رصيد الطالب غير كافٍ. يلزم تجديد أو شحن الباقة لإتمام الجدول. يمكنك تفعيل خيار «تجديد / تفعيل باقة الطالب تلقائياً» بالأسفل.' : 'Package balance is less than required sessions. Enable auto-renew below to proceed.') . '</div>
                                                    </div>
                                                </div>
                                                <div class="rec-chip-group">
                                                    <span class="rec-chip">' . ($isAr ? 'المتاح فقط: ' : 'Available: ') . '<strong style="color: #fb7185;">' . $quota['remaining_sessions'] . '</strong></span>
                                                    <span class="rec-chip">' . ($isAr ? 'المطلوب: ' : 'Required: ') . '<strong style="color: #f59e0b;">' . $quota['required_sessions'] . '</strong></span>
                                                    <span class="rec-chip" style="background: rgba(244, 63, 94, 0.25); border-color: rgba(244, 63, 94, 0.5); color: #fff;">' . ($isAr ? 'عجز: ' : 'Deficit: ') . '<strong style="color: #fda4af;">-' . $deficit . '</strong> ' . ($isAr ? 'حصة' : '') . '</span>
                                                </div>
                                            </div>
                                        ');
                                    }
                                } else {
                                    return new HtmlString('
                                        <div class="rec-quota-card rec-quota-card-no-package">
                                            <div style="display: flex; align-items: center; gap: 0.85rem;">
                                                <div class="rec-quota-icon-badge rec-quota-no-package-icon">
                                                    <i class="fa-solid fa-circle-exclamation"></i>
                                                </div>
                                                <div style="flex: 1;">
                                                    <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                                        <span style="font-weight: 800; font-size: 0.875rem; color: #ffffff;">' . ($isAr ? 'لا توجد باقة حصص نشطة للطالب في هذا المقرر' : 'No Active Student Package Found') . '</span>
                                                        <span style="background: rgba(245, 158, 11, 0.25); color: #fef3c7; border: 1px solid rgba(245, 158, 11, 0.4); padding: 0.15rem 0.6rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 700;">⚠️ ' . ($isAr ? 'يلزم التفعيل' : 'Activation Required') . '</span>
                                                    </div>
                                                    <div style="color: #fde68a; font-size: 0.75rem; margin-top: 0.25rem; line-height: 1.4;">' . ($isAr ? 'الطالب ليس لديه باقة حصص مفعّلة في هذا المقرر. يمكنك تفعيل خيار «تجديد / تفعيل باقة الطالب تلقائياً» بالأسفل لإنشاء أو تجديد الباقة مع هذا الجدول مباشرة.' : 'Student does not have an active package. Enable auto-renew below to create or renew a package on save.') . '</div>
                                                </div>
                                            </div>
                                        </div>
                                    ');
                                }
                            }),

                        Section::make($isAr ? 'تجديد / تفعيل باقة الطالب تلقائياً (Auto-Renew)' : 'Auto-Renew & Student Package Provisioning')
                            ->icon('heroicon-o-sparkles')
                            ->iconColor('warning')
                            ->description($isAr ? 'في حال عدم وجود باقة أو عجز في الرصيد، يمكنك تفعيل هذا الخيار لإنشاء أو تجديد الباقة تلقائياً عند حفظ الجدول دون أي تعطل' : 'Automatically provision or renew student package upon saving schedule')
                            ->collapsible()
                            ->collapsed(fn ($get) => empty($get('auto_renew_package')))
                            ->visible(fn ($get) => ! empty($get('student_user_id')))
                            ->columnSpanFull()
                            ->columns(2)
                            ->components([
                                Toggle::make('auto_renew_package')
                                    ->label($isAr ? 'تفعيل أو تجديد باقة الطالب تلقائياً عند حفظ هذا الجدول' : 'Auto-Renew / Provision Package with Schedule')
                                    ->helperText($isAr ? 'عند التفعيل، سيتم تفعيل أو تجديد باقة الطالب بالحصص المحددة فوراً عند حفظ هذا الجدول والسماح بإنشاء الحصص.' : 'Package will be provisioned or renewed automatically upon saving.')
                                    ->live()
                                    ->afterStateUpdated(fn ($state, $set, $get) => self::syncAutoEndDateAndQuota($set, $get))
                                    ->columnSpanFull(),

                                Radio::make('renew_action_type')
                                    ->label($isAr ? 'نوع إجراء التجديد' : 'Renewal Action')
                                    ->options([
                                        'auto' => $isAr ? 'تلقائي (تجديد الباقة الحالية إن وجدت، أو إنشاء باقة جديدة)' : 'Auto (Renew existing or create new)',
                                        'renew' => $isAr ? 'تجديد الباقة الحالية وتعيين رصيد الحصص' : 'Renew existing package',
                                        'top_up' => $isAr ? 'إضافة رصيد حصص للباقة الحالية (Top-up)' : 'Top up additional sessions',
                                        'create_new' => $isAr ? 'إنشاء باقة جديدة منفصلة' : 'Create brand new package',
                                    ])
                                    ->default('auto')
                                    ->visible(fn ($get) => (bool) $get('auto_renew_package'))
                                    ->columnSpanFull(),

                                Select::make('renew_package_template_id')
                                    ->label($isAr ? 'نموذج الباقة (اختياري)' : 'Package Template (Optional)')
                                    ->options(fn () => PackageTemplate::where('is_active', true)->pluck('name', 'id'))
                                    ->searchable()
                                    ->nullable()
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        if ($state) {
                                            $template = PackageTemplate::find($state);
                                            if ($template && $template->sessions_count) {
                                                $service = app(\App\Services\Session\RecurringScheduleService::class);
                                                $calc = $service->calculateEndDateAndQuota([
                                                    'course_id' => $get('course_id'),
                                                    'student_user_id' => $get('student_user_id'),
                                                    'start_date' => $get('start_date') ?: now()->toDateString(),
                                                    'recurrence_type' => $get('recurrence_type') ?: 'weekly',
                                                    'days_of_week' => (array) ($get('days_of_week') ?? []),
                                                ]);
                                                $minRequired = max(1, (int) ($calc['target_sessions'] ?? 1));
                                                $set('renew_sessions_count', max($template->sessions_count, $minRequired));
                                                self::syncAutoEndDateAndQuota($set, $get);
                                            }
                                        }
                                    })
                                    ->visible(fn ($get) => (bool) $get('auto_renew_package'))
                                    ->columnSpan(1),

                                TextInput::make('renew_sessions_count')
                                    ->label($isAr ? 'عدد الحصص (لا يمكن أن يقل عن حصص المقرر)' : 'Package Sessions (Min. Required)')
                                    ->numeric()
                                    ->required(fn ($get) => (bool) $get('auto_renew_package'))
                                    ->live()
                                    ->afterStateUpdated(fn ($state, $set, $get) => self::syncAutoEndDateAndQuota($set, $get))
                                    ->default(function ($get) {
                                        $service = app(\App\Services\Session\RecurringScheduleService::class);
                                        $calc = $service->calculateEndDateAndQuota([
                                            'course_id' => $get('course_id'),
                                            'student_user_id' => $get('student_user_id'),
                                            'start_date' => $get('start_date') ?: now()->toDateString(),
                                            'recurrence_type' => $get('recurrence_type') ?: 'weekly',
                                            'days_of_week' => (array) ($get('days_of_week') ?? []),
                                        ]);
                                        return max(1, (int) ($calc['target_sessions'] ?? 1));
                                    })
                                    ->minValue(function ($get) {
                                        $service = app(\App\Services\Session\RecurringScheduleService::class);
                                        $calc = $service->calculateEndDateAndQuota([
                                            'course_id' => $get('course_id'),
                                            'student_user_id' => $get('student_user_id'),
                                            'start_date' => $get('start_date') ?: now()->toDateString(),
                                            'recurrence_type' => $get('recurrence_type') ?: 'weekly',
                                            'days_of_week' => (array) ($get('days_of_week') ?? []),
                                        ]);
                                        return max(1, (int) ($calc['target_sessions'] ?? 1));
                                    })
                                    ->helperText(function ($get) use ($isAr) {
                                        $service = app(\App\Services\Session\RecurringScheduleService::class);
                                        $calc = $service->calculateEndDateAndQuota([
                                            'course_id' => $get('course_id'),
                                            'student_user_id' => $get('student_user_id'),
                                            'start_date' => $get('start_date') ?: now()->toDateString(),
                                            'recurrence_type' => $get('recurrence_type') ?: 'weekly',
                                            'days_of_week' => (array) ($get('days_of_week') ?? []),
                                        ]);
                                        $minRequired = max(1, (int) ($calc['target_sessions'] ?? 1));
                                        return $isAr
                                            ? "الحد الأدنى لحصص هذا المقرر هو {$minRequired} حصة. لا يمكن تحديد عدد أقل."
                                            : "Minimum required for this course is {$minRequired} sessions. Cannot be less.";
                                    })
                                    ->visible(fn ($get) => (bool) $get('auto_renew_package'))
                                    ->columnSpan(1),

                                DatePicker::make('renew_expires_at')
                                    ->label($isAr ? 'تاريخ انتهاء صلاحية الباقة (اختياري)' : 'Package Expiry Date (Optional)')
                                    ->default(now()->addMonths(3))
                                    ->visible(fn ($get) => (bool) $get('auto_renew_package'))
                                    ->columnSpan(1),
                            ]),

                        Select::make('recurrence_type')
                            ->label($isAr ? 'نمط التكرار' : 'Recurrence Pattern')
                            ->options([
                                'weekly' => $isAr ? 'أسبوعي' : 'Weekly',
                                'biweekly' => $isAr ? 'كل أسبوعين' : 'Bi-weekly',
                                'daily' => $isAr ? 'يومي' : 'Daily',
                                'monthly' => $isAr ? 'شهري' : 'Monthly',
                            ])
                            ->default('weekly')
                            ->live()
                            ->afterStateUpdated(fn ($state, $set, $get) => self::syncAutoEndDateAndQuota($set, $get))
                            ->required()
                            ->columnSpan(1),

                        Select::make('status')
                            ->label($isAr ? 'حالة الجدول' : 'Schedule Status')
                            ->options([
                                'active' => $isAr ? 'نشط وفعال' : 'Active',
                                'paused' => $isAr ? 'متوقف مؤقتاً' : 'Paused',
                                'completed' => $isAr ? 'مكتمل' : 'Completed',
                                'cancelled' => $isAr ? 'ملغي' : 'Cancelled',
                            ])
                            ->default('active')
                            ->required()
                            ->columnSpanFull(),
                    ]),

                // ── 2. DEFAULT SESSION SETTINGS & TIMING ─────────────────────
                Section::make($isAr ? 'الإعدادات العامة والافتراضية للجلسات' : 'Default Session Settings & Timing')
                    ->icon('heroicon-o-clock')
                    ->iconColor('amber')
                    ->description($isAr ? 'التوقيت الافتراضي للبدء، مدة الحصة، وتواريخ سريان الدورة' : 'Default start times, session duration, and calendar validity')
                    ->columnSpan([
                        'default' => 12,
                        'xl' => 6,
                    ])
                    ->columns(2)
                    ->components([
                        TimePicker::make('start_time')
                            ->label($isAr ? 'وقت البدء الافتراضي' : 'Default Start Time')
                            ->required()
                            ->seconds(false)
                            ->default('10:00')
                            ->live(debounce: 500)
                            ->columnSpan(1),

                        TextInput::make('duration_minutes')
                            ->label($isAr ? 'مدة الحصة (بالدقائق)' : 'Duration (Minutes)')
                            ->numeric()
                            ->default(60)
                            ->suffix($isAr ? 'دقيقة' : 'min')
                            ->required()
                            ->live(debounce: 500)
                            ->columnSpan(1),

                        DatePicker::make('start_date')
                            ->label($isAr ? 'تاريخ بداية الجدول' : 'Start Date')
                            ->required()
                            ->default(now()->toDateString())
                            ->live()
                            ->afterStateUpdated(fn ($state, $set, $get) => self::syncAutoEndDateAndQuota($set, $get))
                            ->columnSpan(1),

                        TextInput::make('end_date')
                            ->label($isAr ? 'تاريخ نهاية الجدول' : 'End Date')
                            ->disabled()
                            ->dehydrated()
                            ->readOnly()
                            ->prefixIcon('heroicon-m-lock-closed')
                            ->prefixIconColor('teal')
                            ->extraInputAttributes([
                                'tabindex' => '-1',
                                'readonly' => 'readonly',
                                'disabled' => 'disabled',
                                'style' => 'pointer-events: none !important; user-select: none !important; cursor: not-allowed !important; font-family: monospace !important; font-weight: 800 !important; font-size: 0.95rem !important; letter-spacing: 0.05em !important; color: #2dd4bf !important;',
                            ])
                            ->default(function () {
                                $service = app(\App\Services\Session\RecurringScheduleService::class);
                                $calc = $service->calculateEndDateAndQuota([
                                    'start_date' => now()->toDateString(),
                                    'days_of_week' => [6, 0],
                                    'recurrence_type' => 'weekly',
                                ]);
                                return $calc['end_date'];
                            })
                            ->helperText(new HtmlString('<span style="display:inline-flex; align-items:center; gap:0.35rem; font-size:0.75rem; font-weight:700; color:#14b8a6; margin-top:0.3rem;"><i class="fa-solid fa-lock" style="font-size:0.7rem;"></i> ' . ($isAr ? 'محسوب تلقائياً وموزع على مدار الشهر حسب حصص المقرر (مقفل)' : 'Auto-computed from course sessions & distributed (Locked)') . '</span>'))
                            ->columnSpan(1),

                        Placeholder::make('live_sync_stats')
                            ->label($isAr ? 'المزامنة التلقائية للجلسات والأيام' : 'Live Session Auto-Sync Overview')
                            ->columnSpanFull()
                            ->content(function ($get) use ($isAr) {
                                $startDate = $get('start_date');
                                $endDate = $get('end_date');
                                $days = (array) ($get('days_of_week') ?? []);
                                $recurrence = $get('recurrence_type') ?? 'weekly';

                                $service = app(\App\Services\Session\RecurringScheduleService::class);

                                if (! $startDate) {
                                    $startDate = now()->toDateString();
                                }

                                if (! $endDate) {
                                    $calc = $service->calculateEndDateAndQuota([
                                        'course_id' => $get('course_id'),
                                        'student_user_id' => $get('student_user_id'),
                                        'start_date' => $startDate,
                                        'recurrence_type' => $recurrence,
                                        'days_of_week' => $days,
                                    ]);
                                    $endDate = $calc['end_date'];
                                }

                                try {
                                    $start = \Carbon\Carbon::parse($startDate);
                                    $end = \Carbon\Carbon::parse($endDate);
                                    if ($end->lt($start)) {
                                        return new HtmlString('<div style="padding: 0.75rem 1rem; border-radius: 0.75rem; background: rgba(244, 63, 94, 0.15); border: 1.5px solid rgba(244, 63, 94, 0.4); color: #fb7185; font-size: 0.8rem; font-weight: 700;">' . ($isAr ? '⚠️ تنبيه: تاريخ النهاية يسبق تاريخ البداية، لن يتم إنشاء أو مزامنة حصص.' : '⚠️ Warning: End date is before start date.') . '</div>');
                                    }

                                    $diffDays = $start->diffInDays($end) + 1;
                                    $diffWeeks = (int) ceil($diffDays / 7);

                                    $previewDates = $service->previewDates([
                                        'start_date' => $start->toDateString(),
                                        'end_date' => $end->toDateString(),
                                        'start_time' => $get('start_time') ?: '10:00',
                                        'duration_minutes' => (int) ($get('duration_minutes') ?: 60),
                                        'recurrence_type' => $recurrence,
                                        'days_of_week' => $days,
                                        'teacher_profile_id' => $get('teacher_profile_id') ?: 0,
                                        'student_user_id' => $get('student_user_id') ?: null,
                                        'course_id' => $get('course_id') ?: null,
                                    ]);

                                    $sessionsCount = count($previewDates);
                                    $daysCount = count($days);

                                    return new HtmlString('
                                        <div class="rec-sync-hud">
                                            <div class="rec-hud-item rec-hud-teal">
                                                <div class="rec-hud-label" style="color: #5eead4;">
                                                    <i class="fa-solid fa-calendar-range"></i> ' . ($isAr ? 'المدة الإجمالية' : 'Duration') . '
                                                </div>
                                                <div class="rec-hud-value">' . ($isAr ? "{$diffWeeks} أسبوع ({$diffDays} يوم)" : "{$diffWeeks} wks ({$diffDays} d)") . '</div>
                                            </div>
                                            <div class="rec-hud-item rec-hud-indigo">
                                                <div class="rec-hud-label" style="color: #a5b4fc;">
                                                    <i class="fa-solid fa-calendar-days"></i> ' . ($isAr ? 'الأيام المحددة' : 'Days/Week') . '
                                                </div>
                                                <div class="rec-hud-value">' . ($isAr ? "{$daysCount} أيام/أسبوع" : "{$daysCount} Days/Wk") . '</div>
                                            </div>
                                            <div class="rec-hud-item rec-hud-emerald">
                                                <div class="rec-hud-label" style="color: #6ee7b7;">
                                                    <i class="fa-solid fa-bolt"></i> ' . ($isAr ? 'إجمالي الحصص' : 'Total Sessions') . '
                                                </div>
                                                <div class="rec-hud-value" style="color: #34d399;">' . ($isAr ? "{$sessionsCount} حصة موزعة" : "{$sessionsCount} Sessions") . '</div>
                                            </div>
                                            <div class="rec-hud-item rec-hud-cyan">
                                                <div class="rec-hud-label" style="color: #67e8f9;">
                                                    <i class="fa-solid fa-flag-checkered"></i> ' . ($isAr ? 'تاريخ الختام' : 'End Date') . '
                                                </div>
                                                <div class="rec-hud-value" style="color: #22d3ee;">' . $end->toDateString() . '</div>
                                            </div>
                                        </div>
                                    ');
                                } catch (\Throwable $e) {
                                    return '';
                                }
                            }),

                        Select::make('meeting_platform')
                            ->label($isAr ? 'منصة البث الافتراضية' : 'Default Meeting Platform')
                            ->options([
                                'agora' => $isAr ? 'غرفة المنصة التفاعلية (Agora)' : 'Agora Interactive Classroom',
                                'google_meet' => 'Google Meet',
                                'zoom' => 'Zoom Meeting',
                                'microsoft_teams' => 'Microsoft Teams',
                                'other' => $isAr ? 'رابط مخصص آخر' : 'Other / Custom Stream Link',
                            ])
                            ->default('agora')
                            ->required()
                            ->columnSpan(1),

                        TextInput::make('meeting_link')
                            ->label($isAr ? 'رابط البث الافتراضي' : 'Default Meeting Link')
                            ->url()
                            ->placeholder('https://zoom.us/j/... or stream link')
                            ->maxLength(500)
                            ->columnSpan(1),

                        Textarea::make('notes')
                            ->label($isAr ? 'ملاحظات إضافية' : 'Admin / Teacher Notes')
                            ->columnSpanFull()
                            ->rows(2)
                            ->nullable(),
                    ]),

                // ── 3. PER-DAY CUSTOM SCHEDULES & LINKS (FULL WIDTH) ─────────
                Section::make(new HtmlString('<i class="fa-solid fa-calendar-days text-emerald-500 me-2"></i>' . ($isAr ? 'تخصيص الأيام والمواعيد المستقلة' : 'Per-Day Custom Schedules & Links')))
                    ->description($isAr ? 'اختر الأيام المحددة للتكرار الأسبوعي، مع إمكانية تخصيص موعد ورابط اجتماع مستقل لكل يوم بشكل منفصل' : 'Select active recurring days and customize timings and meeting links per day independently')
                    ->columnSpanFull()
                    ->columns(1)
                    ->components([
                        CheckboxList::make('days_of_week')
                            ->label($isAr ? 'الأيام المحددة للتكرار الأسبوعي' : 'Weekly Recurring Days')
                            ->options([
                                6 => $isAr ? 'السبت' : 'Saturday',
                                0 => $isAr ? 'الأحد' : 'Sunday',
                                1 => $isAr ? 'الإثنين' : 'Monday',
                                2 => $isAr ? 'الثلاثاء' : 'Tuesday',
                                3 => $isAr ? 'الأربعاء' : 'Wednesday',
                                4 => $isAr ? 'الخميس' : 'Thursday',
                                5 => $isAr ? 'الجمعة' : 'Friday',
                            ])
                            ->columns([
                                'default' => 2,
                                'sm' => 4,
                                'lg' => 7,
                            ])
                            ->default([6, 0])
                            ->columnSpanFull()
                            ->live()
                            ->afterStateUpdated(fn ($state, $set, $get) => self::syncAutoEndDateAndQuota($set, $get))
                            ->extraAttributes(['class' => 'ec-recurring-days-picker'])
                            ->hintAction(
                                Action::make('syncMainSettings')
                                    ->label($isAr ? 'مزامنة الإعدادات الرئيسية مع كافة الأيام المحددة' : 'Sync Main Settings to All Days')
                                    ->icon('heroicon-o-arrow-path')
                                    ->color('primary')
                                    ->action(function ($get, $set) use ($isAr) {
                                        $mainTime = $get('start_time') ?: '10:00';
                                        $mainDuration = (int) ($get('duration_minutes') ?: 60);
                                        $mainLink = $get('meeting_link') ?: '';
                                        $selectedDays = $get('days_of_week') ?: [];
                                        $dayTimes = $get('day_start_times') ?: [];
                                        $dayDurations = $get('day_durations') ?: [];
                                        $dayLinks = $get('day_meeting_links') ?: [];
                                        foreach ($selectedDays as $d) {
                                            $dayTimes[(string) $d] = $mainTime;
                                            $dayDurations[(string) $d] = $mainDuration;
                                            if (! empty($mainLink)) {
                                                $dayLinks[(string) $d] = $mainLink;
                                            }
                                        }
                                        $set('day_start_times', $dayTimes);
                                        $set('day_durations', $dayDurations);
                                        $set('day_meeting_links', $dayLinks);
                                        Notification::make()
                                            ->title($isAr ? 'تمت مزامنة الإعدادات بنجاح' : 'Main settings synced')
                                            ->body($isAr ? 'تم تطبيق وقت البدء والمدة ورابط البث على جميع الأيام المحددة.' : 'Start time, duration, and link applied to all selected days.')
                                            ->success()
                                            ->send();
                                    })
                            ),

                        Grid::make([
                            'default' => 1,
                            'md' => 2,
                            'xl' => 3,
                        ])
                            ->columnSpanFull()
                            ->schema(
                                collect([
                                    6 => ['label' => $isAr ? 'يوم السبت' : 'Saturday'],
                                    0 => ['label' => $isAr ? 'يوم الأحد' : 'Sunday'],
                                    1 => ['label' => $isAr ? 'يوم الإثنين' : 'Monday'],
                                    2 => ['label' => $isAr ? 'يوم الثلاثاء' : 'Tuesday'],
                                    3 => ['label' => $isAr ? 'يوم الأربعاء' : 'Wednesday'],
                                    4 => ['label' => $isAr ? 'يوم الخميس' : 'Thursday'],
                                    5 => ['label' => $isAr ? 'يوم الجمعة' : 'Friday'],
                                ])->map(function ($info, $dayNum) use ($isAr) {
                                    return Section::make(new HtmlString('<i class="fa-solid fa-calendar-check text-teal-400 me-1.5"></i>' . $info['label']))
                                        ->compact()
                                        ->extraAttributes(['class' => 'ec-day-schedule-card'])
                                        ->visible(fn ($get) => in_array($dayNum, $get('days_of_week') ?? []))
                                        ->columns(2)
                                        ->schema([
                                            TimePicker::make("day_start_times.{$dayNum}")
                                                ->label($isAr ? 'وقت البدء' : 'Start Time')
                                                ->default('10:00')
                                                ->seconds(false)
                                                ->columnSpan(1),

                                            TextInput::make("day_durations.{$dayNum}")
                                                ->label($isAr ? 'المدة (بالدقائق)' : 'Duration (min)')
                                                ->numeric()
                                                ->default(60)
                                                ->suffix($isAr ? 'دقيقة' : 'min')
                                                ->columnSpan(1),

                                            TextInput::make("day_meeting_links.{$dayNum}")
                                                ->label($isAr ? 'رابط الاجتماع المخصص (اختياري)' : 'Custom Meeting Link (Optional)')
                                                ->placeholder('https://zoom.us/j/... or stream link')
                                                ->url()
                                                ->columnSpanFull(),
                                        ]);
                                })->values()->all()
                            ),
                    ]),
            ]);
    }

    /**
     * Real-time synchronizer for automatic end_date calculation and student package quota
     */
    public static function syncAutoEndDateAndQuota($set, $get): void
    {
        try {
            $service = app(\App\Services\Session\RecurringScheduleService::class);
            $res = $service->calculateEndDateAndQuota([
                'course_id' => $get('course_id'),
                'student_user_id' => $get('student_user_id'),
                'start_date' => $get('start_date') ?: now()->toDateString(),
                'recurrence_type' => $get('recurrence_type') ?: 'weekly',
                'days_of_week' => (array) ($get('days_of_week') ?? []),
            ]);

            if (! empty($res['end_date'])) {
                $set('end_date', $res['end_date']);
            }
        } catch (\Throwable $e) {
            // Graceful fallback
        }
    }
}
