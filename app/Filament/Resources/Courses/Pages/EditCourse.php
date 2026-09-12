<?php

namespace App\Filament\Resources\Courses\Pages;

use App\Filament\Resources\Courses\CourseResource;
use App\Models\Course;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class EditCourse extends EditRecord
{
    protected static string $resource = CourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->before(function (DeleteAction $action) {
                    $record = $this->getRecord();
                    if ($record->enrollments()->exists()) {
                        Notification::make()
                            ->title(__('Cannot Delete Course'))
                            ->body(__('This course has active student enrollments. Please unenroll or transfer students before deleting.', ['count' => $record->enrollments()->count()]))
                            ->danger()
                            ->persistent()
                            ->send();

                        $action->halt();
                    }
                }),
            ForceDeleteAction::make()
                ->before(function (ForceDeleteAction $action) {
                    $record = $this->getRecord();
                    if ($record->enrollments()->exists()) {
                        Notification::make()
                            ->title(__('Cannot Permanently Delete Course'))
                            ->body(__('This course has active student enrollments. You must remove all enrollments first.'))
                            ->danger()
                            ->persistent()
                            ->send();

                        $action->halt();
                    }
                }),
            RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title'] ?? 'course');
        }

        $existing = Course::withTrashed()
            ->where('slug', $data['slug'])
            ->where('id', '!=', $this->record->id)
            ->first();

        if ($existing) {
            Notification::make()
                ->title(__('Duplicate Course Slug'))
                ->body(__('The slug ":slug" is already taken by another course. Please choose a different slug.', ['slug' => $data['slug']]))
                ->danger()
                ->persistent()
                ->send();

            throw ValidationException::withMessages([
                'slug' => __('This course slug is already in use.'),
            ]);
        }

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        try {
            $record->update($data);
            return $record;
        } catch (UniqueConstraintViolationException|QueryException $e) {
            Notification::make()
                ->title(__('Duplicate Record Error'))
                ->body(__('Could not update course: A duplicate entry already exists with this title or slug in the database.'))
                ->danger()
                ->persistent()
                ->send();

            $this->halt();
        }
    }
}
