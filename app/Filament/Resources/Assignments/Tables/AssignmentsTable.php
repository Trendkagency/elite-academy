<?php

namespace App\Filament\Resources\Assignments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
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
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
