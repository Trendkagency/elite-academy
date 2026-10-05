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
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class AssignmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('course.title')
                    ->label(__('Course'))
                    ->placeholder(fn ($record) => $record->session?->course?->title ?? '—')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('liveSession.title')
                    ->label(__('Live Session'))
                    ->placeholder(fn ($record) => $record->session?->title ?? '—')
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
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                \Filament\Actions\Action::make('worksheet')
                    ->label(__('Worksheet'))
                    ->icon(\Filament\Support\Icons\Heroicon::OutlinedArrowDownTray)
                    ->color('info')
                    ->visible(fn ($record) => ! empty($record->attachment_file_path))
                    ->url(fn ($record) => \Illuminate\Support\Facades\Storage::disk('public')->url($record->attachment_file_path))
                    ->openUrlInNewTab(),
                ViewAction::make(),
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
