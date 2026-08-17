<?php

namespace App\Filament\Resources\StudentPackages\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StudentPackagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('studentUser.name')
                    ->label('Student Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('packageTemplate.name')
                    ->label('Package Plan')
                    ->placeholder('Custom Package Plan')
                    ->searchable(),
                TextColumn::make('remaining_sessions')
                    ->label('Remaining Credits')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state > 5 => 'success',
                        $state > 0 => 'warning',
                        default => 'danger',
                    })
                    ->sortable(),
                TextColumn::make('used_sessions')
                    ->label('Used Sessions')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_sessions')
                    ->label('Total Credits')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'exhausted' => 'danger',
                        default => 'warning',
                    }),
                TextColumn::make('expires_at')
                    ->label('Expires At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('addCredits')
                    ->label('Add Extra Credits')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->form([
                        TextInput::make('count')
                            ->label('Number of sessions to add')
                            ->numeric()
                            ->default(5)
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $record->increment('remaining_sessions', (int) $data['count']);
                        $record->increment('total_sessions', (int) $data['count']);
                        if ($record->remaining_sessions > 0 && $record->status === 'exhausted') {
                            $record->update(['status' => 'active']);
                        }
                        Notification::make()
                            ->title("Added {$data['count']} session credits successfully!")
                            ->success()
                            ->send();
                    }),
                Action::make('deductCredit')
                    ->label('Deduct 1 Session')
                    ->icon('heroicon-o-minus-circle')
                    ->color('warning')
                    ->action(function ($record) {
                        $success = $record->deductSession(null, 'Manual Admin Deduction');
                        if ($success) {
                            Notification::make()->title('1 Session credit deducted successfully')->success()->send();
                        } else {
                            Notification::make()->title('No remaining credits or package inactive')->danger()->send();
                        }
                    }),
                Action::make('refundCredit')
                    ->label('Refund 1 Session')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->action(function ($record) {
                        $record->refundSession(null, 'Manual Admin Refund');
                        Notification::make()->title('1 Session credit refunded successfully')->info()->send();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
