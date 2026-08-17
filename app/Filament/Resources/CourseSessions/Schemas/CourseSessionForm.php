<?php

namespace App\Filament\Resources\CourseSessions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CourseSessionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('course_id')
                    ->relationship('course', 'title')
                    ->required(),
                TextInput::make('title')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('duration_minutes')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('video_url')
                    ->url(),
                Textarea::make('content')
                    ->columnSpanFull(),
                Toggle::make('is_free_demo')
                    ->required(),
            ]);
    }
}
