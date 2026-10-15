<?php

namespace App\Filament\Resources\RecurringSchedules\Pages;

use App\Filament\Resources\RecurringSchedules\RecurringScheduleResource;
use App\Services\Session\RecurringScheduleService;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditRecurringSchedule extends EditRecord
{
    protected static string $resource = RecurringScheduleResource::class;

    public function getTitle(): string
    {
        return app()->getLocale() === 'ar' ? 'تعديل الجدول الأسبوعي المتكرر' : 'Edit Recurring Schedule';
    }

    protected function getHeaderActions(): array
    {
        $isAr = app()->getLocale() === 'ar';
        $record = $this->getRecord();
        $sessionsCount = $record->liveSessions()->count();
        $completedCount = $record->liveSessions()->where('status', 'completed')->count();

        return [
            \Filament\Actions\Action::make('recordPastAttendanceTop')
                ->label($isAr ? 'تسجيل حضور جلسة سابقة (خصم من الباقة)' : 'Record Past Attendance & Deduct')
                ->icon('heroicon-o-clipboard-document-check')
                ->color('success')
                ->modalHeading($isAr ? 'تسجيل حضور جلسة سابقة وخصم الحصة من باقة الطالب' : 'Record Past Session Attendance & Deduct')
                ->modalSubheading($isAr ? 'اختر الحصة المراد تسجيلها وتحديد تاريخ الحضور الفعلي وسيتم خصمها فوراً من رصيد باقة الطالب.' : 'Select the session, set the actual attendance date, and deduct from package.')
                ->modalWidth('7xl')
                ->modalSubmitActionLabel($isAr ? 'تأكيد الحضور وخصم الحصة الآن' : 'Confirm Attendance & Deduct')
                ->form(function () use ($record, $isAr) {
                    $targetStudentId = $record->student_user_id;
                    $studentUser = $targetStudentId ? \App\Models\User::find($targetStudentId) : null;
                    $course = $record->course;

                    $activePackage = $targetStudentId ? \App\Models\StudentPackage::where('student_user_id', $targetStudentId)
                        ->where(function ($q) use ($record) {
                            if ($record->course_id) {
                                $q->where('course_id', $record->course_id)
                                    ->orWhereNull('course_id');
                            }
                        })
                        ->where('status', 'active')
                        ->latest('created_at')
                        ->first() : null;

                    $sessionOptions = $record->liveSessions()
                        ->orderBy('scheduled_at', 'asc')
                        ->get()
                        ->mapWithKeys(function (\App\Models\LiveSession $s) use ($isAr) {
                            $dt = $s->scheduled_at ? \Carbon\Carbon::parse($s->scheduled_at)->format('Y-m-d h:i A') : 'N/A';
                            $day = $s->scheduled_at ? \Carbon\Carbon::parse($s->scheduled_at)->locale(app()->getLocale())->translatedFormat('l') : '';
                            $isPast = $s->scheduled_at && \Carbon\Carbon::parse($s->scheduled_at)->isPast();
                            $pastTag = $isPast ? ($isAr ? ' [⚠️ تاريخ سابق]' : ' [Past Date]') : '';
                            $statusTag = $s->status === 'completed' ? ($isAr ? ' (مكتملة ومسجلة)' : ' (Completed)') : '';
                            return [$s->id => "{$s->title} — {$day} {$dt} {$pastTag} {$statusTag}"];
                        });

                    return [
                        \Filament\Forms\Components\Placeholder::make('header_package_info')
                            ->hiddenLabel()
                            ->columnSpanFull()
                            ->content(function () use ($studentUser, $activePackage, $course, $isAr) {
                                $studentName = $studentUser?->name ?: ($isAr ? 'فصل جماعي' : 'Group Cohort');
                                $profile = $studentUser?->studentProfile;
                                $studentCode = $profile?->student_code ? "#{$profile->student_code}" : '—';
                                $packageName = $activePackage?->packageTemplate?->name ?: ($isAr ? 'باقة مخصصة' : 'Standard Package');
                                $remaining = $activePackage ? $activePackage->remaining_sessions : 0;
                                $total = $activePackage ? $activePackage->total_sessions : 0;
                                $used = $activePackage ? $activePackage->used_sessions : 0;

                                return new \Illuminate\Support\HtmlString('
                                    <div class="rec-attendance-hud-banner">
                                        <div class="rec-hud-grid">
                                            <div class="rec-hud-card">
                                                <div class="rec-hud-card-icon rec-card-teal-icon">
                                                    <i class="fa-solid fa-graduation-cap"></i>
                                                </div>
                                                <div class="rec-hud-card-body">
                                                    <div class="rec-hud-card-label">' . ($isAr ? 'الطالب المستهدف' : 'Student') . '</div>
                                                    <div class="rec-hud-card-title">' . e($studentName) . '</div>
                                                    <div class="rec-hud-card-sub text-teal-600 dark:text-teal-400 font-mono">' . e($studentCode) . '</div>
                                                </div>
                                            </div>
                                            <div class="rec-hud-card">
                                                <div class="rec-hud-card-icon rec-card-indigo-icon">
                                                    <i class="fa-solid fa-box-archive"></i>
                                                </div>
                                                <div class="rec-hud-card-body">
                                                    <div class="rec-hud-card-label">' . ($isAr ? 'باقة الحصص الحالية' : 'Active Package') . '</div>
                                                    <div class="rec-hud-card-title">' . ($activePackage ? e($packageName) : ($isAr ? 'لا توجد باقة نشطة' : 'No Package')) . '</div>
                                                    <div class="rec-hud-card-sub text-slate-500 dark:text-slate-400">' . e($course?->title ?: 'Course') . '</div>
                                                </div>
                                            </div>
                                            <div class="rec-hud-card">
                                                <div class="rec-hud-card-icon rec-card-emerald-icon">
                                                    <i class="fa-solid fa-bolt"></i>
                                                </div>
                                                <div class="rec-hud-card-body">
                                                    <div class="rec-hud-card-label">' . ($isAr ? 'الرصيد المتاح' : 'Available Balance') . '</div>
                                                    <div class="rec-hud-card-title text-emerald-600 dark:text-emerald-400 font-mono">' . $remaining . ' ' . ($isAr ? 'حصة متبقية' : 'left') . '</div>
                                                    <div class="rec-hud-card-sub text-slate-500 dark:text-slate-400">' . ($isAr ? "مستخدم: {$used} من {$total}" : "Used: {$used} / {$total}") . '</div>
                                                </div>
                                            </div>
                                            <div class="rec-hud-card">
                                                <div class="rec-hud-card-icon rec-card-amber-icon">
                                                    <i class="fa-solid fa-clock-rotate-left"></i>
                                                </div>
                                                <div class="rec-hud-card-body">
                                                    <div class="rec-hud-card-label">' . ($isAr ? 'تأثير الخصم' : 'Impact') . '</div>
                                                    <div class="rec-hud-card-title text-amber-600 dark:text-amber-400">' . ($isAr ? 'خصم 1 حصة فوري' : '-1 Session Credit') . '</div>
                                                    <div class="rec-hud-card-sub text-slate-500 dark:text-slate-400">' . ($isAr ? 'توثيق وتعديل فوري' : 'Instant Log Sync') . '</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                ');
                            }),

                        \Filament\Forms\Components\Select::make('live_session_id')
                            ->label(new \Illuminate\Support\HtmlString('<span class="flex items-center gap-1.5 font-bold text-sm"><i class="fa-solid fa-list-check text-teal-500"></i> ' . ($isAr ? 'اختر الحصة المراد تسجيل الحضور لها' : 'Target Session') . '</span>'))
                            ->options($sessionOptions)
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state) {
                                    $s = \App\Models\LiveSession::find($state);
                                    if ($s && $s->scheduled_at) {
                                        $set('attended_at', $s->scheduled_at->format('Y-m-d H:i:s'));
                                    }
                                }
                            })
                            ->columnSpanFull(),

                        \Filament\Forms\Components\Radio::make('attendance_status')
                            ->label(new \Illuminate\Support\HtmlString('<span class="flex items-center gap-1.5 font-bold text-sm"><i class="fa-solid fa-clipboard-user text-teal-500"></i> ' . ($isAr ? 'تأكيد حالة الحضور الفعلي والخصم' : 'Confirm Attendance Status & Deduction') . '</span>'))
                            ->options([
                                'present' => new \Illuminate\Support\HtmlString('<span class="rec-radio-opt"><i class="fa-solid fa-circle-check text-emerald-500"></i><strong>' . ($isAr ? 'حاضر' : 'Present') . '</strong> <span class="text-xs opacity-75">(' . ($isAr ? 'سيتم خصم حصة واحدة تلقائياً من باقة الطالب' : '1 session credit deducted') . ')</span></span>'),
                                'late' => new \Illuminate\Support\HtmlString('<span class="rec-radio-opt"><i class="fa-solid fa-clock text-amber-500"></i><strong>' . ($isAr ? 'حاضر متأخر' : 'Late') . '</strong> <span class="text-xs opacity-75">(' . ($isAr ? 'حضور متأخر مع خصم حصة واحدة من الباقة' : 'Late attendance with 1 deduction') . ')</span></span>'),
                                'excused' => new \Illuminate\Support\HtmlString('<span class="rec-radio-opt"><i class="fa-solid fa-shield-halved text-cyan-500"></i><strong>' . ($isAr ? 'معذور رسمي' : 'Excused') . '</strong> <span class="text-xs opacity-75">(' . ($isAr ? 'عذر رسمي معتمد — يُحفظ رصيد باقة الطالب دون خصم' : 'Official excuse — balance preserved') . ')</span></span>'),
                                'absent' => new \Illuminate\Support\HtmlString('<span class="rec-radio-opt"><i class="fa-solid fa-circle-xmark text-rose-500"></i><strong>' . ($isAr ? 'غائب عن الحصة' : 'Absent') . '</strong> <span class="text-xs opacity-75">(' . ($isAr ? 'تسجيل غياب الطالب' : 'Mark absent') . ')</span></span>'),
                            ])
                            ->default('present')
                            ->required()
                            ->columnSpanFull(),

                        \Filament\Forms\Components\DateTimePicker::make('attended_at')
                            ->label(new \Illuminate\Support\HtmlString('<span class="flex items-center gap-1.5 font-bold text-sm"><i class="fa-solid fa-calendar-day text-teal-500"></i> ' . ($isAr ? 'تاريخ وتوقيت الحضور الفعلي (يمكن تحديد تاريخ قديم)' : 'Actual Attendance Date & Time') . '</span>'))
                            ->default(now())
                            ->required()
                            ->helperText($isAr ? 'حدد التاريخ الدقيق الذي حضر فيه الطالب فعلياً ليتم توثيقه وخصم الحصة من الباقة.' : 'Set the exact past or present attendance date.')
                            ->columnSpanFull(),

                        \Filament\Forms\Components\Toggle::make('deduct_package')
                            ->label(new \Illuminate\Support\HtmlString('<span class="flex items-center gap-1.5 font-bold text-sm"><i class="fa-solid fa-receipt text-emerald-500"></i> ' . ($isAr ? 'خصم الحصة من باقة الطالب في النظام' : 'Deduct Session Credit from Student Package') . '</span>'))
                            ->default(true)
                            ->visible(fn($get) => in_array($get('attendance_status'), ['present', 'late'])),

                        \Filament\Forms\Components\Textarea::make('notes')
                            ->label(new \Illuminate\Support\HtmlString('<span class="flex items-center gap-1.5 font-bold text-sm"><i class="fa-solid fa-comment-dots text-indigo-500"></i> ' . ($isAr ? 'ملاحظات وتوثيق الحضور' : 'Notes / Justification') . '</span>'))
                            ->rows(2)
                            ->columnSpanFull(),
                    ];
                })
                ->action(function (array $data) use ($record, $isAr): void {
                    $sessionId = (int) $data['live_session_id'];
                    $session = \App\Models\LiveSession::findOrFail($sessionId);
                    $studentUserId = (int) ($session->student_user_id ?: $record->student_user_id);

                    if (!$studentUserId) {
                        \Filament\Notifications\Notification::make()
                            ->title($isAr ? 'تنبيه' : 'Notice')
                            ->body($isAr ? 'يرجى ربط الحصة بطالب أولاً لتطبيق الخصم.' : 'Session is not associated with a student.')
                            ->warning()
                            ->send();
                        return;
                    }

                    $attendanceStatus = $data['attendance_status'] ?? 'present';
                    $attendedAt = !empty($data['attended_at']) ? \Carbon\Carbon::parse($data['attended_at']) : ($session->scheduled_at ?: now());
                    $deductPackage = (bool) ($data['deduct_package'] ?? true);
                    $notes = $data['notes'] ?? null;

                    $service = app(\App\Services\Session\SessionAttendanceDeductionService::class);
                    $res = $service->recordAttendanceAndDeduct(
                        $session,
                        $studentUserId,
                        $attendanceStatus,
                        $attendedAt,
                        $notes,
                        auth()->user(),
                        $deductPackage
                    );

                    if ($res['success']) {
                        \Filament\Notifications\Notification::make()
                            ->title($isAr ? 'تم تسجيل الحضور وتحديث باقة الطالب بنجاح' : 'Attendance Updated')
                            ->body($res['message'])
                            ->success()
                            ->send();
                    } else {
                        \Filament\Notifications\Notification::make()
                            ->title($isAr ? 'تنبيه' : 'Notice')
                            ->body($res['message'])
                            ->warning()
                            ->send();
                    }
                }),

            \Filament\Actions\Action::make('renewStudentPackageTop')
                ->label($isAr ? 'تجديد / تفعيل باقة الطالب' : 'Renew / Provision Package')
                ->icon('heroicon-o-sparkles')
                ->color('warning')
                ->visible(fn () => ! empty($record->student_user_id))
                ->modalHeading($isAr ? 'تجديد أو شحن باقة الطالب فوراً' : 'Renew or Top-up Student Package')
                ->modalSubheading($isAr ? 'يمكنك تجديد باقة الطالب الحالية أو إنشاء باقة جديدة وتعيين الرصيد فوراً لهذا المقرر.' : 'Instantly renew or top-up package for this student and course.')
                ->modalWidth('2xl')
                ->modalSubmitActionLabel($isAr ? 'تأكيد التجديد وتحديث الرصيد الآن' : 'Confirm Renewal Now')
                ->form(function () use ($record, $isAr) {
                    $studentId = $record->student_user_id;
                    $studentUser = $studentId ? \App\Models\User::find($studentId) : null;
                    $course = $record->course;

                    $service = app(\App\Services\Session\RecurringScheduleService::class);
                    $calc = $service->calculateEndDateAndQuota([
                        'course_id' => $record->course_id,
                        'student_user_id' => $studentId,
                        'start_date' => $record->start_date ?: now()->toDateString(),
                        'recurrence_type' => $record->recurrence_type ?: 'weekly',
                        'days_of_week' => (array) ($record->days_of_week ?? []),
                    ]);
                    $minRequired = max(1, (int) ($calc['target_sessions'] ?? 1));

                    $existingPackage = $studentId ? \App\Models\StudentPackage::where('student_user_id', $studentId)
                        ->where(function ($q) use ($record) {
                            if ($record->course_id) {
                                $q->where('course_id', $record->course_id)->orWhereNull('course_id');
                            }
                        })
                        ->orderByRaw('course_id IS NOT NULL DESC')
                        ->latest('created_at')
                        ->first() : null;

                    return [
                        \Filament\Forms\Components\Placeholder::make('package_status_overview')
                            ->hiddenLabel()
                            ->columnSpanFull()
                            ->content(function () use ($studentUser, $course, $existingPackage, $minRequired, $isAr) {
                                $studentName = $studentUser?->name ?: "Student #{$studentUser?->id}";
                                $packageName = $existingPackage?->packageTemplate?->name ?: ($existingPackage ? ($isAr ? 'باقة مخصصة' : 'Custom Package') : ($isAr ? 'لا توجد باقة نشطة' : 'No Package'));
                                $currentRemaining = $existingPackage ? $existingPackage->remaining_sessions : 0;
                                $statusBadge = $existingPackage
                                    ? ($existingPackage->status === 'active' ? '🟢 ' . ($isAr ? 'نشطة' : 'Active') : '🔴 ' . ($isAr ? 'منتهية/مستنفذة' : 'Exhausted'))
                                    : '⚠️ ' . ($isAr ? 'غير موجودة' : 'Not Found');

                                return new \Illuminate\Support\HtmlString('
                                    <div class="rec-attendance-hud-banner">
                                        <div class="rec-hud-grid">
                                            <div class="rec-hud-card">
                                                <div class="rec-hud-card-icon rec-card-teal-icon">
                                                    <i class="fa-solid fa-graduation-cap"></i>
                                                </div>
                                                <div class="rec-hud-card-body">
                                                    <div class="rec-hud-card-label">' . ($isAr ? 'الطالب' : 'Student') . '</div>
                                                    <div class="rec-hud-card-title">' . e($studentName) . '</div>
                                                    <div class="rec-hud-card-sub text-teal-600 dark:text-teal-400 font-mono">' . e($course?->title ?: 'Course') . '</div>
                                                </div>
                                            </div>
                                            <div class="rec-hud-card">
                                                <div class="rec-hud-card-icon rec-card-indigo-icon">
                                                    <i class="fa-solid fa-box-archive"></i>
                                                </div>
                                                <div class="rec-hud-card-body">
                                                    <div class="rec-hud-card-label">' . ($isAr ? 'الباقة الحالية' : 'Current Package') . '</div>
                                                    <div class="rec-hud-card-title">' . e($packageName) . '</div>
                                                    <div class="rec-hud-card-sub">' . $statusBadge . '</div>
                                                </div>
                                            </div>
                                            <div class="rec-hud-card">
                                                <div class="rec-hud-card-icon rec-card-emerald-icon">
                                                    <i class="fa-solid fa-layer-group"></i>
                                                </div>
                                                <div class="rec-hud-card-body">
                                                    <div class="rec-hud-card-label">' . ($isAr ? 'الرصيد المتاح' : 'Remaining') . '</div>
                                                    <div class="rec-hud-card-title font-mono text-emerald-600 dark:text-emerald-400">' . $currentRemaining . ' ' . ($isAr ? 'حصة' : 'credits') . '</div>
                                                    <div class="rec-hud-card-sub">' . ($isAr ? "مطلوب: {$minRequired} حصة" : "Req: {$minRequired}") . '</div>
                                                </div>
                                            </div>
                                            <div class="rec-hud-card">
                                                <div class="rec-hud-card-icon rec-card-amber-icon">
                                                    <i class="fa-solid fa-lock"></i>
                                                </div>
                                                <div class="rec-hud-card-body">
                                                    <div class="rec-hud-card-label">' . ($isAr ? 'الحد الأدنى المطلوب' : 'Min Required') . '</div>
                                                    <div class="rec-hud-card-title text-amber-600 dark:text-amber-400 font-mono">' . $minRequired . ' ' . ($isAr ? 'حصة' : 'sessions') . '</div>
                                                    <div class="rec-hud-card-sub text-xs">' . ($isAr ? 'لا يمكن التجديد بأقل منه' : 'Enforced limit') . '</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                ');
                            }),

                        \Filament\Forms\Components\Radio::make('action_type')
                            ->label(new \Illuminate\Support\HtmlString('<span class="flex items-center gap-1.5 font-bold text-sm"><i class="fa-solid fa-gear text-teal-500"></i> ' . ($isAr ? 'نوع إجراء التجديد' : 'Action Type') . '</span>'))
                            ->options([
                                'auto' => new \Illuminate\Support\HtmlString('<span class="rec-radio-opt"><i class="fa-solid fa-wand-magic-sparkles text-purple-500"></i><strong>' . ($isAr ? 'تلقائي' : 'Auto') . '</strong> <span class="text-xs opacity-75">(' . ($isAr ? 'تجديد الباقة الحالية إن وجدت أو إنشاء باقة جديدة' : 'Renew existing or create new') . ')</span></span>'),
                                'renew' => new \Illuminate\Support\HtmlString('<span class="rec-radio-opt"><i class="fa-solid fa-rotate text-emerald-500"></i><strong>' . ($isAr ? 'تجديد الباقة الحالية' : 'Renew Package') . '</strong> <span class="text-xs opacity-75">(' . ($isAr ? 'إعادة ضبط الرصيد وتنشيط الباقة' : 'Reset balance & activate') . ')</span></span>'),
                                'top_up' => new \Illuminate\Support\HtmlString('<span class="rec-radio-opt"><i class="fa-solid fa-plus-circle text-cyan-500"></i><strong>' . ($isAr ? 'شحن رصيد إضافي (Top-up)' : 'Top-up') . '</strong> <span class="text-xs opacity-75">(' . ($isAr ? 'إضافة حصص فوق الرصيد الحالي' : 'Add to existing balance') . ')</span></span>'),
                                'create_new' => new \Illuminate\Support\HtmlString('<span class="rec-radio-opt"><i class="fa-solid fa-square-plus text-amber-500"></i><strong>' . ($isAr ? 'إنشاء باقة جديدة منفصلة' : 'Create New') . '</strong> <span class="text-xs opacity-75">(' . ($isAr ? 'بدء باقة جديدة للطالب' : 'Start separate new package') . ')</span></span>'),
                            ])
                            ->default('auto')
                            ->required()
                            ->columnSpanFull(),

                        \Filament\Forms\Components\Select::make('package_template_id')
                            ->label(new \Illuminate\Support\HtmlString('<span class="flex items-center gap-1.5 font-bold text-sm"><i class="fa-solid fa-box text-teal-500"></i> ' . ($isAr ? 'نموذج الباقة (اختياري)' : 'Package Template (Optional)') . '</span>'))
                            ->options(\App\Models\PackageTemplate::where('is_active', true)->pluck('name', 'id'))
                            ->searchable()
                            ->nullable()
                            ->live()
                            ->afterStateUpdated(function ($state, $set) use ($minRequired) {
                                if ($state) {
                                    $tmpl = \App\Models\PackageTemplate::find($state);
                                    if ($tmpl && $tmpl->sessions_count) {
                                        $set('sessions_count', max($tmpl->sessions_count, $minRequired));
                                    }
                                }
                            })
                            ->columnSpanFull(),

                        \Filament\Forms\Components\TextInput::make('sessions_count')
                            ->label(new \Illuminate\Support\HtmlString('<span class="flex items-center gap-1.5 font-bold text-sm"><i class="fa-solid fa-list-ol text-teal-500"></i> ' . ($isAr ? 'عدد الحصص (لا يمكن أن يقل عن حصص المقرر)' : 'Package Sessions (Min. Required)') . '</span>'))
                            ->numeric()
                            ->required()
                            ->default($minRequired)
                            ->minValue($minRequired)
                            ->helperText($isAr
                                ? "الحد الأدنى لحصص هذا المقرر هو {$minRequired} حصة. لا يمكن تحديد عدد أقل."
                                : "Minimum required for this course is {$minRequired} sessions. Cannot be less.")
                            ->columnSpanFull(),

                        \Filament\Forms\Components\DatePicker::make('expires_at')
                            ->label(new \Illuminate\Support\HtmlString('<span class="flex items-center gap-1.5 font-bold text-sm"><i class="fa-solid fa-calendar text-teal-500"></i> ' . ($isAr ? 'تاريخ انتهاء صلاحية الباقة' : 'Expiry Date') . '</span>'))
                            ->default(now()->addMonths(3))
                            ->columnSpanFull(),
                    ];
                })
                ->action(function (array $data) use ($record, $isAr): void {
                    $service = app(\App\Services\Session\RecurringScheduleService::class);
                    $package = $service->provisionOrRenewStudentPackage([
                        'student_user_id' => $record->student_user_id,
                        'course_id' => $record->course_id,
                        'sessions_count' => (int) $data['sessions_count'],
                        'package_template_id' => $data['package_template_id'] ?? null,
                        'expires_at' => $data['expires_at'] ?? null,
                        'action_type' => $data['action_type'] ?? 'auto',
                        'reason' => 'Package Renewed/Provisioned from Recurring Schedule Edit Page',
                        'start_date' => $record->start_date ?: now()->toDateString(),
                        'recurrence_type' => $record->recurrence_type ?: 'weekly',
                        'days_of_week' => (array) ($record->days_of_week ?? []),
                    ], auth()->user());

                    \Filament\Notifications\Notification::make()
                        ->title($isAr ? 'تم تجديد / تفعيل باقة الطالب بنجاح' : 'Package Renewed Successfully')
                        ->body($isAr
                            ? "أصبح رصيد الطالب الآن {$package->remaining_sessions} حصة متاحة، وأصبحت الباقة صالحة للجدولة والخصم."
                            : "Student now has {$package->remaining_sessions} sessions available.")
                        ->success()
                        ->send();
                }),

            DeleteAction::make(),
        ];
    }


    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        app(RecurringScheduleService::class)->updateEntireSchedule($record, $data, auth()->user());
        return $record->fresh();
    }
}
