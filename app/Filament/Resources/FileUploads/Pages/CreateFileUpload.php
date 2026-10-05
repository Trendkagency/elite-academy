<?php

namespace App\Filament\Resources\FileUploads\Pages;

use App\Filament\Resources\FileUploads\FileUploadResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateFileUpload extends CreateRecord
{
    protected static string $resource = FileUploadResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        if (! empty($data['file_path'])) {
            $filePath = $data['file_path'];
            if (Storage::disk('public')->exists($filePath)) {
                $data['file_size'] = Storage::disk('public')->size($filePath);
                $data['mime_type'] = Storage::disk('public')->mimeType($filePath) ?: 'application/octet-stream';
            } else {
                $data['file_size'] = 0;
                $data['mime_type'] = 'application/octet-stream';
            }

            if (empty($data['original_name'])) {
                $data['original_name'] = basename($filePath);
            }

            $ext = strtolower(pathinfo($data['original_name'], PATHINFO_EXTENSION));
            $data['file_type'] = $ext === 'pdf' ? 'pdf' : 'image';
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->record;
        if ($record->category === 'homework' && empty($record->assignment_id) && $record->course_id) {
            $assignment = \App\Models\Assignment::create([
                'teacher_profile_id' => $record->teacher_profile_id,
                'course_id' => $record->course_id,
                'title' => $record->title,
                'description' => $record->description,
                'attachment_file_path' => $record->file_path,
                'attachment_file_name' => $record->original_name,
                'duration_minutes' => 45,
                'due_at' => $record->due_at ?? now()->addDays(3),
                'status' => 'published',
                'passing_score' => 70.0,
            ]);
            $record->updateQuietly(['assignment_id' => $assignment->id]);
        }
    }
}
