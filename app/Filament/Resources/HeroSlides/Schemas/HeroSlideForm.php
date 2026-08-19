<?php

namespace App\Filament\Resources\HeroSlides\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class HeroSlideForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label(__('Title / Headline'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('track_label')
                    ->label(__('Track Badge Label'))
                    ->placeholder('e.g. 🚀 FUTURE TALENT ACCELERATOR')
                    ->maxLength(100),
                Textarea::make('subtitle')
                    ->label(__('Subtitle / Description'))
                    ->rows(3)
                    ->columnSpanFull(),
                FileUpload::make('image')
                    ->label(__('Slide Image (Drag & Drop or Upload)'))
                    ->disk('public')
                    ->directory('hero-slides')
                    ->visibility('public')
                    ->image()
                    ->imageEditor()
                    ->columnSpanFull(),
                TextInput::make('cta_primary_url')
                    ->label(__('Primary CTA Link URL'))
                    ->placeholder('/subjects'),
                TextInput::make('cta_secondary_url')
                    ->label(__('Secondary CTA Link URL'))
                    ->placeholder('/student-portal'),
                TextInput::make('sort_order')
                    ->label(__('Sort Order'))
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->label(__('Active Status'))
                    ->default(true),
            ]);
    }
}