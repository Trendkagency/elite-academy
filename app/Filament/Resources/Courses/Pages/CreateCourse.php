<?php

namespace App\Filament\Resources\Courses\Pages;

use App\Filament\Resources\Courses\CourseResource;
use App\Models\Course;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreateCourse extends CreateRecord
{
    protected static string $resource = CourseResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title'] ?? 'course');
        }

        $existing = Course::withTrashed()->where('slug', $data['slug'])->first();
        if ($existing) {
            Notification::make()
                ->title(__('Duplicate Course Slug'))
                ->body(__('A course with the slug ":slug" already exists. Please choose a different title or slug.', ['slug' => $data['slug']]))
                ->danger()
                ->persistent()
                ->send();

            throw ValidationException::withMessages([
                'slug' => __('This course slug is already in use.'),
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
                ->title(__('Duplicate Course Error'))
                ->body(__('Could not create course: A course with this title or slug already exists in the database.'))
                ->danger()
                ->persistent()
                ->send();

            $this->halt();
        }
    }
}
