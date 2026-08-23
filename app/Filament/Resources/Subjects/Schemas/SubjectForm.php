<?php

namespace App\Filament\Resources\Subjects\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SubjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                FileUpload::make('image')
                    ->label(__('Subject Cover Image (Drag & Drop)'))
                    ->disk('public')
                    ->directory('subjects')
                    ->visibility('public')
                    ->image()
                    ->imageEditor(),
                TextInput::make('rating_avg')
                    ->label(__('Student Rating Override'))
                    ->numeric()
                    ->step('0.1')
                    ->minValue(0)
                    ->maxValue(5)
                    ->placeholder('e.g. 4.9')
                    ->helperText(__('Leave blank to calculate dynamically from courses & teachers.')),
                TextInput::make('students_count')
                    ->label(__('Active Students Override'))
                    ->numeric()
                    ->minValue(0)
                    ->placeholder('e.g. 2450')
                    ->helperText(__('Leave blank to calculate dynamically from enrollments & teachers.')),
                TextInput::make('video_lessons_count')
                    ->label(__('Video Lessons Override'))
                    ->numeric()
                    ->minValue(0)
                    ->placeholder('e.g. 48')
                    ->helperText(__('Leave blank to calculate dynamically from course sessions.')),
                TextInput::make('active_courses_count')
                    ->label(__('Active Courses Override'))
                    ->numeric()
                    ->minValue(0)
                    ->placeholder('e.g. 2')
                    ->helperText(__('Leave blank to calculate dynamically from active courses.')),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
