<?php

namespace App\Filament\Resources\ParentProfiles\Schemas;

use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class ParentProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Parent User Account')
                    ->searchable()
                    ->required(),
                Select::make('students')
                    ->relationship('students', 'name')
                    ->label('Linked Children / Students (Multi-Select)')
                    ->multiple()
                    ->preload()
                    ->searchable(),
            ]);
    }
}
