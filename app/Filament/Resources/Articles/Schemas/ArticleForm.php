<?php

namespace App\Filament\Resources\Articles\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ArticleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (string $operation, $state, callable $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),
                Select::make('category')
                    ->options([
                        'Programming' => 'Programming',
                        'AI & Tech' => 'AI & Tech',
                        'Study Tips' => 'Study Tips',
                        'Announcements' => 'Announcements',
                        'Mathematics' => 'Mathematics',
                        'Science' => 'Science',
                    ])
                    ->required(),
                Select::make('author_user_id')
                    ->relationship('authorUser', 'name')
                    ->label('Author'),
                Textarea::make('excerpt')
                    ->rows(2)
                    ->columnSpanFull(),
                Textarea::make('content')
                    ->required()
                    ->rows(8)
                    ->columnSpanFull(),
                SpatieMediaLibraryFileUpload::make('featured_image')
                    ->collection('featured_image')
                    ->image()
                    ->label('Featured Image Upload (Spatie Media)'),
                TextInput::make('read_time_minutes')
                    ->required()
                    ->numeric()
                    ->default(5)
                    ->label('Est. Read Time (Minutes)'),
                DateTimePicker::make('published_at')
                    ->default(now()),
                Toggle::make('is_published')
                    ->label('Show / Publish Blog Post on Website')
                    ->default(true),
            ]);
    }
}
