<?php

namespace App\Filament\Resources\StudentPackages\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StudentPackageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('student_user_id')
                    ->relationship(
                        name: 'studentUser',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn ($query) => $query->whereHas('studentProfile')
                            ->orWhere(function ($q) {
                                $q->whereDoesntHave('adminProfile')
                                  ->whereDoesntHave('teacherProfile')
                                  ->whereDoesntHave('parentProfile');
                            })
                    )
                    ->label('Student User (Searchable)')
                    ->searchable()
                    ->preload()
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} ({$record->email})")
                    ->required(),
                Select::make('package_template_id')
                    ->relationship('packageTemplate', 'name')
                    ->label('Package Plan Template')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Select::make('course_id')
                    ->relationship('course', 'title')
                    ->label('Target Course (Optional)')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                TextInput::make('total_sessions')
                    ->label('Total Session Credits')
                    ->required()
                    ->numeric()
                    ->default(12),
                TextInput::make('remaining_sessions')
                    ->label('Remaining Available Credits')
                    ->required()
                    ->numeric()
                    ->default(12),
                TextInput::make('used_sessions')
                    ->label('Used Sessions')
                    ->required()
                    ->numeric()
                    ->default(0),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending Activation',
                        'active' => 'Active',
                        'exhausted' => 'Exhausted (0 Credits)',
                        'suspended' => 'Suspended',
                    ])
                    ->default('active')
                    ->required(),
                DateTimePicker::make('activated_at')
                    ->label('Activation Date'),
                DateTimePicker::make('expires_at')
                    ->label('Expiration Date'),
            ]);
    }
}
