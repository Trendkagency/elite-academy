<?php

namespace App\Filament\Resources\StudentPackages\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StudentPackagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('studentUser.name')
                    ->label('Student')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->studentUser?->email ?? '—'),

                TextColumn::make('packageTemplate.name')
                    ->label('Package Plan')
                    ->placeholder('— Custom Plan —')
                    ->searchable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('course.title')
                    ->label('Restricted Course')
                    ->placeholder('All Courses')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('remaining_sessions')
                    ->label('Remaining')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state > 5  => 'success',
                        $state > 0  => 'warning',
                        default     => 'danger',
                    })
                    ->formatStateUsing(fn (int $state) => "{$state} sessions")
                    ->sortable(),

                TextColumn::make('total_sessions')
                    ->label('Total / Used')
                    ->formatStateUsing(fn ($state, $record) => "{$record->total_sessions} / {$record->used_sessions}")
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active'    => 'success',
                        'exhausted' => 'danger',
                        'suspended' => 'gray',
                        default     => 'warning',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'active'    => '✅ Active',
                        'exhausted' => '❌ Exhausted',
                        'suspended' => '🚫 Suspended',
                        'pending'   => '⏳ Pending',
                        default     => ucfirst($state),
                    }),

                TextColumn::make('activated_at')
                    ->label('Activated')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('expires_at')
                    ->label('Expires')
                    ->date('d M Y')
                    ->placeholder('No Expiry')
                    ->sortable()
                    ->color(fn ($record) => $record->expires_at && $record->expires_at->isPast() ? 'danger' : null),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active'    => '✅ Active',
                        'pending'   => '⏳ Pending',
                        'exhausted' => '❌ Exhausted',
                        'suspended' => '🚫 Suspended',
                    ]),
            ])
            ->recordActions([
                // ── Renew Package ────────────────────────────────────────────
                Action::make('renewPackage')
                    ->label('🔄 Renew Package')
                    ->icon('heroicon-o-arrow-path')
                    ->color('primary')
                    ->modalHeading(fn ($record) => "Renew Package — {$record->studentUser?->name}")
                    ->modalDescription(fn ($record) => "Current balance: {$record->remaining_sessions} / {$record->total_sessions} sessions. Renewing will reset credits and re-activate the package.")
                    ->form([
                        \Filament\Schemas\Components\Section::make('Renewal Plan')
                            ->description('Choose a package template to auto-fill credits, or enter manually.')
                            ->schema([
                                Select::make('package_template_id')
                                    ->label('Package Template (Optional)')
                                    ->options(fn () => \App\Models\PackageTemplate::where('is_active', true)
                                        ->get()
                                        ->mapWithKeys(fn ($t) => [$t->id => "{$t->name} — {$t->sessions_count} sessions" . ($t->price ? " ({$t->price} SAR)" : '')])
                                        ->toArray()
                                    )
                                    ->live()
                                    ->afterStateUpdated(function ($state, \Filament\Schemas\Components\Utilities\Set $set) {
                                        if ($state) {
                                            $template = \App\Models\PackageTemplate::find($state);
                                            if ($template) {
                                                $set('new_total_sessions', $template->sessions_count);
                                            }
                                        }
                                    })
                                    ->nullable()
                                    ->native(false)
                                    ->helperText('Selecting a template auto-fills the session count below.'),

                                TextInput::make('new_total_sessions')
                                    ->label('New Total Session Credits')
                                    ->numeric()
                                    ->minValue(1)
                                    ->required()
                                    ->default(fn ($record) => $record->total_sessions)
                                    ->suffix('sessions')
                                    ->helperText('This will become the new total AND remaining credits (used resets to 0).'),
                            ]),

                        \Filament\Schemas\Components\Section::make('Validity & Reason')
                            ->columns(2)
                            ->schema([
                                \Filament\Forms\Components\DateTimePicker::make('new_expires_at')
                                    ->label('New Expiry Date (Optional)')
                                    ->nullable()
                                    ->default(fn ($record) => $record->expires_at)
                                    ->helperText('Leave empty to keep the current expiry.'),

                                TextInput::make('renewal_reason')
                                    ->label('Renewal Reason / Note')
                                    ->default('Package Renewal')
                                    ->required()
                                    ->maxLength(200),
                            ]),
                    ])
                    ->action(function ($record, array $data) {
                        $newExpiry = ! empty($data['new_expires_at'])
                            ? \Carbon\Carbon::parse($data['new_expires_at'])
                            : $record->expires_at;

                        $record->renewPackage(
                            newTotalSessions: (int) $data['new_total_sessions'],
                            packageTemplateId: $data['package_template_id'] ?? null,
                            newExpiresAt: $newExpiry,
                            reason: $data['renewal_reason'] ?? 'Package Renewal',
                        );

                        Notification::make()
                            ->title("✅ Package renewed for {$record->studentUser?->name}")
                            ->body("{$data['new_total_sessions']} session credits activated. Status: Active.")
                            ->success()
                            ->send();
                    }),

                // ── Quick: Add Extra Credits ─────────────────────────────────
                Action::make('addCredits')
                    ->label('Add Credits')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->form([
                        TextInput::make('count')
                            ->label('Sessions to Add')
                            ->numeric()
                            ->minValue(1)
                            ->default(5)
                            ->required()
                            ->suffix('sessions'),
                    ])
                    ->action(function ($record, array $data) {
                        $n = (int) $data['count'];
                        $record->increment('remaining_sessions', $n);
                        $record->increment('total_sessions', $n);
                        if ($record->remaining_sessions > 0 && $record->status === 'exhausted') {
                            $record->update(['status' => 'active']);
                        }
                        Notification::make()
                            ->title("✅ Added {$n} session credits to {$record->studentUser?->name}")
                            ->success()
                            ->send();
                    }),

                // ── Quick: Change Status ─────────────────────────────────────
                Action::make('changeStatus')
                    ->label('Change Status')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->form([
                        Select::make('status')
                            ->label('New Status')
                            ->options([
                                'active'    => '✅ Active',
                                'pending'   => '⏳ Pending',
                                'exhausted' => '❌ Exhausted',
                                'suspended' => '🚫 Suspended',
                            ])
                            ->required()
                            ->native(false),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update(['status' => $data['status']]);
                        Notification::make()
                            ->title("Package status updated to: {$data['status']}")
                            ->success()
                            ->send();
                    }),

                // ── Quick: Deduct 1 Session ──────────────────────────────────
                Action::make('deductCredit')
                    ->label('Deduct 1')
                    ->icon('heroicon-o-minus-circle')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalHeading('Deduct 1 Session Credit')
                    ->modalDescription(fn ($record) => "This will remove 1 session credit from {$record->studentUser?->name}. Current balance: {$record->remaining_sessions}.")
                    ->action(function ($record) {
                        $success = $record->deductSession(null, 'Manual Admin Deduction');
                        if ($success) {
                            Notification::make()->title('1 session credit deducted')->success()->send();
                        } else {
                            Notification::make()->title('Cannot deduct — no credits or package inactive')->danger()->send();
                        }
                    }),

                EditAction::make()->label('Edit'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->emptyStateHeading('No packages assigned yet')
            ->emptyStateDescription('Use the "Assign Package to Student" button above to issue the first package.')
            ->emptyStateIcon('heroicon-o-credit-card');
    }
}
