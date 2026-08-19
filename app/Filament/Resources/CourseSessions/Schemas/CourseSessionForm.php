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
                    ->relationship('course', 'title')
                    ->required(),
                TextInput::make('title')
                    ->required(),
                DateTimePicker::make('scheduled_at')
                    ->label('Scheduled Date & Time (تاريخ وتوقيت الحصة المحددة)')
                    ->nullable(),
                DateTimePicker::make('start_at')
                    ->label('Start Date & Time (وقت البدء الرسمية)')
                    ->nullable(),
                DateTimePicker::make('end_at')
                    ->label('End Date & Time (وقت الانتهاء)')
                    ->nullable(),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('duration_minutes')
                    ->required()
                    ->numeric()
                    ->default(60),
                TextInput::make('video_url')
                    ->url(),
                Textarea::make('description')
                    ->columnSpanFull(),
                Textarea::make('content')
                    ->columnSpanFull(),
                Toggle::make('is_free_demo')
                    ->label('🎓 First Session Free Trial (حصة تجريبية مجانية)')
                    ->default(false),
            ]);
    }
}
