<?php

namespace App\Filament\Resources\RecurringSchedules\Schemas;

use App\Models\Course;
use App\Models\TeacherProfile;
use App\Models\User;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class RecurringScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label(app()->getLocale() === 'ar' ? 'عنوان الجدول الأسبوعي' : 'Cohort Schedule Title')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Weekly Cohort Schedule'),

                Select::make('teacher_profile_id')
                    ->label(app()->getLocale() === 'ar' ? 'المعلم المسؤول' : 'Assigned Teacher')
                    ->options(fn () => TeacherProfile::with('user')->get()->mapWithKeys(fn ($tp) => [
                        $tp->id => ($tp->user?->name ?: 'Teacher #' . $tp->id) . ' (' . ($tp->title ?: 'Faculty') . ')',
                    ]))
                    ->searchable()
                    ->required(),

                Select::make('course_id')
                    ->label(app()->getLocale() === 'ar' ? 'الكورس المرتبط' : 'Associated Course')
                    ->relationship('course', 'title')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('student_user_id')
                    ->label(app()->getLocale() === 'ar' ? 'الطالب (اختياري للدروس الخصوصية)' : '1-on-1 Student (Optional)')
                    ->options(function () {
                        $query = User::query();
                        if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'role')) {
                            $query->where('role', 'student');
                        } elseif (method_exists(User::class, 'scopeRoleStudent')) {
                            $query->roleStudent();
                        } else {
                            $query->whereHas('studentProfile');
                        }
                        return $query->pluck('name', 'id');
                    })
                    ->searchable()
                    ->nullable()
                    ->helperText(app()->getLocale() === 'ar' ? 'اتركه فارغاً إذا كان الجدول لكامل طلاب الدفعة / الكورس' : 'Leave empty for course-wide public cohort'),

                Select::make('recurrence_type')
                    ->label(app()->getLocale() === 'ar' ? 'نمط التكرار' : 'Recurrence Pattern')
                    ->options([
                        'weekly' => app()->getLocale() === 'ar' ? 'أسبوعي (Weekly)' : 'Weekly',
                        'biweekly' => app()->getLocale() === 'ar' ? 'كل أسبوعين (Bi-weekly)' : 'Bi-weekly',
                        'daily' => app()->getLocale() === 'ar' ? 'يومي (Daily)' : 'Daily',
                        'monthly' => app()->getLocale() === 'ar' ? 'شهري (Monthly)' : 'Monthly',
                    ])
                    ->default('weekly')
                    ->required(),

                CheckboxList::make('days_of_week')
                    ->label(app()->getLocale() === 'ar' ? 'أيام الأسبوع' : 'Days of Week')
                    ->options([
                        6 => app()->getLocale() === 'ar' ? 'السبت (Saturday)' : 'Saturday',
                        0 => app()->getLocale() === 'ar' ? 'الأحد (Sunday)' : 'Sunday',
                        1 => app()->getLocale() === 'ar' ? 'الإثنين (Monday)' : 'Monday',
                        2 => app()->getLocale() === 'ar' ? 'الثلاثاء (Tuesday)' : 'Tuesday',
                        3 => app()->getLocale() === 'ar' ? 'الأربعاء (Wednesday)' : 'Wednesday',
                        4 => app()->getLocale() === 'ar' ? 'الخميس (Thursday)' : 'Thursday',
                        5 => app()->getLocale() === 'ar' ? 'الجمعة (Friday)' : 'Friday',
                    ])
                    ->columns(3)
                    ->default([6, 0]),

                TimePicker::make('start_time')
                    ->label(app()->getLocale() === 'ar' ? 'وقت البدء' : 'Start Time')
                    ->required()
                    ->default('10:00'),

                TimePicker::make('end_time')
                    ->label(app()->getLocale() === 'ar' ? 'وقت الانتهاء' : 'End Time')
                    ->nullable(),

                TextInput::make('duration_minutes')
                    ->label(app()->getLocale() === 'ar' ? 'مدة الحصة (بالدقائق)' : 'Duration (Minutes)')
                    ->numeric()
                    ->default(60)
                    ->required(),

                DatePicker::make('start_date')
                    ->label(app()->getLocale() === 'ar' ? 'تاريخ بداية الجدول' : 'Start Date')
                    ->required()
                    ->default(now()->toDateString()),

                DatePicker::make('end_date')
                    ->label(app()->getLocale() === 'ar' ? 'تاريخ نهاية الجدول' : 'End Date')
                    ->required()
                    ->default(now()->addMonths(3)->toDateString()),

                Select::make('meeting_platform')
                    ->label(app()->getLocale() === 'ar' ? 'منصة البث الافتراضية' : 'Default Meeting Platform')
                    ->options([
                        'agora' => 'Agora Classroom (غرفة المنصة التفاعلية المباشرة)',
                        'google_meet' => 'Google Meet',
                        'zoom' => 'Zoom Meeting',
                        'microsoft_teams' => 'Microsoft Teams',
                        'other' => 'Other / Custom Stream URL',
                    ])
                    ->default('agora')
                    ->required(),

                TextInput::make('meeting_link')
                    ->label(app()->getLocale() === 'ar' ? 'رابط البث الافتراضي' : 'Default Meeting Link')
                    ->url()
                    ->placeholder('https://zoom.us/j/... or stream link')
                    ->maxLength(500),

                Select::make('status')
                    ->label(app()->getLocale() === 'ar' ? 'حالة الجدول' : 'Schedule Status')
                    ->options([
                        'active' => 'Active (نشط وفعال)',
                        'paused' => 'Paused (متوقف مؤقتاً)',
                        'completed' => 'Completed (مكتمل)',
                        'cancelled' => 'Cancelled (ملغي)',
                    ])
                    ->default('active')
                    ->required(),

                Textarea::make('notes')
                    ->label(app()->getLocale() === 'ar' ? 'ملاحظات إضافية' : 'Admin / Teacher Notes')
                    ->columnSpanFull()
                    ->rows(3)
                    ->nullable(),
            ]);
    }
}
