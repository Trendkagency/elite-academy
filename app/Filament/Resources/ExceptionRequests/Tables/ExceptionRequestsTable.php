<?php

namespace App\Filament\Resources\ExceptionRequests\Tables;

use App\Services\Notification\FcmNotificationService;
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
                    ->label('Student Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('scope')
                    ->label('Scope')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'global' => 'purple',
                        default => 'info',
                    }),
                TextColumn::make('course.title')
                    ->label('Specific Course')
                    ->placeholder('Global System Exemption')
                    ->searchable(),
                IconColumn::make('is_global')
                    ->label('Is Global')
                    ->boolean(),
                TextColumn::make('reason')
                    ->label('Reason / Excuse')
                    ->limit(35)
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'warning',
                    }),
                TextColumn::make('created_at')
                    ->label('Submitted At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Approve Exception')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function ($record) {
                        $record->update(['status' => 'approved']);
                        app(FcmNotificationService::class)->notifyExceptionStatus($record);

                        Notification::make()
                            ->title('Student Exception Approved & Notification Sent 🔔')
                            ->success()
                            ->send();
                    })
                    ->visible(fn ($record) => $record->status !== 'approved'),

                Action::make('reject')
                    ->label('Reject Exception')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->action(function ($record) {
                        $record->update(['status' => 'rejected']);
                        app(FcmNotificationService::class)->notifyExceptionStatus($record);

                        Notification::make()
                            ->title('Student Exception Rejected & Notification Sent 🔔')
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
            ]);
    }
}
