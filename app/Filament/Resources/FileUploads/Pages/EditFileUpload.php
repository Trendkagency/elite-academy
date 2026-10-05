<?php

namespace App\Filament\Resources\FileUploads\Pages;

use App\Filament\Resources\FileUploads\FileUploadResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditFileUpload extends EditRecord
{
    protected static string $resource = FileUploadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (! empty($data['file_path'])) {
            $filePath = $data['file_path'];
            if (Storage::disk('public')->exists($filePath)) {
                $data['file_size'] = Storage::disk('public')->size($filePath);
                $data['mime_type'] = Storage::disk('public')->mimeType($filePath) ?: 'application/octet-stream';
            }

            if (empty($data['original_name'])) {
                $data['original_name'] = basename($filePath);
            }

            $ext = strtolower(pathinfo($data['original_name'], PATHINFO_EXTENSION));
            $data['file_type'] = $ext === 'pdf' ? 'pdf' : 'image';
        }

        return $data;
    }
}
