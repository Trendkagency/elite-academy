<?php

namespace App\Filament\Resources\ExceptionRequests\Tables;

use App\Services\Exception\ExceptionRequestService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ExceptionRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('studentUser.name')
                    ->label(__('Student Name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('scope')
                    ->label(__('Scope'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'global' => 'purple',
                        default => 'info',
                    }),
                TextColumn::make('course.title')
                    ->label(__('Specific Course'))
                    ->placeholder(__('Global System Exemption'))
                    ->searchable(),
                TextColumn::make('liveSession.title')
                    ->label(__('Specific Session'))
                    ->placeholder(__('Not Session Specific'))
                    ->formatStateUsing(function ($record) {
                        if (! $record->liveSession) return __('General Course');
                        $scheduled = $record->liveSession->scheduled_at ? $record->liveSession->scheduled_at->format('M d, H:i') : '';
                        return ($record->liveSession->title ?: __('Live Session #' . $record->liveSession->id)) . ($scheduled ? " ({$scheduled})" : '');
                    })
                    ->searchable(),
                IconColumn::make('is_global')
                    ->label(__('Is Global'))
                    ->boolean(),
                TextColumn::make('reason')
                    ->label(__('Reason / Excuse'))
                    ->limit(35)
                    ->searchable(),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'warning',
                    }),
                TextColumn::make('reviewer.name')
                    ->label(__('Reviewed By'))
                    ->placeholder(__('Pending'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('Submitted At'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label(__('Approve (Keep Session)'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading(__('Approve Exception Request'))
                    ->modalDescription(__('Approving this request will excuse the student. Their session package balance will NOT be deducted (or will be refunded if previously deducted).'))
                    ->action(function ($record) {
                        app(ExceptionRequestService::class)->approve($record, auth()->user());

                        Notification::make()
                            ->title(__('Student Exception Approved'))
                            ->body(__('The exception was approved. Session credit is preserved.'))
                            ->success()
                            ->send();
                    })
                    ->visible(fn ($record) => $record->status !== 'approved'),

                Action::make('reject')
                    ->label(__('Reject (Deduct Session)'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading(__('Reject Exception Request'))
                    ->modalDescription(__('Rejecting this request will mark the absence as unexcused and DEDUCT 1 session from the student\'s active package balance.'))
                    ->action(function ($record) {
                        app(ExceptionRequestService::class)->reject($record, auth()->user());

                        Notification::make()
                            ->title(__('Student Exception Rejected'))
                            ->body(__('The exception was rejected. 1 session credit has been deducted from the student package.'))
                            ->danger()
                            ->send();
                    })
                    ->visible(fn ($record) => $record->status !== 'rejected'),

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
