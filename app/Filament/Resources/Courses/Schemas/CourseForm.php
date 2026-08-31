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
                Section::make('📚 Course Basic Information')
                    ->columns(2)
                    ->columnSpanFull()
                    ->components([
                        Select::make('subject_id')
                            ->relationship('subject', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('grade_level_id')
                            ->relationship('gradeLevel', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        Select::make('teacher_id')
                            ->relationship('teacher')
                            ->getOptionLabelFromRecordUsing(fn ($record) => ($record->user?->name ?: $record->title ?: 'Teacher #' . $record->id) . ($record->specialization ? ' — ' . $record->specialization : ''))
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('title')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                        TextInput::make('slug')
                            ->required(),
                        Textarea::make('description')
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

                Section::make('🎓 First Free Demo Session & Live Attendance Policy')
                    ->description('Manage whether the 1st session of this course is a free trial demo. Students can attend the 1st live session without an active paid package.')
                    ->columns(2)
                    ->columnSpanFull()
                    ->components([
                        Toggle::make('has_free_demo')
                            ->label('🎓 First Session Free Trial (حصّة أولى مجانيّة)')
                            ->helperText('When enabled, students can attend the 1st live session for free without consuming package credits.')
                            ->default(true),

                        Toggle::make('is_active')
                            ->label('Active Status (مُفعل)')
                            ->default(true),

                        Toggle::make('is_accredited')
                            ->label('Accredited Course (معتمد)'),

                        TextInput::make('sessions_count')
                            ->label('Total Course Sessions')
                            ->numeric()
                            ->default(12)
                            ->required(),

                        TextInput::make('session_duration_minutes')
                            ->label('Session Duration (Minutes)')
                            ->numeric()
                            ->default(60)
                            ->required(),
                    ]),
            ]);
    }
}
