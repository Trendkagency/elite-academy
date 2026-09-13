<?php

namespace App\Filament\Resources\AssignmentSubmissions\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AssignmentSubmissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('assignment_id')
                    ->relationship(
                        name: 'assignment',
                        titleAttribute: 'title',
                        modifyQueryUsing: function ($query, $get) {
                            if ($studentId = $get('student_user_id')) {
                                $enrolledCourseIds = \App\Models\CourseEnrollment::where('student_user_id', $studentId)
                                    ->pluck('course_id')
                                    ->filter()
                                    ->toArray();
                                if (! empty($enrolledCourseIds)) {
                                    $query->whereIn('course_id', $enrolledCourseIds);
                                }
                            }
                            return $query->latest('created_at');
                        }
                    )
                    ->label(app()->getLocale() === 'ar' ? 'الواجب / الاختبار' : 'Assignment / Exam')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function ($state, $set, $get) {
                        if ($state && $studentId = $get('student_user_id')) {
                            $assignment = \App\Models\Assignment::find($state);
                            if ($assignment && $assignment->course_id) {
                                $enrollment = \App\Models\CourseEnrollment::where('student_user_id', $studentId)
                                    ->where('course_id', $assignment->course_id)
                                    ->first();
                                if ($enrollment) {
                                    $set('course_enrollment_id', $enrollment->id);
                                }
                            }
                        }
                    })
                    ->required(),

                Select::make('student_user_id')
                    ->relationship(
                        name: 'studentUser',
                        titleAttribute: 'name',
                        modifyQueryUsing: function ($query, $get) {
                            $query->whereHas('studentProfile');
                            if ($assignmentId = $get('assignment_id')) {
                                $assignment = \App\Models\Assignment::find($assignmentId);
                                if ($assignment && $assignment->course_id) {
                                    $query->whereHas('courseEnrollments', fn ($q) => $q->where('course_id', $assignment->course_id));
                                }
                            }
                            return $query->latest('created_at');
                        }
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} ({$record->email})")
                    ->label(app()->getLocale() === 'ar' ? 'اسم الطالب' : 'Student Name')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function ($state, $set, $get) {
                        if ($state && $assignmentId = $get('assignment_id')) {
                            $assignment = \App\Models\Assignment::find($assignmentId);
                            if ($assignment && $assignment->course_id) {
                                $enrollment = \App\Models\CourseEnrollment::where('student_user_id', $state)
                                    ->where('course_id', $assignment->course_id)
                                    ->first();
                                if ($enrollment) {
                                    $set('course_enrollment_id', $enrollment->id);
                                }
                            }
                        }
                    })
                    ->required(),

                Select::make('course_enrollment_id')
                    ->relationship(
                        name: 'enrollment',
                        titleAttribute: 'id',
                        modifyQueryUsing: function ($query, $get) {
                            if ($studentId = $get('student_user_id')) {
                                $query->where('student_user_id', $studentId);
                            }
                            if ($assignmentId = $get('assignment_id')) {
                                $assignment = \App\Models\Assignment::find($assignmentId);
                                if ($assignment && $assignment->course_id) {
                                    $query->where('course_id', $assignment->course_id);
                                }
                            }
                            return $query->latest('created_at')->with(['studentUser', 'course']);
                        }
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record) => "Enrollment #{$record->id} — " . ($record->studentUser?->name ?? 'Student') . " (" . ($record->course?->title ?? 'Course') . ")")
                    ->label(app()->getLocale() === 'ar' ? 'التسجيل في الكورس' : 'Course Enrollment')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function ($state, $set) {
                        if ($state) {
                            $enrollment = \App\Models\CourseEnrollment::find($state);
                            if ($enrollment) {
                                $set('student_user_id', $enrollment->student_user_id);
                            }
                        }
                    })
                    ->required(),

                Select::make('status')
                    ->options([
                        'completed' => app()->getLocale() === 'ar' ? 'مكتمل (ناجح)' : 'Completed (Passed)',
                        'reviewed' => app()->getLocale() === 'ar' ? 'تم التقييم' : 'Reviewed',
                        'submitted' => app()->getLocale() === 'ar' ? 'تم التسليم' : 'Submitted',
                        'late' => app()->getLocale() === 'ar' ? 'تسليم متأخر' : 'Late Submission',
                        'in_progress' => app()->getLocale() === 'ar' ? 'جاري الحل' : 'In Progress',
                        'pending' => app()->getLocale() === 'ar' ? 'قيد المراجعة' : 'Pending Review',
                    ])
                    ->default('completed')
                    ->required()
                    ->label(app()->getLocale() === 'ar' ? 'حالة التسليم' : 'Submission Status'),

                TextInput::make('grade')
                    ->label(app()->getLocale() === 'ar' ? 'الدرجة بالنسبة المئوية (%)' : 'Grade Percentage (%)')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->suffix('%')
                    ->default(100)
                    ->required(),

                TextInput::make('score')
                    ->label(app()->getLocale() === 'ar' ? 'النقاط المحصلة' : 'Points Earned')
                    ->numeric()
                    ->nullable(),

                TextInput::make('total_points')
                    ->label(app()->getLocale() === 'ar' ? 'إجمالي نقاط الواجب' : 'Total Available Points')
                    ->numeric()
                    ->nullable(),

                DateTimePicker::make('started_at')
                    ->label(app()->getLocale() === 'ar' ? 'تاريخ ووقت بدء الحل' : 'Started At'),

                DateTimePicker::make('submitted_at')
                    ->label(app()->getLocale() === 'ar' ? 'تاريخ ووقت التسليم' : 'Submitted At'),

                Textarea::make('teacher_notes')
                    ->label(app()->getLocale() === 'ar' ? 'ملاحظات وتوجيهات المعلم' : 'Teacher Feedback & Notes')
                    ->rows(3)
                    ->columnSpanFull(),

                Textarea::make('evaluation_notes')
                    ->label(app()->getLocale() === 'ar' ? 'ملاحظات النظام / التقييم' : 'System Evaluation Notes')
                    ->rows(2)
                    ->columnSpanFull(),
            ]);
    }
}
