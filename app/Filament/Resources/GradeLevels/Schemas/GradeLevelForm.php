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
                Section::make('🏫 Grade Level Details (بيانات الصف الدراسي)')
                    ->description('Manage educational grade levels (e.g. الصف الثالث الثانوي) accessible to students and courses')
                    ->columns(2)
                    ->columnSpanFull()
                    ->components([
                        TextInput::make('name')
                            ->label('Grade Level Name (اسم الصف الدراسي)')
                            ->placeholder('e.g. الصف الثالث الثانوي')
                            ->required()
                            ->maxLength(100)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state ?: 'grade-level'))),

                        TextInput::make('slug')
                            ->label('Slug / Identifier')
                            ->placeholder('e.g. grade-12')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(100),

                        TextInput::make('sort_order')
                            ->label('Sort Order (ترتيب العرض)')
                            ->numeric()
                            ->default(1)
                            ->required(),

                        Toggle::make('is_active')
                            ->label('Active Status (مُفعل)')
                            ->helperText('Enable or disable this grade level across student portal and course offerings')
                            ->default(true),
                    ]),
            ]);
    }
}