<?php

namespace App\Filament\Resources\Articles\Schemas;

use App\Models\Category;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
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
                    ->label(__('Title'))
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (string $operation, $state, callable $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                TextInput::make('slug')
                    ->label(__('Slug'))
                    ->required()
                    ->unique(ignoreRecord: true),
                Select::make('category')
                    ->label(__('Category'))
                    ->options(function () {
                        $dbCategories = Category::query()->where('is_active', true)->orderBy('sort_order')->pluck('name', 'name')->toArray();
                        $defaults = [
                            'Programming' => 'Programming',
                            'AI & Tech' => 'AI & Tech',
                            'Study Tips' => 'Study Tips',
                            'Announcements' => 'Announcements',
                            'Mathematics' => 'Mathematics',
                            'Science' => 'Science',
                        ];
                        return ! empty($dbCategories) ? array_merge($defaults, $dbCategories) : $defaults;
                    })
                    ->searchable()
                    ->preload()
                    ->createOptionUsing(function (string $value) {
                        $category = Category::firstOrCreate(
                            ['slug' => Str::slug($value ?: 'category')],
                            ['name' => $value, 'sort_order' => 0, 'is_active' => true]
                        );
                        return $category->name;
                    })
                    ->required(),
                Select::make('author_user_id')
                    ->relationship('authorUser', 'name')
                    ->label(__('Author')),
                Textarea::make('excerpt')
                    ->label(__('Excerpt'))
                    ->rows(2)
                    ->columnSpanFull(),
                Textarea::make('content')
                    ->label(__('Content'))
                    ->required()
                    ->rows(8)
                    ->columnSpanFull(),
                SpatieMediaLibraryFileUpload::make('featured_image')
                    ->collection('featured_image')
                    ->disk('public')
                    ->visibility('public')
                    ->image()
                    ->imageEditor()
                    ->label(__('Featured Image Upload (Spatie Media)')),
                TextInput::make('read_time_minutes')
                    ->required()
                    ->numeric()
                    ->default(5)
                    ->label(__('Est. Read Time (Minutes)')),
                DateTimePicker::make('published_at')
                    ->label(__('Published At'))
                    ->default(now()),
                Toggle::make('is_published')
                    ->label(__('Show / Publish Blog Post on Website'))
                    ->default(true),
            ]);
    }
}
