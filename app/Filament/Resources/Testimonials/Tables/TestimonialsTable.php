<?php

namespace App\Filament\Resources\Testimonials\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TestimonialsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('reviewer_type')
                    ->label(__('Role'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'student' => 'info',
                        'parent' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('course_name')
                    ->label(__('Course')),
                TextColumn::make('rating')
                    ->label(__('Rating'))
                    ->html()
                    ->formatStateUsing(fn (int $state): string => '<span style="color:#f59e0b; font-weight:800; display:inline-flex; align-items:center; gap:4px;">' . $state . '/5 <i class="fa-solid fa-star"></i></span>'),
                IconColumn::make('is_featured')
                    ->label(__('Featured'))
                    ->boolean(),
                IconColumn::make('is_verified')
                    ->label(__('Verified'))
                    ->boolean(),
            ])
            ->defaultSort('sort_order', 'asc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}