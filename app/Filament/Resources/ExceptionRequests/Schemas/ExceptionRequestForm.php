<?php

namespace App\Filament\Resources\ExceptionRequests\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ExceptionRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('student_user_id')
                    ->relationship(
                        name: 'studentUser',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn ($query) => $query->whereHas('studentProfile')->latest('created_at')
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} ({$record->email})")
                    ->label(__('Student'))
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('scope')
                    ->options([
                        'course' => __('Single Course Exception'),
                        'global' => __('Global System Exception (All Courses)'),
                    ])
                    ->label(__('Exception Scope'))
                    ->default('course')
                    ->required(),
                Select::make('course_id')
                    ->relationship('course', 'title', modifyQueryUsing: fn ($query) => $query->latest('created_at'))
                    ->label(__('Specific Target Course'))
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Toggle::make('is_global')
                    ->label(__('Apply Globally across all courses for this student'))
                    ->default(false),
                Select::make('live_session_id')
                    ->relationship(
                        name: 'liveSession',
                        titleAttribute: 'id',
                        modifyQueryUsing: fn ($query) => $query->with(['course', 'subject'])->latest('created_at')
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record) => __('Session') . " #{$record->id} — " . ($record->course?->title ?? $record->subject?->name ?? __('Live Session')) . " (" . ($record->scheduled_at?->format('Y-m-d H:i') ?? __('Unscheduled')) . ")")
                    ->label(__('Live Session (Optional)'))
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Textarea::make('reason')
                    ->label(__('Excuse / Reason for Exception'))
                    ->required()
                    ->rows(4)
                    ->columnSpanFull(),
                TextInput::make('attachment_path')
                    ->label(__('Attachment Document Path')),
                Select::make('status')
                    ->label(__('Status'))
                    ->options([
                        'pending' => __('Pending Review'),
                        'approved' => __('Approved (Grants Exemption)'),
                        'rejected' => __('Rejected'),
                    ])
                    ->default('pending')
                    ->required(),
            ]);
    }
}
