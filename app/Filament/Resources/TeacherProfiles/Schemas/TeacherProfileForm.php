<?php

namespace App\Filament\Resources\TeacherProfiles\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class TeacherProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (string $operation, $state, callable $set) => $operation === 'create' ? $set('slug', Str::slug('teacher-'.$state)) : null),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('title')
                    ->label('Professional Title')
                    ->required(),
                TextInput::make('specialization')
                    ->label('Specialization / Subject Focus')
                    ->required(),
                Textarea::make('bio')
                    ->label('Biography & Achievements')
                    ->rows(4)
                    ->columnSpanFull(),
                SpatieMediaLibraryFileUpload::make('photo')
                    ->collection('photo')
                    ->image()
                    ->label('Teacher Photo Upload (Spatie Media)'),
                TextInput::make('years_experience')
                    ->numeric()
                    ->default(5)
                    ->label('Years of Experience'),
                TextInput::make('rating_avg')
                    ->required()
                    ->numeric()
                    ->default(4.9)
                    ->label('Rating (out of 5.0)'),
                TextInput::make('students_count')
                    ->required()
                    ->numeric()
                    ->default(100)
                    ->label('Students Count'),
                Toggle::make('is_featured')
                    ->label('Feature on Homepage & Top Banner')
                    ->default(true),
                Toggle::make('is_public')
                    ->label('Publicly Visible on Website')
                    ->default(true),
                Toggle::make('show_contact_info')
                    ->label('Show Contact Details')
                    ->default(false),
            ]);
    }
}
