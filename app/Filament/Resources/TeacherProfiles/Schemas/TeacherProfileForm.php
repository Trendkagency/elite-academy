<?php

namespace App\Filament\Resources\TeacherProfiles\Schemas;

use App\Models\TeacherProfile;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TeacherProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship(
                        name: 'user',
                        modifyQueryUsing: function (Builder $query, ?Model $record, string $operation) {
                            return $query->where(function (Builder $q) use ($record, $operation) {
                                if ($operation === 'edit' && $record?->user_id) {
                                    $q->whereDoesntHave('teacherProfile')->orWhere('id', $record->user_id);
                                } else {
                                    $q->whereDoesntHave('teacherProfile');
                                }
                            });
                        }
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} ({$record->email})" . ($record->phone ? " — {$record->phone}" : ''))
                    ->label('Teacher Account User')
                    ->searchable(['name', 'email', 'phone'])
                    ->preload()
                    ->required()
                    ->unique(TeacherProfile::class, 'user_id', ignoreRecord: true)
                    ->live()
                    ->afterStateUpdated(function (string $operation, $state, callable $set) {
                        if ($operation === 'create' && $state) {
                            $user = User::find($state);
                            $nameSlug = $user ? Str::slug($user->name) : 'teacher';
                            $set('slug', $nameSlug ? "{$nameSlug}-{$state}" : "teacher-{$state}");
                        }
                    }),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('title')
                    ->label('Professional Title')
                    ->required(),
                TextInput::make('specialization')
                    ->label('Specialization / Subject Focus')
                    ->required(),
                Textarea::make('bio')
                    ->label('Biography & Achievements')
                    ->rows(4)
                    ->columnSpanFull(),
                SpatieMediaLibraryFileUpload::make('photo')
                    ->collection('photo')
                    ->image()
                    ->label('Teacher Photo Upload (Spatie Media)'),
                TextInput::make('years_experience')
                    ->numeric()
                    ->default(5)
                    ->label('Years of Experience'),
                TextInput::make('rating_avg')
                    ->required()
                    ->numeric()
                    ->default(4.9)
                    ->label('Rating (out of 5.0)'),
                TextInput::make('students_count')
                    ->required()
                    ->numeric()
                    ->default(100)
                    ->label('Students Count'),
                Toggle::make('is_featured')
                    ->label('Feature on Homepage & Top Banner')
                    ->default(true),
                Toggle::make('is_public')
                    ->label('Publicly Visible on Website')
                    ->default(true),
                Toggle::make('show_contact_info')
                    ->label('Show Contact Details')
                    ->default(false),
            ]);
    }
}
