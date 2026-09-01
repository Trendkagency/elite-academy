<?php

namespace App\Filament\Resources\ParentProfiles\Schemas;

use App\Models\ParentProfile;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ParentProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Parent Account Information')
                    ->description('Link parent account with registered student children')
                    ->columnSpanFull()
                    ->components([
                        Select::make('user_id')
                            ->relationship(
                                name: 'user',
                                modifyQueryUsing: function (Builder $query, ?Model $record, string $operation) {
                                    return $query->where(function (Builder $q) use ($record, $operation) {
                                        if ($operation === 'edit' && $record?->user_id) {
                                            $q->whereDoesntHave('parentProfile')->orWhere('id', $record->user_id);
                                        } else {
                                            $q->whereDoesntHave('parentProfile');
                                        }
                                    });
                                }
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} ({$record->email})" . ($record->phone ? " — {$record->phone}" : ''))
                            ->label('Parent User Account')
                            ->searchable(['name', 'email', 'phone'])
                            ->preload()
                            ->required()
                            ->unique(ParentProfile::class, 'user_id', ignoreRecord: true),
                        Select::make('students')
                            ->relationship(
                                name: 'students',
                                modifyQueryUsing: fn (Builder $query) => $query->whereHas('studentProfile')
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} ({$record->email})" . ($record->phone ? " — {$record->phone}" : ''))
                            ->label('Linked Children / Students (Select & Manage)')
                            ->multiple()
                            ->preload()
                            ->searchable(['name', 'email', 'phone']),
                    ]),

                Section::make('Linked Children Detailed Academic Overview (تفاصيل أبناء ولي الأمر)')
                    ->description('View detailed academic status, active package credits, and grade level for each linked child with interactive visual cards')
                    ->columnSpanFull()
                    ->visible(fn (string $operation) => $operation === 'edit')
                    ->components([
                        View::make('filament.resources.parent-profile.children-details'),
                    ]),
            ]);
    }
}
