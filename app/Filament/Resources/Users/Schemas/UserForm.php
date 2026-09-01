<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\GradeLevel;
use App\Models\Subject;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('User Credentials & Account Role'))
                    ->description(__('Configure account authentication details, approval status, and assigned portal role'))
                    ->columns(2)
                    ->columnSpanFull()
                    ->components([
                        TextInput::make('name')
                            ->label(__('Full Name'))
                            ->placeholder('e.g. Ahmed Ali')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label(__('Email Address'))
                            ->email()
                            ->placeholder('user@elite-academy.com')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->label(__('Phone Number'))
                            ->tel()
                            ->placeholder('+201000000000')
                            ->maxLength(30),

                        Select::make('assigned_role')
                            ->label(__('Assigned System Role & Portal Access'))
                            ->options([
                                'student' => __('Student (Student Portal Access)'),
                                'parent'  => __('Parent (Parent Portal Access)'),
                                'teacher' => __('Teacher (Teacher Portal Access)'),
                                'admin'   => __('Admin (Full Panel Access)'),
                            ])
                            ->default('student')
                            ->required()
                            ->live()
                            ->helperText(__('Determines portal access. Relevant profile fields will open dynamically below.'))
                            ->afterStateHydrated(function ($component, ?Model $record) {
                                if ($record) {
                                    $component->state($record->getRoleName());
                                }
                            })
                            ->dehydrated(true),

                        Select::make('status')
                            ->label(__('Account Approval Status'))
                            ->options([
                                'approved' => __('Approved'),
                                'pending'  => __('Pending Approval'),
                                'rejected' => __('Rejected'),
                                'suspended'=> __('Suspended'),
                            ])
                            ->default('approved')
                            ->required(),

                        TextInput::make('password')
                            ->label(__('Password'))
                            ->password()
                            ->revealable()
                            ->required(fn (string $operation) => $operation === 'create')
                            ->dehydrated(fn ($state) => filled($state))
                            ->helperText(fn (string $operation) => $operation === 'edit' ? __('Leave blank to keep current password.') : __('Default initial password.'))
                            ->default('Password123!'),

                        DateTimePicker::make('email_verified_at')
                            ->label(__('Email Verified At'))
                            ->default(now()),
                    ]),

                // 1. Student Academic Profile Fields (Only visible when role is student)
                Section::make(__('Student Academic Profile & Grade Level'))
                    ->description(__('Specify student grade level, school name, and enrolled subject courses'))
                    ->columns(2)
                    ->columnSpanFull()
                    ->visible(fn ($get) => $get('assigned_role') === 'student')
                    ->components([
                        Select::make('grade_level_id')
                            ->label(__('Grade Level'))
                            ->options(fn () => GradeLevel::orderBy('sort_order')->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->placeholder(__('Select Grade Level...'))
                            ->afterStateHydrated(function ($component, ?Model $record) {
                                if ($record && $record->studentProfile) {
                                    $component->state($record->studentProfile->grade_level_id);
                                }
                            })
                            ->dehydrated(true),

                        TextInput::make('school_name')
                            ->label(__('School Name'))
                            ->placeholder('e.g. Cairo International School')
                            ->afterStateHydrated(function ($component, ?Model $record) {
                                if ($record && $record->studentProfile) {
                                    $component->state($record->studentProfile->school_name);
                                }
                            })
                            ->dehydrated(true),

                        Select::make('student_subjects')
                            ->label(__('Enrolled Subjects'))
                            ->options(fn () => Subject::orderBy('sort_order')->pluck('name', 'id'))
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->afterStateHydrated(function ($component, ?Model $record) {
                                if ($record && $record->studentProfile) {
                                    $component->state($record->studentProfile->subjects()->pluck('subjects.id')->toArray());
                                }
                            })
                            ->dehydrated(true)
                            ->columnSpanFull(),
                    ]),

                // 2. Teacher Professional Profile Fields (Only visible when role is teacher)
                Section::make(__('Teacher Professional Profile'))
                    ->description(__('Configure instructor academic title, subject specialization, and experience'))
                    ->columns(2)
                    ->columnSpanFull()
                    ->visible(fn ($get) => $get('assigned_role') === 'teacher')
                    ->components([
                        TextInput::make('teacher_title')
                            ->label(__('Professional Title'))
                            ->placeholder('e.g. Senior Physics Lecturer / دكتور الفيزياء التطبيقية')
                            ->afterStateHydrated(function ($component, ?Model $record) {
                                if ($record && $record->teacherProfile) {
                                    $component->state($record->teacherProfile->title);
                                }
                            })
                            ->dehydrated(true),

                        TextInput::make('teacher_specialization')
                            ->label(__('Specialization Focus'))
                            ->placeholder('e.g. Advanced Physics & Mechanics')
                            ->afterStateHydrated(function ($component, ?Model $record) {
                                if ($record && $record->teacherProfile) {
                                    $component->state($record->teacherProfile->specialization);
                                }
                            })
                            ->dehydrated(true),

                        TextInput::make('teacher_experience')
                            ->label(__('Years of Experience'))
                            ->numeric()
                            ->default(5)
                            ->afterStateHydrated(function ($component, ?Model $record) {
                                if ($record && $record->teacherProfile) {
                                    $component->state($record->teacherProfile->years_experience);
                                }
                            })
                            ->dehydrated(true),
                    ]),

                // 3. Parent Profile Fields (Only visible when role is parent)
                Section::make(__('Parent Profile & Linked Children'))
                    ->description(__('Link registered students to this parent account for academic monitoring'))
                    ->columns(1)
                    ->columnSpanFull()
                    ->visible(fn ($get) => $get('assigned_role') === 'parent')
                    ->components([
                        Select::make('parent_students')
                            ->label(__('Linked Children / Students'))
                            ->options(fn () => User::whereHas('studentProfile')->pluck('name', 'id'))
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->afterStateHydrated(function ($component, ?Model $record) {
                                if ($record && $record->parentProfile) {
                                    $component->state($record->parentProfile->students()->pluck('users.id')->toArray());
                                }
                            })
                            ->dehydrated(true),
                    ]),
            ]);
    }
}

