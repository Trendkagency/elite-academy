<?php

namespace App\Filament\Resources\LiveSessions\Schemas;

use App\Models\Course;
use App\Models\LiveSession;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class LiveSessionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label(app()->getLocale() === 'ar' ? 'عنوان الحصة' : 'Session Title')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Live Class Session'),

                Select::make('teacher_profile_id')
                    ->label(app()->getLocale() === 'ar' ? 'المعلم' : 'Teacher')
                    ->options(fn () => TeacherProfile::with('user')->get()->mapWithKeys(fn ($tp) => [
                        $tp->id => ($tp->user?->name ?: 'Teacher #' . $tp->id) . ' (' . ($tp->title ?: 'Faculty') . ')',
                    ]))
                    ->searchable()
                    ->required(),

                Select::make('course_id')
                    ->label(app()->getLocale() === 'ar' ? 'الكورس' : 'Course')
                    ->relationship('course', 'title')
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $course = Course::find($state);
                            if ($course && $course->subject_id) {
                                $set('subject_id', $course->subject_id);
                            }
                        }
                    }),

                Select::make('subject_id')
                    ->label(app()->getLocale() === 'ar' ? 'المادة الدراسية' : 'Subject')
                    ->relationship('subject', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('student_user_id')
                    ->label(app()->getLocale() === 'ar' ? 'طالب الحصة الخاصة (اختياري للدروس الفردية)' : '1-on-1 Student (Optional)')
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
                    ->helperText(app()->getLocale() === 'ar' ? 'اتركه فارغاً إذا كانت الحصة جماعية لكافة طلاب الكورس' : 'Leave empty for full group class cohort'),

                Select::make('recurring_schedule_id')
                    ->label(app()->getLocale() === 'ar' ? 'الجدول المتكرر التابع له' : 'Recurring Schedule Cohort')
                    ->relationship('recurringSchedule', 'title')
                    ->searchable()
                    ->nullable(),

                DateTimePicker::make('scheduled_at')
                    ->label(app()->getLocale() === 'ar' ? 'تاريخ ووقت الحصة' : 'Scheduled Date & Time')
                    ->required()
                    ->default(now()->addHour()),

                DateTimePicker::make('start_at')
                    ->label(app()->getLocale() === 'ar' ? 'وقت البدء الفعلي' : 'Effective Start Time')
                    ->nullable(),

                DateTimePicker::make('end_at')
                    ->label(app()->getLocale() === 'ar' ? 'وقت الانتهاء الفعلي' : 'Effective End Time')
                    ->nullable(),

                TextInput::make('duration_minutes')
                    ->label(app()->getLocale() === 'ar' ? 'المدة بالدقائق' : 'Duration (Minutes)')
                    ->numeric()
                    ->default(60)
                    ->required(),

                Select::make('meeting_platform')
                    ->label(app()->getLocale() === 'ar' ? 'منصة البث المباشر' : 'Meeting Platform')
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
                    ->label(app()->getLocale() === 'ar' ? 'رابط غرفة البث / الاجتماع' : 'Meeting / Broadcast URL')
                    ->url()
                    ->placeholder('https://zoom.us/j/... or classroom stream link')
                    ->maxLength(500)
                    ->helperText(app()->getLocale() === 'ar' ? 'رابط البث أو الغرفة التي يدخل إليها الطلاب والمعلم' : 'Direct room URL visible to enrolled students and teacher'),

                Select::make('status')
                    ->label(app()->getLocale() === 'ar' ? 'حالة الحصة' : 'Session Status')
                    ->options([
                        'scheduled' => 'Scheduled (مجدولة)',
                        'link_visible' => 'Link Visible (الرابط مفعل)',
                        'in_progress' => 'In Progress (جارية الآن)',
                        'completed' => 'Completed (مكتملة)',
                        'cancelled' => 'Cancelled (ملغاة)',
                        'cancelled_by_teacher' => 'Cancelled by Teacher (ملغاة من المعلم)',
                        'rescheduled' => 'Rescheduled (مؤجلة)',
                    ])
                    ->default('scheduled')
                    ->required(),

                Select::make('lifecycle_state')
                    ->label(app()->getLocale() === 'ar' ? 'حالة دورة الحياة' : 'Lifecycle State')
                    ->options([
                        'scheduled' => 'Scheduled',
                        'upcoming' => 'Upcoming',
                        'ready' => 'Ready',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        'rescheduled' => 'Rescheduled',
                        'missed' => 'Missed',
                    ])
                    ->default('scheduled'),

                Toggle::make('is_free_demo')
                    ->label(app()->getLocale() === 'ar' ? '🎓 حصة تجريبية مجانية (Free Demo)' : '🎓 Free Demo Trial Session')
                    ->default(false),

                Toggle::make('is_override')
                    ->label(app()->getLocale() === 'ar' ? '⚠️ استثناء مخصص من الجدول الدوري' : '⚠️ Schedule Override')
                    ->default(false),

                TextInput::make('override_reason')
                    ->label(app()->getLocale() === 'ar' ? 'سبب التعديل / الاستثناء' : 'Override Reason')
                    ->maxLength(255)
                    ->nullable(),

                Textarea::make('teacher_notes')
                    ->label(app()->getLocale() === 'ar' ? 'ملاحظات المعلم والإدارة' : 'Teacher / Admin Notes')
                    ->columnSpanFull()
                    ->rows(3)
                    ->nullable(),
            ]);
    }
}
