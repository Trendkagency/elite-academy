<?php

namespace App\Filament\Resources\RecurringSchedules\Schemas;

use App\Models\Course;
use App\Models\TeacherProfile;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RecurringScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        $isAr = app()->getLocale() === 'ar';

        return $schema
            ->components([
                Section::make($isAr ? 'البيانات الأساسية للجدول الأسبوعي' : 'Basic Cohort Schedule Information')
                    ->columns(2)
                    ->components([
                        TextInput::make('title')
                            ->label($isAr ? 'عنوان الجدول الأسبوعي' : 'Cohort Schedule Title')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Weekly Cohort Schedule'),

                        Select::make('teacher_profile_id')
                            ->label($isAr ? 'المعلم المسؤول' : 'Assigned Teacher')
                            ->options(fn () => TeacherProfile::with('user')->get()->mapWithKeys(fn ($tp) => [
                                $tp->id => ($tp->user?->name ?: 'Teacher #' . $tp->id) . ' (' . ($tp->title ?: 'Faculty') . ')',
                            ]))
                            ->searchable()
                            ->required(),

                        Select::make('course_id')
                            ->label($isAr ? 'الكورس المرتبط' : 'Associated Course')
                            ->relationship('course', 'title')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('student_user_id')
                            ->label(__('1-on-1 Student (Optional)'))
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
                            ->helperText(__('Leave empty for course-wide public cohort')),

                        Select::make('recurrence_type')
                            ->label($isAr ? 'نمط التكرار' : 'Recurrence Pattern')
                            ->options([
                                'weekly' => $isAr ? 'أسبوعي' : 'Weekly',
                                'biweekly' => $isAr ? 'كل أسبوعين' : 'Bi-weekly',
                                'daily' => $isAr ? 'يومي' : 'Daily',
                                'monthly' => $isAr ? 'شهري' : 'Monthly',
                            ])
                            ->default('weekly')
                            ->required(),

                        Select::make('status')
                            ->label($isAr ? 'حالة الجدول' : 'Schedule Status')
                            ->options([
                                'active' => $isAr ? 'نشط وفعال' : 'Active',
                                'paused' => $isAr ? 'متوقف مؤقتاً' : 'Paused',
                                'completed' => $isAr ? 'مكتمل' : 'Completed',
                                'cancelled' => $isAr ? 'ملغي' : 'Cancelled',
                            ])
                            ->default('active')
                            ->required(),
                    ]),

                Section::make($isAr ? 'الإعدادات العامة والافتراضية للجلسات' : 'Default Session Settings & Timing')
                    ->columns(2)
                    ->components([
                        TimePicker::make('start_time')
                            ->label($isAr ? 'وقت البدء الافتراضي' : 'Default Start Time')
                            ->required()
                            ->default('10:00'),

                        TextInput::make('duration_minutes')
                            ->label($isAr ? 'مدة الحصة (بالدقائق)' : 'Duration (Minutes)')
                            ->numeric()
                            ->default(60)
                            ->required(),

                        DatePicker::make('start_date')
                            ->label($isAr ? 'تاريخ بداية الجدول' : 'Start Date')
                            ->required()
                            ->default(now()->toDateString()),

                        DatePicker::make('end_date')
                            ->label($isAr ? 'تاريخ نهاية الجدول' : 'End Date')
                            ->required()
                            ->default(now()->addMonths(3)->toDateString()),

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
                            ->required(),

                        TextInput::make('meeting_link')
                            ->label($isAr ? 'رابط البث الافتراضي' : 'Default Meeting Link')
                            ->url()
                            ->placeholder('https://zoom.us/j/... or stream link')
                            ->maxLength(500),

                        Textarea::make('notes')
                            ->label($isAr ? 'ملاحظات إضافية' : 'Admin / Teacher Notes')
                            ->columnSpanFull()
                            ->rows(2)
                            ->nullable(),
                    ]),

                Section::make($isAr ? 'تخصيص الأيام والمواعيد المستقلة' : 'Per-Day Custom Schedules & Links')
                    ->description($isAr ? 'اختر الأيام المحددة للتكرار، ويمكنك تحديد وقت خاص ومدة ورابط اجتماع لكل يوم مستقبلي بشكل مستقل.' : 'Select days to recur and customize start times, durations & links independently per day.')
                    ->columns(1)
                    ->components([
                        CheckboxList::make('days_of_week')
                            ->label($isAr ? 'الأيام المحددة للتكرار' : 'Selected Days of Week')
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
                            ->hintAction(
                                Action::make('syncMainSettings')
                                    ->label($isAr ? 'مزامنة الإعدادات الرئيسية مع الأيام المحددة' : 'Sync Main Settings to All Days')
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
                                            ->title($isAr ? 'تمت مزامنة الإعدادات الرئيسية مع الأيام المحددة' : 'Main settings synced to selected days')
                                            ->body($isAr ? 'تم نسخ وقت البدء والمدة ورابط الاجتماع إلى جميع الأيام المختارة بنجاح.' : 'Start time, duration, and meeting link applied across all active days.')
                                            ->success()
                                            ->send();
                                    })
                            ),

                        Grid::make([
                            'default' => 1,
                            'lg' => 2,
                        ])
                            ->columnSpanFull()
                            ->schema(
                                collect([
                                    6 => ['label' => $isAr ? 'السبت' : 'Saturday'],
                                    0 => ['label' => $isAr ? 'الأحد' : 'Sunday'],
                                    1 => ['label' => $isAr ? 'الإثنين' : 'Monday'],
                                    2 => ['label' => $isAr ? 'الثلاثاء' : 'Tuesday'],
                                    3 => ['label' => $isAr ? 'الأربعاء' : 'Wednesday'],
                                    4 => ['label' => $isAr ? 'الخميس' : 'Thursday'],
                                    5 => ['label' => $isAr ? 'الجمعة' : 'Friday'],
                                ])->map(function ($info, $dayNum) use ($isAr) {
                                    return Fieldset::make($info['label'])
                                        ->visible(fn ($get) => in_array($dayNum, $get('days_of_week') ?? []))
                                        ->columns(2)
                                        ->schema([
                                            TimePicker::make("day_start_times.{$dayNum}")
                                                ->label($isAr ? 'وقت البدء' : 'Start Time')
                                                ->default('10:00')
                                                ->columnSpan(1),
                                            TextInput::make("day_durations.{$dayNum}")
                                                ->label($isAr ? 'المدة (بالدقائق)' : 'Duration (min)')
                                                ->numeric()
                                                ->default(60)
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
}
