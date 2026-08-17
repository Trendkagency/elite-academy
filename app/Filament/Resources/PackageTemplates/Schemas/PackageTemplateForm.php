<?php

namespace App\Filament\Resources\PackageTemplates\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PackageTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Package Plan Name (e.g., Monthly Pro 12 Sessions)')
                    ->required(),
                TextInput::make('sessions_count')
                    ->label('Total Included Sessions')
                    ->required()
                    ->numeric()
                    ->default(12),
                TextInput::make('price')
                    ->label('Price')
                    ->required()
                    ->numeric()
                    ->default(150.00)
                    ->prefix('$'),
                Textarea::make('description')
                    ->label('Package Plan Details & Perks')
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label('Is Active / Available for Enrollment')
                    ->default(true)
                    ->required(),
            ]);
    }
}
