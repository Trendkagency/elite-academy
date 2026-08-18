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
                    ->relationship('assignment', 'title')
                    ->label('Assignment / Exam Title')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('student_user_id')
                    ->relationship('studentUser', 'name')
                    ->label('Student Name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('course_enrollment_id')
                    ->relationship(
                        name: 'enrollment',
                        titleAttribute: 'id',
                        modifyQueryUsing: fn ($query) => $query->with(['studentUser', 'course'])
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record) => "Enrollment #{$record->id} — " . ($record->studentUser?->name ?? 'Student') . " (" . ($record->course?->title ?? 'Course') . ")")
                    ->label('Course Enrollment')
                    ->searchable()
                    ->preload()
                    ->required(),
                DateTimePicker::make('submitted_at')
                    ->label('Submission Date & Time'),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending Review',
                        'submitted' => 'Submitted',
                        'completed' => 'Completed (Passed)',
                        'late' => 'Submitted Late',
                    ])
                    ->default('completed')
                    ->required(),
                TextInput::make('grade')
                    ->label('Grade Percentage (%)')
                    ->numeric()
                    ->default(100),
                Textarea::make('teacher_notes')
                    ->label('Teacher Feedback & Review Notes')
                    ->rows(4)
                    ->columnSpanFull(),
            ]);
    }
}
