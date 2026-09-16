<?php

namespace App\Filament\Resources\HeroSlides\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HeroSlidesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // Drag-to-reorder handle is added via ->reorderable() on the table
                TextColumn::make('sort_order')
                    ->label(__('Order'))
                    ->sortable()
                    ->width('60px'),

                ImageColumn::make('image')
                    ->label(__('Preview'))
                    ->disk('public')
                    ->height(56)
                    ->width(100)
                    ->extraImgAttributes(['class' => 'rounded-lg object-cover'])
                    ->defaultImageUrl(fn () => asset('images/hero_student.webp')),

                TextColumn::make('title')
                    ->label(__('Headline'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->title),

                TextColumn::make('track_label')
                    ->label(__('Badge'))
                    ->limit(30)
                    ->placeholder('—'),

                TextColumn::make('accent_color')
                    ->label(__('Accent'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'purple' => 'purple',
                        'orange' => 'warning',
                        'rose'   => 'danger',
                        'sky'    => 'info',
                        'amber'  => 'warning',
                        default  => 'success', // teal
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                IconColumn::make('is_active')
                    ->label(__('Active'))
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->reorderable('sort_order')
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}