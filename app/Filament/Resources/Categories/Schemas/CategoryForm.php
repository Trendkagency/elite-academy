<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('Category Details'))
                    ->description(__('Organize academic subjects, courses, and educational tracks under specialized categories'))
                    ->columns(2)
                    ->columnSpanFull()
                    ->components([
                        TextInput::make('name')
                            ->label(__('Category Name'))
                            ->placeholder('e.g. Natural Sciences')
                            ->required()
                            ->maxLength(100)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state ?: 'category'))),

                        TextInput::make('slug')
                            ->label(__('Slug / URL Identifier'))
                            ->placeholder('e.g. natural-sciences')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(100),

                        ColorPicker::make('color_theme')
                            ->label(__('Color Theme'))
                            ->default('#0D9488')
                            ->nullable(),

                        TextInput::make('sort_order')
                            ->label(__('Sort Order'))
                            ->numeric()
                            ->default(0)
                            ->required(),

                        Toggle::make('is_active')
                            ->label(__('Active Status'))
                            ->helperText(__('Enable or disable this category across the website, student portal, and subject directory'))
                            ->default(true),
                    ]),
            ]);
    }
}
