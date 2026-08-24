<?php

namespace App\Filament\Resources\MeetingProviders\Pages;

use App\Filament\Resources\MeetingProviders\MeetingProviderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMeetingProviders extends ListRecords
{
    protected static string $resource = MeetingProviderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
