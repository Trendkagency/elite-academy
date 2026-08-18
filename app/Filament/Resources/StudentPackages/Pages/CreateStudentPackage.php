<?php

namespace App\Filament\Resources\StudentPackages\Pages;

use App\Filament\Resources\StudentPackages\StudentPackageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStudentPackage extends CreateRecord
{
    protected static string $resource = StudentPackageResource::class;

    public function getTitle(): string
    {
        return '📦 Assign Package to Student';
    }

    public function getSubheading(): ?string
    {
        return 'Issue a new session package to a student. Select a template to auto-fill credits, or enter custom values.';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure activated_at is set if not provided
        $data['activated_at'] ??= now();

        // Auto-compute remaining = total - used if not explicitly set
        if (! isset($data['remaining_sessions']) || $data['remaining_sessions'] === null) {
            $data['remaining_sessions'] = max(0, ($data['total_sessions'] ?? 0) - ($data['used_sessions'] ?? 0));
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return '✅ Package assigned successfully!';
    }
}
