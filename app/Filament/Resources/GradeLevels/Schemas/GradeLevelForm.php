<?php

namespace App\Filament\Resources\GradeLevels\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class GradeLevelForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('Grade Level Details'))
                    ->description(__('Manage educational grade levels accessible to students and courses'))
                    ->columns(2)
                    ->columnSpanFull()
                    ->components([
                        TextInput::make('name')
                            ->label(__('Grade Level Name'))
                            ->placeholder('e.g. Third Secondary Grade')
                            ->required()
                            ->maxLength(100)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state ?: 'grade-level'))),

                        TextInput::make('slug')
                            ->label(__('Slug / Identifier'))
                            ->placeholder('e.g. grade-12')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(100),

                        TextInput::make('sort_order')
                            ->label(__('Sort Order'))
                            ->numeric()
                            ->default(1)
                            ->required(),

                        Toggle::make('is_active')
                            ->label(__('Active Status'))
                            ->helperText(__('Enable or disable this grade level across student portal and course offerings'))
                            ->default(true),
                    ]),
            ]);
    }
}