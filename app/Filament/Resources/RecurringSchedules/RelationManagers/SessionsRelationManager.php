<?php

namespace App\Filament\Resources\RecurringSchedules\RelationManagers;

use App\Models\LiveSession;
use App\Models\StudentPackage;
use App\Models\User;
use App\Services\Session\RecurringScheduleService;
use App\Services\Session\SessionAttendanceDeductionService;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class SessionsRelationManager extends RelationManager
{
    protected static string $relationship = 'sessions';

    protected static string|BackedEnum|null $icon = 'heroicon-o-calendar-days';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        $isAr = app()->getLocale() === 'ar';
        return $isAr ? '📅 جدول وسجل الحصص والحضور (الحصص المجدولة والسابقة والمكتملة)' : 'Cohort Sessions & Attendance Register';
    }

    public function form(Schema $schema): Schema
    {
        $isAr = app()->getLocale() === 'ar';

        return $schema
            ->components([
                TextInput::make('title')
                    ->label($isAr ? 'عنوان الحصة' : 'Session Title')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(2),

                DateTimePicker::make('scheduled_at')
                    ->label($isAr ? 'موعد الحصة' : 'Scheduled Date & Time')
                    ->required()
                    ->columnSpan(1),

                TextInput::make('duration_minutes')
                    ->label($isAr ? 'المدة (بالدقائق)' : 'Duration (Minutes)')
                    ->numeric()
                    ->default(60)
                    ->suffix($isAr ? 'دقيقة' : 'min')
                    ->required()
                    ->columnSpan(1),

                Select::make('meeting_platform')
                    ->label($isAr ? 'منصة الاجتماع' : 'Meeting Platform')
                    ->options([
                        'agora' => 'Agora Classroom',
                        'google_meet' => 'Google Meet',
                        'zoom' => 'Zoom Meeting',
                        'microsoft_teams' => 'Microsoft Teams',
                        'other' => 'Other / Custom Link',
                    ])
                    ->default('agora')
                    ->required()
                    ->columnSpan(1),

                TextInput::make('meeting_link')
                    ->label($isAr ? 'رابط البث المباشر' : 'Meeting Stream Link')
                    ->url()
                    ->placeholder('https://zoom.us/j/... or stream link')
                    ->columnSpan(1),

                Select::make('status')
                    ->label($isAr ? 'حالة الحصة' : 'Session Status')
                    ->options([
                        'scheduled' => $isAr ? 'مجدولة' : 'Scheduled',
                        'link_visible' => $isAr ? 'الرابط متاح' : 'Link Visible',
                        'in_progress' => $isAr ? 'جارية الآن' : 'In Progress',
                        'completed' => $isAr ? 'مكتملة ومسجلة' : 'Completed',
                        'cancelled' => $isAr ? 'ملغاة' : 'Cancelled',
                        'rescheduled' => $isAr ? 'مؤجلة / معدلة' : 'Rescheduled',
                    ])
                    ->default('scheduled')
                    ->required()
                    ->columnSpan(1),

                Select::make('attendance_status')
                    ->label($isAr ? 'حالة الحضور' : 'Attendance Status')
                    ->options([
                        'present' => $isAr ? 'حاضر (تم الخصم)' : 'Present (Deducted)',
                        'late' => $isAr ? 'حاضر متأخر (تم الخصم)' : 'Late (Deducted)',
                        'excused' => $isAr ? 'معذور (غير مخصوم)' : 'Excused (No Deduction)',
                        'absent' => $isAr ? 'غائب' : 'Absent',
                    ])
                    ->nullable()
                    ->columnSpan(1),

                Textarea::make('teacher_notes')
                    ->label($isAr ? 'ملاحظات المعلم / الإدارة' : 'Teacher / Admin Notes')
                    ->columnSpan(2),
            ]);
    }

    public function table(Table $table): Table
    {
        $isAr = app()->getLocale() === 'ar';
        $recurringSchedule = $this->getOwnerRecord();

        return $table
            ->recordTitleAttribute('title')
            ->defaultSort('scheduled_at', 'asc')
            ->columns([
                TextColumn::make('sequence_idx')
                    ->label('#')
                    ->rowIndex()
                    ->alignCenter()
                    ->extraCellAttributes(['style' => 'width: 48px; font-weight: 800; font-family: monospace;']),

                TextColumn::make('title')
                    ->label($isAr ? 'عنوان الحصة' : 'Session Title')
                    ->weight('bold')
                    ->searchable()
                    ->wrap()
                    ->description(function (LiveSession $record) use ($isAr) {
                        $diff = $record->scheduled_at ? Carbon::parse($record->scheduled_at)->diffForHumans() : '';
                        $isPast = $record->scheduled_at && Carbon::parse($record->scheduled_at)->isPast();
                        if ($record->status === 'completed') {
                            return ($isAr ? 'تمت الحصة بنجاح' : 'Completed');
                        }
                        if ($isPast) {
                            return ($isAr ? "موعد سابق ({$diff}) — بانتظار التحضير" : "Past due ({$diff}) — Attendance Needed");
                        }
                        return ($isAr ? "موعد قادم ({$diff})" : "Upcoming ({$diff})");
                    }),

                TextColumn::make('scheduled_at')
                    ->label($isAr ? 'تاريخ ووقت الحصة' : 'Scheduled Date & Time')
                    ->dateTime('Y-m-d h:i A')
                    ->sortable()
                    ->description(fn (LiveSession $record): string => $record->scheduled_at ? Carbon::parse($record->scheduled_at)->locale(app()->getLocale())->translatedFormat('l') : '')
                    ->extraCellAttributes(function (LiveSession $record) {
                        $isPast = $record->scheduled_at && Carbon::parse($record->scheduled_at)->isPast();
                        if ($isPast && $record->status !== 'completed') {
                            return [
                                'style' => 'font-weight: 700; color: #f59e0b;',
                            ];
                        }
                        return ['style' => 'font-weight: 600;'];
                    }),

                TextColumn::make('student.name')
                    ->label($isAr ? 'الطالب' : 'Student')
                    ->placeholder(function () use ($recurringSchedule, $isAr) {
                        return $recurringSchedule->studentUser?->name ?: ($isAr ? 'فصل جماعي (جميع طلاب المقرر)' : 'Course Cohort Group');
                    })
                    ->description(function (LiveSession $record) use ($recurringSchedule) {
                        $stu = $record->studentUser ?: $recurringSchedule->studentUser;
                        if (! $stu) return null;
                        $profile = $stu->studentProfile;
                        return $profile?->student_code ? "#{$profile->student_code}" : $stu->email;
                    })
                    ->searchable(),

                TextColumn::make('duration_minutes')
                    ->label($isAr ? 'المدة' : 'Duration')
                    ->suffix(' ' . ($isAr ? 'دقيقة' : 'min'))
                    ->alignCenter()
                    ->toggleable(),

                TextColumn::make('meeting_platform')
                    ->label($isAr ? 'المنصة' : 'Platform')
                    ->badge()
                    ->colors([
                        'primary' => 'agora',
                        'info' => 'zoom',
                        'success' => 'google_meet',
                        'warning' => 'microsoft_teams',
                        'gray' => 'other',
                    ])
                    ->toggleable(),

                TextColumn::make('status')
                    ->label($isAr ? 'حالة الجلسة' : 'Session Status')
                    ->badge()
                    ->colors([
                        'info' => 'scheduled',
                        'warning' => fn ($state) => in_array($state, ['link_visible', 'rescheduled']),
                        'success' => fn ($state) => in_array($state, ['in_progress', 'completed']),
                        'danger' => fn ($state) => in_array($state, ['cancelled', 'cancelled_by_teacher']),
                    ])
                    ->formatStateUsing(function ($state) use ($isAr) {
                        return match ($state) {
                            'scheduled' => $isAr ? 'مجدولة' : 'Scheduled',
                            'link_visible' => $isAr ? 'الرابط متاح' : 'Link Visible',
                            'in_progress' => $isAr ? 'جارية' : 'In Progress',
                            'completed' => $isAr ? 'مكتملة' : 'Completed',
                            'cancelled', 'cancelled_by_teacher' => $isAr ? 'ملغاة' : 'Cancelled',
                            'rescheduled' => $isAr ? 'مؤجلة' : 'Rescheduled',
                            default => $state,
                        };
                    })
                    ->sortable(),

                TextColumn::make('attendance_badge')
                    ->label($isAr ? 'حالة الحضور والخصم' : 'Attendance & Deduction')
                    ->html()
                    ->state(function (LiveSession $record) use ($isAr) {
                        $att = $record->attendance_status;
                        $isPast = $record->scheduled_at && Carbon::parse($record->scheduled_at)->isPast();

                        if ($att === 'present') {
                            return new HtmlString('<span class="rec-badge rec-badge-success"><i class="fa-solid fa-circle-check"></i> ' . ($isAr ? 'حاضر (تم الخصم)' : 'Present (Deducted)') . '</span>');
                        }
                        if ($att === 'late') {
                            return new HtmlString('<span class="rec-badge rec-badge-warning"><i class="fa-solid fa-clock"></i> ' . ($isAr ? 'متأخر (تم الخصم)' : 'Late (Deducted)') . '</span>');
                        }
                        if ($att === 'excused') {
                            return new HtmlString('<span class="rec-badge rec-badge-info"><i class="fa-solid fa-shield-halved"></i> ' . ($isAr ? 'معذور (محفوظ)' : 'Excused (Preserved)') . '</span>');
                        }
                        if ($att === 'absent') {
                            return new HtmlString('<span class="rec-badge rec-badge-danger"><i class="fa-solid fa-circle-xmark"></i> ' . ($isAr ? 'غائب' : 'Absent') . '</span>');
                        }

                        if ($record->status === 'completed') {
                            return new HtmlString('<span class="rec-badge rec-badge-success"><i class="fa-solid fa-check-double"></i> ' . ($isAr ? 'تمت الحصة' : 'Completed') . '</span>');
                        }

                        if ($isPast) {
                            return new HtmlString('<span class="rec-badge rec-badge-amber-pulse"><i class="fa-solid fa-triangle-exclamation"></i> ' . ($isAr ? 'تاريخ سابق (تحضير مطلوب)' : 'Past Date (Needs Attendance)') . '</span>');
                        }

                        return new HtmlString('<span class="rec-badge rec-badge-neutral"><i class="fa-solid fa-hourglass-start"></i> ' . ($isAr ? 'بانتظار الموعد' : 'Pending Date') . '</span>');
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label($isAr ? 'حالة الجلسة' : 'Status Filter')
                    ->options([
                        'scheduled' => $isAr ? 'مجدولة' : 'Scheduled',
                        'completed' => $isAr ? 'مكتملة' : 'Completed',
                        'in_progress' => $isAr ? 'جارية الآن' : 'In Progress',
                        'cancelled' => $isAr ? 'ملغاة' : 'Cancelled',
                        'rescheduled' => $isAr ? 'مؤجلة' : 'Rescheduled',
                    ]),

                SelectFilter::make('attendance_status')
                    ->label($isAr ? 'حالة الحضور' : 'Attendance Filter')
                    ->options([
                        'present' => $isAr ? 'حاضر (مخصوم)' : 'Present',
                        'late' => $isAr ? 'متأخر (مخصوم)' : 'Late',
                        'excused' => $isAr ? 'معذور' : 'Excused',
                        'absent' => $isAr ? 'غائب' : 'Absent',
                    ]),
            ])
            ->headerActions([
                // ── 1. PROMINENT ATTENDANCE ACTION FOR ANY PAST / NEW SESSION ─────────
                Action::make('recordPastAttendance')
                    ->label($isAr ? 'تسجيل حضور جلسة سابقة (خصم من باقة الطالب)' : 'Record Attendance for Past Session & Deduct')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('success')
                    ->button()
                    ->modalHeading($isAr ? 'تسجيل حضور جلسة سابقة وخصم الحصة من باقة الطالب' : 'Record Past Session Attendance & Deduct Package Balance')
                    ->modalSubheading($isAr ? 'يمكنك اختيار أي حصة من حصص هذا الجدول الأسبوعي (خاصة التواريخ السابقة) وتسجيل الحضور الفعلي وتطبيق الخصم فوراً من رصيد باقة الطالب.' : 'Select any session from this schedule (especially past dates), record attendance, and immediately deduct from student package balance.')
                    ->modalWidth('7xl')
                    ->modalSubmitActionLabel($isAr ? 'تأكيد الحضور وخصم الحصة من الباقة' : 'Confirm Attendance & Deduct')
                    ->form(function () use ($recurringSchedule, $isAr) {
                        $targetStudentId = $recurringSchedule->student_user_id;
                        $studentUser = $targetStudentId ? User::find($targetStudentId) : null;
                        $course = $recurringSchedule->course;

                        $activePackage = $targetStudentId ? StudentPackage::where('student_user_id', $targetStudentId)
                            ->where(function ($q) use ($recurringSchedule) {
                                if ($recurringSchedule->course_id) {
                                    $q->where('course_id', $recurringSchedule->course_id)
                                      ->orWhereNull('course_id');
                                }
                            })
                            ->where('status', 'active')
                            ->latest('created_at')
                            ->first() : null;

                        // Sessions options
                        $sessionOptions = $recurringSchedule->liveSessions()
                            ->orderBy('scheduled_at', 'asc')
                            ->get()
                            ->mapWithKeys(function (LiveSession $s) use ($isAr) {
                                $dt = $s->scheduled_at ? Carbon::parse($s->scheduled_at)->format('Y-m-d h:i A') : 'N/A';
                                $day = $s->scheduled_at ? Carbon::parse($s->scheduled_at)->locale(app()->getLocale())->translatedFormat('l') : '';
                                $isPast = $s->scheduled_at && Carbon::parse($s->scheduled_at)->isPast();
                                $pastTag = $isPast ? ($isAr ? ' [تاريخ سابق]' : ' [Past Date]') : '';
                                $statusTag = $s->status === 'completed' ? ($isAr ? ' (مكتملة ومسجلة)' : ' (Completed)') : '';
                                return [$s->id => "{$s->title} — {$day} {$dt} {$pastTag} {$statusTag}"];
                            });

                        return [
                            Placeholder::make('student_package_info')
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

                                    return new HtmlString('
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

                            Select::make('live_session_id')
                                ->label(new HtmlString('<span class="flex items-center gap-1.5 font-bold text-sm"><i class="fa-solid fa-list-check text-teal-500"></i> ' . ($isAr ? 'اختر الحصة المراد تسجيل الحضور لها' : 'Target Session') . '</span>'))
                                ->options($sessionOptions)
                                ->searchable()
                                ->preload()
                                ->required()
                                ->live()
                                ->afterStateUpdated(function ($state, $set) {
                                    if ($state) {
                                        $s = LiveSession::find($state);
                                        if ($s && $s->scheduled_at) {
                                            $set('attended_at', $s->scheduled_at->format('Y-m-d H:i:s'));
                                        }
                                    }
                                })
                                ->columnSpanFull(),

                            Radio::make('attendance_status')
                                ->label(new HtmlString('<span class="flex items-center gap-1.5 font-bold text-sm"><i class="fa-solid fa-clipboard-user text-teal-500"></i> ' . ($isAr ? 'تأكيد حالة الحضور الفعلي والخصم' : 'Confirm Attendance Status & Deduction') . '</span>'))
                                ->options([
                                    'present' => new HtmlString('<span class="rec-radio-opt"><i class="fa-solid fa-circle-check text-emerald-500"></i><strong>' . ($isAr ? 'حاضر' : 'Present') . '</strong> <span class="text-xs opacity-75">(' . ($isAr ? 'سيتم خصم حصة واحدة تلقائياً من باقة الطالب' : '1 session credit deducted') . ')</span></span>'),
                                    'late' => new HtmlString('<span class="rec-radio-opt"><i class="fa-solid fa-clock text-amber-500"></i><strong>' . ($isAr ? 'حاضر متأخر' : 'Late') . '</strong> <span class="text-xs opacity-75">(' . ($isAr ? 'حضور متأخر مع خصم حصة واحدة من الباقة' : 'Late attendance with 1 deduction') . ')</span></span>'),
                                    'excused' => new HtmlString('<span class="rec-radio-opt"><i class="fa-solid fa-shield-halved text-cyan-500"></i><strong>' . ($isAr ? 'معذور رسمي' : 'Excused') . '</strong> <span class="text-xs opacity-75">(' . ($isAr ? 'عذر رسمي معتمد — يُحفظ رصيد باقة الطالب دون خصم' : 'Official excuse — balance preserved') . ')</span></span>'),
                                    'absent' => new HtmlString('<span class="rec-radio-opt"><i class="fa-solid fa-circle-xmark text-rose-500"></i><strong>' . ($isAr ? 'غائب عن الحصة' : 'Absent') . '</strong> <span class="text-xs opacity-75">(' . ($isAr ? 'تسجيل غياب الطالب' : 'Mark absent') . ')</span></span>'),
                                ])
                                ->default('present')
                                ->required()
                                ->columnSpanFull(),

                            DateTimePicker::make('attended_at')
                                ->label(new HtmlString('<span class="flex items-center gap-1.5 font-bold text-sm"><i class="fa-solid fa-calendar-day text-teal-500"></i> ' . ($isAr ? 'تاريخ وتوقيت الحضور الفعلي (يمكن تحديد تاريخ قديم)' : 'Actual Attendance Date & Time') . '</span>'))
                                ->default(now())
                                ->required()
                                ->helperText($isAr ? 'حدد التاريخ والوقت الذي حضر فيه الطالب فعلياً. سيتم تسجيله في سجلات الحضور وخصم الحصة من الباقة بشكل موثق.' : 'Specify the exact date the student attended. This will be stamped in attendance logs and deduct from the package.')
                                ->columnSpanFull(),

                            Toggle::make('deduct_package')
                                ->label(new HtmlString('<span class="flex items-center gap-1.5 font-bold text-sm"><i class="fa-solid fa-receipt text-teal-500"></i> ' . ($isAr ? 'تفعيل الخصم من باقة الطالب' : 'Deduct Session Credit from Student Package') . '</span>'))
                                ->default(true)
                                ->helperText($isAr ? 'عند التفعيل، يتم خصم 1 حصة وتحديث رصيد الباقة المتبقي فوراً.' : 'Deducts 1 session credit from active package upon confirmation.')
                                ->visible(fn ($get) => in_array($get('attendance_status'), ['present', 'late'])),

                            Textarea::make('notes')
                                ->label(new HtmlString('<span class="flex items-center gap-1.5 font-bold text-sm"><i class="fa-solid fa-pen-to-square text-teal-500"></i> ' . ($isAr ? 'ملاحظات وتوثيق الحضور' : 'Attendance Notes / Justification') . '</span>'))
                                ->placeholder($isAr ? 'اكتب أي ملاحظات أو توثيق للحصة السابقة (اختياري)...' : 'Optional notes regarding past session attendance...')
                                ->rows(2)
                                ->columnSpanFull(),
                        ];
                    })
                    ->action(function (array $data) use ($recurringSchedule, $isAr): void {
                        $sessionId = (int) $data['live_session_id'];
                        $session = LiveSession::findOrFail($sessionId);
                        $studentUserId = (int) ($session->student_user_id ?: $recurringSchedule->student_user_id);

                        if (! $studentUserId) {
                            Notification::make()
                                ->title($isAr ? 'تعذر تسجيل الحضور الفردي' : 'Student not assigned')
                                ->body($isAr ? 'هذا الجدول ليس مخصصاً لطالب فردي محدد. يرجى اختيار حصة مرتبطة بطالب.' : 'This session is not linked to an individual student.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $attendanceStatus = $data['attendance_status'] ?? 'present';
                        $attendedAt = ! empty($data['attended_at']) ? Carbon::parse($data['attended_at']) : ($session->scheduled_at ?: now());
                        $deductPackage = (bool) ($data['deduct_package'] ?? true);
                        $notes = $data['notes'] ?? null;

                        $service = app(SessionAttendanceDeductionService::class);
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
                            Notification::make()
                                ->title($isAr ? 'تم تسجيل الحضور وتحديث الباقة بنجاح' : 'Attendance Recorded Successfully')
                                ->body($res['message'])
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title($isAr ? 'تنبيه' : 'Notice')
                                ->body($res['message'])
                                ->warning()
                                ->send();
                        }
                    }),

                // ── 2. ADD EXTRA MANUAL SESSION ───────────────────────────────
                Action::make('addExtraSession')
                    ->label($isAr ? 'إضافة حصة استثنائية للجدول' : 'Add Single Extra Session')
                    ->icon('heroicon-o-plus')
                    ->color('primary')
                    ->modalHeading($isAr ? 'إضافة حصة جديدة لهذا الجدول الأسبوعي' : 'Add Extra Live Session to this Cohort')
                    ->modalWidth('lg')
                    ->form([
                        TextInput::make('title')
                            ->label($isAr ? 'عنوان الحصة' : 'Session Title')
                            ->default(fn () => $recurringSchedule->title . ' (Extra)')
                            ->required(),

                        DateTimePicker::make('scheduled_at')
                            ->label($isAr ? 'تاريخ ووقت الحصة' : 'Date & Time')
                            ->default(now())
                            ->required(),

                        TextInput::make('duration_minutes')
                            ->label($isAr ? 'المدة (بالدقائق)' : 'Duration (min)')
                            ->numeric()
                            ->default((int) ($recurringSchedule->duration_minutes ?: 60))
                            ->required(),

                        TextInput::make('meeting_link')
                            ->label($isAr ? 'رابط الاجتماع' : 'Meeting Link')
                            ->url()
                            ->default($recurringSchedule->meeting_link),
                    ])
                    ->action(function (array $data) use ($recurringSchedule, $isAr): void {
                        $start = Carbon::parse($data['scheduled_at']);
                        $duration = (int) ($data['duration_minutes'] ?: 60);
                        $end = $start->copy()->addMinutes($duration);

                        $newSession = LiveSession::create([
                            'title' => $data['title'],
                            'student_user_id' => $recurringSchedule->student_user_id,
                            'teacher_profile_id' => $recurringSchedule->teacher_profile_id,
                            'course_id' => $recurringSchedule->course_id,
                            'subject_id' => $recurringSchedule->course?->subject_id,
                            'recurring_schedule_id' => $recurringSchedule->id,
                            'scheduled_at' => $start,
                            'start_at' => $start,
                            'end_at' => $end,
                            'duration_minutes' => $duration,
                            'meeting_link' => $data['meeting_link'] ?: $recurringSchedule->meeting_link,
                            'meeting_platform' => $recurringSchedule->meeting_platform ?: 'agora',
                            'status' => 'scheduled',
                            'lifecycle_state' => 'scheduled',
                            'is_override' => true,
                        ]);

                        if ($recurringSchedule->student_user_id) {
                            \App\Models\StudentSession::create([
                                'student_user_id' => $recurringSchedule->student_user_id,
                                'live_session_id' => $newSession->id,
                                'session_status' => 'scheduled',
                            ]);
                        }

                        Notification::make()
                            ->title($isAr ? 'تمت إضافة الحصة بنجاح' : 'Session Created')
                            ->success()
                            ->send();
                    }),

                // ── 3. RESYNC FUTURE SESSIONS ────────────────────────────────
                Action::make('resyncSessions')
                    ->label($isAr ? 'مزامنة الحصص مع القواعد' : 'Resync Schedule Sessions')
                    ->icon('heroicon-o-arrow-path')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalHeading($isAr ? 'تأكيد مزامنة الحصص' : 'Confirm Sessions Resync')
                    ->modalDescription($isAr ? 'سيتم تحديث مواعيد وروابط الحصص المستقبلية لتتوافق مع إعدادات الأيام المختارة. الحصص المكتملة والمعدلة يدوياً لن تتأثر.' : 'Future session dates and links will be aligned with the active recurring days rule. Completed sessions will remain untouched.')
                    ->action(function () use ($recurringSchedule, $isAr): void {
                        app(RecurringScheduleService::class)->syncFutureSessions($recurringSchedule, auth()->user());
                        Notification::make()
                            ->title($isAr ? 'تمت مزامنة الحصص بنجاح' : 'Sessions Resynced')
                            ->success()
                            ->send();
                    }),
            ])
            ->recordActions([
                // ── ACTION: ATTENDANCE & DEDUCTION MODAL (FULL PAGE / LARGE) ─
                Action::make('markAttendance')
                    ->label($isAr ? 'تسجيل الحضور وخصم الرصيد' : 'Record Attendance & Deduct')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->modalHeading(fn (LiveSession $record) => ($isAr ? 'تسجيل حضور الحصة: ' : 'Record Attendance: ') . $record->title)
                    ->modalSubheading($isAr ? 'قم بتسجيل وتأكيد حضور الطالب في هذا الموعد، وتطبيق الخصم الفوري من باقة الحصص وتوثيق سجل الحضور.' : 'Confirm student attendance and apply automatic credit deduction from package.')
                    ->modalWidth('7xl')
                    ->modalSubmitActionLabel($isAr ? 'تأكيد الحضور وخصم الحصة الآن' : 'Confirm Attendance & Deduct')
                    ->form(function (LiveSession $record) use ($recurringSchedule, $isAr) {
                        $targetStudentId = $record->student_user_id ?: $recurringSchedule->student_user_id;
                        $studentUser = $targetStudentId ? User::find($targetStudentId) : null;
                        $course = $record->course ?: $recurringSchedule->course;

                        $activePackage = $targetStudentId ? StudentPackage::where('student_user_id', $targetStudentId)
                            ->where(function ($q) use ($course) {
                                if ($course) {
                                    $q->where('course_id', $course->id)
                                      ->orWhereNull('course_id');
                                }
                            })
                            ->where('status', 'active')
                            ->latest('created_at')
                            ->first() : null;

                        $scheduledAt = $record->scheduled_at ?: now();
                        $isPast = $scheduledAt->isPast();

                        return [
                            Placeholder::make('session_and_student_info')
                                ->hiddenLabel()
                                ->columnSpanFull()
                                ->content(function () use ($record, $studentUser, $activePackage, $course, $scheduledAt, $isPast, $isAr) {
                                    $studentName = $studentUser?->name ?: ($isAr ? 'فصل جماعي' : 'Group Cohort');
                                    $profile = $studentUser?->studentProfile;
                                    $studentCode = $profile?->student_code ? "#{$profile->student_code}" : '—';
                                    $packageName = $activePackage?->packageTemplate?->name ?: ($isAr ? 'باقة مخصصة' : 'Standard Package');
                                    $remaining = $activePackage ? $activePackage->remaining_sessions : 0;
                                    $total = $activePackage ? $activePackage->total_sessions : 0;
                                    $used = $activePackage ? $activePackage->used_sessions : 0;

                                    $diff = $scheduledAt->diffForHumans();
                                    $dayName = $scheduledAt->locale(app()->getLocale())->translatedFormat('l, d M Y - h:i A');

                                    return new HtmlString('
                                        <div class="rec-attendance-hud-banner">
                                            <div class="rec-hud-grid">
                                                <div class="rec-hud-card">
                                                    <div class="rec-hud-card-icon rec-card-teal-icon">
                                                        <i class="fa-solid fa-graduation-cap"></i>
                                                    </div>
                                                    <div class="rec-hud-card-body">
                                                        <div class="rec-hud-card-label">' . ($isAr ? 'الطالب' : 'Student') . '</div>
                                                        <div class="rec-hud-card-title">' . e($studentName) . '</div>
                                                        <div class="rec-hud-card-sub text-teal-600 dark:text-teal-400 font-mono">' . e($studentCode) . '</div>
                                                    </div>
                                                </div>
                                                <div class="rec-hud-card">
                                                    <div class="rec-hud-card-icon rec-card-indigo-icon">
                                                        <i class="fa-solid fa-calendar-day"></i>
                                                    </div>
                                                    <div class="rec-hud-card-body">
                                                        <div class="rec-hud-card-label">' . ($isAr ? 'موعد الحصة المجدول' : 'Scheduled Slot') . '</div>
                                                        <div class="rec-hud-card-title text-xs">' . e($dayName) . '</div>
                                                        <div class="rec-hud-card-sub ' . ($isPast ? 'text-amber-500 font-bold' : 'text-slate-400') . '">' . ($isPast ? '<i class="fa-solid fa-triangle-exclamation"></i> ' . ($isAr ? "تاريخ سابق ({$diff})" : "Past ({$diff})") : '<i class="fa-solid fa-clock"></i> ' . $diff) . '</div>
                                                    </div>
                                                </div>
                                                <div class="rec-hud-card">
                                                    <div class="rec-hud-card-icon rec-card-emerald-icon">
                                                        <i class="fa-solid fa-ticket"></i>
                                                    </div>
                                                    <div class="rec-hud-card-body">
                                                        <div class="rec-hud-card-label">' . ($isAr ? 'رصيد الباقة' : 'Package Quota') . '</div>
                                                        <div class="rec-hud-card-title text-emerald-600 dark:text-emerald-400 font-mono">' . $remaining . ' <span class="text-xs font-normal text-slate-400">' . ($isAr ? 'حصة متبقية' : 'left') . '</span></div>
                                                        <div class="rec-hud-card-sub text-slate-500 dark:text-slate-400">' . ($isAr ? "مستخدم: {$used} من {$total}" : "{$used} of {$total}") . '</div>
                                                    </div>
                                                </div>
                                                <div class="rec-hud-card">
                                                    <div class="rec-hud-card-icon rec-card-amber-icon">
                                                        <i class="fa-solid fa-receipt"></i>
                                                    </div>
                                                    <div class="rec-hud-card-body">
                                                        <div class="rec-hud-card-label">' . ($isAr ? 'اسم الباقة النشطة' : 'Package') . '</div>
                                                        <div class="rec-hud-card-title truncate">' . e($packageName) . '</div>
                                                        <div class="rec-hud-card-sub text-amber-600 dark:text-amber-400 font-bold"><i class="fa-solid fa-bolt"></i> ' . ($isAr ? 'خصم 1 حصة فوري' : '-1 Session Credit') . '</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    ');
                                }),

                            Radio::make('attendance_status')
                                ->label(new HtmlString('<span class="flex items-center gap-1.5 font-bold text-sm"><i class="fa-solid fa-clipboard-user text-teal-500"></i> ' . ($isAr ? 'تأكيد حالة الحضور الفعلي' : 'Attendance Status') . '</span>'))
                                ->options([
                                    'present' => new HtmlString('<span class="rec-radio-opt"><i class="fa-solid fa-circle-check text-emerald-500"></i><strong>' . ($isAr ? 'حاضر' : 'Present') . '</strong> <span class="text-xs opacity-75">(' . ($isAr ? 'سيتم خصم حصة واحدة من باقة الطالب' : '1 session deducted') . ')</span></span>'),
                                    'late' => new HtmlString('<span class="rec-radio-opt"><i class="fa-solid fa-clock text-amber-500"></i><strong>' . ($isAr ? 'حاضر متأخر' : 'Late') . '</strong> <span class="text-xs opacity-75">(' . ($isAr ? 'سيتم خصم حصة واحدة من باقة الطالب' : '1 session deducted') . ')</span></span>'),
                                    'excused' => new HtmlString('<span class="rec-radio-opt"><i class="fa-solid fa-shield-halved text-cyan-500"></i><strong>' . ($isAr ? 'معذور رسمي' : 'Excused') . '</strong> <span class="text-xs opacity-75">(' . ($isAr ? 'حفظ رصيد الباقة دون خصم' : 'Credits preserved') . ')</span></span>'),
                                    'absent' => new HtmlString('<span class="rec-radio-opt"><i class="fa-solid fa-circle-xmark text-rose-500"></i><strong>' . ($isAr ? 'غائب عن الحصة' : 'Absent') . '</strong></span>'),
                                ])
                                ->default(fn () => in_array($record->attendance_status, ['present', 'late', 'excused', 'absent']) ? $record->attendance_status : 'present')
                                ->required()
                                ->columnSpanFull(),

                            DateTimePicker::make('attended_at')
                                ->label(new HtmlString('<span class="flex items-center gap-1.5 font-bold text-sm"><i class="fa-solid fa-calendar-day text-teal-500"></i> ' . ($isAr ? 'تاريخ وتوقيت الحضور الفعلي (يمكن تعديل التاريخ لأي موعد سابق)' : 'Actual Attendance Date & Time') . '</span>'))
                                ->default(fn () => $record->scheduled_at ?: now())
                                ->required()
                                ->helperText($isAr ? 'في حال حضر الطالب في تاريخ سابق، حدد التاريخ الدقيق وسيتم تسجيله وتوثيق خصم الحصة بأثر رجعي.' : 'If the student attended on a past date, pick the date and it will be recorded retroactively.')
                                ->columnSpanFull(),

                            Toggle::make('deduct_package')
                                ->label(new HtmlString('<span class="flex items-center gap-1.5 font-bold text-sm"><i class="fa-solid fa-receipt text-teal-500"></i> ' . ($isAr ? 'خصم الحصة من باقة الطالب في النظام' : 'Deduct Session Credit from Student Package') . '</span>'))
                                ->default(true)
                                ->visible(fn ($get) => in_array($get('attendance_status'), ['present', 'late'])),

                            Textarea::make('notes')
                                ->label(new HtmlString('<span class="flex items-center gap-1.5 font-bold text-sm"><i class="fa-solid fa-pen-to-square text-teal-500"></i> ' . ($isAr ? 'ملاحظات المعلم / سبب الحضور السابق' : 'Notes / Justification') . '</span>'))
                                ->placeholder($isAr ? 'اكتب ملاحظاتك على الحضور هنا...' : 'Notes regarding this attendance entry...')
                                ->rows(2)
                                ->columnSpanFull(),
                        ];
                    })
                    ->action(function (LiveSession $record, array $data) use ($recurringSchedule, $isAr): void {
                        $targetStudentId = (int) ($record->student_user_id ?: $recurringSchedule->student_user_id);

                        if (! $targetStudentId) {
                            Notification::make()
                                ->title($isAr ? 'تنبيه' : 'Notice')
                                ->body($isAr ? 'هذه الحصة ليست مرتبطة بطالب فردي محدد لتطبيق الخصم.' : 'Session is not associated with an individual student.')
                                ->warning()
                                ->send();
                            return;
                        }

                        $attendanceStatus = $data['attendance_status'] ?? 'present';
                        $attendedAt = ! empty($data['attended_at']) ? Carbon::parse($data['attended_at']) : ($record->scheduled_at ?: now());
                        $deductPackage = (bool) ($data['deduct_package'] ?? true);
                        $notes = $data['notes'] ?? null;

                        $service = app(SessionAttendanceDeductionService::class);
                        $res = $service->recordAttendanceAndDeduct(
                            $record,
                            $targetStudentId,
                            $attendanceStatus,
                            $attendedAt,
                            $notes,
                            auth()->user(),
                            $deductPackage
                        );

                        if ($res['success']) {
                            Notification::make()
                                ->title($isAr ? 'تم تسجيل الحضور وتحديث الباقة بنجاح' : 'Attendance Updated')
                                ->body($res['message'])
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title($isAr ? 'تنبيه' : 'Notice')
                                ->body($res['message'])
                                ->warning()
                                ->send();
                        }
                    }),

                // ── ACTION: RESCHEDULE / EDIT TIMING ──────────────────────────
                Action::make('reschedule')
                    ->label($isAr ? 'تعديل الموعد' : 'Reschedule')
                    ->icon('heroicon-o-calendar')
                    ->color('warning')
                    ->modalHeading($isAr ? 'تعديل موعد الحصة' : 'Reschedule Session')
                    ->form([
                        DateTimePicker::make('new_scheduled_at')
                            ->label($isAr ? 'الموعد الجديد' : 'New Date & Time')
                            ->default(fn (LiveSession $record) => $record->scheduled_at)
                            ->required(),

                        TextInput::make('duration_minutes')
                            ->label($isAr ? 'المدة (بالدقائق)' : 'Duration (Minutes)')
                            ->numeric()
                            ->default(fn (LiveSession $record) => $record->duration_minutes ?: 60)
                            ->required(),

                        TextInput::make('override_reason')
                            ->label($isAr ? 'سبب التعديل / التأجيل' : 'Reason for Rescheduling')
                            ->placeholder($isAr ? 'مثال: طلب ولي الأمر / عطل تقني' : 'e.g. Student request')
                            ->required(),
                    ])
                    ->action(function (LiveSession $record, array $data) use ($isAr): void {
                        $newStart = Carbon::parse($data['new_scheduled_at']);
                        $duration = (int) ($data['duration_minutes'] ?: 60);
                        $newEnd = $newStart->copy()->addMinutes($duration);

                        $record->update([
                            'original_scheduled_at' => $record->original_scheduled_at ?: $record->scheduled_at,
                            'scheduled_at' => $newStart,
                            'start_at' => $newStart,
                            'end_at' => $newEnd,
                            'duration_minutes' => $duration,
                            'is_override' => true,
                            'override_reason' => $data['override_reason'],
                            'status' => 'rescheduled',
                        ]);

                        Notification::make()
                            ->title($isAr ? 'تم تأجيل وتعديل موعد الحصة بنجاح' : 'Session Rescheduled')
                            ->success()
                            ->send();
                    }),

                // ── ACTION: UPDATE MEETING LINK ──────────────────────────────
                Action::make('updateLink')
                    ->label($isAr ? 'تحديث الرابط' : 'Set Link')
                    ->icon('heroicon-o-link')
                    ->color('info')
                    ->modalHeading($isAr ? 'تحديث رابط البث المباشر' : 'Update Meeting Broadcast URL')
                    ->form([
                        Select::make('meeting_platform')
                            ->label($isAr ? 'المنصة' : 'Platform')
                            ->options([
                                'agora' => 'Agora Classroom',
                                'google_meet' => 'Google Meet',
                                'zoom' => 'Zoom Meeting',
                                'microsoft_teams' => 'Microsoft Teams',
                                'other' => 'Other Stream Link',
                            ])
                            ->default(fn (LiveSession $record) => $record->meeting_platform ?: 'agora')
                            ->required(),

                        TextInput::make('meeting_link')
                            ->label($isAr ? 'رابط الاجتماع' : 'Meeting Link')
                            ->url()
                            ->placeholder('https://zoom.us/j/... or classroom stream link')
                            ->default(fn (LiveSession $record) => $record->meeting_link)
                            ->required(),
                    ])
                    ->action(function (LiveSession $record, array $data) use ($isAr): void {
                        $record->update([
                            'meeting_platform' => $data['meeting_platform'],
                            'meeting_link' => $data['meeting_link'],
                            'status' => in_array($record->status, ['completed', 'cancelled']) ? $record->status : 'link_visible',
                            'link_visible_at' => now(),
                        ]);

                        Notification::make()
                            ->title($isAr ? 'تم تحديث رابط البث بنجاح' : 'Link Updated')
                            ->success()
                            ->send();
                    }),

                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->before(function (LiveSession $record, DeleteAction $action) use ($isAr) {
                        // Check if session was already deducted from package
                        $hasDeduction = \App\Models\PackageTransaction::where('live_session_id', $record->id)
                            ->where('type', 'session_deduct')
                            ->exists();

                        if ($hasDeduction && $record->student_user_id) {
                            // Automatically refund package credit
                            app(SessionAttendanceDeductionService::class)->refundIfPreviouslyDeducted(
                                $record,
                                (int) $record->student_user_id,
                                auth()->user(),
                                'Session Deleted by Admin - Credit Restored'
                            );
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
