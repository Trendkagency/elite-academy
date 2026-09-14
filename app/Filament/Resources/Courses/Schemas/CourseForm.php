<?php

namespace App\Filament\Resources\Courses\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('Course Basic Information'))
                    ->columns(2)
                    ->columnSpanFull()
                    ->components([
                        Select::make('subject_id')
                            ->label(__('Subject'))
                            ->relationship('subject', 'name', modifyQueryUsing: fn (\Illuminate\Database\Eloquent\Builder $query) => $query->latest('created_at'))
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('grade_level_id')
                            ->label(__('Grade Level'))
                            ->relationship('gradeLevel', 'name', modifyQueryUsing: fn (\Illuminate\Database\Eloquent\Builder $query) => $query->orderBy('sort_order', 'asc'))
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        Select::make('teacher_id')
                            ->label(__('Teacher'))
                            ->relationship('teacher', modifyQueryUsing: fn (\Illuminate\Database\Eloquent\Builder $query) => $query->with('user')->latest('created_at'))
                            ->getOptionLabelFromRecordUsing(fn ($record) => ($record->user?->name ?: $record->title ?: 'Teacher #' . $record->id) . ($record->specialization ? ' — ' . $record->specialization : ''))
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('title')
                            ->label(__('Course Title'))
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                        TextInput::make('slug')
                            ->label(__('Slug'))
                            ->required()
                            ->maxLength(150)
                            ->unique(ignoreRecord: true)
                            ->helperText(__('Unique URL identifier for this course.')),
                        Textarea::make('description')
                            ->label(__('Description'))
                            ->columnSpanFull(),
                        FileUpload::make('image')
                            ->label(__('Course Thumbnail Image'))
                            ->disk('public')
                            ->directory('courses')
                            ->visibility('public')
                            ->image()
                            ->imageEditor()
                            ->columnSpanFull(),
                        TextInput::make('demo_video_url')
                            ->label(__('Free Demo Video URL (MP4 / YouTube Link)'))
                            ->placeholder('https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4')
                            ->columnSpanFull(),
                    ]),

                Section::make(__('First Free Demo Session & Live Attendance Policy'))
                    ->description(__('Manage whether the 1st session of this course is a free trial demo. Students can attend the 1st live session without an active paid package.'))
                    ->columns(2)
                    ->columnSpanFull()
                    ->components([
                        Toggle::make('has_free_demo')
                            ->label(__('First Session Free Trial'))
                            ->helperText(__('When enabled, students can attend the 1st live session for free without consuming package credits.'))
                            ->default(true),

                        Toggle::make('is_active')
                            ->label(__('Active Status'))
                            ->default(true),

                        Toggle::make('is_accredited')
                            ->label(__('Accredited Course')),

                        TextInput::make('sessions_count')
                            ->label(__('Total Course Sessions'))
                            ->numeric()
                            ->default(12)
                            ->required(),

                        TextInput::make('session_duration_minutes')
                            ->label(__('Session Duration (Minutes)'))
                            ->numeric()
                            ->default(60)
                            ->required(),
                    ]),
            ]);
    }
}
