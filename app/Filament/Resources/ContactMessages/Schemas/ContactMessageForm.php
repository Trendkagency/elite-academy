<?php

namespace App\Filament\Resources\ContactMessages\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ContactMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('full_name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('subject'),
                Textarea::make('message')
                    ->required()
                    ->columnSpanFull(),
                Select::make('status')
                    ->options(['new' => 'New', 'read' => 'Read', 'replied' => 'Replied', 'archived' => 'Archived'])
                    ->default('new')
                    ->required(),
                DateTimePicker::make('replied_at'),
            ]);
    }
}
