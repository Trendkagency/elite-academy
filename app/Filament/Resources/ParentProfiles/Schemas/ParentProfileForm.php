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
                Section::make(__('Parent Account Information'))
                    ->description(__('Link parent account with registered student children'))
                    ->columnSpanFull()
                    ->components([
                        Select::make('user_id')
                            ->relationship(
                                name: 'user',
                                modifyQueryUsing: function (Builder $query, ?Model $record, ?string $operation) {
                                    return $query->where(function (Builder $q) use ($record, $operation) {
                                        // Exclude Admins, Teachers, and Students
                                        $q->whereDoesntHave('adminProfile')
                                          ->whereNotIn('email', ['admin@elite-academy.com', 'admin@elite.edu'])
                                          ->whereDoesntHave('teacherProfile')
                                          ->whereDoesntHave('studentProfile');

                                        if ($operation === 'edit' && $record?->user_id) {
                                            $q->where(function ($subQ) use ($record) {
                                                $subQ->whereDoesntHave('parentProfile')
                                                     ->orWhere('id', $record->user_id);
                                            });
                                        } else {
                                            $q->whereDoesntHave('parentProfile');
                                        }
                                    })->latest('created_at');
                                }
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} ({$record->email})" . ($record->phone ? " — {$record->phone}" : ''))
                            ->label(__('Parent User Account'))
                            ->helperText(__('Select an existing parent account, or click (+) to register a new parent directly.'))
                            ->searchable(['name', 'email', 'phone'])
                            ->preload()
                            ->required()
                            ->unique(ParentProfile::class, 'user_id', ignoreRecord: true)
                            ->createOptionForm([
                                \Filament\Forms\Components\TextInput::make('name')
                                    ->label(__('Parent Full Name'))
                                    ->placeholder(__('e.g. Mahmoud Ali'))
                                    ->required()
                                    ->maxLength(255),
                                \Filament\Forms\Components\TextInput::make('email')
                                    ->label(__('Email Address'))
                                    ->email()
                                    ->placeholder('parent@elite-academy.com')
                                    ->required()
                                    ->unique(\App\Models\User::class, 'email')
                                    ->maxLength(255),
                                \Filament\Forms\Components\TextInput::make('phone')
                                    ->label(__('Phone Number'))
                                    ->tel()
                                    ->placeholder('+201000000000')
                                    ->unique(\App\Models\User::class, 'phone')
                                    ->maxLength(30),
                                \Filament\Forms\Components\TextInput::make('password')
                                    ->label(__('Account Password'))
                                    ->password()
                                    ->revealable()
                                    ->default('Password123!')
                                    ->helperText(__('Default temporary password. The parent can change it later.'))
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
                                ->modalHeading(__('Register & Link New Parent Account'))
                                ->modalDescription(__('Create a new parent user account and attach it to this parent profile.'))
                                ->modalSubmitActionLabel(__('Create Account'))
                                ->modalWidth('lg')
                            ),
                        Select::make('students')
                            ->relationship(
                                name: 'students',
                                modifyQueryUsing: fn (Builder $query) => $query->whereHas('studentProfile')
                                    ->whereDoesntHave('teacherProfile')
                                    ->whereDoesntHave('adminProfile')
                                    ->whereNotIn('email', ['admin@elite-academy.com', 'admin@elite.edu'])
                                    ->latest('created_at')
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} ({$record->email})" . ($record->phone ? " — {$record->phone}" : ''))
                            ->label(__('Linked Children / Students (Select & Manage)'))
                            ->multiple()
                            ->preload()
                            ->searchable(['name', 'email', 'phone']),
                    ]),

                Section::make(__('Linked Children Detailed Academic Overview'))
                    ->description(__('View detailed academic status, active package credits, and grade level for each linked child with interactive visual cards'))
                    ->columnSpanFull()
                    ->visible(fn (string $operation) => $operation === 'edit')
                    ->components([
                        View::make('filament.resources.parent-profile.children-details'),
                    ]),
            ]);
    }
}
