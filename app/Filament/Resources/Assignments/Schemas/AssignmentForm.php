<?php

namespace App\Filament\Resources\Assignments\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AssignmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Assignment Overview & Session Linking')
                    ->columnSpanFull()
                    ->components([
                        TextInput::make('title')
                            ->label('Assignment Title')
                            ->placeholder('e.g. Session #3 Chemistry Quiz')
                            ->required()
                            ->columnSpanFull()
                            ->live(onBlur: true)
                            ->validationMessages([
                                'required' => 'Assignment Title is required. Please enter a valid title.',
                            ]),
                        Textarea::make('description')
                            ->label('Instructions & Overview')
                            ->rows(3)
                            ->columnSpanFull(),
                        Grid::make(2)->components([
                            Select::make('live_session_id')
                                ->relationship(
                                    name: 'liveSession',
                                    titleAttribute: 'id',
                                    modifyQueryUsing: fn ($query) => $query->with(['course', 'subject', 'studentUser', 'teacherProfile.user'])
                                )
                                ->getOptionLabelFromRecordUsing(fn ($record) => "Session #{$record->id} — " . ($record->course?->title ?? $record->subject?->name ?? 'Live Session') . " (" . ($record->scheduled_at?->format('Y-m-d H:i') ?? 'Unscheduled') . ")")
                                ->label('Live Session (Optional)')
                                ->searchable()
                                ->preload()
                                ->nullable(),
                            Select::make('course_id')
                                ->relationship('course', 'title')
                                ->label('Target Course (Optional)')
                                ->searchable()
                                ->preload()
                                ->nullable(),
                            Select::make('teacher_profile_id')
                                ->relationship(
                                    name: 'teacherProfile',
                                    titleAttribute: 'id',
                                    modifyQueryUsing: fn ($query) => $query->with('user')
                                )
                                ->getOptionLabelFromRecordUsing(fn ($record) => $record->user?->name ? "{$record->user->name} (" . ($record->specialization ?? 'Teacher') . ")" : "Teacher #{$record->id}")
                                ->label('Assigned Teacher (Optional)')
                                ->searchable()
                                ->preload()
                                ->nullable(),
                            Select::make('course_session_id')
                                ->relationship('session', 'title')
                                ->label('Course Session / Lesson (Optional)')
                                ->searchable()
                                ->preload()
                                ->nullable(),
                        ]),
                    ]),

                Section::make('Rules & Passing Thresholds')
                    ->columnSpanFull()
                    ->components([
                        Grid::make(4)->components([
                            TextInput::make('passing_score')
                                ->required()
                                ->numeric()
                                ->default(70.00)
                                ->minValue(0)
                                ->maxValue(100)
                                ->live(onBlur: true)
                                ->label('Passing Score (%)')
                                ->validationMessages([
                                    'required' => 'Passing score threshold is required.',
                                    'numeric' => 'Passing score must be a number.',
                                    'min' => 'Passing score cannot be less than 0%.',
                                    'max' => 'Passing score cannot exceed 100%.',
                                ]),
                            TextInput::make('duration_minutes')
                                ->required()
                                ->numeric()
                                ->default(30)
                                ->minValue(1)
                                ->live(onBlur: true)
                                ->label('Duration (Minutes)')
                                ->validationMessages([
                                    'required' => 'Duration in minutes is required.',
                                    'numeric' => 'Duration must be a number.',
                                    'min' => 'Duration must be at least 1 minute.',
                                ]),
                            TextInput::make('max_attempts')
                                ->required()
                                ->numeric()
                                ->default(1)
                                ->minValue(1)
                                ->live(onBlur: true)
                                ->label('Max Attempts Allowed')
                                ->validationMessages([
                                    'required' => 'Maximum allowed attempts is required.',
                                    'numeric' => 'Max attempts must be a number.',
                                    'min' => 'Max attempts must be at least 1.',
                                ]),
                            Select::make('status')
                                ->options([
                                    'published' => 'Published / Active',
                                    'draft' => 'Draft',
                                    'closed' => 'Closed',
                                ])
                                ->default('published')
                                ->live()
                                ->required(),
                        ]),
                        Grid::make(2)->components([
                            DateTimePicker::make('start_at')
                                ->live(onBlur: true)
                                ->label('Available From (Start Time)'),
                            DateTimePicker::make('due_at')
                                ->live(onBlur: true)
                                ->label('Deadline (Due Date & Time)'),
                        ]),
                        Toggle::make('is_mandatory')
                            ->label('Mandatory Assignment (Prerequisite for Next Session)')
                            ->live()
                            ->default(true),
                    ]),

                Section::make('MSQ Questions & Answer Options Builder')
                    ->columnSpanFull()
                    ->description('Create multiple choice / multiple select questions. Mark the correct option(s) for automated server-side evaluation.')
                    ->components([
                        Repeater::make('questions')
                            ->relationship('questions')
                            ->orderColumn('sort_order')
                            ->schema([
                                Grid::make(3)->components([
                                    Select::make('question_type')
                                        ->options([
                                            'text' => 'Text Only',
                                            'image' => 'Image Only',
                                            'both' => 'Text & Image',
                                        ])
                                        ->default('text')
                                        ->required(),
                                    TextInput::make('points')
                                        ->numeric()
                                        ->default(1.00)
                                        ->required()
                                        ->minValue(0.1)
                                        ->live(onBlur: true)
                                        ->label('Question Points')
                                        ->validationMessages([
                                            'required' => 'Question points value is required.',
                                            'numeric' => 'Points must be a number.',
                                            'min' => 'Points must be at least 0.1.',
                                        ]),
                                    Toggle::make('is_multiple_choice')
                                        ->label('Allow Multiple Correct Options (MSQ)')
                                        ->live()
                                        ->default(false),
                                ]),
                                Textarea::make('question_text')
                                    ->label('Question Text')
                                    ->live(onBlur: true)
                                    ->rows(2),
                                FileUpload::make('image_path')
                                    ->label('Question Image (Optional)')
                                    ->image()
                                    ->directory('assignment-questions'),

                                Section::make('Answer Options')
                                    ->components([
                                        Repeater::make('options')
                                            ->relationship('options')
                                            ->orderColumn('sort_order')
                                            ->schema([
                                                Grid::make(12)->components([
                                                    TextInput::make('option_text')
                                                        ->label('Option Text')
                                                        ->columnSpan(8)
                                                        ->required()
                                                        ->live(onBlur: true)
                                                        ->validationMessages([
                                                            'required' => 'Option text is required.',
                                                        ]),
                                                    Toggle::make('is_correct')
                                                        ->label('Is Correct Answer?')
                                                        ->columnSpan(4)
                                                        ->inline(false),
                                                ]),
                                                FileUpload::make('image_path')
                                                    ->label('Option Image (Optional)')
                                                    ->image()
                                                    ->directory('assignment-options'),
                                            ])
                                            ->minItems(2)
                                            ->defaultItems(4)
                                            ->collapsible(),
                                    ]),
                            ])
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['question_text'] ?? 'Question'),
                    ]),
            ]);
    }
}
