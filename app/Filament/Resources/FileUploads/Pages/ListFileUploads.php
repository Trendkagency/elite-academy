<?php

namespace App\Filament\Resources\FileUploads\Pages;

use App\Filament\Resources\FileUploads\FileUploadResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFileUploads extends ListRecords
{
    protected static string $resource = FileUploadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
