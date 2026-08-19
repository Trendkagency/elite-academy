<?php

namespace App\Filament\Resources\ParentProfiles\Schemas;

use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;

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
                            ->relationship('user')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} ({$record->email})")
                            ->label('Parent User Account')
                            ->searchable(['name', 'email', 'phone'])
                            ->preload()
                            ->required(),
                        Select::make('students')
                            ->relationship('students')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} ({$record->email})")
                            ->label('Linked Children / Students (Select & Manage)')
                            ->multiple()
                            ->preload()
                            ->searchable(['name', 'email']),
                    ]),

                Section::make('Linked Children Detailed Academic Overview (تفاصيل أبناء ولي الأمر)')
                    ->description('View detailed academic status, active package credits, and grade level for each linked child with interactive visual cards')
                    ->columnSpanFull()
                    ->components([
                        View::make('filament.resources.parent-profile.children-details'),
                    ]),
            ]);
    }
}
