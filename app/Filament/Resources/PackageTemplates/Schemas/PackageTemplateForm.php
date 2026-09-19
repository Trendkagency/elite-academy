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
                    ->label(__('Package Plan Name'))
                    ->placeholder(__('e.g., Monthly Pro 12 Sessions'))
                    ->required(),
                TextInput::make('sessions_count')
                    ->label(__('Total Included Sessions'))
                    ->required()
                    ->numeric()
                    ->default(12),
                Textarea::make('description')
                    ->label(__('Package Plan Details & Perks'))
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label(__('Is Active / Available for Enrollment'))
                    ->default(true)
                    ->required(),
            ]);
    }
}
