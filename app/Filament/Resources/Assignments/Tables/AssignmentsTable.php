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
                    ->label(__('Course'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('session.title')
                    ->label(__('Session'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title')
                    ->label(__('Assignment Title'))
                    ->weight('bold')
                    ->searchable()
                    ->wrap()
                    ->lineClamp(2),
                TextColumn::make('passing_grade')
                    ->label(__('Passing Grade (%)'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('submissions_count')
                    ->counts('submissions')
                    ->label(__('Submissions')),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge(),
                TextColumn::make('due_at')
                    ->label(__('Due Date'))
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
            ])
            ->defaultSort('created_at', 'desc');
    }
}
