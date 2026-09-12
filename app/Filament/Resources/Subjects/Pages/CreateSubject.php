<?php

namespace App\Filament\Resources\Subjects\Pages;

use App\Filament\Resources\Subjects\SubjectResource;
use App\Models\Subject;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreateSubject extends CreateRecord
{
    protected static string $resource = SubjectResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name'] ?? 'subject');
        }

        // Check if slug already exists (including soft-deleted records)
        $existing = Subject::withTrashed()->where('slug', $data['slug'])->first();
        if ($existing) {
            Notification::make()
                ->title(__('Duplicate Subject'))
                ->body(__('The slug ":slug" already exists in the system (or was previously archived). Please enter a unique slug or name.', ['slug' => $data['slug']]))
                ->danger()
                ->persistent()
                ->send();

            throw ValidationException::withMessages([
                'slug' => __('This slug already exists. Please choose a different slug.'),
            ]);
        }

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        try {
            return static::getModel()::create($data);
        } catch (UniqueConstraintViolationException|QueryException $e) {
            Notification::make()
                ->title(__('Duplicate Record Error'))
                ->body(__('Could not create subject: A subject with this name or slug already exists in the database.'))
                ->danger()
                ->persistent()
                ->send();

            $this->halt();
        }
    }
}
