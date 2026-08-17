<?php

namespace App\Filament\Resources\Assignments\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AssignmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('course_session_id')
                    ->relationship('session', 'title')
                    ->label('Course Session')
                    ->searchable()
                    ->required(),
                TextInput::make('title')
                    ->label('Assignment Title')
                    ->required(),
                Textarea::make('description')
                    ->label('Assignment Instructions & Homework Details')
                    ->rows(4)
                    ->columnSpanFull(),
                TextInput::make('passing_grade')
                    ->required()
                    ->numeric()
                    ->default(70)
                    ->label('Passing Grade (%)'),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(1),
                DateTimePicker::make('due_at')
                    ->label('Due Date & Time'),
                Select::make('status')
                    ->options([
                        'published' => 'Published / Active',
                        'draft' => 'Draft',
                        'closed' => 'Closed',
                    ])
                    ->default('published')
                    ->required(),
            ]);
    }
}
