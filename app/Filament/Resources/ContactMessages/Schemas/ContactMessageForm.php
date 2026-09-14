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
                    ->label(__('Full Name'))
                    ->required(),
                TextInput::make('email')
                    ->label(__('Email address'))
                    ->email()
                    ->required(),
                TextInput::make('phone')
                    ->label(__('Phone'))
                    ->tel(),
                TextInput::make('subject')
                    ->label(__('Subject')),
                Textarea::make('message')
                    ->label(__('Message'))
                    ->required()
                    ->columnSpanFull(),
                Select::make('status')
                    ->label(__('Status'))
                    ->options([
                        'new' => __('New'),
                        'read' => __('Read'),
                        'replied' => __('Replied'),
                        'archived' => __('Archived'),
                    ])
                    ->default('new')
                    ->required(),
                DateTimePicker::make('replied_at')
                    ->label(__('Replied At')),
            ]);
    }
}
