<?php

namespace App\Filament\Resources\Courses\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('subject_id')
                    ->relationship('subject', 'name')
                    ->required(),
                Select::make('grade_level_id')
                    ->relationship('gradeLevel', 'name'),
                Select::make('teacher_id')
                    ->relationship('teacher', 'title')
                    ->required(),
                TextInput::make('title')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                FileUpload::make('image')
                    ->image(),
                TextInput::make('sessions_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('session_duration_minutes')
                    ->required()
                    ->numeric()
                    ->default(60),
                TextInput::make('rating_avg')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('reviews_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('enrollments_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('has_free_demo')
                    ->required(),
                Toggle::make('is_accredited')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
