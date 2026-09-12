<?php

namespace App\Filament\Resources\Subjects\Pages;

use App\Filament\Resources\Subjects\SubjectResource;
use App\Models\Subject;
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

class EditSubject extends EditRecord
{
    protected static string $resource = SubjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->before(function (DeleteAction $action) {
                    $record = $this->getRecord();
                    if ($record->courses()->exists()) {
                        Notification::make()
                            ->title(__('Cannot Delete Subject'))
                            ->body(__('This subject has :count active courses linked to it. Please reassign or delete its courses first.', ['count' => $record->courses()->count()]))
                            ->danger()
                            ->persistent()
                            ->send();

                        $action->halt();
                    }
                }),
            ForceDeleteAction::make()
                ->before(function (ForceDeleteAction $action) {
                    $record = $this->getRecord();
                    if ($record->courses()->exists()) {
                        Notification::make()
                            ->title(__('Cannot Permanently Delete Subject'))
                            ->body(__('This subject has active courses attached. Remove or reassign them before permanently deleting.', ['count' => $record->courses()->count()]))
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
            $data['slug'] = Str::slug($data['name'] ?? 'subject');
        }

        // Check if slug exists in any OTHER record (including soft-deleted)
        $existing = Subject::withTrashed()
            ->where('slug', $data['slug'])
            ->where('id', '!=', $this->record->id)
            ->first();

        if ($existing) {
            Notification::make()
                ->title(__('Duplicate Subject'))
                ->body(__('The slug ":slug" is already taken by another subject. Please choose a different slug.', ['slug' => $data['slug']]))
                ->danger()
                ->persistent()
                ->send();

            throw ValidationException::withMessages([
                'slug' => __('This slug is already in use by another subject.'),
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
                ->body(__('Could not update subject: A subject with this name or slug already exists in the database.'))
                ->danger()
                ->persistent()
                ->send();

            $this->halt();
        }
    }
}
