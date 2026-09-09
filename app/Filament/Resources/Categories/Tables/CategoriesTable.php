<?php

namespace App\Filament\Resources\Categories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Category Name'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('slug')
                    ->label(__('Slug'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('color_theme')
                    ->label(__('Color Theme'))
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => $state ?: 'Default'),

                TextColumn::make('subjects_count')
                    ->counts('subjects')
                    ->label(__('Linked Subjects'))
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label(__('Sort Order'))
                    ->numeric()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label(__('Active'))
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order', 'asc')
            ->filters([
                SelectFilter::make('is_active')
                    ->label(__('Active Status'))
                    ->options([
                        '1' => __('Active Only'),
                        '0' => __('Inactive Only'),
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->before(function (DeleteAction $action, $record) {
                        if ($record->subjects()->exists()) {
                            Notification::make()
                                ->title(__('Cannot Delete Category'))
                                ->body(__('This category has linked academic subjects. Please reassign or remove them first.'))
                                ->danger()
                                ->send();

                            $action->halt();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
