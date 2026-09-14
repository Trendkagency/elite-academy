<?php

namespace App\Filament\Resources\CourseSessions\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CourseSessionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('course_id')
                    ->label(__('Course'))
                    ->relationship('course', 'title')
                    ->required(),
                TextInput::make('title')
                    ->label(__('Session Title'))
                    ->required(),
                DateTimePicker::make('scheduled_at')
                    ->label(__('Scheduled Date & Time'))
                    ->nullable(),
                DateTimePicker::make('start_at')
                    ->label(__('Start Date & Time'))
                    ->nullable(),
                DateTimePicker::make('end_at')
                    ->label(__('End Date & Time'))
                    ->nullable(),
                TextInput::make('sort_order')
                    ->label(__('Sort Order'))
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('duration_minutes')
                    ->label(__('Session Duration (Minutes)'))
                    ->required()
                    ->numeric()
                    ->default(60),
                TextInput::make('video_url')
                    ->label(__('Video URL'))
                    ->url(),
                Textarea::make('description')
                    ->label(__('Description'))
                    ->columnSpanFull(),
                Textarea::make('content')
                    ->label(__('Content'))
                    ->columnSpanFull(),
                Toggle::make('is_free_demo')
                    ->label(__('First Session Free Trial'))
                    ->default(false),
            ]);
    }
}
