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
                            return $query;
                        }
                    )
                    ->label(__('Assignment / Exam Title'))
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
                            return $query;
                        }
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} ({$record->email})")
                    ->label(__('Student Name'))
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
                            return $query->with(['studentUser', 'course']);
                        }
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record) => "Enrollment #{$record->id} — " . ($record->studentUser?->name ?? 'Student') . " (" . ($record->course?->title ?? 'Course') . ")")
                    ->label(__('Course Enrollment'))
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

                DateTimePicker::make('submitted_at')
                    ->label(__('Submission Date & Time')),

                Select::make('status')
                    ->options([
                        'pending' => __('Pending Review'),
                        'submitted' => __('Submitted'),
                        'completed' => __('Completed (Passed)'),
                        'late' => __('Submitted Late'),
                    ])
                    ->default('completed')
                    ->required()
                    ->label(__('Submission Status')),

                TextInput::make('grade')
                    ->label(__('Grade Percentage (%)'))
                    ->numeric()
                    ->default(100),

                Textarea::make('teacher_notes')
                    ->label(__('Teacher Feedback & Review Notes'))
                    ->rows(4)
                    ->columnSpanFull(),
            ]);
    }
}
