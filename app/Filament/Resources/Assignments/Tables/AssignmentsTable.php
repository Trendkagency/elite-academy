<?php

namespace App\Filament\Resources\Assignments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class AssignmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('session.course.title')
                    ->label('Course')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('session.title')
                    ->label('Session')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title')
                    ->label('Assignment Title')
                    ->searchable(),
                TextColumn::make('passing_grade')
                    ->label('Passing Grade (%)')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('submissions_count')
                    ->counts('submissions')
                    ->label('Submissions'),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('due_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }
}
