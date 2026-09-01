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
                Section::make(__('Assignment Overview & Course Linking'))
                    ->columnSpanFull()
                    ->components([
                        TextInput::make('title')
                            ->label(__('Assignment Title'))
                            ->placeholder(__('e.g. Session #3 Chemistry Quiz & Problem Set'))
                            ->required()
                            ->columnSpanFull()
                            ->live(onBlur: true)
                            ->validationMessages([
                                'required' => __('Assignment Title is required. Please enter a valid title.'),
                            ]),

                        Textarea::make('description')
                            ->label(__('Instructions & Overview'))
                            ->placeholder(__('Provide solving guidelines, formulas allowed, or preparation steps for students...'))
                            ->rows(3)
                            ->columnSpanFull(),

                        Grid::make(2)->components([
                            Select::make('course_id')
                                ->relationship(
                                    name: 'course',
                                    titleAttribute: 'title',
                                    modifyQueryUsing: function ($query, $get) {
                                        if ($teacherId = $get('teacher_profile_id')) {
                                            $query->where('teacher_id', $teacherId);
                                        }
                                        return $query;
                                    }
                                )
                                ->label(__('Target Course (Recommended)'))
                                ->searchable()
                                ->preload()
                                ->live()
                                ->afterStateUpdated(function ($state, $set, $get) {
                                    if ($state) {
                                        $course = \App\Models\Course::find($state);
                                        if ($course && $course->teacher_id) {
                                            $set('teacher_profile_id', $course->teacher_id);
                                        }
                                    }
                                })
                                ->nullable()
                                ->helperText(__('Selecting a course auto-assigns its teacher and filters available sessions.')),

                            Select::make('teacher_profile_id')
                                ->relationship(
                                    name: 'teacherProfile',
                                    titleAttribute: 'id',
                                    modifyQueryUsing: fn ($query) => $query->with('user')
                                )
                                ->getOptionLabelFromRecordUsing(fn ($record) => $record->user?->name ? "{$record->user->name} (" . ($record->specialization ?? 'Teacher') . ")" : "Teacher #{$record->id}")
                                ->label(__('Assigned Teacher / Instructor'))
                                ->searchable()
                                ->preload()
                                ->live()
                                ->afterStateUpdated(function ($state, $set, $get) {
                                    if ($state) {
                                        $currentCourseId = $get('course_id');
                                        if ($currentCourseId) {
                                            $course = \App\Models\Course::find($currentCourseId);
                                            if ($course && (int) $course->teacher_id !== (int) $state) {
                                                $set('course_id', null);
                                                $set('course_session_id', null);
                                                $set('live_session_id', null);
                                            }
                                        }
                                    }
                                })
                                ->nullable()
                                ->helperText(__('Selecting a teacher filters the courses list to only show their courses.')),

                            Select::make('course_session_id')
                                ->relationship(
                                    name: 'session',
                                    titleAttribute: 'title',
                                    modifyQueryUsing: function ($query, $get) {
                                        if ($courseId = $get('course_id')) {
                                            $query->where('course_id', $courseId);
                                        } elseif ($teacherId = $get('teacher_profile_id')) {
                                            $query->whereHas('course', fn ($q) => $q->where('teacher_id', $teacherId));
                                        }
                                        return $query;
                                    }
                                )
                                ->label(__('Course Lesson / Module (Optional)'))
                                ->searchable()
                                ->preload()
                                ->live()
                                ->afterStateUpdated(function ($state, $set) {
                                    if ($state) {
                                        $session = \App\Models\CourseSession::find($state);
                                        if ($session && $session->course_id) {
                                            $set('course_id', $session->course_id);
                                            $course = \App\Models\Course::find($session->course_id);
                                            if ($course && $course->teacher_id) {
                                                $set('teacher_profile_id', $course->teacher_id);
                                            }
                                        }
                                    }
                                })
                                ->nullable()
                                ->helperText(__('Attach directly to a recorded lesson module.')),

                            Select::make('live_session_id')
                                ->relationship(
                                    name: 'liveSession',
                                    titleAttribute: 'id',
                                    modifyQueryUsing: function ($query, $get) {
                                        if ($courseId = $get('course_id')) {
                                            $query->where('course_id', $courseId);
                                        } elseif ($teacherId = $get('teacher_profile_id')) {
                                            $query->where('teacher_profile_id', $teacherId);
                                        }
                                        return $query->with(['course', 'subject', 'studentUser', 'teacherProfile.user']);
                                    }
                                )
                                ->getOptionLabelFromRecordUsing(fn ($record) => "Live Session #{$record->id} — " . ($record->title ?? $record->course?->title ?? 'Session') . " (" . ($record->scheduled_at?->format('Y-m-d H:i') ?? 'Unscheduled') . ")")
                                ->label(__('Live Stream Session (Optional)'))
                                ->searchable()
                                ->preload()
                                ->live()
                                ->afterStateUpdated(function ($state, $set) {
                                    if ($state) {
                                        $session = \App\Models\LiveSession::find($state);
                                        if ($session) {
                                            if ($session->course_id) {
                                                $set('course_id', $session->course_id);
                                            }
                                            if ($session->teacher_profile_id) {
                                                $set('teacher_profile_id', $session->teacher_profile_id);
                                            }
                                        }
                                    }
                                })
                                ->nullable()
                                ->helperText(__('Linking makes this assignment a prerequisite for entering the live stream.')),
                        ]),
                    ]),

                Section::make(__('Evaluation Rules & Deadlines'))
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
                                ->label(__('Passing Score (%)'))
                                ->helperText(__('Minimum score to pass'))
                                ->validationMessages([
                                    'required' => __('Passing score threshold is required.'),
                                    'numeric' => __('Passing score must be a number.'),
                                    'min' => __('Passing score cannot be less than 0%.'),
                                    'max' => __('Passing score cannot exceed 100%.'),
                                ]),

                            TextInput::make('duration_minutes')
                                ->required()
                                ->numeric()
                                ->default(30)
                                ->minValue(1)
                                ->live(onBlur: true)
                                ->label(__('Exam Timer (Minutes)'))
                                ->helperText(__('Countdown duration'))
                                ->validationMessages([
                                    'required' => __('Duration in minutes is required.'),
                                    'numeric' => __('Duration must be a number.'),
                                    'min' => __('Duration must be at least 1 minute.'),
                                ]),

                            TextInput::make('max_attempts')
                                ->required()
                                ->numeric()
                                ->default(1)
                                ->minValue(1)
                                ->live(onBlur: true)
                                ->label(__('Max Attempts Allowed'))
                                ->validationMessages([
                                    'required' => __('Maximum allowed attempts is required.'),
                                    'numeric' => __('Max attempts must be a number.'),
                                    'min' => __('Max attempts must be at least 1.'),
                                ]),

                            Select::make('status')
                                ->options([
                                    'published' => __('Published / Active'),
                                    'draft' => __('Draft / Hidden'),
                                    'closed' => __('Closed / Expired'),
                                ])
                                ->default('published')
                                ->live()
                                ->required()
                                ->label(__('Publication Status')),
                        ]),

                        Grid::make(2)->components([
                            DateTimePicker::make('start_at')
                                ->live(onBlur: true)
                                ->label(__('Available From (Start Date & Time)')),

                            DateTimePicker::make('due_at')
                                ->live(onBlur: true)
                                ->label(__('Submission Deadline (Due Date & Time)')),
                        ]),

                        Toggle::make('is_mandatory')
                            ->label(__('Mandatory Prerequisite Assignment (Required to unlock live sessions)'))
                            ->live()
                            ->default(true),
                    ]),

                Section::make(__('MSQ Questions & Answer Options Builder'))
                    ->columnSpanFull()
                    ->description(__('Add interactive questions. Toggle "Is Correct Answer?" for the correct choice to enable automated instant grading.'))
                    ->components([
                        Repeater::make('questions')
                            ->relationship('questions')
                            ->orderColumn('sort_order')
                            ->schema([
                                Grid::make(3)->components([
                                    Select::make('question_type')
                                        ->options([
                                            'text' => __('Text Only'),
                                            'image' => __('Image Only'),
                                            'both' => __('Text & Image'),
                                        ])
                                        ->default('text')
                                        ->required()
                                        ->label(__('Question Type')),

                                    TextInput::make('points')
                                        ->numeric()
                                        ->default(1.00)
                                        ->required()
                                        ->minValue(0.1)
                                        ->live(onBlur: true)
                                        ->label(__('Question Points / Weight'))
                                        ->validationMessages([
                                            'required' => __('Question points value is required.'),
                                            'numeric' => __('Points must be a number.'),
                                            'min' => __('Points must be at least 0.1.'),
                                        ]),

                                    Toggle::make('is_multiple_choice')
                                        ->label(__('Allow Multiple Correct Answers (MSQ)'))
                                        ->live()
                                        ->default(false),
                                ]),

                                Textarea::make('question_text')
                                    ->label(__('Question Text / Problem Statement'))
                                    ->placeholder(__('Type the question or problem prompt here...'))
                                    ->live(onBlur: true)
                                    ->rows(2),

                                FileUpload::make('image_path')
                                    ->label(__('Question Diagram / Image (Optional)'))
                                    ->disk('public')
                                    ->directory('assignment-questions')
                                    ->visibility('public')
                                    ->image(),

                                Section::make(__('Answer Choices'))
                                    ->components([
                                        Repeater::make('options')
                                            ->relationship('options')
                                            ->orderColumn('sort_order')
                                            ->schema([
                                                Grid::make(12)->components([
                                                    TextInput::make('option_text')
                                                        ->label(__('Option Text'))
                                                        ->placeholder(__('e.g. Option choice answer...'))
                                                        ->columnSpan(8)
                                                        ->required()
                                                        ->live(onBlur: true)
                                                        ->validationMessages([
                                                            'required' => __('Option text is required.'),
                                                        ]),

                                                    Toggle::make('is_correct')
                                                        ->label(__('Correct Answer? ✅'))
                                                        ->columnSpan(4)
                                                        ->inline(false),
                                                ]),

                                                FileUpload::make('image_path')
                                                    ->label(__('Choice Image (Optional)'))
                                                    ->disk('public')
                                                    ->directory('assignment-options')
                                                    ->visibility('public')
                                                    ->image(),
                                            ])
                                            ->minItems(2)
                                            ->defaultItems(4)
                                            ->collapsible(),
                                    ]),
                            ])
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['question_text'] ?? __('Question')),
                    ]),
            ]);
    }
}
