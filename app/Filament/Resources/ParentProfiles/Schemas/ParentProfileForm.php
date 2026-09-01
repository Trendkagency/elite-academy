<?php

namespace App\Filament\Resources\ParentProfiles\Schemas;

use App\Models\ParentProfile;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ParentProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Parent Account Information')
                    ->description('Link parent account with registered student children')
                    ->columnSpanFull()
                    ->components([
                        Select::make('user_id')
                            ->relationship(
                                name: 'user',
                                modifyQueryUsing: function (Builder $query, ?Model $record, ?string $operation) {
                                    return $query->where(function (Builder $q) use ($record, $operation) {
                                        if ($operation === 'edit' && $record?->user_id) {
                                            $q->whereDoesntHave('parentProfile')->orWhere('id', $record->user_id);
                                        } else {
                                            $q->whereDoesntHave('parentProfile');
                                        }
                                    });
                                }
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} ({$record->email})" . ($record->phone ? " — {$record->phone}" : ''))
                            ->label('Parent User Account')
                            ->helperText('Select an existing account without a parent profile, or click (+) to register a new parent directly.')
                            ->searchable(['name', 'email', 'phone'])
                            ->preload()
                            ->required()
                            ->unique(ParentProfile::class, 'user_id', ignoreRecord: true)
                            ->createOptionForm([
                                \Filament\Forms\Components\TextInput::make('name')
                                    ->label('Parent Full Name')
                                    ->placeholder('e.g. Mahmoud Ali')
                                    ->required()
                                    ->maxLength(255),
                                \Filament\Forms\Components\TextInput::make('email')
                                    ->label('Email Address')
                                    ->email()
                                    ->placeholder('parent@elite-academy.com')
                                    ->required()
                                    ->unique(\App\Models\User::class, 'email')
                                    ->maxLength(255),
                                \Filament\Forms\Components\TextInput::make('phone')
                                    ->label('Phone Number')
                                    ->tel()
                                    ->placeholder('+201000000000')
                                    ->unique(\App\Models\User::class, 'phone')
                                    ->maxLength(30),
                                \Filament\Forms\Components\TextInput::make('password')
                                    ->label('Account Password')
                                    ->password()
                                    ->revealable()
                                    ->default('Password123!')
                                    ->helperText('Default temporary password. The parent can change it later.')
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
                                ->modalHeading('Register & Link New Parent Account')
                                ->modalDescription('Create a new parent user account and attach it to this parent profile.')
                                ->modalSubmitActionLabel('Create Account')
                                ->modalWidth('lg')
                            ),
                        Select::make('students')
                            ->relationship(
                                name: 'students',
                                modifyQueryUsing: fn (Builder $query) => $query->whereHas('studentProfile')
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} ({$record->email})" . ($record->phone ? " — {$record->phone}" : ''))
                            ->label('Linked Children / Students (Select & Manage)')
                            ->multiple()
                            ->preload()
                            ->searchable(['name', 'email', 'phone']),
                    ]),

                Section::make('Linked Children Detailed Academic Overview (تفاصيل أبناء ولي الأمر)')
                    ->description('View detailed academic status, active package credits, and grade level for each linked child with interactive visual cards')
                    ->columnSpanFull()
                    ->visible(fn (string $operation) => $operation === 'edit')
                    ->components([
                        View::make('filament.resources.parent-profile.children-details'),
                    ]),
            ]);
    }
}
