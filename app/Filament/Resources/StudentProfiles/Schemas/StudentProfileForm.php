<?php

namespace App\Filament\Resources\StudentProfiles\Schemas;

use App\Models\StudentProfile;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class StudentProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('👤 Student User Account Credentials & Status')
                    ->description('Manage student user profile, account status, email, and phone credentials')
                    ->columns(2)
                    ->columnSpanFull()
                    ->components([
                        Select::make('user_id')
                            ->relationship(
                                name: 'user',
                                modifyQueryUsing: function (Builder $query, ?Model $record, ?string $operation) {
                                    return $query->where(function (Builder $q) use ($record, $operation) {
                                        if ($operation === 'edit' && $record?->user_id) {
                                            $q->whereDoesntHave('studentProfile')->orWhere('id', $record->user_id);
                                        } else {
                                            $q->whereDoesntHave('studentProfile');
                                        }
                                    });
                                }
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} ({$record->email})" . ($record->phone ? " — {$record->phone}" : ''))
                            ->label('Student Account User')
                            ->helperText('Select an existing account without a student profile, or click (+) to register a new student directly.')
                            ->searchable(['name', 'email', 'phone'])
                            ->preload()
                            ->required()
                            ->unique(StudentProfile::class, 'user_id', ignoreRecord: true)
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Student Full Name')
                                    ->placeholder('e.g. Youssef Ahmed')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('email')
                                    ->label('Email Address')
                                    ->email()
                                    ->placeholder('student@elite-academy.com')
                                    ->required()
                                    ->unique(User::class, 'email')
                                    ->maxLength(255),
                                TextInput::make('phone')
                                    ->label('Phone Number')
                                    ->tel()
                                    ->placeholder('+201000000000')
                                    ->unique(User::class, 'phone')
                                    ->maxLength(30),
                                TextInput::make('password')
                                    ->label('Account Password')
                                    ->password()
                                    ->revealable()
                                    ->default('Password123!')
                                    ->helperText('Default temporary password. The student can change it later.')
                                    ->required()
                                    ->maxLength(255),
                                Select::make('status')
                                    ->label('Account Approval Status')
                                    ->options([
                                        'approved' => '✅ Approved (مقبول)',
                                        'pending' => '⏳ Pending Approval (قيد المراجعة)',
                                    ])
                                    ->default('approved')
                                    ->required(),
                            ])
                            ->createOptionAction(fn ($action) => $action
                                ->modalHeading('Register & Link New Student Account')
                                ->modalDescription('Create a new student user account and instantly attach it to this academic profile.')
                                ->modalSubmitActionLabel('Create Account')
                                ->modalWidth('lg')
                            )
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $user = User::find($state);
                                    if ($user) {
                                        $set('user_status', $user->status?->value ?? (string) $user->status);
                                    }
                                }
                            }),

                        Select::make('user_status')
                            ->label('Account Approval Status')
                            ->options([
                                'pending' => '⏳ Pending Approval (قيد المراجعة)',
                                'approved' => '✅ Approved (مقبول)',
                                'rejected' => '❌ Rejected (مرفوض)',
                                'suspended' => '🚫 Suspended (معلق)',
                            ])
                            ->default('approved')
                            ->required()
                            ->afterStateHydrated(function ($component, ?Model $record) {
                                if ($record && $record->user) {
                                    $component->state($record->user->status?->value ?? (string) $record->user->status);
                                }
                            })
                            ->dehydrated(false),
                    ]),

                Section::make('🏫 Academic Profile & School Metadata')
                    ->description('Manage grade level, school name, enrolled subjects, and free trial session flag')
                    ->columns(2)
                    ->columnSpanFull()
                    ->components([
                        Select::make('grade_level_id')
                            ->relationship('gradeLevel', 'name')
                            ->label('Grade Level')
                            ->searchable()
                            ->preload(),

                        TextInput::make('school_name')
                            ->label('School Name')
                            ->placeholder('e.g. Al-Bayan International School'),

                        Select::make('subjects')
                            ->relationship('subjects', 'name')
                            ->label('Enrolled Subjects')
                            ->multiple()
                            ->preload()
                            ->searchable(),

                        DatePicker::make('date_of_birth')
                            ->label('Date of Birth'),

                        Toggle::make('has_used_free_session')
                            ->label('Used Free Trial Session')
                            ->helperText('Flag indicating if student consumed their free trial session credit.')
                            ->columnSpanFull(),
                    ]),

                Section::make('📊 360° Student Academic Overview, Packages & Submissions')
                    ->description('Real-time overview of student active session package credits, linked parents, enrolled courses, and homework submissions')
                    ->columnSpanFull()
                    ->visible(fn (string $operation) => $operation === 'edit')
                    ->components([
                        View::make('filament.resources.student-profile.academic-overview'),
                    ]),
            ]);
    }
}
