<?php

namespace App\Filament\Resources\AssignmentSubmissions\Schemas;

use App\Enums\SubmissionStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AssignmentSubmissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('assignment_id')
                    ->relationship('assignment', 'title')
                    ->required(),
                Select::make('student_user_id')
                    ->relationship('studentUser', 'name')
                    ->required(),
                TextInput::make('course_enrollment_id')
                    ->required()
                    ->numeric(),
                DateTimePicker::make('submitted_at'),
                Select::make('status')
                    ->options(SubmissionStatus::class)
                    ->default('pending')
                    ->required(),
                TextInput::make('grade')
                    ->numeric(),
                Textarea::make('teacher_notes')
                    ->columnSpanFull(),
                DateTimePicker::make('reviewed_at'),
                TextInput::make('reviewed_by')
                    ->numeric(),
            ]);
    }
}
