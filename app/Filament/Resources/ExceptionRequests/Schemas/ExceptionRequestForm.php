<?php

namespace App\Filament\Resources\ExceptionRequests\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ExceptionRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('student_user_id')
                    ->relationship('studentUser', 'name')
                    ->label('Student')
                    ->searchable()
                    ->required(),
                Select::make('scope')
                    ->options([
                        'course' => 'Single Course Exception',
                        'global' => 'Global System Exception (All Courses)',
                    ])
                    ->label('Exception Scope')
                    ->default('course')
                    ->required(),
                Select::make('course_id')
                    ->relationship('course', 'title')
                    ->label('Specific Target Course')
                    ->searchable()
                    ->nullable(),
                Toggle::make('is_global')
                    ->label('Apply Globally across all courses for this student')
                    ->default(false),
                Select::make('live_session_id')
                    ->relationship('liveSession', 'id')
                    ->label('Live Session (Optional)')
                    ->searchable()
                    ->nullable(),
                Textarea::make('reason')
                    ->label('Excuse / Reason for Exception')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull(),
                TextInput::make('attachment_path')
                    ->label('Attachment Document Path'),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending Review',
                        'approved' => 'Approved (Grants Exemption)',
                        'rejected' => 'Rejected',
                    ])
                    ->default('pending')
                    ->required(),
            ]);
    }
}
