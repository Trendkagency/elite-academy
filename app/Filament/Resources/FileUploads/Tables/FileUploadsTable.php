<?php

namespace App\Filament\Resources\FileUploads\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class FileUploadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('Title'))
                    ->weight('bold')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->original_name),

                TextColumn::make('file_type')
                    ->label(__('Type'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => strtoupper($state))
                    ->color(fn ($state) => $state === 'pdf' ? 'danger' : 'info')
                    ->sortable(),

                TextColumn::make('formatted_size')
                    ->label(__('Size'))
                    ->sortable(query: fn ($query, $direction) => $query->orderBy('file_size', $direction)),

                TextColumn::make('uploader.name')
                    ->label(__('Uploaded By'))
                    ->badge()
                    ->color(fn ($record) => $record->uploader?->isStudent() ? 'warning' : ($record->uploader?->isTeacher() ? 'success' : 'primary'))
                    ->searchable(),

                TextColumn::make('course.title')
                    ->label(__('Course'))
                    ->placeholder('—')
                    ->searchable(),

                TextColumn::make('studentUser.name')
                    ->label(__('Target Student'))
                    ->placeholder(__('All / General'))
                    ->searchable(),

                TextColumn::make('category')
                    ->label(__('Purpose'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => match($state) {
                        'homework' => __('Homework'),
                        'submission' => __('Submission'),
                        default => __('Material'),
                    })
                    ->color(fn ($state) => match($state) {
                        'homework' => 'warning',
                        'submission' => 'info',
                        default => 'success',
                    })
                    ->sortable(),

                TextColumn::make('due_at')
                    ->label(__('Due Date'))
                    ->dateTime('Y-m-d H:i')
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('downloads_count')
                    ->label(__('Downloads'))
                    ->numeric()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('Uploaded At'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label(__('Purpose / Category'))
                    ->options([
                        'material' => __('Study Material'),
                        'homework' => __('Homework Assignment'),
                        'submission' => __('Student Submission'),
                    ]),
                SelectFilter::make('file_type')
                    ->label(__('File Type'))
                    ->options([
                        'pdf' => 'PDF',
                        'image' => __('Image'),
                    ]),
                SelectFilter::make('course_id')
                    ->label(__('Course'))
                    ->relationship('course', 'title'),
                TrashedFilter::make(),
            ])
            ->actions([
                Action::make('download')
                    ->label(__('Download'))
                    ->icon(Heroicon::OutlinedArrowDownTray)
                    ->color('success')
                    ->url(fn ($record) => route('portal.files.download', $record->id))
                    ->openUrlInNewTab(),

                Action::make('preview')
                    ->label(__('Preview'))
                    ->icon(Heroicon::OutlinedEye)
                    ->color('info')
                    ->url(fn ($record) => route('portal.files.preview', $record->id))
                    ->openUrlInNewTab(),

                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
