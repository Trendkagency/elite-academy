<?php

namespace App\Filament\Resources\TeacherProfiles\Schemas;

use App\Models\TeacherProfile;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TeacherProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('👤 Teacher Account & Public Identity')
                    ->description('Link or create a user account and configure public visibility')
                    ->columns(2)
                    ->columnSpanFull()
                    ->components([
                        Select::make('user_id')
                            ->relationship(
                                name: 'user',
                                modifyQueryUsing: function (Builder $query, ?Model $record, ?string $operation) {
                                    return $query->where(function (Builder $q) use ($record, $operation) {
                                        // Exclude Admins, Students, and Parents
                                        $q->whereDoesntHave('adminProfile')
                                          ->whereNotIn('email', ['admin@elite-academy.com', 'admin@elite.edu'])
                                          ->whereDoesntHave('studentProfile')
                                          ->whereDoesntHave('parentProfile');

                                        if ($operation === 'edit' && $record?->user_id) {
                                            $q->where(function ($subQ) use ($record) {
                                                $subQ->whereDoesntHave('teacherProfile')
                                                     ->orWhere('id', $record->user_id);
                                            });
                                        } else {
                                            $q->whereDoesntHave('teacherProfile');
                                        }
                                    });
                                }
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} ({$record->email})" . ($record->phone ? " — {$record->phone}" : ''))
                            ->label('Teacher Account User')
                            ->helperText('Select an existing teacher account, or click (+) to create a new teacher account directly.')
                            ->searchable(['name', 'email', 'phone'])
                            ->preload()
                            ->required()
                            ->unique(TeacherProfile::class, 'user_id', ignoreRecord: true)
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Full Name')
                                    ->placeholder('e.g. Dr. Ahmed Mahmoud')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('email')
                                    ->label('Email Address')
                                    ->email()
                                    ->placeholder('teacher@elite-academy.com')
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
                                    ->helperText('Default temporary password. The teacher can change it later.')
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
                                ->modalHeading('Create & Link New Teacher Account')
                                ->modalDescription('Fill in the credentials to immediately register a new user account and attach it to this Teacher Profile.')
                                ->modalSubmitActionLabel('Create Account')
                                ->modalWidth('lg')
                            )
                            ->live()
                            ->afterStateUpdated(function (string $operation, $state, callable $set) {
                                if ($operation === 'create' && $state) {
                                    $user = User::find($state);
                                    $nameSlug = $user ? Str::slug($user->name) : 'teacher';
                                    $set('slug', $nameSlug ? "{$nameSlug}-{$state}" : "teacher-{$state}");
                                }
                            }),

                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->helperText('Unique URL slug for teacher public profile page.'),

                        Toggle::make('is_featured')
                            ->label('Feature on Homepage & Top Banner')
                            ->default(true),

                        Toggle::make('is_public')
                            ->label('Publicly Visible on Website')
                            ->default(true),

                        Toggle::make('show_contact_info')
                            ->label('Show Contact Details on Profile')
                            ->default(false),
                    ]),

                Section::make('🎓 Professional Details & Bio')
                    ->description('Academic title, specialization, rating, and biographical overview')
                    ->columns(2)
                    ->columnSpanFull()
                    ->components([
                        TextInput::make('title')
                            ->label('Professional Title')
                            ->placeholder('e.g. Senior Physics Lecturer / دكتور الفيزياء التطبيقية')
                            ->required(),

                        TextInput::make('specialization')
                            ->label('Specialization / Subject Focus')
                            ->placeholder('e.g. Advanced Physics & Mechanics')
                            ->required(),

                        TextInput::make('years_experience')
                            ->numeric()
                            ->default(5)
                            ->label('Years of Experience'),

                        TextInput::make('rating_avg')
                            ->required()
                            ->numeric()
                            ->default(4.9)
                            ->label('Rating (out of 5.0)'),

                        TextInput::make('students_count')
                            ->required()
                            ->numeric()
                            ->default(100)
                            ->label('Enrolled Students Count'),

                        Textarea::make('bio')
                            ->label('Biography & Academic Achievements')
                            ->placeholder('Overview of qualifications, teaching methodology, and accomplishments...')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),

                Section::make('📸 Teacher Photo & Spatie Media')
                    ->description('Upload instructor profile picture')
                    ->columnSpanFull()
                    ->components([
                        SpatieMediaLibraryFileUpload::make('photo')
                            ->collection('photo')
                            ->disk('public')
                            ->visibility('public')
                            ->image()
                            ->imageEditor()
                            ->label('Teacher Photo Upload (Spatie Media)'),
                    ]),
            ]);
    }
}
