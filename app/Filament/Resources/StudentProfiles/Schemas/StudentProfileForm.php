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
                Section::make(__('Student User Account Credentials & Status'))
                    ->icon('heroicon-o-user')
                    ->description(__('Manage student user profile, account status, email, and phone credentials'))
                    ->columns(2)
                    ->columnSpanFull()
                    ->components([
                        Select::make('user_id')
                            ->relationship(
                                name: 'user',
                                modifyQueryUsing: function (Builder $query, ?Model $record, ?string $operation) {
                                    return $query->where(function (Builder $q) use ($record, $operation) {
                                        // Exclude Admins, Teachers, and Parents
                                        $q->whereDoesntHave('adminProfile')
                                          ->whereNotIn('email', ['admin@elite-academy.com', 'admin@elite.edu'])
                                          ->whereDoesntHave('teacherProfile')
                                          ->whereDoesntHave('parentProfile');

                                        if ($operation === 'edit' && $record?->user_id) {
                                            $q->where(function ($subQ) use ($record) {
                                                $subQ->whereDoesntHave('studentProfile')
                                                     ->orWhere('id', $record->user_id);
                                            });
                                        } else {
                                            $q->whereDoesntHave('studentProfile');
                                        }
                                    });
                                }
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} ({$record->email})" . ($record->phone ? " — {$record->phone}" : ''))
                            ->label(__('Student Account User'))
                            ->helperText(__('Select an existing student account, or click (+) to register a new student directly.'))
                            ->searchable(['name', 'email', 'phone'])
                            ->preload()
                            ->required()
                            ->unique(StudentProfile::class, 'user_id', ignoreRecord: true)
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label(__('Student Full Name'))
                                    ->placeholder('e.g. Youssef Ahmed')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('email')
                                    ->label(__('Email Address'))
                                    ->email()
                                    ->placeholder('student@elite-academy.com')
                                    ->required()
                                    ->unique(User::class, 'email')
                                    ->maxLength(255),
                                TextInput::make('phone')
                                    ->label(__('Phone Number'))
                                    ->tel()
                                    ->placeholder('+201000000000')
                                    ->unique(User::class, 'phone')
                                    ->maxLength(30),
                                TextInput::make('password')
                                    ->label(__('Account Password'))
                                    ->password()
                                    ->revealable()
                                    ->default('Password123!')
                                    ->helperText(__('Default temporary password. The student can change it later.'))
                                    ->required()
                                    ->maxLength(255),
                                Select::make('status')
                                    ->label(__('Account Approval Status'))
                                    ->options([
                                        'approved' => __('Approved'),
                                        'pending' => __('Pending Approval'),
                                    ])
                                    ->default('approved')
                                    ->required(),
                            ])
                            ->createOptionAction(fn ($action) => $action
                                ->modalHeading(__('Register & Link New Student Account'))
                                ->modalDescription(__('Create a new student user account and instantly attach it to this academic profile.'))
                                ->modalSubmitActionLabel(__('Create Account'))
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
                            ->label(__('Account Approval Status'))
                            ->options([
                                'pending' => __('Pending Approval'),
                                'approved' => __('Approved'),
                                'rejected' => __('Rejected'),
                                'suspended' => __('Suspended'),
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

                Section::make(__('Academic Profile & School Metadata'))
                    ->icon('heroicon-o-academic-cap')
                    ->description(__('Manage grade level, school name, enrolled subjects, and free trial session flag'))
                    ->columns(2)
                    ->columnSpanFull()
                    ->components([
                        Select::make('grade_level_id')
                            ->relationship('gradeLevel', 'name')
                            ->label(__('Grade Level'))
                            ->searchable()
                            ->preload(),

                        TextInput::make('school_name')
                            ->label(__('School Name'))
                            ->placeholder('e.g. Al-Bayan International School'),

                        Select::make('subjects')
                            ->relationship('subjects', 'name')
                            ->label(__('Enrolled Subjects'))
                            ->multiple()
                            ->preload()
                            ->searchable(),

                        DatePicker::make('date_of_birth')
                            ->label(__('Date of Birth')),

                        Toggle::make('has_used_free_session')
                            ->label(__('Used Free Trial Session'))
                            ->helperText(__('Flag indicating if student consumed their free trial session credit.'))
                            ->columnSpanFull(),
                    ]),

                Section::make(__('Student Avatar & Profile Picture'))
                    ->icon('heroicon-o-camera')
                    ->description(__('Upload or change student profile picture'))
                    ->columnSpanFull()
                    ->components([
                        \Filament\Forms\Components\FileUpload::make('avatar')
                            ->label(__('Student Profile Photo'))
                            ->disk('public')
                            ->directory('avatars')
                            ->visibility('public')
                            ->image()
                            ->imageEditor()
                            ->avatar()
                            ->circleCropper()
                            ->helperText(__('Upload a square portrait photo for the student profile (JPG, PNG, WebP).'))
                            ->columnSpanFull(),
                    ]),

                Section::make(__('360° Student Academic Overview, Packages & Submissions'))
                    ->icon('heroicon-o-chart-bar')
                    ->description(__('Real-time overview of student active session package credits, linked parents, enrolled courses, and homework submissions'))
                    ->columnSpanFull()
                    ->visible(fn (string $operation) => $operation === 'edit')
                    ->components([
                        View::make('filament.resources.student-profile.academic-overview'),
                    ]),
            ]);
    }
}
